<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    //afficher tous les produits 
    public function index(){
        //lhnee bach tjib les produits m3a les categories mte3ehom 
        $products=Product::with('category')->get();
        return response()->json([
            'products'=>$products
        ],200);
    }
    //afficher un produit par son id 
    public function show($id){
        $product=Product::with ('category')->find($id);
        if (!$product){
            return response()->json ([
                'error'=>'sorry product not found '
            ],404);

        }
        return respnse()->json([
            'product'=>$product

        ],200);

}    

    //creeer un produit
    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'category_id'=>'required|exists:categories,id',
            'price'=>'required|numeric|min:0',
            'stock_quantity'=>'required|integer|min:0',
            'image'=>'nullable|string',
            'qr_code'=>'nullable|string',
        ]);
        $product=Product::create([
            'name'=>$request->name,
            'category_id'=>$request->category_id,
            'price'=>$request->price,
            'stock_quantity'=>$request->stock_quantity,
            'image'=>$request->image,
            'qr_code'=>$request->qr_code,
        ]);
        return response()->json([
            'message'=>'product created successfully',
            'product'=>$product
        ],201);
    }




    //modifier un produit 
    public function update(Request $request,$id){
        $product=Product::find($id);
        if(!$product){
            return response()->json ([
                'error'=>'product not found plz try another one'
            ],404);
        }
        $request->validate([
            'name'=>'sometimes|required|string|max:255',
            'category_id'=>'sometimes|required|exists:categories,id',
            'price'=>'sometimes|required|numeric|min:0',
            'stock_quantity'=>'sometimes|required|integer|min:0',
            'image'=>'nullable|string',
            'qr_code'=>'nullable|string',

        ]);
        $product->update($request->all());
        return response()->json ([
            'message'=>'product updated successfully',
            'product'=>$product
        ],200);
      


    }
    //suuprimer un produit 
    public function destroy($id){
        $product=Product::find($id);
        if(!$product){
            return response()->json ([
                'error'=>'product not found'
            ],404);
        }
        $product->delete();
        return response()->json([
            'message'=>'product deleted successfully'
        ],200);
    }
}
