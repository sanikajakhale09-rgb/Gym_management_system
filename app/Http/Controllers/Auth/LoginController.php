<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // १. व्हॅलिडेशन
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // २. लॉगिन तपासणे
        $credentials = $request->only('email', 'password');

        if (Auth::validate($credentials)) {
            /** @var User $user */
            $user = User::where('email', $credentials['email'])->first();

            // जर युजरने 2FA Enable केले असेल तरच OTP पेजवर पाठवा
            if ($user && $user->google2fa_enabled) {
                session(['2fa_user_id' => $user->id]);
                return redirect()->route('login.2fa');
            }

            // 2FA नसेल तर नेहमीप्रमाणे सरळ लॉगिन करा
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('/admin/panel');
            }
        }

        // ३. चुकीची माहिती दिल्यास मेसेज दाखवणे
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // 2FA OTP Form दाखवणे
    public function show2faForm()
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.login-2fa');
    }

    // 2FA OTP Verify करणे
    public function verify2fa(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = session('2fa_user_id');
        /** @var User $user */
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        // Google Authenticator OTP व्हेरिफाय करण्यासाठी टाइम-बेस्ड चेक
        $timestamp = floor(time() / 30);
        $valid = false;
        $secretKey = $this->base32Decode($user->google2fa_secret ?? '');

        for ($i = -1; $i <= 1; $i++) {
            $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $timestamp + $i), $secretKey, true);
            $code = sprintf('%06u', (unpack('N', substr($hash, ord(substr($hash, -1)) & 0x0F, 4))[1] & 0x7FFFFFFF) % 1000000);
            if (hash_equals($code, $request->otp)) {
                $valid = true;
                break;
            }
        }

        if ($valid) {
            session()->forget('2fa_user_id');
            Auth::login($user);
            request()->session()->regenerate();
            return redirect()->intended('/admin/panel');
        }

        return back()->withErrors(['otp' => 'Invalid Authenticator Code. Please try again.']);
    }

    // Base32 Decoder Helper Method with strict string typehint
    private function base32Decode(string $secret): string
    {
        if (empty($secret)) return '';
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];
            if ($char === '=') break;
            $val = strpos($base32Chars, $char);
            if ($val === false) continue;

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $result;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}