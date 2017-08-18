<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->group(['middleware' => 'dom'], function () use ($app) {
    // 获取资源目录
    $defaultController = 'IndexController';
    $app->get('/avgleinit', $defaultController . '@avgleInit');
    $app->post('/contact', $defaultController . '@contact');
    // 索引视频
    $app->get('/archive', $defaultController . '@archive');



    $app->group(['prefix' => 'wallet', 'middleware' => 'token'], function () use ($app) {
        $User = 'WalletController@';
        $app->post('/', $User . 'index');
        $app->post('/agent', $User . 'agent');
        $app->post('/paycheck', $User . 'paycheck');
        $app->post('/paylist', $User . 'payList');
        $app->post('/paybanks', $User . 'payBanks');
        $app->post('/order', $User . 'order');
        $app->post('/cash', $User . 'cash');
        $app->post('/trans', $User . 'trans');

        // 下发某款游戏的访问UR
        $app->post('/geturl', $User . 'getUrl');
        $app->options('/geturl', $User . 'getUrl');


        $app->options('/', $User . 'index');
        $app->options('/agent', $User . 'agent');
        $app->options('/paycheck', $User . 'paycheck');
        $app->options('/paylist', $User . 'payList');
        $app->options('/paybanks', $User . 'payBanks');
        $app->options('/order', $User . 'order');
        $app->options('/cash', $User . 'cash');
        $app->options('/trans', $User . 'trans');


    });
});



// index, show这些则不需要$app->group(['prefix' => 'projects'], function ($app){ $app->get('/', 'App\Http\Controllers\ProjectsController@index'); $app->get('/{projectId}', 'App\Http\Controllers\ProjectsController@show');});


/*    Route::get('logout', ['as' => 'logout', 'uses' => $Authority . 'logout']);

    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('signin', ['as' => 'signin', 'uses' => $Authority . 'signin']);
        Route::any('pt-transfer', ['as' => 'pt-transfer', 'uses' => $Authority . 'ptTransfer']);
        Route::post('reg/{code?}', ['as' => 'reg', 'uses' => $Authority . 'reg','before'=>'max-access']);
        Route::any('check-field-is-exist/{field}/{username}',['as'=>'check-field-is-exist','uses'=>$Authority.'checkFieldIsExist']);
        Route::any('check-captcha-error',['as'=>'check-captcha-error','uses'=>$Authority.'checkCaptchaError']);
        Route::post('post-forgot-password',['as'=>'post-forgot-password','uses'=>$Authority.'postForgotPassword']);
        Route::get('get-reset/{token}',['as'=>'get-reset','uses'=>$Authority.'getReset']);
        Route::post('post-reset',['as'=>'post-reset','uses'=>$Authority.'postReset']);
        Route::get('activate-email',['as'=>'activate-email','uses'=>$Authority.'activateEmail']);
        Route::post('get-pt-balance/{user_id}',['as'=>'get-pt-balance','uses'=>$Authority.'getBalance']);
    });*/

// $app->post('/user/signup', 'UserController@signup');

// Route::any('signup/{code?}', ['as' => 'signup', 'uses' => $Authority . 'signup','before'=>'max-access']);


// Route::get('/register', 'UserController@register')->name('register');

/*Route::any('user/register', ['as' => $resource . '.card-lock', 'uses' => $controller . 'cardLock']);

$app->get('user/register', function () use ($app) {
    $app->post('register', 'App\Http\Controllers\UserController@register');
});*/

/*$app->group(['prefix' => 'user'], function() use ($app) {
    $app->get('register', 'App\Http\Controllers\Users\UserController@register');
    $app->post('register', 'App\Http\Controllers\UserController@register');
});*/


# User管理
/*Route::group(['prefix' => 'user'], function() {
    Route::get('register', function() {
        echo 'zzz';
        exit;
    });
});*/