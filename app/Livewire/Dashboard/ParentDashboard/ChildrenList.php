<?php

namespace App\Livewire\Dashboard\ParentDashboard;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('dashboard')]
class ChildrenList extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        
        $children = Student::where('parent_mobile_1', $user->phone)
            ->orWhere('parent_mobile_2', $user->phone)
            ->orWhere('parent_mobile_3', $user->phone)
            ->get();

        $can_share_opinion = $children->where('can_share_opinion', true)->count() > 0;

        return view('livewire.dashboard.parent-dashboard.children-list', [
            'children' => $children,
            'can_share_opinion' => $can_share_opinion,
        ]);
    }
}
