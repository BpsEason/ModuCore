<?php

namespace App\Modules\User\Services;

use App\Models\User; // 使用基礎的 User 模型
use App\Modules\User\Contracts\UserInterface; // Updated to UserInterface
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserService
 * @package App\Modules\User\Services
 *
 * 處理使用者業務邏輯的服務層。
 * 負責資料存取、業務規則應用等。
 */
class UserService implements UserInterface # Implements UserInterface
{
    /**
     * 取得所有使用者清單。
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllUsers()
    {
        return User::all();
    }

    /**
     * 根據 ID 取得單一使用者。
     *
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id)
    {
        return User::find($id);
    }

    /**
     * 建立新使用者。
     *
     * @param array $data 包含使用者資料的陣列 (name, email, password)
     * @return User
     */
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']); // 密碼雜湊
        $user = User::create($data);
        Cache::forget('all_users'); // 清除快取
        // 可以在此處賦予新使用者預設角色或權限
        return $user;
    }

    /**
     * 更新使用者資料。
     *
     * @param int $id
     * @param array $data 欲更新的資料
     * @return User|null
     */
    public function updateUser(int $id, array $data)
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']); // 密碼雜湊
        }

        $user->update($data);
        Cache::forget('all_users'); // 清除快取
        return $user;
    }

    /**
     * 刪除使用者。
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        $deleted = $user->delete();
        if ($deleted) {
            Cache::forget('all_users'); // 清除快取
        }
        return $deleted;
    }

    // Implementing methods from UserInterface
    public function getAll() { return $this->getAllUsers(); }
    public function getById(int $id) { return $this->getUserById($id); }
    public function create(array $data) { return $this->createUser($data); }
    public function update(int $id, array $data) { return $this->updateUser($id, $data); }
    public function delete(int $id): bool { return $this->deleteUser($id); }
}
