<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwoFactorController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // जर गुप्त कि (secret) नसेल तर नवीन जनरेट करा
        if (!$user->google2fa_secret) {
            $google2fa = app('pragmarx.google2fa');
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $google2fa = app('pragmarx.google2fa');
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'GymAdminPanel',
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.two-factor', compact('user', 'qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        /** @var User $user */
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if ($valid) {
            $user->google2fa_enabled = true;
            $user->save();
            return back()->with('success', 'Two-Factor Authentication successfully enabled!');
        }

        return back()->with('error', 'Invalid OTP code. Please check your authenticator app.');
    }

    public function disable(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('success', 'Two-Factor Authentication has been disabled.');
    }
}