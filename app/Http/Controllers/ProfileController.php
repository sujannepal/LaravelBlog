<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;//required to use file
use Illuminate\Support\Facades\File;//required to use file
use Illuminate\Support\Facades\URL;//required to use file

use App\Profile;
use Auth; //It is required, since we need user id from user table in profile table

class ProfileController extends Controller
{
    //function to display profile
    public function profile(){
    	return view('profiles.profile');
    }

    //function to add profile
    public function addProfile(Request $request){   	 	

    	$this->validate($request, [
    		'name' => 'required',
    		'designation' => 'required',
    		'profile_pic' => 'required'

    	]);
    	
    	$profiles = new profile;
    	$profiles->user_id = Auth::user()->id;
    	$profiles->name = $request->input('name');
    	$profiles->designation = $request->input('designation');

    	if(Input::hasFile('profile_pic')){
    		$file = Input::file('profile_pic');
    		$file->move(public_path(). '/uploads', $file->getClientOriginalName());
    		$url = URL::to("/").'/uploads/'. $file->getClientOriginalName();
    		
    	}
    	$profiles->profile_pic = $url;
    	$profiles->save();
    	return redirect('/home')->with('response','Profile Added Successfully');
    }
}
