<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;//required to use file
use Illuminate\Support\Facades\File;//required to use file
use Illuminate\Support\Facades\URL;//required to use file

use Illuminate\Support\Facades\DB;//required to use join condition


use App\Category; //to use Category table (model)
use App\Post; //to deal with POST table
use App\Like; //to deal with LIKE table
use App\Dislike; //to deal with DISLIKE table
use App\Comment; //to deal with Comment table
use App\Profile;


use Auth; //It is required, since we need user id from user table in profile table




class PostController extends Controller
{
    //function to display page for posting data
    public function post(){
    	$categories = Category::all();
    	
    	return view('posts.post', ['categories' => $categories]);
    }

    //function to publish the posted data
    public function addPost(Request $request){
    	$this->validate($request, [
    		'post_title' => 'required',
    		'post_body' => 'required',
    		'category_id' => 'required',    		
    		'post_image' => 'required'

    	]);

    	$posts = new Post;
    	$posts->user_id = Auth::user()->id;
    	$posts->post_title = $request->input('post_title');
    	$posts->post_body = $request->input('post_body');
    	$posts->category_id = $request->input('category_id');


    	if(Input::hasFile('post_image')){
    		$file = Input::file('post_image');
    		$file->move(public_path(). '/posts', $file->getClientOriginalName());
    		$url = URL::to("/").'/posts/'. $file->getClientOriginalName();
    		
    	}
    	$posts->post_image = $url;
    	$posts->save();
    	return redirect('/home')->with('response','Post Added Successfully');


    }

    //function to view post in detail
    public function view($post_id){
        $posts = Post::where('id','=',$post_id)->get();

        //code for like dislike starts
        $likePost = Post::find($post_id);
        $likeCtr = Like::where(['post_id' => $likePost->id])->count();

        $dislikePost = Post::find($post_id);
        $dislikeCtr = Dislike::where(['post_id' => $dislikePost->id])->count();
            //return $likeCtr;
            //exit();
        //code for like dislike ends

        $categories = Category::all();

        /* Join for displaying cooments starts */
         $comments = DB::table('users')
            ->join('comments', 'users.id', '=', 'comments.user_id')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->select('users.name', 'comments.*')
            ->where(['posts.id' => $post_id])
            ->get();
        /* Join for displaying cooments ends */




        return view('posts.view',['posts'=>$posts,'categories'=>$categories, 'likeCtr' => $likeCtr, 'dislikeCtr' => $dislikeCtr, 'comments' => $comments]);
            
           
            //return $categories;
           // exit();

           
    }

    //function to edit post /after clicking on 'edit' option
    public function edit($post_id){
        $categories = Category::all();
        $posts = Post::find($post_id);
        $category = Category::find($posts->category_id);
        return view('posts.edit', ['categories' => $categories, 'posts' => $posts, 'category' => $category]);
    }

    //function for updating / works after clicking update
    public function editPost(Request $request, $post_id){
        $this->validate($request, [
            'post_title' => 'required',
            'post_body' => 'required',
            'category_id' => 'required',            
            'post_image' => 'required'

        ]);

        $posts = new Post;
        $posts->user_id = Auth::user()->id;
        $posts->post_title = $request->input('post_title');
        $posts->post_body = $request->input('post_body');
        $posts->category_id = $request->input('category_id');


        if(Input::hasFile('post_image')){
            $file = Input::file('post_image');
            $file->move(public_path(). '/posts', $file->getClientOriginalName());
            $url = URL::to("/").'/posts/'. $file->getClientOriginalName();
            
        }
        $posts->post_image = $url;
        $data = array(
            'post_title' => $posts->post_title,
            'user_id' => $posts->user_id,

            'post_body' => $posts->post_body,
            'category_id' => $posts->category_id,
            'post_image' => $posts->post_image

        );
        Post::where('id', $post_id)
        ->update($data);

        $posts->update();
        return redirect('/home')->with('response','Post Updated Successfully');



    }

    //function to delete post
    public function deletePost($post_id){
        Post::where('id', $post_id)
        ->delete();
        return redirect('/home')->with('response','Post Deleted Successfully');

    }

    //function to display articles acc. to category
   public function category($cat_id){
    $categories = Category::all();
    //for joining two tables: categories and posts
    $posts = DB::table('posts')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->select('posts.*', 'categories.*')
            ->where(['categories.id' => $cat_id])
            ->get();

            //return $posts;
           // exit();

    return view('categories.categoriesposts', ['categories' => $categories, 'posts' => $posts]);
   }


   //function to count and display like
   public function like($id){
        $loggedin_user = Auth::user()->id;
        $like_user = Like::where(['user_id' => $loggedin_user, 'post_id' => $id])->first();

        if(empty($like_user->user_id)){
            $user_id = Auth::user()->id;
            $email = Auth::user()->email;
            $post_id = $id;

            $like = new Like; //create object of like model

            $like->user_id = $user_id;
            $like->email = $email;
            $like->post_id = $post_id;
            $like->save();

            return redirect("/view/{$id}");
        }
        else {
            return redirect("/view/{$id}");

        }
   }




   //function to count and display dislike
   public function dislike($id){
        $loggedin_user = Auth::user()->id;
        $dislike_user = Dislike::where(['user_id' => $loggedin_user, 'post_id' => $id])->first();

        if(empty($dislike_user->user_id)){
            $user_id = Auth::user()->id;
            $email = Auth::user()->email;
            $post_id = $id;

            $dislike = new Dislike; //create object of dislike model

            $dislike->user_id = $user_id;
            $dislike->email = $email;
            $dislike->post_id = $post_id;
            $dislike->save();

            return redirect("/view/{$id}");
        }
        else {
            return redirect("/view/{$id}");

        }
   }

   //function for comment section
   public function comment(Request $request, $post_id){
         $this->validate($request, [
            'comment' => 'required'

        ]);
         $comment = new Comment; 
         $comment->user_id = Auth::user()->id;
         $comment->post_id = $post_id;

         $comment->comment = $request->input('comment');
         $comment->save();
         return redirect("/view/{$post_id}")->with('response','Comment Added Successfully');

   }

   //function for searching
    public function search(Request $request){
        $user_id = Auth::user()->id;
        $profile = Profile::find($user_id);
        $keyword = $request->input('search');
        $posts = Post::where('post_title', 'LIKE', '%'.$keyword.'%')->get();
        return view('posts.searchposts', ['profile' => $profile, 'posts' => $posts]);
        


   }
}
