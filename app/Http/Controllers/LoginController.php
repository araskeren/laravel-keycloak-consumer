<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SocialiteProviders\Keycloak\Keycloak;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{
    public function redirectToKeycloak()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function handleKeycloakCallback(Request $request)
    {
        $keycloakUser = Socialite::driver('keycloak')->user();
        $user = User::where('keycloak_id', $keycloakUser->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => $keycloakUser->nickname,
                'email' => $keycloakUser->nickname . '@keycloak.com',
                'keycloak_id' => $keycloakUser->id,
                'password' => bcrypt('password'),
            ]);
        }
        // make login using id
        auth()->login($user, true);
        return redirect()->intended('/admin');
    }
}
