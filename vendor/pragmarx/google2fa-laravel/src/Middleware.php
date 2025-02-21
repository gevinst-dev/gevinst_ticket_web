<?php

namespace PragmaRX\Google2FALaravel;

use App\Models\User;
use Closure;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Auth;

class Middleware
{
    public function handle($request, Closure $next)
    {
        // $user = \Auth::user();
        // if($user && $user->google2fa_secret && $user->google2fa_enable==1){
            $authenticator = app(Authenticator::class)->boot($request);

            if ($authenticator->isAuthenticated()) {
                return $next($request);
            }
            
            return $authenticator->makeRequestOneTimePasswordResponse();
        // }
        // elseif($user->google2fa_secret && $user->google2fa_enable==0)
        // {
        //     return redirect()->route('profile')
        //         ->with('error', 'Two Factor Authenticator Code field is required. Please set it up first.');
        // }
        // return $next($request);
    }
}
