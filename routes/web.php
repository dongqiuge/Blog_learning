<?php

use Illuminate\Support\Facades\Route;

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

// 首页
Route::get('/', 'StaticPagesController@home')->name('home');
// 帮助页面
Route::get('help', 'StaticPagesController@help')->name('help');
// 关于页面
Route::get('about', 'StaticPagesController@about')->name('about');

// 用户注册
Route::get('signup', 'UsersController@create')->name('signup');

// 用户资源路由
Route::resource('users', 'UsersController');
// 相当于定义了
// Route::get('/users', 'UsersController@index')->name('users.index');
// Route::get('/users/create', 'UsersController@create')->name('users.create');
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');
// Route::post('/users', 'UsersController@store')->name('users.store');
// Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
// Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
// Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');

// 显示登录页面
Route::get('login', 'SessionsController@create')->name('login');
// 执行登录操作
Route::post('login', 'SessionsController@store')->name('login');
// 退出登录
Route::delete('logout', 'SessionsController@destroy')->name('logout');

// 激活用户账号
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 显示密码重设页面, 填写 Email 的表单
Route::get('password/reset', 'PasswordController@showLinkRequestForm')->name('password.request');
// 发送密码重设邮件, 处理表单提交，成功的话就发送邮件，附带 Token 的链接
Route::post('password/email', 'PasswordController@sendResetLinkEmail')->name('password.email');
// 密码更新页面, 显示更新密码的表单，包含 token
Route::get('password/reset/{token}', 'PasswordController@showResetForm')->name('password.reset');
// 执行密码更新操作, 对提交过来的 token 和 email 数据进行配对，正确的话更新密码
Route::post('password/reset', 'PasswordController@reset')->name('password.update');
