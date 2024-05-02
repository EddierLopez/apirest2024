<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;


Route::prefix('v1')->group(
    function(){
        //RUTAS ESPECIFICAS
        Route::post('/user/login',[UserController::class,'login']);

        //RUTAS AUTOMATICAS Restful
        Route::resource('/category',CategoryController::class,['except'=>['create','edit']]);
        Route::resource('/user',UserController::class,['except'=>['create','edit']])->middleware(ApiAuthMiddleware::class);
    }
);

