<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::inertia('/', 'welcome')->name('home');


Route::view("/welcome", "app");

// dynamic route
Route::get("/about/{name}", function ($name) {
    return view("about.index", ["name" => $name]);
});

// User CRUD Routes (RESTful / MVC)
Route::get('/users', [UserController::class, 'index']);                    // Read all
Route::post('/users', [UserController::class, 'store']);                   // Create
Route::get('/users/complex', [UserController::class, 'complexQuery']);     // Complex Query demo
Route::get('/users/{user}', [UserController::class, 'show']);               // Read one
Route::put('/users/{user}', [UserController::class, 'update']);             // Update
Route::delete('/users/{user}', [UserController::class, 'destroy']);          // Delete

// Playground routes
Route::get("/details/{name}", [UserController::class, "show"]);
Route::get("/dynamic-name/{name1}/{name2}/{name3}", [UserController::class, "dynamic"]);
Route::get("/print-array", [UserController::class, "loop"]);