<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Http\Resources\BlogPost as BlogPostResource;
use Illuminate\Http\Response;
use Validator;


class BlogPostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blog_post = BlogPost::all();
        return $this->sendResponse(['data' =>$blog_post], 'Blog Post list');
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
        $blog_post = BlogPost::find($id);
        if (is_null($blog_post)) {
            return $this->handleError('Blog Post found!');
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

        $blog_post->title = $input['title'];
        $blog_post->body = $input['body'];
        $blog_post->save();
        
        return $this->handleResponse(new BlogPostResource($blog_post), 'Case-Sub successfully updated!');
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
        return $this->handleResponse(new BlogPostResource($blog_post), 'Blog Post deleted!');
    }
}
