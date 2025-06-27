<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 角色表
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('角色名稱，例如 admin, editor, user');
            $table->text('description')->nullable()->comment('角色描述');
            $table->timestamps();
        });

        // 權限表
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('權限名稱，例如 create-user, view-users');
            $table->text('description')->nullable()->comment('權限描述');
            $table->timestamps();
        });

        // 使用者-角色 中間表 (多對多)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']); // 複合主鍵
            $table->timestamps();
        });

        // 角色-權限 中間表 (多對多)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']); // 複合主鍵
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
