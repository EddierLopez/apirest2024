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
        Route::post('/user/signup',[UserController::class,'store']);
        Route::get('/user/getidentity',[UserController::class,'getIdentity'])->middleware(ApiAuthMiddleware::class);
        Route::post('/user/upload',[UserController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
        Route::get('/user/getimage/{filename}',[UserController::class,'getImage'])->middleware(ApiAuthMiddleware::class);

        Route::get('/category/{id}',[CategoryController::class,'show']);
        Route::get('/category',[CategoryController::class,'index']);

        Route::get('/post/{id}',[PostController::class,'show']);
        Route::get('/post',[PostController::class,'index']);
        Route::post('/post/upload',[PostController::class,'upload'])->middleware(ApiAuthMiddleware::class);
        Route::get('/post/getimage/{filename}',[PostController::class,'getImage']);

        //RUTAS AUTOMATICAS Restful
        Route::resource('/category',CategoryController::class,['except'=>['show','index','create','edit']])->middleware(ApiAuthMiddleware::class);
        Route::resource('/user',UserController::class,['except'=>['create','edit']])->middleware(ApiAuthMiddleware::class);
        Route::resource('/post',PostController::class,['except'=>['show','index','create','edit']])->middleware(ApiAuthMiddleware::class);
});

