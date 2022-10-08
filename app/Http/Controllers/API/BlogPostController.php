<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Http\Resources\BlogPost as BlogPostResource;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Support\Facades\Redis;


class BlogPostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $cachedBlog = Redis::get('blog');
        if(isset($cachedBlog)) {
            $blog_post = json_decode($cachedBlog, FALSE);
            return $this->sendResponse(['data' =>$blog_post], 'Blog Post list from cache');
        }else {
            $blog_post = BlogPost::all();
            Redis::set('blog', $blog_post);
            return $this->sendResponse(['data' =>$blog_post], 'Blog Post list from database');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'body' => 'required'
        ]);
        if($validator->fails()){
            return $this->handleError($validator->errors());       
        }
        $blog_post = BlogPost::create($input);
        Redis::set('blog_'.$blog_post->id, $blog_post);

        return $this->handleResponse(new BlogPostResource($blog_post), 'Blog Post created!');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cachedBlog = Redis::get('blog_'.$id);
        if(isset($cachedBlog)) {
            $blog_post = json_decode($cachedBlog, FALSE);
            return $this->sendResponse(['data' =>$blog_post], 'Blog Post list from cache');
        }else {
            $blog_post = BlogPost::find($id);
            Redis::set('blog_'.$id, $blog_post);
            return $this->sendResponse(['data' =>$blog_post], 'Blog Post list from database');
        }
        return $this->handleResponse(new BlogPostResource($blog_post), 'Blog Post retrieved.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogPost $blog_post)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'body' => 'required'
        ]);

        if($validator->fails()){
            return $this->handleError($validator->errors());       
        }
        Redis::del('blog_' . $blog_post->id);
        $blog_post->title = $input['title'];
        $blog_post->body = $input['body'];
        $blog_post->save();
        Redis::set('blog_' . $blog_post->id, $blog_post);
        return $this->handleResponse(new BlogPostResource($blog_post), 'Blog Post successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogPost $blog_post)
    {
        $blog_post->delete();
        Redis::del('blog_' . $blog_post->id);
        return $this->handleResponse(new BlogPostResource($blog_post), 'Blog Post deleted!');
    }
}
