<?php

namespace App\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Exceptions\CustomBusinessException; // Assuming you have this exception class
use Illuminate\Support\Facades\Cache; // Added for cache-control example

/**
 * Class PaymentController
 * @package App\Modules\Payment\Controllers
 *
 * @OA\Tag(
 * name="金流管理",
 * description="金流相關的 API 操作"
 * )
 */
class PaymentController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * @OA\Post(
     * path="/api/payments/create",
     * summary="發起付款請求",
     * tags={"金流管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"order_id","amount","product_name"},
     * @OA\Property(property="order_id", type="string", example="ORDER_XYZ123"),
     * @OA\Property(property="amount", type="number", format="float", example=100.50),
     * @OA\Property(property="product_name", type="string", example="商品 A")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="付款請求已發起",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=422, description="驗證失敗"),
     * @OA\Response(response=500, description="付款請求失敗")
     * )
     */
    public function createPayment(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'product_name' => 'required|string',
        ]);

        try {
            $result = $this->paymentGateway->processPayment($validatedData);
            return response()->json([
                'status' => 'success',
                'message' => '付款請求已發起',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            // Log the actual exception for debugging
            \Log::error("Payment creation failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('付款請求失敗: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/payments/callback",
     * summary="處理金流平台的回呼通知",
     * tags={"金流管理"},
     * description="此路由通常無需認證，由金流平台直接呼叫。",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * description="金流平台回呼的任意數據"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="回呼處理成功",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=400, description="回呼處理失敗"),
     * @OA\Response(response=500, description="回呼處理異常")
     * )
     */
    public function handleCallback(Request $request)
    {
        $callbackData = $request->all();
        try {
            $result = $this->paymentGateway->handleCallback($callbackData);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => '回呼處理成功',
                    'data' => $result
                ]);
            } else {
                throw new CustomBusinessException('回呼處理失敗', 400);
            }
        } catch (\Exception $e) {
            \Log::error("Payment callback handling failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('回呼處理異常: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/payments/{orderId}/status",
     * summary="查詢訂單付款狀態",
     * tags={"金流管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="orderId",
     * in="path",
     * required=true,
     * description="訂單編號",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="訂單狀態查詢成功",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=500, description="訂單狀態查詢失敗")
     * )
     */
    public function queryPaymentStatus(string $orderId)
    {
        try {
            // 從服務取得結果
            $result = $this->paymentGateway->queryPaymentStatus($orderId);

            // 為可快取的 API 添加 Cache-Control 頭部
            return response()->json([
                'status' => 'success',
                'message' => '訂單狀態查詢成功',
                'data' => $result
            ])->header('Cache-Control', 'public, max-age=300'); // Cache for 5 minutes

        } catch (\Exception $e) {
            \Log::error("Payment status query failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('訂單狀態查詢失敗: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/payments/{orderId}/refund",
     * summary="執行退款",
     * tags={"金流管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="orderId",
     * in="path",
     * required=true,
     * description="訂單編號",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"amount"},
     * @OA\Property(property="amount", type="number", format="float", example=50.00)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="退款請求已送出",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=422, description="驗證失敗"),
     * @OA\Response(response=500, description="退款請求失敗")
     * )
     */
    public function refundPayment(Request $request, string $orderId)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $result = $this->paymentGateway->refundPayment($orderId, $validatedData['amount']);
            return response()->json([
                'status' => 'success',
                'message' => '退款請求已送出',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error("Payment refund failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('退款請求失敗: ' . $e->getMessage(), 500);
        }
    }
}
