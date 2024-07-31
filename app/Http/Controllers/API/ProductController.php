<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;

use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(): JsonResponse
    {
        $products = Product::with('category')->get();
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'exists:categories,id'
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products','public');
            $input['image'] = $path;
        }
        // dd($input);
        $product = Product::create($input);
        $product->load('category');
        
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    } 
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
        $product->load('category');
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'exists:categories,id'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products','public');
            $input['image'] = $path;

            $old_image = $product->image;
            if ($old_image && Storage::disk('public')->exists($old_image)) {
                Storage::disk('public')->delete($old_image);
            }
        }
        
        $product->update($input);
        $product->load('category');
        
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}