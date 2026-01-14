<?php

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

$router->group(['middleware' => 'client.credentials'], function() use ($router) { // keeping middleware consistent with others if they have it, but bootstrapping from books probably had it commented or not. Let's check. 
    // Usually auth is handled by gateway, but microservices might reuse checks. 
    // Looking at Authors/Books they usually don't have auth middleware by default in this workshop unless specified.
    // I will stick to basic routes without middleware for now to ensure it works, then add if needed.
    // Actually, I'll assume no middleware for now to match the "open" nature inside the private network.
});

$router->get('/loans', 'LoanController@index');
$router->post('/loans', 'LoanController@store');
$router->get('/loans/overdue', 'LoanController@overdue'); // Specific route before {id} to avoid collision if {id} catches 'overdue' (though id is usually numeric, it's safer)
$router->get('/loans/{loan}', 'LoanController@show');
$router->put('/loans/{loan}', 'LoanController@update');
$router->delete('/loans/{loan}', 'LoanController@destroy');
$router->get('/loans/user/{user}', 'LoanController@byUser');
