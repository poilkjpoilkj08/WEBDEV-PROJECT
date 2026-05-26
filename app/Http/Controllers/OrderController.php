<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request){
        $status = $request->query('status'); // 'all', 'paid', 'pending', or null for all
        $userRoles = Auth::user()->roles->pluck('role')->toArray();
        
        $query = Order::with('user');
        
        if(in_array('admin', $userRoles)){
            //Admin can see all orders
            $query = $query->orderByRaw('created_at DESC');
        }
        else{
            $query = $query->where('user_id', '=', Auth::id())->orderByRaw('created_at DESC');
        }
        
        // Apply status filter
        if($status === 'paid'){
            $query = $query->where('status', 'paid');
        } elseif($status === 'pending'){
            $query = $query->where('status', 'pending');
        }
        
        $orders = $query->get();

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
    public function adminIndex() {
      $orders = Order::with('user')->orderByDesc('created_at')->get();
      return view('admin.orders.index', compact('orders'));
    }
}
