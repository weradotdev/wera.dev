<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

#[Group('Authentication')]
class AuthController extends Controller
{
    /**
     * Login with email or phone.
     *
     * @unauthenticated
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $identifier = $request->input('email');
        $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        /** @var User|null $user */
        $user = User::firstWhere($column, $identifier);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No user exists with these credentials.'],
            ]);
        }

        if (! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        $token = $user->createToken('wera')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Register a new user.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = User::create([
            'name'     => $payload['name'],
            'email'    => $payload['email'],
            'phone'    => $payload['phone'] ?? $payload['email'],
            'type'     => 'developer',
            'password' => Hash::make($payload['password']),
        ]);

        $token = $user->createToken('wera')->plainTextToken;

        return response()->json(
            [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
            201
        );
    }

    /**
     * Logout a user.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'User logged out successfully.',
        ], 200);
    }

    /**
     * Stream token (stub for call/stream integrations).
     */
    public function streamToken(Request $request): JsonResponse
    {
        return response()->json(['token' => null], 501);
    }

    /**
     * Get authenticated user.
     *
     * @return JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json(new UserResource($request->user()), 200);
    }

    /**
     * Update the authenticated user's avatar.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $payload = $request->validate([
            'avatar' => ['required', 'image', 'max:10240'],
        ]);

        $file = $payload['avatar'];
        $extension = $file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;

        if (filled($user->avatar)) {
            Storage::disk('public')->delete('avatars/'.$user->avatar);
        }

        $file->storeAs('avatars', $filename, 'public');

        $user->forceFill([
            'avatar' => $filename,
        ])->save();

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'user' => new UserResource($user->fresh()),
        ], 200);
    }

    /**
     * Save the user's pin
     *
     * @return mixed|JsonResponse
     */
    public function savePin(Request $request)
    {
        $user = User::find(Auth::id());

        $user->pin = $request->pin;
        $user->save();

        return response()->json([
            'message' => 'Pin saved successfully.',
        ], 200);
    }

    /**
     * Verify the user's pin
     *
     * @return mixed|JsonResponse
     */
    public function verifyPin(Request $request)
    {
        $user = Auth::user();

        if ($user->pin !== $request->pin) {
            return response()->json([
                'message' => 'The provided PIN is incorrect.',
            ], 401);
        }

        return response()->json([
            'message' => 'OTP verified successfully.',
        ], 200);
    }

    /**
     * Send the user's otp
     *
     * @return mixed|JsonResponse
     */
    public function sendOtp(Request $request)
    {
        $user = User::find(Auth::id());

        $user->otp = (string) rand(100000, 999999);
        $user->save();

        return response()->json([
            'message' => 'OTP sent successfully.',
        ], 200);
    }

    /**
     * Verify the user's otp
     *
     * @return mixed|JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $user = User::find(Auth::id());

        if ($user->otp !== $request->input('otp')) {
            return response()->json([
                'message' => 'The provided OTP is incorrect.',
            ], 401);
        }

        $user->otp = null;
        $user->save();

        return response()->json([
            'message' => 'OTP verified successfully.',
        ], 200);
    }

    /**
     * Send email verification OTP
     *
     * @return JsonResponse
     */
    public function sendEmailVerification(Request $request, User $user)
    {
        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 400);
        }

        // Generate OTP
        $user->otp = (string) rand(100000, 999999);
        $user->save();

        return response()->json([
            'message' => 'Verification code sent to email.',
        ], 200);
    }

    /**
     * Verify email with OTP
     *
     * @return JsonResponse
     */
    public function verifyEmail(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($user->otp !== $request->input('otp')) {
            return response()->json([
                'message' => 'The provided verification code is incorrect.',
            ], 401);
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully.',
            'user'    => new UserResource($user),
        ], 200);
    }

    /**
     * Send phone verification OTP
     *
     * @return JsonResponse
     */
    public function sendPhoneVerification(Request $request, User $user)
    {
        // Check if already verified
        if ($user->phone_verified_at) {
            return response()->json([
                'message' => 'Phone already verified.',
            ], 400);
        }

        // Generate OTP
        $user->otp = (string) rand(100000, 999999);
        $user->save();

        return response()->json([
            'message' => 'Verification code sent to phone.',
        ], 200);
    }

    /**
     * Verify phone with OTP
     *
     * @return JsonResponse
     */
    public function verifyPhone(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($user->otp !== $request->input('otp')) {
            return response()->json([
                'message' => 'The provided verification code is incorrect.',
            ], 401);
        }

        $user->phone_verified_at = now();
        $user->otp = null;
        $user->save();

        return response()->json([
            'message' => 'Phone verified successfully.',
            'user'    => new UserResource($user),
        ], 200);
    }
}
