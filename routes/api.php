<?php

use Illuminate\Http\Request;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\ResponseQuizController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResources([
    'quizzes' => QuizController::class,
    'questions' => QuestionController::class,
    'variants' => VariantController::class,
    'responses' => ResponseQuizController::class
]);

Route::get('/personalQuizzes', [QuizController::class, "getPersonalQuizzes"]);
Route::get('/responses/getAll/{id}', [ResponseQuizController::class, "getAll"]);

Route::post('/moveQuestions', [QuestionController::class, "moveQuestions"]);
Route::post('/quizzes/update/{id}', [QuizController::class, "updateQuiz"]);
Route::post('/uploadImage/{id}', [QuizController::class, "uploadImage"]);
Route::post('/user/edit/{id}', [UserController::class, "edit"])->middleware('bearer-auth');;
Route::post('/register', [UserController::class, "register"]);
Route::post('/login', [UserController::class, "login"]);
Route::get('/refresh', [UserController::class, "refresh"])->middleware('bearer-auth');
// test

// Route::get('test', function () {
//     return [
//         "key" => \Illuminate\Support\Str::random(30),
//         "port" => 4743,
//         "api_url" => "http://test-hide-url.dev"
//     ];
// })->middleware('bearer-auth');
