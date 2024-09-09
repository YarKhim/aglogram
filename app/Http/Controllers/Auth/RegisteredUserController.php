<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Chat;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use phpseclib3\Crypt\RSA;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $key = RSA::createKey(4096);
        $privateKey = $key->toString('PKCS8');
        $publicKey = $key->getPublicKey()->toString('PKCS8');
        $all_users =
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'username' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'lastname' => ['required', 'string', 'max:255'],
            ]);

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->private_key = $privateKey;
        $user->public_key = $publicKey;
        $user->save();
        $users = User::all();
        $this_user = User::where('email', $request->email)->first();
        $key = Key::createNewRandomKey();
        $key_result_string = $key->saveToAsciiSafeString();
        foreach ($users as $USER) {
            $chat  = Chat::create([
                'creator' => $USER->id,
                'invted' => $this_user->id,
                'symmetric_chat_key' => $key_result_string,
            ]);
            $chat->save();
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
