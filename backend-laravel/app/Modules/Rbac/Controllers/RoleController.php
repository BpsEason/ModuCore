<?php

namespace App\Modules\Rbac\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Models\Role;
use App\Core\Models\Permission;
use App\Exceptions\CustomBusinessException; // Assuming you have this exception class

/**
 * Class RoleController
 * @package App\Modules\Rbac\Controllers
 *
 * @OA\Tag(
 * name="RBAC 角色管理",
 * description="角色與權限相關的 API 操作"
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/rbac/roles",
     * summary="取得所有角色列表",
     * tags={"RBAC 角色管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="成功取得角色列表",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Role"))
     * )
     * )
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json(['status' => 'success', 'data' => $roles]);
    }

    /**
     * @OA\Post(
     * path="/api/rbac/roles",
     * summary="建立新角色",
     * tags={"RBAC 角色管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="new_role"),
     * @OA\Property(property="description", type="string", example="新角色的描述"),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="integer"), example={1, 2})
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="角色建立成功",
     * @OA\JsonContent(ref="#/components/schemas/Role")
     * )
     * ),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        if (isset($validatedData['permissions'])) {
            $role->permissions()->sync($validatedData['permissions']);
        }

        return response()->json(['status' => 'success', 'message' => '角色建立成功', 'data' => $role->load('permissions')], 201);
    }

    /**
     * @OA\Get(
     * path="/api/rbac/roles/{id}",
     * summary="根據 ID 取得單一角色",
     * tags={"RBAC 角色管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="角色 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="成功取得角色資料",
     * @OA\JsonContent(ref="#/components/schemas/Role")
     * ),
     * @OA\Response(response=404, description="找不到角色")
     * )
     */
    public function show(int $id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return response()->json(['status' => 'error', 'message' => '找不到角色'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $role]);
    }

    /**
     * @OA\Put(
     * path="/api/rbac/roles/{id}",
     * summary="更新角色資料",
     * tags={"RBAC 角色管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="角色 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="name", type="string", example="updated_role"),
     * @OA\Property(property="description", type="string", example="更新後的描述"),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="integer"), example={1})
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="角色更新成功",
     * @OA\JsonContent(ref="#/components/schemas/Role")
     * ),
     * @OA\Response(response=404, description="找不到角色或更新失敗"),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function update(Request $request, int $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => 'error', 'message' => '找不到角色'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validatedData['name'] ?? $role->name,
            'description' => $validatedData['description'] ?? $role->description,
        ]);

        if (isset($validatedData['permissions'])) {
            $role->permissions()->sync($validatedData['permissions']);
        }

        return response()->json(['status' => 'success', 'message' => '角色更新成功', 'data' => $role->load('permissions')]);
    }

    /**
     * @OA\Delete(
     * path="/api/rbac/roles/{id}",
     * summary="刪除角色",
     * tags={"RBAC 角色管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="角色 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="角色刪除成功"
     * ),
     * @OA\Response(response=404, description="找不到角色或刪除失敗")
     * )
     */
    public function destroy(int $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => 'error', 'message' => '找不到角色'], 404);
        }
        $role->delete();
        return response()->json(['status' => 'success', 'message' => '角色刪除成功'], 204);
    }
}
