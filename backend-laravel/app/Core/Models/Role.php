<?php
namespace App\Core\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Core\Models\Permission;

/**
 * @OA\Schema(
 * schema="Role",
 * title="角色",
 * description="RBAC 角色模型",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", description="角色名稱 (唯一)", example="admin"),
 * @OA\Property(property="description", type="string", description="角色描述", example="系統管理員"),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="創建時間"),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="更新時間"),
 * @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/Permission"), description="此角色擁有的權限")
 * )
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * 角色擁有的權限。
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * 獲取擁有此角色的用戶。
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
