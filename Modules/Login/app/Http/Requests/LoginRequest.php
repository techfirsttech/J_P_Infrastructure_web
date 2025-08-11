<?php

namespace Modules\Login\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\User\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // Changed from 'email' to 'login'
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $field = filter_var($this->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (is_numeric($this->input('login')) ? 'mobile' : 'username');

        $credentials = [
            $field => $this->input('login'),
            'password' => $this->input('password'),
        ];

        $user = User::where($field, $this->input('login'))->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'login' => __('Your account has been blocked. Please contact the administrator.'),
            ]);
        }

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            $user->increment('login_attempts');

            if ($user->login_attempts >= 3) {
                $user->is_blocked = true;
                $user->save();

                throw ValidationException::withMessages([
                    'login' => __('Too many failed login attempts. Your account has been blocked.'),
                ]);
            }

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        // *** SUCCESSFUL LOGIN ***

        // Reset login attempts and clear rate limiter
        $user->login_attempts = 0;
        $user->save();
        RateLimiter::clear($this->throttleKey());

        // Regenerate session (recommended after login)
        $this->session()->regenerate();

        // LOGOUT OTHER DEVICES if enabled in config/env
        // if (config('auth.logout_other_devices') == 1) {
        //     auth()->logoutOtherDevices($this->input('password'));
        // }
    }

    /**
     * Ensure the login request is not rate limited.
     * @throws \Illuminate\Validation\ValidationException
     */

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}
