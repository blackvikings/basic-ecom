<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use  Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $products = Product::select('*');
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function(Product $product){
                    return  '<button class="edit btn btn-primary btn-sm" onclick="editProduct('.$product->id.')" >Edit</button>&nbsp;&nbsp;
                            <button class="Delete btn btn-danger btn-sm" onclick="deleteProduct('.$product->id.')" >Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required',
                'price' => 'required|numeric',
                'description' => 'required',
                'images' => 'required',
                'images.*' => 'mimes:jpg,jpeg,png'
            ]);

            $product = new Product();
            $product->product_name = $request->name;
            $product->product_price = $request->price;
            $product->product_description = $request->description;

            $insert = [];
            if($request->TotalFiles > 0){
                for ($x = 0; $x < $request->TotalFiles; $x++)
                {
                    if ($request->hasFile('images'.$x))
                    {
                        $file = $request->file('images'.$x);
                        $imageName = md5(date("Y-m-d")).md5(time()).'.'.$file->extension();
                        $file->move(public_path('/images'), $imageName);
                        $insert[$x] = '/images/'.$imageName;
                    }
                }
            }

            $product->images = json_encode($insert);
            $product->save();
            return response()->json(['success'=>'Product added successfully']);
        }
        else{
            return response()->json(["message" => "Something is worng."]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    {
        if ($request->ajax()) {

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, Request $request)
    {
        if ($request->ajax()) {
            $data['id'] = $product->id;
            $data['name'] = $product->product_name;
            $data['price'] = $product->product_price;
            $data['description'] = $product->product_description;
            $data['images'] = json_decode($product->images);
            return response()->json(['product' => $data], 201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product, Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'editProductName' => 'required',
                'editProductPrice' => 'required|numeric',
                'editProductDescription' => 'required',
            ]);

            $product->product_name = $request->editProductName;
            $product->product_price = $request->editProductPrice;
            $product->product_description = $request->editProductDescription;

            $insert = [];
            if($request->TotalFiles > 0){
                for ($x = 0; $x < $request->TotalFiles; $x++)
                {
                    if ($request->hasFile('images'.$x))
                    {
                        $file = $request->file('images'.$x);
                        $imageName = md5(time()).'.'.$file->extension();
                        $file->move(public_path('/images'), $imageName);
                        $insert[$x] = '/images/'.$imageName;
                        if ($request->has('imagePaths')){
                            array_push($insert, $request->imagePaths);
                        }
                        $product->images = json_encode($insert);
                    }

                }
            }
            else{
                $product->images = json_encode($request->imagePaths);
            }

            $product->save();
            return response()->json(['success'=>'Product added successfully']);
        }
        else{
            return response()->json(["message" => "Something is worng."]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Request $request)
    {
        if ($request->ajax()) {
            $product->delete();
            return response()->json(['message' => 'Product Deleted Successfully'], 201);
        }
    }
}
