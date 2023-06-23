<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $alreadySent = DB::table('password_resets')
            ->where('email', $user->email)
            ->first();

        if ($alreadySent) {
            return response()->json([
                'code' => 500,
                'message' => "Sorry, We already sent the link to your email account. Please check you mail inbox!"
            ]);
        }

        if ($user) {
            $resetPasswordLinkExpiration = now()->addMinutes(config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'));

            $token = Str::random(60);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'expires_at' => $resetPasswordLinkExpiration,
            ]);

            $resetUrl = env('APP_BASE_URL') .  '/reset-password/' . $token . '/' . $user->email;
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $resetUrl));

            return response()->json([
                'code' => 200,
                'message' => "Success, please visit you mail inbox for the password reset instructions!"
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Sorry, the email you entered is not in our list!"
            ]);
        }
    }

    public function resetPassword(Request $request, $token, $email)
    {
        $token = $request->token;
        $newEmail = $request->email;
        $password = $request->password;

        // Check if the token and email match in the password reset table
        $passwordReset = DB::table('password_resets')
            ->where('email', $newEmail)
            ->where('token', $token)
            ->first();

        if (!$passwordReset || !$this->isPasswordResetValid($passwordReset)) {
            return response()->json([
                'code' => 400,
                'message' => 'Invalid or expired password reset link.',
            ]);
        }

        $user = User::where('email', $email)->first();
        $newPassword = $user->update([
            'password' => Hash::make($password),
        ]);

        if ($newPassword) {
            // Remove the password reset record from the table
            DB::table('password_resets')
                ->where('email', $email)
                ->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Your password has been successfully changed!',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to change password!',
            ]);
        }
    }

    private function isPasswordResetValid($passwordReset)
    {
        $expiresAt = Carbon::parse($passwordReset->expires_at);
        return !$expiresAt->isPast();
    }
}
