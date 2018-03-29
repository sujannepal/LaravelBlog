<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category; //this should be added to use Category model

class CategoryController extends Controller
{
    //function to get category
    public function category(){
    	return view('categories.category');
    }

    //function to add category
    public function addCategory(Request $request){
    	$this->validate($request, [
    		'category' => 'required'
    	]);

    	$category = new Category;
    	$category->category = $request->input('category');// this 'category' inside input is same as name='category' in form
    	$category->save(); //save this category in database
    	return redirect('/category')->with('response','Category added successfully');
    }

}
