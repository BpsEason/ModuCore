<?php
namespace App\Core\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Pivot
{
    use HasFactory;
    protected $table = 'user_roles';
    protected $fillable = ['user_id', 'role_id'];
}
