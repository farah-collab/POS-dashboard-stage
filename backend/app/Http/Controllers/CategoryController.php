<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index(){
        $categories=Category::all();
        return response()->json ([
            'categories'=>$categories
        ], 200);

    }
    public function show ($id){
        $category=Category::find($id);
        if(!$category){
            return response()->json([
                'error'=>'category not found plz try another one'
            ],404);
        }
        return response()->json([
            'category'=>$category
        ],200);
   }

   
   public function store(Request $request){
    $request->validate([
        'name'=>'required|string|max:255',
        'description'=>'nullable|string',
    ]);
    $category=Category::create([
        'name'=>$request->name,
        'description'=>$request->description,
    ]);
    return response()->json( [
        'message'=>'category created successfully',
        'category'=>$category
    ],201);
   }



    public function update(Request $request,$id){
        $category=Category::find($id);
        if(!$category){
            return response()->json ([
                'error'=>'category not found plz try another one'
            ],404);
        }
        $request->validate ([
            'name'=>'sometimes|required|string|max:255',
            'description'=>'nullable|string',
        ]);
        $category->update($request->only(['name','description']));
        return response()->json ([
            'message'=>'category updated successfully',
            'category'=>$category
        ],200);
    }


    public function destroy($id){
        $category=Category::find($id);
        if(!$category){
            return response()->json ([
                'error'=>'category not found plz try another one'
            ],404);
        }
        $category->delete();

        return response()->json ([
            'message'=>'category deleted successfully'
        ],200);         
    }
}
