<?php

namespace App\Services;

use App\Models\AcademicCycle;
use App\Models\BadgeSetting;
use App\Models\Student;
use App\Models\StudentAnswer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EvaluationService
{
    /**
     * Get the badge for a specific percentage
     */
    public function getBadgeForPercentage(float $percentage): ?BadgeSetting
    {
        return BadgeSetting::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }

    /**
     * Check if weekly evaluation can be shown (only on or after Thursday for the current week).
     * For simplicity, if we are in the week it's Thursday or later.
     * Note: We'll assume Thursday is day of week 4 (where 0=Sun, 4=Thu).
     * Since week tracking might be abstract, we just check if today is >= Thursday.
     * In a real app, week start date should be configured.
     * For now, we will just return true if it's Thursday, Friday, or Saturday.
     */
    public function canShowWeeklyEvaluation(): bool
    {
        // 4 = Thursday, 5 = Friday, 6 = Saturday
        return in_array(Carbon::now()->dayOfWeek, [Carbon::THURSDAY, Carbon::FRIDAY, Carbon::SATURDAY]);
    }

    /**
     * Get weekly evaluation for a student
     */
    public function getWeeklyEvaluation(Student $student, int $week, AcademicCycle $cycle): array
    {
        if (!$this->canShowWeeklyEvaluation()) {
            return [
                'is_visible' => false,
                'message' => __('lang.evaluation_not_ready_yet'),
            ];
        }

        $answers = StudentAnswer::with('question')
            ->where('student_id', $student->id)
            ->where('cycle_id', $cycle->id)
            ->where('week', $week)
            ->get();

        if ($answers->isEmpty()) {
            return [
                'is_visible' => true,
                'has_data' => false,
            ];
        }

        $subjectResults = [];
        $totalQuestions = $answers->count();
        $totalCorrect = 0;

        $answersBySubject = $answers->groupBy(fn ($answer) => $answer->question->subject);

        foreach ($answersBySubject as $subject => $subjectAnswers) {
            $correctCount = $subjectAnswers->where('is_correct', true)->count();
            $totalCount = $subjectAnswers->count();
            $percentage = $totalCount > 0 ? ($correctCount / $totalCount) * 100 : 0;
            
            $totalCorrect += $correctCount;

            $subjectResults[$subject] = [
                'percentage' => round($percentage, 2),
                'badge' => $this->getBadgeForPercentage($percentage),
                'correct' => $correctCount,
                'total' => $totalCount,
            ];
        }

        $overallPercentage = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;

        return [
            'is_visible' => true,
            'has_data' => true,
            'subjects' => $subjectResults,
            'overall_percentage' => round($overallPercentage, 2),
            'overall_badge' => $this->getBadgeForPercentage($overallPercentage),
        ];
    }

    /**
     * Get overall evaluation for the 12-week cycle
     */
    public function getOverallEvaluation(Student $student, AcademicCycle $cycle): array
    {
        $answers = StudentAnswer::with('question')
            ->where('student_id', $student->id)
            ->where('cycle_id', $cycle->id)
            ->get();

        if ($answers->isEmpty()) {
            return [
                'has_data' => false,
            ];
        }

        $totalQuestions = $answers->count();
        $totalCorrect = $answers->where('is_correct', true)->count();
        
        $overallPercentage = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;

        return [
            'has_data' => true,
            'overall_percentage' => round($overallPercentage, 2),
            'overall_badge' => $this->getBadgeForPercentage($overallPercentage),
        ];
    }
}
