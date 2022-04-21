<?php

namespace App\Dao;

use App\Contracts\Dao\PostDaoInterface;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostDao implements PostDaoInterface
{
    public function search($request)
    {
        if (Auth::check()) {
            $data = Post::leftJoin('users', 'users.id', 'posts.created_by')
                        ->select('*', 'posts.id as id')
                        ->where('title', 'LIKE', '%' . $request->searchInfo . '%')
                        ->orWhere('description', 'LIKE', '%' . $request->searchInfo . '%')
                        ->orWhere('name', 'LIKE', '%' . $request->searchInfo . '%')
                        ->orderBy('posts.updated_at', 'DESC')
                        ->paginate(5)
                        ->setpath('');
            $data->appends(array('searchInfo' => $request->searchInfo));
        } else {
            $data = Post::leftJoin('users', 'users.id', 'posts.created_by')
                        ->select('*', 'posts.id as id')
                        ->where([
                            ['public_flag', '=', 1],
                            ['title', 'LIKE', '%' . $request->searchInfo . '%'],
                        ])
                        ->orWhere([
                            ['public_flag', '=', 1],
                            ['description', 'LIKE', '%' . $request->searchInfo . '%'],
                        ])
                        ->orWhere([
                            ['public_flag', '=', 1],
                            ['name', 'LIKE', '%' . $request->searchInfo . '%'],
                        ])
                        ->orderBy('posts.updated_at', 'DESC')
                        ->paginate(5)
                        ->setpath('');
            $data->appends(array('searchInfo' => $request->searchInfo));
        }
        return $data;
    }

    public function getAllPost()
    {
        if (Auth::check()) {
                $data = Post::leftJoin('users', 'users.id', 'posts.created_by')
                            ->select('*', 'posts.id as id')
                            ->orderBy('posts.updated_at', 'DESC')
                            ->paginate(5);
        } else {
                $data = Post::leftJoin('users', 'users.id', 'posts.created_by')
                            ->select('*', 'posts.id as id')
                            ->where('public_flag', '=', 1)
                            ->orderBy('posts.updated_at', 'DESC')
                            ->paginate(5);
        }
        return $data;
    }

    public function insert($insertData)
    {
        Post::create($insertData);
    }

    public function getPostById($id)
    {
        $data = Post::find($id);
        return $data;
    }

    public function update($updateData)
    {
        Post::whereId($updateData['id'])->update($updateData);
    }

    public function delete($id)
    {
        Post::whereId($id)->delete();
    }

    public function insertComment($request)
    {
        Comment::create($request->except('_token'));
    }
}
