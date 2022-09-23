<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Article::paginate(2);
        // return response([ 'posts' =>  PostResource::collection(Article::paginate(3)), 'message' => 'Successful'], 200);
        return new PostResource($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'content' => 'required|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $image_path = $request->file('image')->store('image', 'public');

        $image_name = str_replace('image/','',$image_path);

        $data['image'] = $image_name;

        $post = Article::create($data);
        return response(['post' => new PostResource($post), 'message' => 'Created successfully'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $post = Article::findOrFail($id);
        return response(['post'=> new PostResource($post), 'message' => 'Success'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
    {
        $post = Article::findOrFail($id);

        $data = $request->all();

        if($request->hasFile('image')){
            $image_path = $request->file('image')->store('image', 'public');
            $image_name = str_replace('image/','',$image_path);
            $data['image'] = $image_name; 
        }else{
            $data['image'] = $post->image;
        }
        
        $post->update($data);

        return response([$data]);


        // if($request->hasFile('image')){
        //     return response([$request->input()]);
        // }else{
        //     return response([$request->all()]);
        // }

        // $post->title = $request->title;
        // $post->content = $request->content;
        // $post->user_id = $request->user_id;
        // $post->category_id = $request->category_id;

        // $post->save();
        return response(['post' => new PostResource($post), 'message' => 'Updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Article::findOrFail($id);

        $post->delete();

        return response(['message' => 'Deleted']);
    }
}
