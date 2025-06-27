<?php

namespace App\Modules\User\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

interface UserInterface
{
    public function getAll(): Collection;
    public function getById(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
}
