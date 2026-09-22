<?php

namespace App\Livewire\Dashboard\Teacher;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;
use Mary\Traits\Toast;

#[Title('teachers')]
#[Lazy]
class TeacherData extends Component
{
    use Toast, WithPagination, WithFileUploads;

    public bool $import_modal = false;
    public $import_file;

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $search_teacher_id;
    public $search_subject;
    public $search_is_substitute;
    public $all_teachers = [];

    public function mount(): void
    {
        $this->all_teachers = User::where('type', 'teacher')->get(['id', 'name', 'phone', 'phone_key'])->map(function ($item): array {
            $phone = $item->phone ? ltrim($item->phone_key, '+') . $item->phone : '';
            return [
                'id' => $item->id,
                'name' => $item->name,
                'sub_label' => "{$item->id} | {$phone}",
            ];
        })->toArray();

        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.teachers'),
                'icon' => 'o-academic-cap',
            ],
        ];
    }

    #[Computed]
    public function totalTeachers(): int
    {
        return User::where('type', 'teacher')->count();
    }

    #[Computed]
    public function totalRegularTeachers(): int
    {
        return User::where('type', 'teacher')->where('is_substitute', false)->count();
    }

    #[Computed]
    public function totalSubstituteTeachers(): int
    {
        return User::where('type', 'teacher')->where('is_substitute', true)->count();
    }

    #[On('render')]
    public function render(): View
    {
        $data['teachers'] = User::query()
            ->where('type', 'teacher')
            ->when($this->search_teacher_id, fn (Builder $query) => $query->where('id', $this->search_teacher_id))
            ->when($this->search_subject, fn (Builder $query) => $query->where('assigned_subject', $this->search_subject))
            ->when($this->search_is_substitute !== null && $this->search_is_substitute !== '', function (Builder $query) {
                $query->where('is_substitute', $this->search_is_substitute);
            })
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.teacher.teacher-data', $data);
    }

    public function delete(User $user): void
    {
        if ($user->type === 'teacher') {
            $user->delete();
            $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.teacher') ?? 'المعلم']));
        }
    }

    public function importData(): void
    {
        $this->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new TeachersImport, $this->import_file->getRealPath());

            $this->success(__('lang.imported_successfully') ?? 'تم استيراد المعلمين بنجاح');
            $this->import_modal = false;
            $this->import_file = null;
        } catch (\Exception $e) {
            $this->error(__('lang.import_error').$e->getMessage());
        }
    }
}
