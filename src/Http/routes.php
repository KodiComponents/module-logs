<?php

$router->group([
    'prefix' => backend_url_segment(),
    'middleware' => ['backend'],
], function (\Illuminate\Routing\Router $router) {
    //\Arcanedev\LogViewer\Http\Routes\LogViewerRoute::register($router);

    $router->get('/logs', [
        'as' => 'log-viewer::logs.list',
        'uses' => 'LogViewerController@listLogs',
    ]);

    $router->delete('/logs/delete', [
        'as' => 'log-viewer::logs.delete',
        'uses' => 'LogViewerController@delete',
    ]);

    $router->group(['prefix' => '/logs/{date}',], function () use ($router) {
        $router->get('/', [
            'as' => 'log-viewer::logs.show',
            'uses' => 'LogViewerController@show',
        ]);

        $router->get('download', [
            'as' => 'log-viewer::logs.download',
            'uses' => 'LogViewerController@download',
        ]);

        $router->get('{level}', [
            'as' => 'log-viewer::logs.filter',
            'uses' => 'LogViewerController@showByLevel',
        ]);
    });
});