<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request){
        $status = $request->query('status'); // 'paid' or 'pending'
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
        }
        
        $orders = $query->get();
        $userRoles = $user->roles->pluck('role')->toArray();

        return view('orders.index', compact('orders', 'userRoles', 'status'));
    }

    public function order_details($order_id){
        $order = Order::with('user', 'order_details.book')->findOrFail($order_id);

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
