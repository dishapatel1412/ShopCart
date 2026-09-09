<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\OtpMail;
use App\Services\SmsService;
use Carbon\Carbon;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return redirect('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registered! Please login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->login)->first();
            $type = 'email';
        } else {
            $user = User::where('mobile_num', $request->login)->first();
            $type = 'mobile';
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        if ($user->role === 'admin') {
            Auth::login($user);
            $request->session()->regenerate();

            $user->session_id = session()->getId();
            $user->save();

            return redirect('/admin/dashboard');
        }

        if ($user->google2fa_enabled) {
            session(['2fa_user_id' => $user->id]);
            return redirect()->route('2fa.form');
        }

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        session([
            'otp_user_id' => $user->id,
            'otp_type' => $type
        ]);

        if ($type === 'email') {
            Mail::to($user->email)->send(new OtpMail($otp));
        } else {
            $smsService = new SmsService();
            $number = '+91' . $user->mobile_num;
            $smsService->sendOtp($number, $otp);
        }

        return redirect()->route('otp.form');
    }   

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found']);
        }

        if (!$user->otp || !$user->otp_expires_at) {
            return back()->withErrors(['otp' => 'OTP expired or already used']);
        }

        if ((string)$user->otp !== (string)$request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP expired']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->session_id = session()->getId();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();
        
        session()->forget('otp_user_id');

        return redirect('/')->with('success', 'Login successful');
    }

    public function show2faForm()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::find(session('2fa_user_id'));

        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA;

        $valid = $google2fa->verifyKey(
            $user->google2fa_secret,
            $request->otp,
            2
        );

        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid code']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->session_id = session()->getId();
        $user->save();

        session()->forget('2fa_user_id');

        return redirect('/dashboard');
    }
    
    public function showEnable2faForm()
    {
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();

        session(['2fa_secret' => $secret]);

        $user = Auth::user();

        $otpUrl = $google2fa->getQRCodeUrl(
            'ShopCart',
            $user->email,
            $secret
        );

        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpUrl);

        return view('auth.enable2fa', compact('qrCodeUrl'));
    }

    public function enable2fa(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $secret = session('2fa_secret');
    
        if (!$secret) {
            return redirect()->route('2fa.enable.form')
                ->withErrors(['otp' => 'Session expired. Try again.']);
        }
    
        $google2fa = new Google2FA();
    
        $valid = $google2fa->verifyKey($secret, $request->otp, 2);
    
        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid code']);
        }
    
        $user = Auth::user();

        $user->google2fa_secret = $secret;
        $user->google2fa_enabled = true;
        $user->save();
    
        session()->forget('2fa_secret');
    
        return redirect()->route('profile.show')
            ->with('success', '2FA enabled successfully');
    }

    public function disable2fa(Request $request)
    {
        $user = Auth::user();

        $user->google2fa_secret = null;
        $user->google2fa_enabled = false;
        $user->save();

        return back()->with('success', '2FA disabled successfully');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
 
        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('success', 'Your password has been reset successfully')
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
