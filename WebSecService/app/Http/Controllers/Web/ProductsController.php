<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsController extends Controller {

	use ValidatesRequests;

	public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

	public function list(Request $request) {

		$query = Product::select("products.*");

		$query->when($request->keywords, 
		fn($q)=> $q->where("name", "like", "%$request->keywords%"));

		$query->when($request->min_price, 
		fn($q)=> $q->where("price", ">=", $request->min_price));
		
		$query->when($request->max_price, fn($q)=> 
		$q->where("price", "<=", $request->max_price));
		
		$query->when($request->order_by, 
		fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));

		$products = $query->get();

		return view('products.list', compact('products'));
	}

	public function edit(Request $request, Product $product = null) {

		if(!auth()->user()) return redirect('/');

		$product = $product??new Product();

		return view('products.edit', compact('product'));
	}

	public function save(Request $request, Product $product = null) {

		$this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
	        'description' => ['required', 'string', 'max:1024'],
	        'price' => ['required', 'numeric'],
	        'qty' => ['required', 'numeric'],
	    ]);

		$product = $product??new Product();
		$product->fill($request->all());
		$product->save();

		return redirect()->route('products_list');
	}

	public function delete(Request $request, Product $product) {

		if(!auth()->user()->hasPermissionTo('delete_products')) abort(401);

		$product->delete();

		return redirect()->route('products_list');
	}
	public function buy($id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();

        // Check if the product is in stock
        if ($product->qty <= 0) {
            return redirect()->route('products_list')->with('error', 'Product is out of stock!');
        }

        // Check if the user has sufficient credit
        if ($user->credit < $product->price) {
            return redirect()->route('insufficient.credit');
			// return redirect()->route('products_list')->with('error', 'insufficient credit!');
        }

        // Deduct the product price from the user's credit
        $user->credit -= $product->price;
        $user->save();

        // Decrease the product stock by 1
        $product->qty -= 1;
        $product->save();

        // Insert the purchase record into the bought_products table
        DB::table('bought_products')->insert([
            'uid' => $user->id,
            'pid' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add a success message to the session
        return redirect()->route('products_list')->with('success', 'Product purchased successfully!');
    }
	public function boughtProducts(Request $request)
    {
        $user = auth()->user();

        // Check if the user is a customer
        if ($user->hasRole('Customer')) {
            // Fetch only the products bought by the logged-in customer
            $boughtProducts = DB::table('bought_products')
                ->join('users', 'bought_products.uid', '=', $user_>$request)
                ->join('products', 'bought_products.pid', '=', 'products.id')
                ->where('bought_products.uid', $user->$id)
                ->select('bought_products.*', 'users.name as user_name', 'products.name as product_name')
                ->orderBy('bought_products.created_at', 'DESC')
                ->get();
        } else {
            // Fetch all bought products for employees or admins
            $boughtProducts = DB::table('bought_products')
                ->join('users', 'bought_products.uid', '=', 'users.id')
                ->join('products', 'bought_products.pid', '=', 'products.id')
                ->select('bought_products.*', 'users.name as user_name', 'products.name as product_name')
                ->orderBy('bought_products.created_at', 'DESC')
                ->get();
        }

        return view('products.bought', compact('boughtProducts'));
    }
} 