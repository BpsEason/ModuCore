<?php
namespace App\Core\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate->Database\Eloquent\Factories\HasFactory;
use App\Core\Models\Role;

/**
 * @OA\Schema(
 * schema="Permission",
 * title="權限",
 * description="RBAC 權限模型",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", description="權限名稱 (唯一)", example="create-user"),
 * @OA\Property(property="description", type="string", description="權限描述", example="創建新使用者帳號"),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="創建時間"),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="更新時間")
 * )
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * 擁有此權限的角色。
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
