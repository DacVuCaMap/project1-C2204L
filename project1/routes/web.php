<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;



Route::prefix('/')->name('user.')->group(function(){
    Route::get('home',[HomeController::class,'homepage'])->name('home');
});

Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/home', [AdminController::class, 'home'])->name('home');
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'postlogin']);
});

Route::prefix('admin/category')->name('category.')->group(function(){
    Route::get('/list', [CategoryController::class, 'list'])->name('list');
    Route::get('/add', [CategoryController::class, 'add'])->name('add');
    Route::post('/add', [CategoryController::class, 'postadd']);
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [CategoryController::class, 'postedit']);
});

Route::prefix('admin/product')->name('product.')->group(function(){
    Route::get('/list', [ProductController::class, 'list'])->name('list');
    Route::get('/add', [ProductController::class, 'add'])->name('add');
    Route::post('/add', [ProductController::class, 'postadd']);
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [ProductController::class, 'postedit']);
});



