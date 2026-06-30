<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private SmsService $sms) {}

    // ─── Step 1: Send OTP ──────────────────────────────────────
    // POST /api/auth/send-otp
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $phone = User::normalizePhone($request->phone);

        // Rate limit: max 3 OTPs per phone per 10 minutes
        $recentCount = OtpCode::where('phone', $phone)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'message' => 'Too many OTP requests. Please wait 10 minutes.',
            ], 429);
        }

        // Invalidate old codes for this phone
        OtpCode::where('phone', $phone)->where('used', false)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Send via Africa's Talking / SMS provider
        $this->sms->send($phone, "Your NyumbaFind code is: {$code}. Valid for 5 minutes.");

        return response()->json([
            'message' => 'OTP sent successfully.',
            'phone'   => $phone,
        ]);
    }

    // ─── Step 2: Verify OTP + register/login ───────────────────
    // POST /api/auth/verify-otp
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code'  => ['required', 'string', 'size:6'],
            'name'  => ['sometimes', 'string', 'max:100'], // required only on first login
        ]);

        $phone = User::normalizePhone($request->phone);

        $otpRecord = OtpCode::where('phone', $phone)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otpRecord || $request->code !== $otpRecord->code) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired OTP.'],
            ]);
        }

        $otpRecord->update(['used' => true]);

        $isNew = ! User::where('phone', $phone)->exists();

        if ($isNew && ! $request->filled('name')) {
            return response()->json([
                'message'      => 'New user. Please provide your name.',
                'requires_name' => true,
            ], 200);
        }

        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['name' => $request->name ?? 'User', 'phone_verified_at' => now()]
        );

        if (! $user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        $token = $user->createToken('nyumbafind-app')->plainTextToken;

        return response()->json([
            'message' => $isNew ? 'Account created successfully.' : 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
                'role'  => $user->role,
            ],
        ]);
    }

    // ─── Logout ────────────────────────────────────────────────
    // POST /api/auth/logout
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // ─── Get authenticated user ────────────────────────────────
    // GET /api/auth/me
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    // ─── Update profile ────────────────────────────────────────
    // PUT /api/auth/profile
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'             => ['sometimes', 'string', 'max:100'],
            'email'            => ['sometimes', 'email', 'unique:users,email,' . $request->user()->id],
            'whatsapp_number'  => ['sometimes', 'string', 'max:15'],
        ]);

        $request->user()->update($request->only('name', 'email', 'whatsapp_number'));

        return response()->json([
            'message' => 'Profile updated.',
            'user'    => $request->user()->fresh(),
        ]);
    }
}
