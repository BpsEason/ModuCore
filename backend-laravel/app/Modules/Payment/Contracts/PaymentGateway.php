<?php

namespace App\Modules\Payment\Contracts;

/**
 * Interface PaymentGateway
 * @package App\Modules\Payment\Contracts
 *
 * 金流服務契約介面。
 * 定義了金流服務應實現的基本方法，支援多平台切換。
 */
interface PaymentGateway
{
    /**
     * 執行付款。
     *
     * @param array $data 訂單及付款相關資料
     * @return array 付款結果
     */
    public function processPayment(array $data): array;

    /**
     * 處理付款回呼 (Callback)。
     *
     * @param array $data 回呼資料
     * @return array 回呼處理結果
     */
    public function handleCallback(array $data): array;

    /**
     * 查詢訂單付款狀態。
     *
     * @param string $orderId 訂單編號
     * @return array 訂單狀態
     */
    public function queryPaymentStatus(string $orderId): array;

    /**
     * 執行退款。
     *
     * @param string $orderId 訂單編號
     * @param float $amount 退款金額
     * @return array 退款結果
     */
    public function refundPayment(string $orderId, float $amount): array;
}
