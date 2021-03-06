<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/importar', function () {
    return view('importarXml');
});



Route::get('/home', 'Controller@showHome');
Route::get('/', 'Controller@showLogar');
Route::post('/logar', 'Controller@validate_login');
Route::get('/deslogar', 'Controller@deslogar');



Route::post('/xml', 'ControllerXml@salvarXml');

Route::post('/filtrar', 'ControllerFiltro@filtrar');
Route::post('/filtrar_graficos', 'ControllerFiltro@filtrar');
Route::post('/filtrar_ajax', 'ControllerFiltro@teste');
Route::get('/deletar', 'ControllerXml@deletarTudo');


Route::get('/usuarios', 'ControllerUsuarios@show');
Route::post('/cadastrar_usuario', 'ControllerUsuarios@create');
Route::post('/deletar_usuario', 'ControllerUsuarios@delete');
Route::post('/show_editar_usuario', 'ControllerUsuarios@showEdit');
Route::post('/editar_usuario', 'ControllerUsuarios@edit');




Auth::routes();


