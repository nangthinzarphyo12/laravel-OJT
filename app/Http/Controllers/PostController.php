<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PostServiceInterface;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->postService->getAllPost();
        return view('posts.index', ['posts' => $data])
            ->with('i', (request()->input('page', 1) - 1) * 5)
            ->with(['searchInfo' => '']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //dd(Auth::check());
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:1000',
        ]);
        $data = $this->postService->insert($request);
        return redirect()->route('postList')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detail = Post::with('comments.user')->where('id', $id)->first();
        $commentDetails = $detail->comments;
        $data = $this->postService->getPostById($id);
        return view('posts.detail', ['post' => $data, 'commentDetails' => $commentDetails]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->postService->getPostById($id);
        return view('posts.edit', ['post' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:1000',
        ]);
        $data = $this->postService->update($request);
        return redirect()->route('posts.detail', $request->id)
            ->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, $id)
    {
        $data = $this->postService->delete($id);
        return redirect()->route('postList')
            ->with('success', 'Post deleted successfully');
    }

    public function search(Request $request)
    {
        $data = $this->postService->search($request);
        return view('posts.index', ['posts' => $data])
            ->with(['searchInfo' => $request->searchInfo]);
    }

    public function commentStore(Request $request, $postId)
    {
        $request->validate([
            'comment_text' => 'required|max:255',
        ]);
        $request['post_id'] = $postId;
        $data = $this->postService->insertComment($request);
        return redirect()->route('posts.detail', $request->post_id)
            ->with('success', 'Comment has been posted.');
    }

}
