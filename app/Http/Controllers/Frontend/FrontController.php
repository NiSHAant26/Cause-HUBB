<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class FrontController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {
        // dd(Post::with('user')->with('comment')->get());
        return Inertia::render('Front/Home', [
            'auth' => auth()->user(),
            'posts' => Post::with('user')->with('comment')->get(),
        ]);
    }

    public function addLike(Request $request, $id)
    {
        $post = Post::find($id);
        // dd($post);
        if(Auth::check())
        {
            $auth = auth()->user()->id;
            $likes = \App\Models\Like::where('user_id', $auth)->where('post_id', $id)->first();
            if($likes)
            {
                $likes->delete();
                $post->likes = $post->likes - 1;
                $post->save();
                return Redirect::route('home');
            }
            else
            {
                $like = new \App\Models\Like();
                $like->user_id = $auth;
                $like->post_id = $id;
                $like->save();
                $post->likes = $post->likes + 1;
                $post->save();
                return Redirect::route('home');
            }
        }
        else
        {
            return Redirect::route('login');
        }
    }

    public function getImage($filename)
    {
        $fileName = Post::where('image', $filename)->first();

        try {
            return response()->file(public_path('images' . DIRECTORY_SEPARATOR . $fileName->image));

        } catch (\Exception $exception) {
            abort('404');
        }
    }

    public function about(): Response
    {
        return Inertia::render('Front/About', [
            'auth' => auth()->user(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Front/Contact', [
            'auth' => auth()->user(),
        ]);
    }

    public function addContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'description' => 'required',
        ]);
        return Redirect::route('home');
    }

    public function donate(): Response
    {
        return Inertia::render('Front/Donation', [
            'auth' => auth()->user(),
        ]);
    }




    /**
     * Update the user's profile information.
     */

}
