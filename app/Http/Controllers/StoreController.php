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
            
            DB::commit();
 
            session()->forget('cart'); // Clear cart
 
            return redirect()->route('store')->with('success', 'Checkout successful! Thank you for your purchase.');
		} catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
}
