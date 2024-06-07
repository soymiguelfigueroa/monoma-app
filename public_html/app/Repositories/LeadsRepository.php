<?php

namespace App\Repositories;

use App\Contracts\LeadsRepositoryInterface;
use App\Models\User;
use App\Models\Candidate;
use Illuminate\Support\Collection;

class LeadsRepository implements LeadsRepositoryInterface
{
    public function getLeads(User $user): ?Collection
    {
        if ($user->role == 'manager') {
            return Candidate::all();
        } else {
            return $user->candidates;
        }
    }
}