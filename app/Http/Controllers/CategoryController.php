<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;


class CategoryController extends Controller
{
    /**
     * Metodo GET para obtener todos los registros
     */
    public function index(){
        $data=Category::all();
        $response=array(
            "status"=>200,
            "message"=>"Todos los registro de categoria",
            "data"=>$data
        );
        return response()->json($response,200);

    }
    /**
     * Metodo POST para crear un registro
     */
    public function store(Request $request){
        $data_input=$request->input('data',null);
        if($data_input){
            $data=json_decode($data_input,true);
            $data=array_map('trim',$data);
            $rules=[
                'name'=>'required|alpha'
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){
                $category=new Category();
                $category->name=$data['name'];
                $category->save();
                $response=array(
                    'status'=>201,
                    'message'=>'Categoria creada',
                    'category'=>$category
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
    public function show($id){
        $data=Category::find($id);
        if(is_object($data)){
            $data=$data->load('posts');
            $response=array(
                'status'=>200,
                'message'=>'Datos de la categoria',
                'category'=>$data
            );
        }else{
            $response=array(
                'status'=>404,
                'message'=>'Recurso no encontrado'                
            );
        }
        return response()->json($response,$response['status']);
    }
    public function destroy($id){
        if(isset($id)){
           $deleted=Category::where('id',$id)->delete();
           if($deleted){
                $response=array(
                    'status'=>200,
                    'message'=>'Categoria eliminada',                    
                );
           }else{
            $response=array(
                'status'=>400,
                'message'=>'No se pudo eliminar el recurso, compruebe que exista'                
            );
           }
        }else{
            $response=array(
                'status'=>406,
                'message'=>'Falta el identificador del recurso a eliminar'                
            );
        }
        return response()->json($response,$response['status']);
    }
}
