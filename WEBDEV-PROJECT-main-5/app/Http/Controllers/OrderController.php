<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request){
        $status = $request->query('status');
        $user = Auth::user();
        
        // User only sees their OWN orders (order history)
        $query = Order::with('user')
            ->where('user_id', '=', $user->id)
            ->orderByRaw('created_at DESC');
        
        // Apply status filter
        if($status === 'paid'){
            $query = $query->where('status', 'paid');
        } elseif($status === 'pending'){
            $query = $query->where('status', 'pending');
        } elseif($status === 'refunded'){
            $query = $query->where('status', 'refunded');
        }
        
        $orders = $query->get();
        $userRoles = $user->roles->pluck('role')->toArray();

        return view('orders.index', compact('orders', 'userRoles', 'status'));
    }

    public function order_details($order_id){
        $order = Order::with('user', 'order_details.book', 'refunds')->findOrFail($order_id);

        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        if(Auth::id() != $order->user_id && !in_array('admin', $userRoles) && !in_array('owner', $userRoles)){
            abort(403);
        }

        return view('orders.show', compact('order', 'userRoles'));
    }
    
    public function adminIndex(Request $request) {
        $status = $request->query('status'); // 'paid' or 'pending'
        
        // Admin sees ALL orders in management panel
        $query = Order::with('user')->orderByDesc('created_at');
        
        // Apply status filter
        if($status === 'paid'){
            $query = $query->where('status', 'paid');
        } elseif($status === 'pending'){
            $query = $query->where('status', 'pending');
        }
        
        $orders = $query->get();
        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Admin: View all refunds with filtering
     */
    public function adminRefundsIndex(Request $request)
    {
        Gate::authorize('update-book'); // Reuse admin authorization
        
        $status = $request->query('status');
        $query = Refund::with('user', 'order')->orderByDesc('created_at');
        
        if ($status && in_array($status, ['pending', 'approved', 'rejected', 'completed'])) {
            $query = $query->where('status', $status);
        }
        
        $refunds = $query->get();
        return view('admin.refunds.index', compact('refunds', 'status'));
    }

    /**
     * Admin: Update order (shipping status, tracking number, etc.)
     */
    public function update(Request $request, $order_id)
    {
        Gate::authorize('update-book'); // Reuse admin authorization
        
        $order = Order::findOrFail($order_id);
        
        // Prevent status changes if delivery has been confirmed by user
        if ($order->delivery_confirmed_by_user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot modify order after delivery confirmation'], 400);
            }
            return back()->with('error', 'Cannot modify this order after delivery confirmation.');
        }
        
        // Prevent status changes if there's a pending or approved refund
        $refundWithIssue = $order->refunds()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($refundWithIssue) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot modify order with pending or approved refund'], 400);
            }
            return back()->with('error', 'Cannot modify order with a pending or approved refund request.');
        }
        
        $validated = $request->validate([
            'status'          => 'nullable|in:pending,paid,cancelled',
            'shipping_status' => 'required|in:pending,processing,delivered',
            'tracking_number' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $updateData = [
            'shipping_status' => $validated['shipping_status'],
        ];

        // Update payment status if provided
        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];

            // If admin manually marks as paid, record paid_at
            if ($validated['status'] === 'paid' && !$order->paid_at) {
                $updateData['paid_at'] = now();
            }

            // If cancelled, clear paid_at and restore stock
            if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
                $updateData['paid_at'] = null;

                // Only restore stock if the order was already paid (stock was deducted on payment)
                if ($order->status === 'paid') {
                    $order->load('order_details.book');
                    foreach ($order->order_details as $detail) {
                        if ($detail->store_id) {
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
            }
        }

        if ($validated['tracking_number']) {
            $updateData['tracking_number'] = $validated['tracking_number'];
        }

        $order->update($updateData);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order updated successfully', 'order' => $order]);
        }

        return back()->with('success', 'Order updated successfully');
    }

    /**
     * User: Confirm delivery of order (after receiving shipment)
     */
    public function confirmDelivery(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $user = Auth::user();

        // Only users can confirm delivery, not admin/owner
        if ($user->roles()->whereIn('role', ['admin', 'owner'])->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Admins cannot confirm delivery for customers'], 403);
            }
            abort(403, 'Admins cannot confirm delivery for customers');
        }

        // Verify order belongs to user
        if ((int)$order->user_id !== (int)$user->id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }

        // Can only confirm if order is delivered
        if ($order->shipping_status !== 'delivered') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Order has not been delivered yet'], 400);
            }
            return back()->with('error', 'Order has not been delivered yet.');
        }

        // Can only confirm within deadline
        if ($order->delivery_confirmation_deadline && now()->isAfter($order->delivery_confirmation_deadline)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Delivery confirmation deadline has passed'], 400);
            }
            return back()->with('error', 'Delivery confirmation deadline has passed.');
        }

        $order->update([
            'delivery_confirmed_at' => now(),
            'delivery_confirmed_by_user' => true,
            'shipping_status' => 'delivered',
            'revenue_recorded' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Delivery confirmed successfully']);
        }

        return back()->with('success', 'Delivery confirmed. Thank you for your purchase!');
    }

    /**
     * User: Request refund for order
     */
    public function requestRefund(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $user = Auth::user();

        // Verify order belongs to user
        if ((int)$order->user_id !== (int)$user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Can only refund if delivery not confirmed
        if (!$order->canRequestRefund()) {
            return response()->json(['error' => 'This order cannot be refunded'], 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Create refund request
        $refund = Refund::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => $validated['reason'],
            'amount' => $order->total_price + $order->shipping_cost,
            'status' => 'pending',
        ]);

        // Update order refund status
        $order->update([
            'refund_requested_at' => now(),
            'refund_status' => 'requested',
            'refund_reason' => $validated['reason'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Refund request submitted']);
        }

        return back()->with('success', 'Refund request submitted. Admin will review shortly.');
    }

    /**
     * Admin: Approve refund
     */
    public function approveRefund(Request $request, $refund_id)
    {
        Gate::authorize('update-book'); // Reuse admin authorization
        
        $refund = Refund::findOrFail($refund_id);
        $order = $refund->order;

        $refund->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $order->update([
            'refund_status' => 'approved',
            'refund_amount' => $refund->amount,
        ]);

        // Restore stock to store
        foreach ($order->order_details as $detail) {
            if ($detail->store_id) {
                $book = $detail->book;
                $storeBook = $book->storeLocations()
                    ->where('store_location_id', $detail->store_id)
                    ->first();
                
                if ($storeBook) {
                    $newStock = $storeBook->pivot->stock + $detail->quantity;
                    $book->storeLocations()
                        ->updateExistingPivot($detail->store_id, ['stock' => $newStock]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Refund approved']);
        }

        return back()->with('success', 'Refund approved');
    }

    /**
     * Admin: Reject refund
     */
    public function rejectRefund(Request $request, $refund_id)
    {
        Gate::authorize('update-book'); // Reuse admin authorization
        
        $refund = Refund::findOrFail($refund_id);
        $order = $refund->order;

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $validated['reason'] ?? null,
        ]);

        $order->update([
            'refund_status' => 'rejected',
            'payment_status' => 'refund_rejected',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Refund rejected']);
        }

        return back()->with('success', 'Refund rejected');
    }

    /**
     * Admin: Mark refund as completed
     */
    public function completeRefund(Request $request, $refund_id)
    {
        Gate::authorize('update-book'); // Reuse admin authorization
        
        $refund = Refund::findOrFail($refund_id);
        $order = $refund->order;

        $refund->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $order->update([
            'refund_status' => 'completed',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Refund marked as completed']);
        }

        return back()->with('success', 'Refund completed');
    }
}