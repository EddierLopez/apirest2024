<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use App\Helpers\JwtAuth;

class UserController extends Controller
{
    //
    public function index(){

    }
    public function show($id){

    }
    public function store(Request $request){
        $data_input=$request->input('data',null);
        if($data_input){
            $data=json_decode($data_input,true);
            $data=array_map('trim',$data);
            $rules=[
                'name'=>'required|alpha',
                'last_name'=>'required', 
                'email'=>'required|email|unique:users',
                'password'=>'required',
                'role'=>'required'               
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){
                $user=new User();
                $user->name=$data['name'];
                $user->last_name=$data['last_name'];
                $user->email=$data['email'];
                $user->password=hash('sha256',$data['password']);
                $user->role=$data['role'];
                $user->save();
                $response=array(
                    'status'=>201,
                    'message'=>'Usuario creado',
                    'user'=>$user
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

    }
    public function destroy($id){

    }
    public function login(Request $request){
        $data_input=$request->input('data',null);
        $data=json_decode($data_input,true);
        $data=array_map('trim',$data);
        $rules=['email'=>'required','password'=>'required'];
        $isValid=\validator($data,$rules);
        if(!$isValid->fails()){
            $jwt=new JwtAuth();
            $response=$jwt->getToken($data['email'],$data['password']);
            return response()->json($response);
        }else{
            $response=array(
                'status'=>406,
                'message'=>'Error en la validación de los datos',
                'errors'=>$isValid->errors(),
            );
            return response()->json($response,406);
        }

    }
    public function getIdentity(Request $request){

    }
    public function uploadImage(Request $request){

    }
    public function getImage($filename){

    }
}
