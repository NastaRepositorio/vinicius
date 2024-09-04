<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;

use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public function mount()
    {
        $this->isAuthUser();
    }

    public function login()
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        return $this->redirecionar();
    }

    protected function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey()
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    protected function isAuthUser()
    {
        if (Auth::check()) {
            return $this->redirecionar();
        }
    }

    protected function redirecionar()
    {
        $userType = Auth::user()->usertype;

        switch ($userType) {
            case 'admin':
                return redirect()->route('home');
            case 'seller':
                return redirect()->route('home_seller');
            case 'user':
                return redirect()->route('home_user');
            default:
                return redirect()->route('login');
        }
    }

}; ?>

<div class="w-full h-screen flex items-center justify-center">
    <div class="md:w-1/3 w-2/3">
        <x-form wire:submit="login">
            <x-input label="Email" wire:model="email" />
            <x-input label="Senha" type="password" wire:model="password" />
            <div class="mt-4">
                <x-checkbox label="Manter login" wire:model="remember" left />
            </div>
            <x-slot:actions>
                <x-button label="Acessar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </div>
</div>
