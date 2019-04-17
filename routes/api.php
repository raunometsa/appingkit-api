<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('users', 'ApiController@createUser')
    ->name('api.users.create');

Route::middleware('auth:api')->group(function () {
    Route::post('posts', 'ApiController@createPost')
            ->name('api.posts.create');

    Route::get('posts', 'ApiController@posts')
            ->name('api.posts');

    Route::post('tasks', 'ApiController@addTask')
            ->name('api.tasks.create');

    Route::get('tasks', 'ApiController@tasks')
            ->name('api.tasks');

    Route::get('tasks/{task_id}/delete', 'ApiController@deleteTask')
            ->name('api.tasks.delete');

    Route::get('tasks/{task_id}/toggle', 'ApiController@toggleTask')
            ->name('api.tasks.toggle');

    Route::post('locations', 'ApiController@pinLocation')
    ->name('api.locations.pin');

    Route::get('locations', 'ApiController@locations')
            ->name('api.locations');

    Route::post('comments', 'ApiController@addComment')
    ->name('api.comments.add');

    Route::get('comments', 'ApiController@comments')
            ->name('api.comments');

    Route::get('comments/{comment_id}/delete', 'ApiController@deleteComment')
            ->name('api.comments.delete');
});
