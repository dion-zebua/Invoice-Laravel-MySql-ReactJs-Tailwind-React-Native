<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendForgotPassword;
use App\Http\Requests\Auth\SendVerification;
use App\Http\Requests\User\ResetPassword as UserResetPassword;
use App\Mail\ResetPassword;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\Verification;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{
    use BaseResponse;

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if ($user && !$user->is_verified) {
            return $this->unauthorizedResponse('Anda belum verifikasi.');
        }

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email / password salah.',
            ], 401);
        }

        $token = $user->createToken('apiToken', expiresAt: Carbon::now()->addMinute(config('sanctum.expiration')))->plainTextToken;
        $user['token'] = $token;

        return response()->json([
            'status' => true,
            'message' => 'Berhasil login.',
            'data' => $user,
        ], 200);
    }

    /**
     * Logout
     */
    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var PersonalAccessToken $user */
        $user->currentAccessToken()->delete();
        return $this->success('Berhasil logout.');
    }

    /**
     * Send verification Email
     */
    public function sendVerification(SendVerification $request)
    {

        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return $this->dataNotFound('Email');
        }

        $tokenTime = Carbon::parse($user->token_verified_before_at);
        if ($user->token_verified_before_at && !$tokenTime->isPast()) {
            return $this->limitTime('verifikasi email', $tokenTime->format('H:i:s'));
        }

        $tokenVerified = Str::random(60);

        DB::beginTransaction();
        try {
            $user->update([
                'token_verified' => Hash::make($tokenVerified),
                'token_verified_before_at' => now()->addMinutes(30),
                'is_verified' => false,
                'email_verified_at' => NULL,
            ]);

            Mail::to($user->email)->send(new Verification($user, $tokenVerified));

            DB::commit();

            return $this->success('Email Verifikasi telah terkirim.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Check verification Email
     */
    public function checkVerification($id, $token)
    {
        $user = User::where('id', $id)
            ->where('is_verified', 0)
            ->whereNotNull('token_verified')
            ->where('token_verified_before_at', '>=', Carbon::now())
            ->first();

        if (!$user || !Hash::check($token, $user->token_verified)) {
            return $this->dataNotFound('Token / Pengguna');
        }

        $user->update([
            'token_verified' => NULL,
            'token_verified_before_at' => Null,
            'is_verified' => true,
            'email_verified_at' => Carbon::now(),
        ]);

        return $this->success('Verifikasi berhasil.');
    }

    /**
     * Send Forgot Password
     */
    public function sendForgotPassword(SendForgotPassword $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return $this->dataNotFound('Email');
        }

        $tokenTime = Carbon::parse($user->token_reset_password_before_at);
        if ($user->token_reset_password_before_at && !$tokenTime->isPast()) {
            return $this->limitTime('reset password', $tokenTime->format('H:i:s'));
        }

        $tokenVerified = Str::random(60);

        DB::beginTransaction();
        try {
            $user->update([
                'token_reset_password' => Hash::make($tokenVerified),
                'token_reset_password_before_at' => now()->addMinutes(30),
            ]);

            Mail::to($user->email)->send(new ResetPassword($user, $tokenVerified));

            DB::commit();

            return $this->success('Email Reset Password telah terkirim.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Send Forgot Password
     */
    public function checkResetPassword($id, $token)
    {

        $user = User::where('id', $id)
            ->whereNotNull('token_reset_password')
            ->where('token_reset_password_before_at', '>=', Carbon::now())
            ->first();

        if (!$user || !Hash::check($token, $user->token_reset_password)) {
            return $this->dataNotFound('Token / Pengguna');
        }

        return $this->success('Token Reset password valid.');
    }

    /**
     * Reset Password
     */
    public function resetPassword(UserResetPassword $request, $id, $token)
    {
        $validated = $request->validated();

        $user = User::where('id', $id)
            ->whereNotNull('token_reset_password')
            ->where('token_reset_password_before_at', '>=', Carbon::now())
            ->first();

        if (!$user || !Hash::check($token, $user->token_reset_password)) {
            return $this->dataNotFound('Token / Pengguna');
        }

        $user->update([
            'token_reset_password' => NULL,
            'token_reset_password_before_at' => NULL,
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success('Reset password berhasil.');
    }

    /**
     * Check Login
     */
    public function checkLogin()
    {
        return $this->dataFound(Auth::user(), 'Pengguna');
    }
}
