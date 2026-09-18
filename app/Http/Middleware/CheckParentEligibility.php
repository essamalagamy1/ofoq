<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Student;

class CheckParentEligibility
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if any of the parent's children have can_share_opinion = true
        $eligible = Student::where(function ($query) use ($user) {
            $query->where('parent_mobile_1', $user->phone)
                  ->orWhere('parent_mobile_2', $user->phone)
                  ->orWhere('parent_mobile_3', $user->phone);
        })->where('can_share_opinion', true)->exists();

        if (!$eligible) {
            return redirect()->route('parent.dashboard')->with('error', __('lang.not_eligible_for_opinion'));
        }

        return $next($request);
    }
}
