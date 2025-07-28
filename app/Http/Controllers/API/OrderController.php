<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $orders;
    /**
     * @var OrderService
     */
    protected OrderService $orderService;

    /**
     * @param OrderRepository $orders
     * @param OrderService $orderService
     */
    public function __construct(OrderRepository $orders, OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->orders = $orders;
    }

    /**
     * @OA\Post(
     *     path="/api/order",
     *     summary="Оформить заказ",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", minimum=1, maximum=10, example=2)
     *                 )
     *             ),
     *             @OA\Property(property="comment", type="string", example="Позвоните перед доставкой")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заказ успешно создан",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=15),
     *             @OA\Property(property="status", type="string", example="created")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     )
     * )
     */
    public function store(StoreOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->input('items'),
                $request->input('comment'),
                $request->user()->id
            );

            return response()->json([
                'order_id' => $order->id,
                'status' => $order->status,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Ошибка при создании заказа: ' . $e->getMessage());

            return response()->json([
                'message' => 'Не удалось создать заказ. Попробуйте позже.',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Получить список заказов текущего пользователя",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список заказов",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="comment", type="string", example="Доставить после 6 вечера"),
     *                 @OA\Property(property="total_amount", type="integer", example=12000),
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="product_id", type="integer", example=3),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="price", type="integer", example=5000),
     *                         @OA\Property(
     *                             property="product",
     *                             type="object",
     *                             @OA\Property(property="name", type="string", example="Телефон")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Пользователь не авторизован")
     * )
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $orders = $this->orders->getUserOrdersWithItems($request->user());

            return response()->json($orders, 200);
        } catch (\Throwable $e) {
            Log::error('Ошибка при получении заказов: ' . $e->getMessage());

            return response()->json([
                'message' => 'Не удалось получить список заказов.',
            ], 500);
        }
    }
}
