<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function show()
    {
        return view('store', [
            'products' => Product::where('stock','>', 0)->with(['product_category'])->get()
        ]);
    }
    public function product_insert_form(){
        return view('product.insert-form', [
        'product_categories' => ProductCategory::get()
        ]);
        
    }

    public function product_edit_form($product_id){
        $product = Product::findOrFail($product_id);
        return view('product.edit-form', [
            'product' => $product,
            'product_categories' => ProductCategory::get()
        ]);
    }

    public function delete_product($product_id){
        if(!Gate::allows('delete-product')) {
            abort(403, 'Unauthorized action.');
        }
        $product = Product::findOrFail($product_id);
        $product->delete();
        return redirect()->route('store')->with('success', 'Product deleted successfully!');
    }


    public function insert_product (Request $request) {

        if(!Gate::allows('insert-product')) {
            abort(403, 'Unauthorized action.');
        }


        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'product_category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'details.string' => 'Product details must be a string.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 1.',
            'stock.required' => 'Stock is required.',
            'stock.integer' => 'Stock must be an integer.',
            'stock.min' => 'Stock cannot be negative.',
            'product_category.required' => 'Product category is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        // Handle file upload
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '-'. $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('product_image'), $imageName);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->details = $request->details;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category_id = $request->product_category;
        $product->image_path = $imageName;
    // Save the product to the database
        $product->save();
        
        return redirect()->route('store')->with('success', 'Product inserted successfully!');
        }


        public function update_product(Request $request, $product_id){

        if(!Gate::allows('edit-product')) {
            abort(403, 'Unauthorized action.');
        }
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'product_category' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
        ], [
            'name.required' => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Product price must be at least 1.',
            'stock.required' => 'Product stock is required.',
            'stock.min' => 'Product stock must be at least 0.',
            'product_category.required' => 'Product category is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
            'image.max' => 'The image may not be greater than 2MB.',
         ]
        );

        $product = Product::findOrFail($product_id);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                unlink(public_path('product_image/' . $product->image_path));
            }
            $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('product_image'), $imageName);
            $product->image_path = $imageName;
        }

        $product->name = $request->name;
        $product->details = $request->details;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category_id = $request->product_category;

        $product->save();

        return redirect()->route('store')->with('success', 'Product updated successfully!');
    }

    public function add_to_cart(Request $request, $product_id){
        $product = Product::findOrFail($product_id);
        $quantity = $request->input('quantity', 1);

        if ($quantity < 1) {
            return redirect()->route('store')->with('error', 'Quantity must be at least 1.');
        }

        $cart = session()->get('cart', []);
        
        $existingQuantity = isset($cart[$product_id]) ? $cart[$product_id]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;

        if ($totalQuantity > $product->stock) {
            return redirect()->route('store')->with('error', 'Requested total quantity exceeds available stock.');
        }

        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $totalQuantity;
        } else {
            $cart[$product_id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }
        
        session()->put('cart', $cart);

        return redirect()->route('store')->with('success', 'Product added to cart successfully!');
    }

    public function view_cart(){
        $cart = session()->get('cart', []);
        return view('store.cart',compact('cart'));
    }

    public function update_cart(Request $request, $product_id){
        $cart = session()->get('cart', []);
        
        if(isset($cart[$product_id])) {
            $quantity = $request->input('quantity', 1);
            
            if($quantity < 1) {
                unset($cart[$product_id]);
            } else {
                $product = Product::findOrFail($product_id);
                if($quantity > $product->stock) {
                    return redirect()->route('view_cart')->with('error', 'Requested quantity exceeds available stock.');
                }
                $cart[$product_id]['quantity'] = $quantity;
            }
            
            session()->put('cart', $cart);
            return redirect()->route('view_cart')->with('success', 'Cart updated successfully!');
        }
        
        return redirect()->route('view_cart')->with('error', 'Product not found in cart.');
    }

    public function remove_from_cart($product_id){
        $cart = session()->get('cart', []);
        
        if(isset($cart[$product_id])) {
            unset($cart[$product_id]);
            session()->put('cart', $cart);
            return redirect()->route('view_cart')->with('success', 'Product removed from cart successfully!');
        }
        
        return redirect()->route('view_cart')->with('error', 'Product not found in cart.');
    }

    public function checkout(Request $request){
        $cart = session('cart', []);
 
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty!');
        }
 
        DB::beginTransaction();
        try {
            $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
 
            $order = Order::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_url' => null,
                'paid_at' => null,
            ]);
 
            foreach ($cart as $product_id => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product_id,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            //Midtrans payment integration
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Prepare item details for Midtrans
            $item_details = [];
            foreach ($cart as $product_id => $item) {
                $item_details[] = [
                    'id'       => $product_id,
                    'price'    => $item['price'],
                    'quantity' => $item['quantity'],
                    'name'     => substr($item['name'], 0, 50)
                ];
            }

            // Create Midtrans Transaction
            $params = [
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => $totalPrice,
                ],
                'item_details' => $item_details,
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
				'callbacks' => [
                    'finish' => route('payment_return', $order->id), // Auto-check status after return
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->payment_url = $snapToken; // Store token to use in the modal later
            $order->save();
            
            DB::commit();
 
            session()->forget('cart'); // Clear cart
            
            // Return to a checkout payment view that triggers the Snap popup
            return view('store.payment', compact('snapToken', 'order'));

            //return redirect()->route('store')->with('success', 'Checkout successful! Thank you for your purchase.');
		} catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function payment_status($order_id){
        $order = Order::findOrFail($order_id);

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        
        try {
            /** @var object $statusResponse */
            $statusResponse = \Midtrans\Transaction::status($order->invoice_number);
            $transactionStatus = $statusResponse->transaction_status;

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $order->status = 'paid';
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }
            } elseif ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->status = 'failed';
            }
            $order->save();

        } catch (\Exception $e) {
            // Transaction not found or other Midtrans API error
 			// Transaction not found usually means user closed the popup before selecting a payment method
            $order->status = 'failed';
            $order->payment_url = null; // Invalidate the token so they cannot use it anymore
            $order->save();
			return redirect()->route('orders')->with('error', 'Unable to retrieve payment status: ' . $e->getMessage());
        }

        if ($order->status == 'paid') {
            return redirect()->route('orders')->with('success', 'Payment successful!');
        } elseif ($order->status == 'pending') {
            return redirect()->route('orders')->with('error', 'Payment is pending. Please complete it.');
        } else {
            return redirect()->route('orders')->with('error', 'Payment failed or expired.');
        }
    }

    public function payment_return($order_id){
        return request()->has('order_id') ? $this->payment_status($order_id) : redirect()->route('payment_status', $order_id);
    }
}
