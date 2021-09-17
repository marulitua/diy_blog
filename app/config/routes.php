<?php

use Framework\Routes;

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\PostController;

Routes::get('/', HomeController::class, 'index');
Routes::get('/login', AuthController::class, 'loginPage');
Routes::post('/login', AuthController::class, 'loginSubmit');
Routes::get('/logout', AuthController::class, 'logout');
Routes::get('/profile', ProfileController::class, 'showProfile');
Routes::post('/post/{$id}', PostController::class, 'showPost');
Routes::get('/versio', function () {
    dump(app());
                    return 43;
              });
