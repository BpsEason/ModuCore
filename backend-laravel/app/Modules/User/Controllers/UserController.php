<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User; // 使用基礎的 User 模型
use App\Modules\User\Services\UserService;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserController
 * @package App\Modules\User\Controllers
 *
 * @OA\Tag(
 * name="使用者管理",
 * description="使用者相關的 API 操作"
 * )
 */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     * path="/api/users",
     * summary="取得所有使用者列表",
     * tags={"使用者管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="成功取得使用者列表",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     * )
     * )
     */
    public function index(Request $request)
    {
        $cacheKey = 'all_users';
        $users = Cache::remember($cacheKey, 60, function () { // Cache for 60 seconds
            return $this->userService->getAllUsers();
        });
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}",
     * summary="根據 ID 取得單一使用者",
     * tags={"使用者管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="使用者 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="成功取得使用者資料",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(response=404, description="找不到使用者")
     * )
     */
    public function show(int $id)
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => '找不到使用者'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    /**
     * @OA\Post(
     * path="/api/users",
     * summary="建立新使用者",
     * tags={"使用者管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * required={"name","email","password"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="使用者建立成功",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userService->createUser($validatedData);
        Cache::forget('all_users'); // Clear cache
        return response()->json(['status' => 'success', 'message' => '使用者建立成功', 'data' => $user], 201);
    }

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * summary="更新使用者資料",
     * tags={"使用者管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="使用者 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="name", type="string", example="Updated John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="updated.john.doe@example.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="使用者資料更新成功",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(response=404, description="找不到使用者或更新失敗"),
     * @OA\Response(response=422, description="驗證失敗")
     * )
     */
    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        $user = $this->userService->updateUser($id, $validatedData);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => '找不到使用者或更新失敗'], 404);
        }
        Cache::forget('all_users'); // Clear cache
        return response()->json(['status' => 'success', 'message' => '使用者資料更新成功', 'data' => $user]);
    }

    /**
     * @OA\Delete(
     * path="/api/users/{id}",
     * summary="刪除使用者",
     * tags={"使用者管理"},
     * security={{"ApiKeyAuth": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="使用者 ID",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="使用者刪除成功"
     * ),
     * @OA\Response(response=404, description="找不到使用者或刪除失敗")
     * )
     */
    public function destroy(int $id)
    {
        $deleted = $this->userService->deleteUser($id);
        if (!$deleted) {
            return response()->json(['status' => 'error', 'message' => '找不到使用者或刪除失敗'], 404);
        }
        Cache::forget('all_users'); // Clear cache
        return response()->json(['status' => 'success', 'message' => '使用者刪除成功'], 204);
    }
}
