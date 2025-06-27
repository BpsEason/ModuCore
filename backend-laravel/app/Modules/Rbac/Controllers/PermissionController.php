<?php

namespace App\Modules\Rbac\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Models\Permission;
use App\Exceptions\CustomBusinessException; // Assuming you have this exception class

/**
 * Class PermissionController
 * @package App\Modules\Rbac\Controllers
 *
 * @OA\Tag(
 * name="RBAC 權限管理",
 * description="權限相關的 API 操作"
 * )
 */
class PermissionController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/rbac/permissions",
     * summary="取得所有權限列表",
     * tags={"RBAC 權限管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="成功取得權限列表",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission"))
     * )
     * )
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json(['status' => 'success', 'data' => $permissions]);
    }

    /**
     * @OA\Post(
     * path="/api/rbac/permissions",
     * summary="建立新權限",
     * tags={"RBAC 權限管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="new_permission"),
     * @OA\Property(property="description", type="string", example="新權限的描述")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="權限建立成功",
     * @OA\JsonContent(ref="#/components/schemas/Permission")
     * ),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($validatedData);

        return response()->json(['status' => 'success', 'message' => '權限建立成功', 'data' => $permission], 201);
    }

    /**
     * @OA\Get(
     * path="/api/rbac/permissions/{id}",
     * summary="根據 ID 取得單一權限",
     * tags={"RBAC 權限管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="權限 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="成功取得權限資料",
     * @OA\JsonContent(ref="#/components/schemas/Permission")
     * ),
     * @OA\Response(response=404, description="找不到權限")
     * )
     */
    public function show(int $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['status' => 'error', 'message' => '找不到權限'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $permission]);
    }

    /**
     * @OA\Put(
     * path="/api/rbac/permissions/{id}",
     * summary="更新權限資料",
     * tags={"RBAC 權限管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="權限 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="name", type="string", example="updated_permission"),
     * @OA\Property(property="description", type="string", example="更新後的描述")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="權限更新成功",
     * @OA\JsonContent(ref="#/components/schemas/Permission")
     * ),
     * @OA\Response(response=404, description="找不到權限或更新失敗"),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function update(Request $request, int $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['status' => 'error', 'message' => '找不到權限'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $permission->update($validatedData);

        return response()->json(['status' => 'success', 'message' => '權限更新成功', 'data' => $permission]);
    }

    /**
     * @OA\Delete(
     * path="/api/rbac/permissions/{id}",
     * summary="刪除權限",
     * tags={"RBAC 權限管理"},
     * security={{"ApiKeyAuth": {}}, {"bearerAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="權限 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="權限刪除成功"
     * ),
     * @OA\Response(response=404, description="找不到權限或刪除失敗")
     * )
     */
    public function destroy(int $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['status' => 'error', 'message' => '找不到權限'], 404);
        }
        $permission->delete();
        return response()->json(['status' => 'success', 'message' => '權限刪除成功'], 204);
    }
}
