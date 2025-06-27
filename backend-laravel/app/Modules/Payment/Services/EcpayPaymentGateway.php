<?php

namespace App\Modules\Payment\Services;

use App\Modules\Payment\Contracts\PaymentGateway;
use Illuminate\Support\Facades\Log;

/**
 * Class EcpayPaymentGateway
 * @package App\Modules\Payment\Services
 *
 * 綠界金流服務實作。
 * 遵循 PaymentGateway 介面，提供綠界金流的特定邏輯。
 *
 * 注意：此處僅為範例骨架，實際串接需引入綠界 SDK 並實作詳細邏輯。
 */
class EcpayPaymentGateway implements PaymentGateway
{
    protected $merchantId;
    protected $hashKey;
    protected $hashIV;

    /**
     * EcpayPaymentGateway 建構子。
     *
     * @param string $merchantId 商家 ID
     * @param string $hashKey Hash Key
     * @param string $hashIV Hash IV
     */
    public function __construct(string $merchantId, string $hashKey, string $hashIV)
    {
        $this->merchantId = $merchantId;
        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
        // 可以在此處初始化綠界 SDK 相關配置
    }

    /**
     * 執行綠界付款。
     *
     * @param array $data 訂單及付款相關資料
     * @return array 付款結果
     */
    public function processPayment(array $data): array
    {
        // 實際串接綠界金流的邏輯，例如：
        // 1. 建立訂單資料 (MerchantTradeNo, TotalAmount, ItemName, ClientBackURL, ReturnURL 等)
        // 2. 產生付款表單或導向連結
        // 3. 返回付款連結或表單資料給前端
        // 僅為範例回傳
        Log::info('Processing Ecpay payment request.', $data);
        return [
            'status' => 'success',
            'message' => '綠界付款請求已送出',
            'payment_url' => 'https://example.ecpay.com/payment_form_placeholder',
            'order_data' => $data
        ];
    }

    /**
     * 處理綠界付款回呼 (Callback)。
     *
     * @param array $data 回呼資料
     * @return array 回呼處理結果
     */
    public function handleCallback(array $data): array
    {
        // 實際處理綠界付款回呼的邏輯，例如：
        // 1. 驗證綠界回傳的檢查碼 (CheckMacValue)
        // 2. 根據回傳結果更新訂單狀態
        // 3. 處理相關業務邏輯 (例如：發送訂單確認信、更新庫存等)
        // 僅為範例回傳
        Log::info('Handling Ecpay payment callback.', $data);
        return [
            'status' => 'success',
            'message' => '綠界回呼處理成功',
            'callback_data' => $data,
            'order_status_updated' => true
        ];
    }

    /**
     * 查詢綠界訂單付款狀態。
     *
     * @param string $orderId 訂單編號 (MerchantTradeNo)
     * @return array 訂單狀態
     */
    public function queryPaymentStatus(string $orderId): array
    {
        // 實際查詢綠界訂單狀態的邏輯，例如透過綠界 API 查詢
        // 僅為範例回傳
        Log::info('Querying Ecpay payment status for order: ' . $orderId);
        return [
            'status' => 'success',
            'message' => '訂單狀態查詢成功',
            'order_id' => $orderId,
            'payment_status' => 'PAID', // 或 UNPAID, FAILED
            'transaction_amount' => 1000
        ];
    }

    /**
     * 執行綠界退款。
     *
     * @param string $orderId 訂單編號
     * @param float $amount 退款金額
     * @return array 退款結果
     */
    public function refundPayment(string $orderId, float $amount): array
    {
        // 實際執行綠界退款的邏輯
        // 僅為範例回傳
        Log::info("Refunding Ecpay payment for order: {$orderId}, amount: {$amount}");
        return [
            'status' => 'success',
            'message' => '綠界退款請求已送出',
            'order_id' => $orderId,
            'refund_amount' => $amount
        ];
    }
}
