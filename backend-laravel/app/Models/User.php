<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache; // 引入 Cache Facade
use App\Core\Models\Role; // 引入 Role 模型

/**
 * @OA\Schema(
 * schema="User",
 * title="使用者",
 * description="應用程式使用者模型",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", description="使用者名稱", example="Test User"),
 * @OA\Property(property="email", type="string", format="email", description="使用者電子郵件 (唯一)", example="test@example.com"),
 * @OA\Property(property="email_verified_at", type="string", format="date-time", readOnly="true", description="電子郵件驗證時間"),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="創建時間"),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="更新時間"),
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 使用者擁有的角色。
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * 檢查使用者是否擁有特定權限。
     *
     * @param string $permission 權限名稱 (例如 'view-users')
     * @return bool
     */
    public function hasPermissionTo(string $permission): bool
    {
        // 使用 Redis 快取使用者權限，有效期 1 小時 (3600 秒)
        // 確保 'user_{id}_permissions' 快取鍵是唯一的
        return Cache::remember("user_{$this->id}_permissions", 3600, function () use ($permission) {
            // 從所有關聯的角色中獲取所有權限，然後扁平化並檢查是否存在指定權限
            return $this->roles()->with('permissions')->get()
                        ->pluck('permissions')->flatten()
                        ->contains('name', $permission);
        });
    }
}
