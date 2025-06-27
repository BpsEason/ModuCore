<?php

namespace App\Modules\Sms\Contracts;

/**
 * Interface SmsService
 * @package App\Modules\Sms\Contracts
 *
 * 簡訊服務契約介面。
 * 定義了簡訊服務應實現的基本方法，支援多平台切換。
 */
interface SmsService
{
    /**
     * 發送單一簡訊。
     *
     * @param string $to 接收者電話號碼 (包含國碼，例如 +886912345678)
     * @param string $message 簡訊內容
     * @return array 發送結果 (包含 status, message, external_id 等)
     */
    public function sendSms(string $to, string $message): array;

    /**
     * 查詢簡訊發送狀態。
     *
     * @param string $externalId 外部簡訊 ID (由簡訊服務提供商回傳的 ID)
     * @return array 簡訊狀態 (例如 SENT, DELIVERED, FAILED)
     */
    public function getSmsStatus(string $externalId): array;

    /**
     * 發送驗證碼簡訊。
     *
     * @param string $to 接收者電話號碼
     * @param string $code 驗證碼
     * @return array 發送結果
     */
    public function sendVerificationCode(string $to, string $code): array;
}
