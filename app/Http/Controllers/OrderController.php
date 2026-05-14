<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(){

        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        if(in_array('admin', $userRoles)){
            //Admin can see all orders
            $orders = Order::with('user')->orderByRaw('created_at DESC')->get();
        }
        else{
            $orders = Order::with('user')->where('user_id', '=', Auth::id())->orderByRaw('created_at DESC')->get();
        }

        return view('orders.index', compact('orders', 'userRoles'));
    }

    public function order_details($order_id){
        $order = Order::with('user', 'order_details.product')->findOrFail($order_id);

        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        if(Auth::id() != $order->user_id && !in_array('admin', $userRoles) && !in_array('owner', $userRoles)){
            abort(403);
        }

        return view('orders.show', compact('order', 'userRoles'));
    }
}
