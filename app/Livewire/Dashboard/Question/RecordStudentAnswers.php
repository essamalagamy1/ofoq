<?php

namespace App\Livewire\Dashboard\Question;

use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('record-answers')]
class RecordStudentAnswers extends Component
{
    use Toast;

    public Question $question;
    public $students = [];
    public $search_student = '';
    public $search_semester = '';
    public $answers = []; // Array of student_id => selected_option

    public function mount(Question $question): void
    {
        $this->question = $question;
        $this->loadStudents();
        $this->loadAnswers();
    }

    public function loadStudents(): void
    {
        $query = Student::where('grade', $this->question->grade);

        if (!empty($this->search_student)) {
            $query->where('name', 'like', "%{$this->search_student}%");
        }

        if (!empty($this->search_semester)) {
            $query->where('semester', $this->search_semester);
        }

        $this->students = $query->orderBy('name')->get();
    }

    public function loadAnswers(): void
    {
        $existingAnswers = StudentAnswer::where('question_id', $this->question->id)->get();
        foreach ($existingAnswers as $answer) {
            $this->answers[$answer->student_id] = $answer->selected_option;
        }
    }

    public function updatedSearchStudent(): void
    {
        $this->loadStudents();
    }

    public function updatedSearchSemester(): void
    {
        $this->loadStudents();
    }

    public function recordAnswer($studentId, $option): void
    {
        $isCorrect = ($option === $this->question->correct_option);

        StudentAnswer::updateOrCreate(
            [
                'student_id' => $studentId,
                'question_id' => $this->question->id,
            ],
            [
                'cycle_id' => $this->question->cycle_id,
                'is_correct' => $isCorrect,
                'selected_option' => $option,
                'week' => $this->question->week,
            ]
        );

        $this->answers[$studentId] = $option;
        
        $studentName = collect($this->students)->firstWhere('id', $studentId)->name ?? '';
        $message = __('lang.answer_recorded_for', ['option' => $option, 'student' => $studentName]) ?? "تم تسجيل الإجابة {$option} للطالب {$studentName}";

        $this->success($message, position: 'toast-top toast-start');
    }

    public function render(): View
    {
        return view('livewire.dashboard.question.record-student-answers')
            ->layout('components.layouts.maryui.app');
    }
}
