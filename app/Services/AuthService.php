<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;

/**
 * 認証に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class AuthService
{
    /**
     * @param AuthManager $auth
     */
    public function __construct(
        private readonly AuthManager $auth,
    ) {
    }

    /**
     * ログイン処理のビジネスロジックを実行します。
     *
     * @param array $credentials ユーザーの認証情報（メールアドレスとパスワード）
     * @return bool ログイン成功時はtrue
     * @throws \Illuminate\Validation\ValidationException 認証失敗時
     */
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        // Googleユーザーが通常のログインを試みた場合はエラーを返す
        if ($user->isGoogleUser()) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        if (!$this->auth->guard()->attempt($credentials)) {
            throw new AuthenticationException('メールアドレスとパスワードが一致しません。');
        }

        return true;
    }

    /**
     * Google OAuth2.0を用いたログインOR新規登録を実行します。
     * @param mixed $googleUser
     * @return object|User|\Illuminate\Database\Eloquent\Model
     */
    public function findOrCreateGoogleUser($googleUser)
    {
        return DB::transaction(function () use ($googleUser) {
            $user = User::withTrashed()->where('email', $googleUser->email)->first();

            if ($user && $user->trashed()) {
                // ソフトデリートされていた場合は復活させる
                $user->restore();
            } elseif (!$user) {
                // 連番IDの生成
                $lastUser = User::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastUser ? intval(substr($lastUser->id, 3)) + 1 : 1; // USRの後の数字を取得
                $customId = 'USR' . str_pad($nextId, 7, '0', STR_PAD_LEFT); // USR00000001形式

                // 一意なIDを生成するループ
                while (User::withTrashed()->where('id', $customId)->exists()) {
                    $nextId++;
                    $customId = 'USR' . str_pad($nextId, 7, '0', STR_PAD_LEFT);
                }

                $user = User::create([
                    'id' => $customId,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'avatar' => $googleUser->picture,
                    'provider_id' => $googleUser->id, // プロバイダーIDを設定
                    'provider' => 'google', // プロバイダー名を設定
                ]);

                $user = User::where('email', $googleUser->email)->first();
            }

            $this->auth->guard()->login($user);

            return $user;
        });
    }

    /**
     * ユーザーをログアウトします。
     *
     * @return void
     */
    public function logout()
    {
        $this->auth->guard()->logout();
    }

    /**
     * ユーザーの新規登録を行います。
     *
     * @param array $data ユーザーの登録情報
     * @return User 登録したユーザーオブジェクト
     */
    public function register(array $data)
    {
        // トランザクション開始
        return DB::transaction(function () use ($data) {
            // 連番IDの生成
            $lastUser = User::withTrashed()->orderBy('id', 'desc')->first();
            $nextId = $lastUser ? intval(substr($lastUser->id, 3)) + 1 : 1; // USRの後の数字を取得
            $customId = 'USR' . str_pad($nextId, 7, '0', STR_PAD_LEFT); // USR00000001形式

            // 一意なIDを生成するループ 念のため
            while (User::withTrashed()->where('id', $customId)->exists()) {
                $nextId++;
                $customId = 'USR' . str_pad($nextId, 7, '0', STR_PAD_LEFT);
            }

            $user = new User();
            $user->id = $customId;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);

            if (isset($data['avatar'])) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->save();

            $user->sendEmailVerificationNotification();

            $user = User::where('email', $data['email'])->first();
            $this->auth->guard()->login($user);

            return $user;
        });
    }

    /**
     * パスワードリセット用のリンクを送信します。
     *
     * @param string $email ユーザーのメールアドレス
     * @return string リセットリンク送信の結果
     */
    public function sendResetLinkEmail(string $email)
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * ユーザーのパスワードをリセットします。
     *
     * @param array $data リセット情報（トークン、メールアドレス、新しいパスワード）
     * @return string パスワードリセットの結果
     */
    public function resetPassword(array $data)
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );
    }

    /**
     * ユーザーのメールアドレスを認証します。
     *
     * @param User $user 認証するユーザー
     * @return bool 認証成功時はtrue
     */
    public function verifyEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return true;
    }

    /**
     * ユーザーのプロフィール情報を更新します。
     *
     * @param User $user 更新するユーザー
     * @param array $data 更新するデータ
     * @return User 更新したユーザーオブジェクト
     */
    public function editProfile(User $user, array $data)
    {
        // トランザクション開始
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['avatar'])) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $avatarPath = $data['avatar']->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->name = $data['name'];
            $user->email = $data['email'];

            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if(isset($data['bio'])) {
                $user->bio = $data['bio'];
            }

            $user->save();

            return $user;
        });
    }

    /**
     * ユーザーを論理削除します。
     *
     * @param User $user 削除するユーザー
     * @return void
     */
    public function deleteUser(User $user)
    {
        // トランザクション開始
        DB::transaction(function () use ($user) {
            // ユーザーを論理削除
            $user->delete(); // soft deleteを使用
        });
    }
}
