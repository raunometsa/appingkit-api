<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image;

use App\User;
use App\Post;
use App\Task;
use App\Location;
use App\Comment;

use Auth;

use Carbon\Carbon;

class ApiController extends Controller
{
    public function createUser(Request $r)
    {
        if (!$r->name) {
            return response()->json(['error' => 'Name missing']);
        }

        if (!$r->email) {
            return response()->json(['error' => 'Email missing']);
        }

        if (!filter_var( $r->email, FILTER_VALIDATE_EMAIL )) {
            return response()->json(['error' => 'Invalid email']);
        }

        if (!$r->password) {
            return response()->json(['error' => 'Password missing']);
        }

        if (strlen($r->password) < 6) {
            return response()->json(['error' => 'Password should be at least 6 chars']);
        }

        $test = User::where('email', $r->email)->first();

        if ($test) {
            return response()->json(['error' => 'Email already exists']);
        }

        $user = new User;
        $user->name = $r->name;
        $user->email = $r->email;
        $user->password = Hash::make($r->password);

        $user->save();

        return response()->json($user);
    }

    public function createPost(Request $r)
    {
        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->text = $r->text;

        $photo = $r->file('photo');

        if ($photo !== null) {
            $name = str_random().'.jpg';

            $original = Image::make($photo);
            $original = $original->stream();

            $screen = Image::make($photo);
            $screen->resize(null, 900, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $screen = $screen->stream();

            Storage::put('public/posts/screens/'.$name, $screen->__toString());
            Storage::put('public/posts/originals/'.$name, $original->__toString());

            $post->photo = $name;
        }

        $post->save();

        return response()->json($post);
    }

    public function posts()
    {
        return Post::orderBy('id', 'desc')->get();
    }

    public function addTask(Request $r)
    {
        if (!$r->title) {
            return response()->json(['error' => 'Task title missing']);
        }

        $task = new Task;
        $task->user_id = Auth::user()->id;
        $task->title = $r->title;
        $task->save();

        return response()->json($task);
    }

    public function tasks()
    {
        return Task::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function deleteTask($task_id)
    {
        Task::where('user_id', Auth::user()->id)
            ->where('id', $task_id)
            ->delete();

        return response()->json(['res' => 'ok']);
    }

    public function toggleTask($task_id)
    {
        $task = Task::where('user_id', Auth::user()->id)
            ->where('id', $task_id)
            ->first();

        if ($task->completed_at) {
            $task->completed_at = null;
        } else {
            $task->completed_at = Carbon::now();
        }

        $task->save();

        return response()->json($task);
    }

    public function pinLocation(Request $r)
    {
        if (!$r->location) {
            return response()->json(['error' => 'Location missing']);
        }

        $location = json_decode($r->location);

        $lat = $location->coords->latitude;
        $lng = $location->coords->longitude;

        $m = new Location;
        $m->user_id = Auth::user()->id;
        $m->lat = $lat;
        $m->lng = $lng;
        $m->save();

        return response()->json($m);
    }

    public function locations()
    {
        return Location::orderBy('id', 'desc')->get();
    }

    public function addComment(Request $r)
    {
        if (!$r->text) {
            return response()->json(['error' => 'Comment missing']);
        }

        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->text = $r->text;
        $comment->save();

        return response()->json($comment);
    }

    public function comments()
    {
        $comments = Comment::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($comments as $comment) {
            $comment->color = color($comment->text);
        }

        return $comments;
    }

    public function deleteComment($comment_id)
    {
        Comment::where('user_id', Auth::user()->id)
            ->where('id', $comment_id)
            ->delete();

        return response()->json(['res' => 'ok']);
    }
}
