<?php

namespace App\Repositories;

use App\Contracts\LeadRepositoryInterface;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeadRepository implements LeadRepositoryInterface
{
    public function createLead(Candidate $candidate, Request $request, User $user): Candidate
    {
        $candidate->name = $request->name;
        $candidate->source = $request->source;
        $candidate->owner = $request->owner;
        $candidate->created_at = Carbon::now()->format('Y-m-d H:m:s');
        $candidate->created_by = $user->id;
        $candidate->save();

        return $candidate;
    }

    public function getLeadById(int $id, User $user): ?Candidate
    {
        $candidate = Candidate::find($id);

        if ($user->role == 'manager' || $candidate->owner == $user->id) {
            return $candidate;
        }
    }
}