<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Novel;
use App\Models\User;

class UserController extends Controller
{
    //
    public function show($user_id)
    {
        //find all novel by user id
        $userData = User::findOrFail($user_id);
        $novels = Novel::where('author_id', $user_id)
        ->paginate(10)
        ->through(function ($novel) {
            return [
                'id' => $novel->id,
                'title' => $novel->title,
                'image_url' => $novel->image_url,
            ];
        });
        $user = [
            'id' => $userData->id,
            'name' => $userData->name,
            'avatar' => $userData->avatar_url,
        ];
        return Inertia::render('Profile/UserDetail', [
            'user' => $user,
            'novels' => $novels,
        ]);
    }
}
