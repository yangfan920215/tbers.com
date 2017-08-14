<?php

use Illuminate\Routing\Router;

Admin::registerHelpersRoutes();

Route::group([
    'prefix'        => config('admin.prefix'),
    'namespace'     => Admin::controllerNamespace(),
    'middleware'    => ['web', 'admin'],
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    Route::group([
        'prefix'        => 'email',
    ], function (Router $router) {
        $router->get('/', 'email\\SendController@index');

        $router->get('template', 'email\\TemplateController@index');
        $router->post('template', 'email\\TemplateController@createTemp');
        $router->get('template/{id}/edit', 'email\\TemplateController@edit');
        $router->put('template/edit', 'email\\TemplateController@editTemp');

        $router->get('sendtype', 'email\\SendTypeController@index');
        $router->get('sendtype/create', 'email\\SendTypeController@create');
        $router->post('sendtype/createTag', 'email\\SendTypeController@createTag');

        $router->post('upload/main', 'email\\UploadController@main');
        $router->get('upload', 'email\\UploadController@index');

        $router->get('send', 'email\\SendController@index');
        $router->post('send/main', 'email\\SendController@main');
    });


    $router->get('email/template/create', 'email\\TemplateController@create');

});


