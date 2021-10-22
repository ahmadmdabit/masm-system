<?php

use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Auth
// Route::post('/api/oauth/logout', ['middleware' => ['auth'], 'uses' => 'AuthController@logout']);

$router->group(['prefix'=>'api'], function () use ($router) {
    // Device register
    $router->post('/devices', 'DeviceController@store');

    $router->group(['middleware'=>'auth'], function () use ($router) {

        // Auth logout
        $router->post('/oauth/logout', 'AuthController@logout');

        // Devices
        $router->get('/devices', 'DeviceController@index');
        $router->get('/devices/{id:[0-9]+}', 'DeviceController@show');
        $router->put('/devices', 'DeviceController@update');
        $router->delete('/devices/{id:[0-9]+}', 'DeviceController@destroy');

        // Purchases
        $router->get('/purchases', 'PurchaseController@index');
        $router->get('/purchases/{id:[0-9]+}', 'PurchaseController@show');
        $router->get('/purchases/check', 'PurchaseController@show');
        $router->post('/purchases', 'PurchaseController@store');
        $router->put('/purchases', 'PurchaseController@update');
        $router->delete('/purchases/{id:[0-9]+}', 'PurchaseController@destroy');

    });

});
