<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function loginSocial(Request $request, string $provider)
    {
        $this->validateProvider($provider);

        // For Facebook, stateless + explicit scopes
        if ($provider === 'facebook') {
            /** @var \Laravel\Socialite\Two\FacebookProvider $driver */
            $driver = Socialite::driver('facebook');
            // @intelephense-ignore-next-linex
            return $driver->setScopes(['public_profile'])
                ->stateless()
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callbackSocial(Request $request, string $provider)
    {
        $this->validateProvider($provider);

        if ($provider === 'facebook') {
            /** @var \Laravel\Socialite\Two\FacebookProvider $driver */
            $driver = Socialite::driver('facebook');
            $response = $driver->stateless()->user();
        } else {
            $response = Socialite::driver($provider)->user();
        }

        $email = $response->getEmail() ?? $response->getId() . '@facebook.local';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $response->getName() ?? $response->getNickname() ?? 'User',
                'password' => bcrypt(Str::random(24)),
            ]
        );
        
        $data = [$provider . '_id' => $response->getId()];
        
        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        $user->update($data);

        Auth::login($user);
        $request->session()->regenerate();

        $user->session_id = session()->getId();
        $user->save();

        return redirect('/dashboard');
    }

    private function validateProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }
    }
}
