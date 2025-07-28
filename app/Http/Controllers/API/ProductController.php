<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected ProductRepository $products;

    /**
     * @param ProductRepository $products
     */
    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Получить список всех товаров",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Список товаров",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Смартфон"),
     *                 @OA\Property(property="description", type="string", example="Описание товара"),
     *                 @OA\Property(property="price", type="number", format="float", example=199.99),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(type="string", example="Электроника")
     *                 ),
     *                 @OA\Property(property="in_stock", type="integer", example=12)
     *             )
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        try {
            $data = $this->products->getAllWithCategoriesAndStock();

            return response()->json($data, 200);
        } catch (\Throwable $e) {
            Log::error('Ошибка при получении списка товаров: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'message' => 'Ошибка при получении товаров'
            ], 500);
        }
    }
}
