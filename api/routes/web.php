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

    $router->group(['middleware'=>'auth'], function () use ($router) {

        // Auth logout
        $router->post('/oauth/logout', 'AuthController@logout');

        // Devices
        $router->get('/devices', 'DeviceController@index');
        $router->get('/devices/{id}', 'DeviceController@show');
        $router->post('/devices', 'DeviceController@store');
        $router->put('/devices', 'DeviceController@update');
        $router->delete('/devices/{id}', 'DeviceController@destroy');

        // Purchases
        $router->get('/purchases', 'PurchaseController@index');
        $router->get('/purchases/{id?}', 'PurchaseController@show');
        $router->post('/purchases', 'PurchaseController@store');
        $router->put('/purchases', 'PurchaseController@update');
        $router->delete('/purchases/{id}', 'PurchaseController@destroy');

    });

});
