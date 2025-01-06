<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use App\Services\AuthService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google\Service\Oauth2;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->authService->login($request->only('email', 'password'));

        $request->session()->regenerateToken();

        return new JsonResponse([
            'message' => 'Authenticated.',
            'status' => 200,
        ]);
    }

    public function googleLogin(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(config('googletoken.clientId'));
        $client->setClientSecret(config('googletoken.secret'));
        $client->setRedirectUri(config('googletoken.redirectUri'));


        // コードを使ってアクセストークンを取得
        $token = $client->fetchAccessTokenWithAuthCode($request->data);

        // アクセストークンを使ってユーザー情報を取得
        $client->setAccessToken($token['access_token']);
        $oauth2 = new Oauth2($client);
        $googleUser = $oauth2->userinfo->get();
        $user = $this->authService->findOrCreateGoogleUser($googleUser);

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Googleログインに成功しました。',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // セッションの内容をログに出力

        return response()->json(['message' => 'ログアウト完了']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/[a-zA-Z]/|regex:/[0-9]/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->authService->register($request->all());
        $request->session()->regenerateToken();

        return response()->json(['message' => 'ユーザーの新規登録に成功しました。']);
    }

    public function user(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        return response()->json($user);
    }

    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        return response()->json($user);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = $this->authService->sendResetLinkEmail($request->email);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed|regex:/[a-zA-Z]/|regex:/[0-9]/',
        ]);

        $status = $this->authService->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if (!$user || $user->deleted_at !== null) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->away(config('frontend.url') . '/user/VerificationSuccess');
        }

        if ($this->authService->verifyEmail($user)) {
            return redirect()->away(config('frontend.url') . '/user/VerificationSuccess');
        }

        return redirect()->away(config('frontend.url') . '/user/VerificationFailed');;
    }

    public function editProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed|regex:/[a-zA-Z]/|regex:/[0-9]/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:255'
        ]);

        $updatedUser = $this->authService->editProfile($user, $request->all());

        return response()->json($updatedUser);
    }

    public function delete(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        // ユーザーの削除処理
        $this->authService->deleteUser($user);

        return response()->json(['message' => 'ユーザーが削除されました。'], 200);
    }
}
