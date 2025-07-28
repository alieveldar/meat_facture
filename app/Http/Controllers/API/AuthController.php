<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

/**
 *
 */
class AuthController extends Controller
{
    /**
     * @param AuthService $authService
     */
    public function __construct(private AuthService $authService) {}
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Регистрация пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Иван"),
     *             @OA\Property(property="phone", type="string", example="+79991234567"),
     *             @OA\Property(property="address", type="string", example="ул. Ленина, д. 1"),
     *             @OA\Property(property="password", type="string", format="password", example="StrongPass1!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="StrongPass1!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная регистрация",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->authService->register($request->validated());

            return response()->json([
                'user' => $data['user'],
                'token' => $data['token'],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Ошибка при регистрации: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка при регистрации пользователя'
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Логин пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "password"},
     *             @OA\Property(property="phone", type="string", example="+79991234567"),
     *             @OA\Property(property="password", type="string", format="password", example="StrongPass1!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный логин",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Неверные данные"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->authService->login($request->validated());

            if (!$data) {
                return response()->json([
                    'message' => 'Неверный телефон или пароль',
                ], 401);
            }

            return response()->json([
                'user' => $data['user'],
                'token' => $data['token'],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Ошибка при логине: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка при авторизации',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Выход пользователя",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный выход",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json(['message' => 'Вы успешно вышли из системы'], 200);
        } catch (\Throwable $e) {
            Log::error('Ошибка при выходе из системы: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка при выходе',
            ], 500);
        }
    }

}
