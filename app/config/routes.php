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
    $salt = "jJQX2MNmdXYmsW0hdjace5xwYMg3XOSc7oh8m0OF"; // Use something really custom instead of ponies
    #$hash = "9943207de1935bf062a4e849477e06145097ec0a411bc28ef139ece9067f5e15";// "***";
    // Replace *** with the result of: echo hash("sha256", "myPassword".$salt);
    dump(hash("sha256", "root".$salt));
                    return 43;
              });
