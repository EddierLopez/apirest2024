<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    //
    public function show($id){
        $data=Post::find($id);
        if(is_object($data)){
            $data=$data->load('category','user');
            $response=array(
                'status'=>200,
                'message'=>'Datos del post',
                'data'=>$data,
            );
        }else{
            $response=array(
                'status'=>404,
                'message'=>'Recurso no encontrado',
            );
        }
        return response()->json($response,$response['status']);
    }
    public function store(Request $request){
        $data_input=$request->input('data',null);
        if($data_input){
            $data=json_decode($data_input,true);
            $data=array_map('trim',$data);
            $rules=[
                'title'=>'required',
                'content'=>'required',
                'image'=>'required',
                'category_id'=>'required'
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){
                $post=new Post();
                $post->title=$data['title'];
                $post->content=$data['content'];
                $post->image=$data['image'];
                $post->category_id=$data['category_id'];
                $jwt=new JwtAuth();
                $post->user_id=$jwt->checkToken($request->header('bearertoken'),true)->iss;
                $post->save();
                $response=array(
                    'status'=>201,
                    'message'=>'Post creado',
                    'category'=>$post
                );
            }else{
                $response=array(
                    'status'=>406,
                    'message'=>'Datos inválidos',
                    'errors'=>$isValid->errors()
                );
            }
        }else{
            $response=array(
                'status'=>400,
                'message'=>'No se encontró el objeto data'                
            );
        }
        return response()->json($response,$response['status']);
    }
}
