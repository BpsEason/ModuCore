<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // 確保引用正確的 User 模型
use App\Core\Models\Role; // 引入核心 Role 模型
use App\Core\Models\Permission; // 引入核心 Permission 模型
use App\Core\Models\UserRole; // 引入 UserRole 模型

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // 創建使用者
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // 請在生產環境中更改此密碼
                'email_verified_at' => now(),
            ]
        );

        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 創建角色
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => '系統管理員，擁有所有權限']);
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['description' => '內容編輯者，可管理部分內容']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer'], ['description' => '資料查看者，僅具備查詢權限']);

        // 創建權限
        $permissions = [
            // User 模組權限
            'view-users' => '查看使用者列表',
            'view-user-detail' => '查看單一使用者詳情',
            'create-user' => '創建使用者',
            'update-user' => '更新使用者',
            'delete-user' => '刪除使用者',

            // Payment 模組權限
            'initiate-payment' => '發起付款',
            'query-payment-status' => '查詢付款狀態',
            'refund-payment' => '執行退款',

            // SMS 模組權限
            'send-sms' => '發送簡訊',
            'query-sms-status' => '查詢簡訊狀態',

            // RBAC 模組權限
            'view-roles' => '查看角色列表',
            'view-role-detail' => '查看單一角色詳情',
            'create-role' => '創建角色',
            'update-role' => '更新角色',
            'delete-role' => '刪除角色',
            'view-permissions' => '查看權限列表',
            'create-permission' => '創建權限',
            'update-permission' => '更新權限',
            'delete-permission' => '刪除權限',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        // 刷新權限列表
        $allPermissions = Permission::all();

        // 為角色分配權限
        // Admin 擁有所有權限
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Editor 擁有部分權限
        $editorRole->permissions()->syncWithoutDetaching([
            $allPermissions->whereIn('name', [
                'view-users', 'view-user-detail', 'update-user',
                'initiate-payment', 'query-payment-status',
                'send-sms', 'query-sms-status',
            ])->pluck('id')
        ]);

        // Viewer 僅擁有查看權限
        $viewerRole->permissions()->syncWithoutDetaching([
            $allPermissions->whereIn('name', [
                'view-users', 'view-user-detail',
                'query-payment-status',
                'query-sms-status',
                'view-roles', 'view-permissions',
            ])->pluck('id')
        ]);

        // 為使用者分配角色
        UserRole::firstOrCreate(['user_id' => $adminUser->id, 'role_id' => $adminRole->id]);
        UserRole::firstOrCreate(['user_id' => $regularUser->id, 'role_id' => $viewerRole->id]);

        $this->command->info('使用者、角色和權限已成功填充！');
    }
}
