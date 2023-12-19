<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Post/Index', [
            'posts' => Post::where('user_id', Auth::user()->id)->get(),
        ]);
    }

    public function add(Request $request): Response
    {
        return Inertia::render('Post/Add', [
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->slug = \Str::slug($request->title);
        $post->user_id = Auth::user()->id;
        if($request->hasFile('image'))
        {
            $image_name = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $image_name);
            $post->image = $image_name;
        }
        $post->description = $request->description;
        $post->save();
        return Redirect::route('post.index');
    }

    public function delete(Request $request, $id)
    {
        $post = Post::find($id);
        $post->delete();
        return Redirect::route('post.index');
    }

    public function addComment(Request $request, $id)
    {
        $comment = new  Comment();
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $id;
        $comment->comment = $request[$id];
        $comment->save();
        return Redirect::route('home');
    }

    /**
     * Update the user's profile information.
     */

}
