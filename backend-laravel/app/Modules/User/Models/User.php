<?php

namespace App\Modules\User\Models;

// 通常這裡會引用基礎的 User 模型，並在需要時進行擴展
use App\Models\User as BaseUser;

/**
 * Class User
 * @package App\Modules\User\Models
 *
 * 為了模組化結構而存在的 User 模型，通常會使用 App\Models\User。
 * 如果此模組有特有的資料庫表或欄位，可以在此定義。
 * 目前直接使用基礎 User 模型的功能。
 */
class User extends BaseUser
{
    // 如果 User 模組有特有的資料庫表或欄位，可以在此定義
    // protected $table = 'module_users';
    // protected $fillable = ['module_specific_field', ...];
}
