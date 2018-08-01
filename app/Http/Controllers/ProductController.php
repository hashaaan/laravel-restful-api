<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requests;
use App\Product;
use App\Exceptions\ProductNotBelongsToUser;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product as ProductResource;
use Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get products
        $products = Product::paginate(15);

        // Return collection of products as a resource
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = new Product;

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->user_id = Auth::id();

        if($product->save()) {

            $product->response = 'added successfully';

            return new ProductResource($product);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get article
        $product = Product::findOrFail($id);

        $product->response = "match found";

        // Return single product as a resource
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->ProductUserCheck($product);

        if($product->update($request->all())) {

            $product->response = 'updated successfully';

            return new ProductResource($product);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get article
        $product = Product::findOrFail($id);

        $this->ProductUserCheck($product);

        if($product->delete()) {

            $product->response = "deleted successfully";

            return new ProductResource($product);
        }
    }

    public function ProductUserCheck($product)
    {
        if(Auth::id() !== $product->user_id)
        {
            throw new ProductNotBelongsToUser;
        }
    }
}
