<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    //
    public function index(){
        $data=Post::all();
        if($data){
            $data->load("user","category");
        }
        $response=array(
            'status'=>200,
            'data'=>$data
        );
        return response()->json($response,200);
    }
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
    public function update(Request $request){
        $dataInput = $request->input('data',null);
        $data= json_decode($dataInput,true);// el true es para pasar ese json a array
        if(!empty($data)){
            $data=array_map('trim',$data);
            $rules=[
                'id'=>'required',
                'title'=>'required',
                'content'=>'required',
                'image'=>'required',
                'category_id'=>'required'
            ];
            //validamos
            $validate = \validator($data, $rules);
            if($validate->fails()){
                $response=array(
                    'status'    =>406,
                    'message'   =>'Los datos enviados son incorrectos',
                    'errors'    => $validate->errors()
                );
            }
            else{
                $id=$data['id'];
                unset($data['id']);
                unset($data['user_id']);
                unset($data['created_at']);
                $updated=Post::where('id',$id)->update($data);
                if($updated>0){
                    $response=array(
                        'status'    =>200,
                        'message'   =>'Actualizado correctamente'
                    );
                }else{
                    $response=array(
                        'status'    =>400,
                        'message'   =>'No se pudo actualizar'
                    );
                }
            }
        }else{
            $response=array(
                'status'    =>400,
                'message'   =>'Faltan parametros'
            );
        }

        return response()->json($response,$response['status']);
    }
    public function delete($id){
        if(isset($id)){
            $deleted=Post::where('id',$id)->delete();
            if($deleted){
                $response=array(
                    'status'    =>200,
                    'message'   =>'Eliminado correctamente'
                );
            }else{
                $response=array(
                    'status'    =>400,
                    'message'   =>'No se pudo eliminar, puede que el registro ya no exista'
                );
            }
        }else{
            $response=array(
                'status'    =>400,
                'message'   =>'Faltan parametros'
            );
        }
        return response()->json($response,$response['status']);
    }
    public function upload(Request $request){
        $image=$request->file('file0');
        $validate=\Validator::make($request->all(),[
            'file0'=>'required|image|mimes:jpg,jpeg,png'
        ]);
        if(!$image || $validate->fails()){
            $response=array(
                'status'    =>406,
                'message'   =>'Error al subir la imagen'
            );
        }
        else{
            $image_name=\Str::uuid().".".$image->getClientOriginalExtension();
            
            \Storage::disk('posts')->put($image_name,\File::get($image));
            $response=array(
                'status'    =>200,
                'image' =>$image_name,
                'message'   =>'Imagen cargada satisfactoriamente'
            );
        }
        return response()->json($response,$response['status']);
    }
    public function getImage($filename){
        $exist=\Storage::disk('posts')->exists($filename);
        if($exist){
            $file=\Storage::disk('posts')->get($filename);
            return new Response($file,200);
        }else{
            $response=array(
                'status'=>404,
                'message'=>'Imagen no existe'
            );
            return response()->json($response,$response['status']);
        }
    }
}
