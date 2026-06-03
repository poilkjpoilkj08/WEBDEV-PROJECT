<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $query = Refund::with(['order', 'user']);

        // Filter by status if provided - only pending, approved, rejected
        if ($request->has('status') && $request->status) {
            if (in_array($request->status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $request->status);
            }
        }

        $refunds = $query->orderBy('id')->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function request(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'reason' => 'required|string|max:500|min:10',
                'image' => 'nullable|image|mimes:jpeg,png,gif|max:5120',
            ]);

            $order = Order::findOrFail($validated['order_id']);

            // Check if order belongs to authenticated user
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to request refund for this order'
                ], 403);
            }

            // Check if ANY refund already exists for this order (prevent duplicate requests)
            $existingRefund = Refund::where('order_id', $order->id)->first();

            if ($existingRefund) {
                $statusMessage = match($existingRefund->status) {
                    'pending' => 'Your refund request is pending review by our admin team',
                    'approved' => 'Your refund request has been approved',
                    'completed' => 'Your refund has already been processed',
                    'rejected' => 'Your refund request was rejected. Please contact support if you have questions',
                    default => 'A refund request already exists for this order'
                };
                
                return response()->json([
                    'success' => false,
                    'message' => $statusMessage
                ], 400);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('refunds', 'public');
            }

            // Create refund request
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'image_path' => $imagePath,
                'amount' => $order->total_price + ($order->shipping_cost ?? 0),
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund request submitted successfully. Admin will review and respond within 24-48 hours.',
                'refund_id' => $refund->id
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Refund request error:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'order_id' => $request->order_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit refund request'
            ], 500);
        }
    }

    public function approve(Request $request, Refund $refund)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Restore stock for all items in the refunded order
        $order = $refund->order()->with('order_details.book')->first();
        if ($order) {
            foreach ($order->order_details as $detail) {
                if ($detail->store_id && $detail->book_id) {
                    $storeBook = $detail->book->storeLocations()
                        ->where('store_location_id', $detail->store_id)
                        ->first();
                    if ($storeBook) {
                        $detail->book->storeLocations()
                            ->updateExistingPivot($detail->store_id, [
                                'stock' => $storeBook->pivot->stock + $detail->quantity
                            ]);
                    }
                }
            }
        }

        $refund->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes ?? null
        ]);

        // Update order status to refunded
        $order->update([
            'status' => 'refunded',
            'refund_status' => 'approved',
            'refund_amount' => $refund->amount,
            'refund_requested_at' => now()
        ]);

        return redirect()->back()->with('success', 'Refund approved and stock restored successfully');
    }

    public function reject(Request $request, Refund $refund)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason ?? 'Rejected by admin'
        ]);

        // Update order status to show refund was rejected
        $order = $refund->order;
        $order->update([
            'refund_status' => 'rejected',
            'refund_requested_at' => now()
        ]);

        return redirect()->back()->with('success', 'Refund rejected');
    }
}
