<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(){

        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        if(in_array('admin', $userRoles)){
            //Admin can see all orders
            $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        }
        else{
            $orders = Order::with('user')->where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        }

        return view('store.orders', compact('orders', 'userRoles'));
    }

    public function payment_status($order_id){
        return redirect()->route('orders')->with('error', 'Payment cancelled or failed. Please create a new order.');
    
        if ($order->status == 'paid') {
            return redirect()->route('orders')->with('success', 'Payment successful!');
        } elseif ($order->status == 'pending') {
            return redirect()->route('orders')->with('error', 'Payment is pending. Please complete it.');
        } else {
            return redirect()->route('orders')->with('error', 'Payment failed or expired.');
        }
    }

    public function order_details($order_id){
        $order = Order::with('user', 'order_details.product')->findOrFail($order_id);

        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        if(Auth::id() != $order->user_id && !in_array('admin', $userRoles) && !in_array('owner', $userRoles)){
            abort(403);
        }

        return view('store.order_details', compact('order', 'userRoles'));
    }
}
