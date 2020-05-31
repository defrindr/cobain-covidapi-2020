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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });
// 


$router->get('/','CovidController@index');
$router->get('/province/{province}','CovidController@province');
$router->get('/jatim','CovidController@jatimall');
$router->get('/jatim/{zone}','CovidController@jatim');
$router->get('/jatim/{zone}','CovidController@jatim');
$router->get('/hospital','CovidController@getRs');
$router->get('/world/{nation}','CovidController@world');
$router->get('/documentation',function() use($router){
	return response("Read Documentation Here . \n<br> <a href='https://gist.github.com/defrindr/5771102cc73f48cfa8baf61fa45b63bc'>https://gist.github.com/defrindr/5771102cc73f48cfa8baf61fa45b63bc</a>");
} );
$router->get('/{notFound}',function() use($router){
	return response("Develop by Defri Indra M");
} );

