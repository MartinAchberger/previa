<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewRegistrationMail;
use App\Mail\B2bRegisteredMail;
use App\Models\B2bUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.dashboard');
        }
        return view('pages.b2b.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = B2bUser::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => 'Nesprávny e-mail alebo heslo.'])->withInput($request->only('email'));
        }

        if ($user->status === 'pending') {
            return back()->withErrors(['email' => 'Účet ešte čaká na schválenie. Ozveme sa do 3 pracovných dní.'])->withInput($request->only('email'));
        }

        if ($user->status === 'disabled') {
            return back()->withErrors(['email' => 'Účet je deaktivovaný. Kontaktujte nás.'])->withInput($request->only('email'));
        }

        Auth::guard('b2b')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('b2b.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('b2b')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function showRegister(): View
    {
        return view('pages.b2b.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'salon_name'   => 'required|string|max:200',
            'contact_name' => 'required|string|max:150',
            'email'        => ['required', 'email', 'max:150', Rule::unique('b2b_users', 'email')],
            'password'     => 'required|string|min:8|confirmed',
            'phone'        => 'nullable|string|max:40',
            'ico'          => 'nullable|string|max:16',
            'vat_id'       => ['nullable', 'string', 'max:24', 'regex:/^[A-Za-z]{2}[0-9A-Za-z]{8,12}$/'],
            'address'      => 'nullable|string|max:200',
            'city'         => 'nullable|string|max:100',
            'zip'          => 'nullable|string|max:16',
        ]);

        $user = B2bUser::create([
            'salon_name'   => $data['salon_name'],
            'contact_name' => $data['contact_name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'phone'        => $data['phone'] ?? null,
            'ico'          => $data['ico'] ?? null,
            'vat_id'       => $data['vat_id'] ?? null,
            'address'      => $data['address'] ?? null,
            'city'         => $data['city'] ?? null,
            'zip'          => $data['zip'] ?? null,
            'country'      => 'SK',
            'status'       => 'pending',
            'tier'         => 'Tier 01',
            'discount_pct' => 0,
        ]);

        try {
            Mail::to($user->email)->send(new B2bRegisteredMail($user));
            Mail::to(config('mail.admin_address'))->send(new AdminNewRegistrationMail($user));
        } catch (Throwable $e) {
            Log::error('B2B registration emails failed', ['user' => $user->email, 'error' => $e->getMessage()]);
        }

        return redirect()->route('b2b.login')->with('success', 'Registrácia odoslaná. Účet aktivujeme zvyčajne do 3 pracovných dní a pošleme ti potvrdenie e-mailom.');
    }
}
