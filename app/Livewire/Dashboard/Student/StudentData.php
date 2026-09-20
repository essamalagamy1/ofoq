<?php

namespace App\Livewire\Dashboard\Student;

use App\Models\Student;
use App\Imports\StudentsImport;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

#[Title('students')]
#[Lazy]
class StudentData extends Component
{
    use Toast, WithPagination, WithFileUploads;

    public bool $import_modal = false;
    public $import_file;
    public ?int $import_grade = null;

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $search_student_id;
    public $all_students = [];

    public function mount(): void
    {
        $this->all_students = Student::get([
            'id', 'name', 
            'parent_mobile_1', 'parent_mobile_1_key', 
            'parent_mobile_2', 'parent_mobile_2_key', 
            'parent_mobile_3', 'parent_mobile_3_key'
        ])->map(function ($item): array {
            $phones = collect([
                $item->parent_mobile_1 ? ltrim($item->parent_mobile_1_key, '+') . $item->parent_mobile_1 : null,
                $item->parent_mobile_2 ? ltrim($item->parent_mobile_2_key, '+') . $item->parent_mobile_2 : null,
                $item->parent_mobile_3 ? ltrim($item->parent_mobile_3_key, '+') . $item->parent_mobile_3 : null,
            ])->filter()->implode(' - ');

            return [
                'id' => $item->id,
                'name' => $item->name,
                'sub_label' => "{$item->id} | " . $phones,
            ];
        })->toArray();

        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.students'),
                'icon' => 'o-users',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        $data['students'] = Student::query()
            ->when($this->search_student_id, fn (Builder $query) => $query->where('id', $this->search_student_id))
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.student.student-data', $data);
    }

    public function delete(Student $student): void
    {
        $student->delete();
        $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.student') ?? 'الطالب']));
    }

    public function importData(): void
    {
        $this->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'import_grade' => 'required|integer|between:3,6',
        ]);

        try {
            Excel::import(new StudentsImport($this->import_grade), $this->import_file->getRealPath());
            
            $this->success(__('lang.imported_successfully') ?? 'تم استيراد الطلاب بنجاح');
            $this->import_modal = false;
            $this->import_file = null;
            $this->import_grade = null;
        } catch (\Exception $e) {
            $this->error(__('lang.import_error') ?? 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }
}
