<?php

namespace App\Modules\Sms\Services;

use App\Modules\Sms\Contracts\SmsService;
use Illuminate\Support\Facades\Log;
// use Twilio\Rest\Client; // 實際專案會需要引入 Twilio SDK

/**
 * Class TwilioSmsService
 * @package App\Modules\Sms\Services
 *
 * Twilio 簡訊服務實作。
 * 遵循 SmsService 介面，提供 Twilio 簡訊的特定邏輯。
 *
 * 注意：此處僅為範例骨架，實際串接需引入 Twilio SDK 並實作詳細邏輯。
 */
class TwilioSmsService implements SmsService
{
    protected $accountSid;
    protected $authToken;
    protected $fromPhoneNumber;
    // protected $twilioClient; // Twilio SDK 客戶端實例

    /**
     * TwilioSmsService 建構子。
     *
     * @param string $accountSid Twilio Account SID
     * @param string $authToken Twilio Auth Token
     * @param string $fromPhoneNumber 發送簡訊的 Twilio 電話號碼
     */
    public function __construct(string $accountSid, string $authToken, string $fromPhoneNumber)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->fromPhoneNumber = $fromPhoneNumber;
        // 可以在此處初始化 Twilio SDK
        // $this->twilioClient = new Client($this->accountSid, $this->authToken);
    }

    /**
     * 發送單一簡訊。
     *
     * @param string $to 接收者電話號碼 (包含國碼)
     * @param string $message 簡訊內容
     * @return array 發送結果
     */
    public function sendSms(string $to, string $message): array
    {
        try {
            // 實際使用 Twilio SDK 發送簡訊的邏輯
            /*
            $result = $this->twilioClient->messages->create(
                $to,
                [
                    'from' => $this->fromPhoneNumber,
                    'body' => $message,
                ]
            );
            return [
                'status' => 'success',
                'message' => '簡訊發送成功',
                'external_id' => $result->sid, // Twilio 返回的 SID
            ];
            */
            // 僅為範例回傳
            Log::info("Sending SMS to {$to} with message: {$message}");
            return [
                'status' => 'success',
                'message' => "簡訊已發送至 {$to}，內容：{$message}",
                'external_id' => 'SM_EXAMPLE_' . uniqid(), // 模擬的外部 ID
            ];
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => '簡訊發送失敗: ' . $e->getMessage(),
                'external_id' => null,
            ];
        }
    }

    /**
     * 查詢簡訊發送狀態。
     *
     * @param string $externalId 外部簡訊 ID
     * @return array 簡訊狀態
     */
    public function getSmsStatus(string $externalId): array
    {
        try {
            // 實際使用 Twilio SDK 查詢簡訊狀態的邏輯
            /*
            $message = $this->twilioClient->messages($externalId)->fetch();
            return [
                'status' => 'success',
                'message' => '簡訊狀態查詢成功',
                'external_id' => $message->sid,
                'delivery_status' => $message->status, // 例如 'queued', 'sending', 'sent', 'delivered', 'failed'
            ];
            */
            // 僅為範例回傳
            Log::info("Querying SMS status for external ID: {$externalId}");
            $statuses = ['queued', 'sending', 'sent', 'delivered', 'failed'];
            return [
                'status' => 'success',
                'message' => "簡訊 {$externalId} 狀態查詢成功",
                'external_id' => $externalId,
                'delivery_status' => $statuses[array_rand($statuses)], // 模擬隨機狀態
            ];
        } catch (\Exception $e) {
            Log::error('SMS status query failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => '簡訊狀態查詢失敗: ' . $e->getMessage(),
                'external_id' => $externalId,
                'delivery_status' => 'unknown',
            ];
        }
    }

    /**
     * 發送驗證碼簡訊。
     *
     * @param string $to 接收者電話號碼
     * @param string $code 驗證碼
     * @return array 發送結果
     */
    public function sendVerificationCode(string $to, string $code): array
    {
        $message = "您的驗證碼是: {$code}。請勿將此代碼分享給任何人。";
        return $this->sendSms($to, $message);
    }
}
