<?php

namespace App\Contracts;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;

interface LeadRepositoryInterface
{
    public function createLead(Candidate $candidate, Request $request, User $user): Candidate;
    public function getLeadById(int $id, User $user): ?Candidate;
}