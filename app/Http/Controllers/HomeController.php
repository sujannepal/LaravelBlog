<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //esp to work by joining two tables
use Auth; //to use auth
Use App\Profile; //to interact with profile table
Use App\users; //to interact with users table
Use App\Post; //to interact with users table


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $profile = DB::table('users')
                    ->join('profiles','users.id', '=', 'profiles.user_id')
                    ->select('users.*', 'profiles.*')
                    ->where(['profiles.user_id' => $user_id])
                    ->first();
        $posts = Post::paginate(2);
              
        return view('home',['profile' => $profile, 'posts' => $posts]);
    }
}
