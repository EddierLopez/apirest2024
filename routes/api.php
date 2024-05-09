<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\ApiAuthMiddleware;


Route::prefix('v1')->group(
    function(){
        //RUTAS ESPECIFICAS
        Route::post('/user/login',[UserController::class,'login']);
        Route::get('/user/getidentity',[UserController::class,'getIdentity'])->middleware(ApiAuthMiddleware::class);
        Route::post('/user/upload',[UserController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
        Route::get('/user/getimage/{filename}',[UserController::class,'getImage'])->middleware(ApiAuthMiddleware::class);

        Route::get('/post/{id}',[PostController::class,'show']);
        Route::post('/post',[PostController::class,'store'])->middleware(ApiAuthMiddleware::class);//Aplicar el middleware

        //RUTAS AUTOMATICAS Restful
        Route::resource('/category',CategoryController::class,['except'=>['create','edit']]);
        Route::resource('/user',UserController::class,['except'=>['create',
        'edit']])->middleware(ApiAuthMiddleware::class);
    }
);

