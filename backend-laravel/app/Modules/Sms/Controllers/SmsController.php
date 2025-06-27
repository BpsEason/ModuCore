<?php

namespace App\Modules\Sms\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Sms\Contracts\SmsService;
use App\Exceptions\CustomBusinessException; // Assuming you have this exception class
use Illuminate\Support\Facades\Cache; // Added for cache-control example

/**
 * Class SmsController
 * @package App\Modules\Sms\Controllers
 *
 * @OA\Tag(
 * name="簡訊管理",
 * description="簡訊相關的 API 操作"
 * )
 */
class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * @OA\Post(
     * path="/api/sms/send",
     * summary="發送簡訊",
     * tags={"簡訊管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"to","message"},
     * @OA\Property(property="to", type="string", example="+886912345678"),
     * @OA\Property(property="message", type="string", example="這是一條測試簡訊。")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="簡訊發送請求已送出",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=422, description="驗證失敗"),
     * @OA\Response(response=500, description="簡訊發送失敗")
     * )
     */
    public function send(Request $request)
    {
        $validatedData = $request->validate([
            'to' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        try {
            $result = $this->smsService->sendSms($validatedData['to'], $validatedData['message']);
            return response()->json([
                'status' => 'success',
                'message' => '簡訊發送請求已送出',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error("SMS sending failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('簡訊發送失敗: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/sms/{externalId}/status",
     * summary="查詢簡訊發送狀態",
     * tags={"簡訊管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="externalId",
     * in="path",
     * required=true,
     * description="外部簡訊 ID",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="簡訊狀態查詢成功",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=500, description="簡訊狀態查詢失敗")
     * )
     */
    public function status(string $externalId)
    {
        try {
            // 從服務取得結果
            $result = $this->smsService->getSmsStatus($externalId);

            // 為可快取的 API 添加 Cache-Control 頭部
            return response()->json([
                'status' => 'success',
                'message' => '簡訊狀態查詢成功',
                'data' => $result
            ])->header('Cache-Control', 'public, max-age=300'); // Cache for 5 minutes

        } catch (\Exception $e) {
            \Log::error("SMS status query failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('簡訊狀態查詢失敗: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/sms/send-verification",
     * summary="發送驗證碼簡訊",
     * tags={"簡訊管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"to","code"},
     * @OA\Property(property="to", type="string", example="+886912345678"),
     * @OA\Property(property="code", type="string", example="123456")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="驗證碼簡訊發送請求已送出",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=422, description="驗證失敗"),
     * @OA\Response(response=500, description="驗證碼簡訊發送失敗")
     * )
     */
    public function sendVerification(Request $request)
    {
        $validatedData = $request->validate([
            'to' => 'required|string',
            'code' => 'required|string|min:4|max:8',
        ]);

        try {
            $result = $this->smsService->sendVerificationCode($validatedData['to'], $validatedData['code']);
            return response()->json([
                'status' => 'success',
                'message' => '驗證碼簡訊發送請求已送出',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error("Verification SMS sending failed: " . $e->getMessage(), ['exception' => $e]);
            throw new CustomBusinessException('驗證碼簡訊發送失敗: ' . $e->getMessage(), 500);
        }
    }
}
