<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\RecruitmentTypeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TalkController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationTokenController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ユーザー認証関連のルート
Route::middleware('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/googleLogin', [AuthController::class, 'googleLogin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/get-user/{id}', [AuthController::class, 'getUser']);

    // 認証されたユーザーに対するルート
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/user/update', [AuthController::class, 'editProfile']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::delete('/user', [AuthController::class, 'delete']);

        // メールアドレス認証の再送信
        Route::post('/email/resend', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent!']);
        })->name('verification.send')->middleware('throttle:6,1');
    });

    // パスワード再設定関連
    Route::post('/reset-password', [AuthController::class, 'reset']);
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);

    // CSRFトークンの取得
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });



    // メールアドレス認証
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

    // お知らせ関連のルート
    Route::get('/notices', [NoticeController::class, 'index']);
    Route::get('/notices/{id}', [NoticeController::class, 'show']);
    Route::middleware(['web', 'auth:sanctum', 'check.role:1'])->group(function () {
        Route::post('/notices', [NoticeController::class, 'store']);
        Route::put('/notices/{id}', [NoticeController::class, 'update']);
        Route::delete('/notices/{id}', [NoticeController::class, 'destroy']);
    });

    // 募集情報関連のルート
    Route::get('/recruitment', [RecruitmentController::class, 'index']);
    Route::get('/recruitment/{id}', [RecruitmentController::class, 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/recruitment', [RecruitmentController::class, 'store']);
        Route::put('/recruitment/{id}', [RecruitmentController::class, 'update']);
        Route::put('/close-recruitment', [RecruitmentController::class, 'closeRecruitment']);
        Route::delete('/recruitment/{id}', [RecruitmentController::class, 'destroy']);
    });

    // 募集種別関連のルート
    Route::get('/recruitmentType', [RecruitmentTypeController::class, 'index']);

    // コメント関連のルート
    Route::get('/comment', [CommentController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/comment', [CommentController::class, 'store']);
    });

    // 応募関連のルート
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/application', [ApplicationController::class, 'store']);
        Route::put('/approval', [ApplicationController::class, 'update']);
    });

    // トーク関連のルート
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/talk', [TalkController::class, 'index']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/messages/{talkId}', [MessageController::class, 'getMessagesByTalkId']);
        Route::post('/messages', [MessageController::class, 'sendMessage']);
        Route::post('/message/set-read', [MessageController::class, 'setReadCount']);

        // firebaseToken
        Route::post('/notification-token', [NotificationTokenController::class, 'store']);
    });
    // 問い合わせ
    Route::post('/contact', [ContactController::class, 'index']);

});

