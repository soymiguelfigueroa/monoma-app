<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface LeadsRepositoryInterface
{
    public function getLeads(User $user): ?Collection;
}