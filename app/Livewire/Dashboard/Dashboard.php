<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\AcademicCycle;
use App\Models\Question;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('home')]
class Dashboard extends Component
{
    public ?int $selected_cycle_id = null;

    public array $questionsBySubjectChart = [];
    public array $questionsByWeekChart = [];
    public array $studentEvaluationsByWeekChart = [];
    public array $topQuestionsChart = [];
    public array $topQuestionsCurrentWeekChart = [];

    public function mount(): void
    {
        view()->share('breadcrumbs', $this->breadcrumbs());

        $activeCycle = AcademicCycle::where('is_active', true)->first() ?? AcademicCycle::first();
        if ($activeCycle) {
            $this->selected_cycle_id = $activeCycle->id;
        }

        $this->updateCharts();
    }

    public function updatedSelectedCycleId(): void
    {
        $this->updateCharts();
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.home'),
                'icon' => 'o-home',
            ],
        ];
    }

    #[Computed]
    public function cycles()
    {
        return AcademicCycle::orderByDesc('id')->get();
    }

    #[Computed]
    public function totalStudents(): int
    {
        return Student::count();
    }

    #[Computed]
    public function totalTeachers(): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count();
    }

    public function updateCharts(): void
    {
        if (! $this->selected_cycle_id) {
            $this->questionsBySubjectChart = [];
            $this->questionsByWeekChart = [];
            $this->studentEvaluationsByWeekChart = [];
            $this->topQuestionsChart = [];
            return;
        }

        // Common options
        $commonOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 1500,
                'easing' => 'easeOutQuart',
            ],
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'font' => ['family' => 'inherit', 'size' => 13],
                        'usePointStyle' => true,
                        'padding' => 20,
                    ]
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(15, 23, 42, 0.9)',
                    'titleFont' => ['family' => 'inherit', 'size' => 14, 'weight' => 'bold'],
                    'bodyFont' => ['family' => 'inherit', 'size' => 13],
                    'padding' => 12,
                    'cornerRadius' => 8,
                ]
            ],
        ];

        $user = auth()->user();
        $isRegularTeacher = $user->hasRole('teacher') && !$user->is_substitute;

        // 1. Questions By Subject Chart
        $questionsSubj = Question::where('cycle_id', $this->selected_cycle_id)
            ->where(function ($q) {
                $q->where('is_parent_suggestion', false)
                  ->orWhere(function ($q2) {
                      $q2->where('is_parent_suggestion', true)->where('status', 'approved');
                  });
            })
            ->when($isRegularTeacher, fn($q) => $q->where('user_id', $user->id))
            ->selectRaw('subject, count(*) as count')
            ->groupBy('subject')
            ->get();

        $labelsSubj = [];
        $dataSubj = [];

        foreach ($questionsSubj as $q) {
            $subjectEnum = \App\Enums\SubjectEnum::coerce($q->subject);
            $labelsSubj[] = $subjectEnum ? $subjectEnum->title() : $q->subject;
            $dataSubj[] = $q->count;
        }

        $this->questionsBySubjectChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelsSubj,
                'datasets' => [
                    [
                        'label' => __('lang.total_questions') ?? 'عدد الأسئلة',
                        'data' => $dataSubj,
                        'backgroundColor' => 'rgba(248, 164, 0, 0.8)',
                        'borderColor' => '#f8a400',
                        'borderWidth' => 2,
                        'borderRadius' => 8,
                        'hoverBackgroundColor' => '#f8a400',
                    ]
                ]
            ],
            'options' => array_merge_recursive($commonOptions, [
                'scales' => [
                    'x' => [
                        'grid' => ['display' => false],
                        'ticks' => ['font' => ['family' => 'inherit']]
                    ],
                    'y' => [
                        'beginAtZero' => true, 
                        'ticks' => ['stepSize' => 1, 'font' => ['family' => 'inherit']],
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.05)', 'borderDash' => [5, 5]]
                    ]
                ]
            ])
        ];

        // 2. Questions By Week Chart
        $questionsWeek = Question::where('cycle_id', $this->selected_cycle_id)
            ->where(function ($q) {
                $q->where('is_parent_suggestion', false)
                  ->orWhere(function ($q2) {
                      $q2->where('is_parent_suggestion', true)->where('status', 'approved');
                  });
            })
            ->when($isRegularTeacher, fn($q) => $q->where('user_id', $user->id))
            ->selectRaw('week, count(*) as count')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $labelsWeek = [];
        $dataWeek = [];

        foreach ($questionsWeek as $q) {
            $labelsWeek[] = (__('lang.week') ?? 'الأسبوع') . ' ' . $q->week;
            $dataWeek[] = $q->count;
        }

        $this->questionsByWeekChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelsWeek,
                'datasets' => [
                    [
                        'label' => __('lang.total_questions') ?? 'عدد الأسئلة',
                        'data' => $dataWeek,
                        'backgroundColor' => 'rgba(76, 29, 149, 0.8)',
                        'borderColor' => '#4c1d95',
                        'borderWidth' => 2,
                        'borderRadius' => 8,
                        'hoverBackgroundColor' => '#4c1d95',
                    ]
                ]
            ],
            'options' => array_merge_recursive($commonOptions, [
                'scales' => [
                    'x' => [
                        'grid' => ['display' => false],
                        'ticks' => ['font' => ['family' => 'inherit']]
                    ],
                    'y' => [
                        'beginAtZero' => true, 
                        'ticks' => ['stepSize' => 1, 'font' => ['family' => 'inherit']],
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.05)', 'borderDash' => [5, 5]]
                    ]
                ]
            ])
        ];

        // 3. Student Evaluations By Week Chart
        $answers = \App\Models\StudentAnswer::whereHas('question', function ($q) use ($isRegularTeacher, $user) {
            $q->where('cycle_id', $this->selected_cycle_id)
                ->where(function ($q2) {
                    $q2->where('is_parent_suggestion', false)
                      ->orWhere(function ($q3) {
                          $q3->where('is_parent_suggestion', true)->where('status', 'approved');
                      });
                })
                ->when($isRegularTeacher, fn($q4) => $q4->where('user_id', $user->id));
        })
            ->selectRaw('week, is_correct, count(*) as count')
            ->groupBy('week', 'is_correct')
            ->orderBy('week')
            ->get();

        $weeksMap = [];
        foreach ($answers as $ans) {
            if (!isset($weeksMap[$ans->week])) {
                $weeksMap[$ans->week] = ['correct' => 0, 'wrong' => 0];
            }
            if ($ans->is_correct) {
                $weeksMap[$ans->week]['correct'] = $ans->count;
            } else {
                $weeksMap[$ans->week]['wrong'] = $ans->count;
            }
        }

        $labelsEval = [];
        $correctData = [];
        $wrongData = [];

        ksort($weeksMap);

        foreach ($weeksMap as $week => $counts) {
            $labelsEval[] = (__('lang.week') ?? 'الأسبوع') . ' ' . $week;
            $correctData[] = $counts['correct'];
            $wrongData[] = $counts['wrong'];
        }

        $this->studentEvaluationsByWeekChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labelsEval,
                'datasets' => [
                    [
                        'label' => __('lang.correct_answers') ?? 'الإجابات الصحيحة',
                        'data' => $correctData,
                        'borderColor' => '#16a34a',
                        'backgroundColor' => 'rgba(22, 163, 74, 0.2)',
                        'borderWidth' => 3,
                        'pointBackgroundColor' => '#16a34a',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => '#16a34a',
                        'pointRadius' => 5,
                        'pointHoverRadius' => 7,
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => __('lang.wrong_answers') ?? 'الإجابات الخاطئة',
                        'data' => $wrongData,
                        'borderColor' => '#dc2626',
                        'backgroundColor' => 'rgba(220, 38, 38, 0.2)',
                        'borderWidth' => 3,
                        'pointBackgroundColor' => '#dc2626',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => '#dc2626',
                        'pointRadius' => 5,
                        'pointHoverRadius' => 7,
                        'fill' => true,
                        'tension' => 0.4,
                    ]
                ]
            ],
            'options' => array_merge_recursive($commonOptions, [
                'interaction' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
                'scales' => [
                    'x' => [
                        'grid' => ['display' => false],
                        'ticks' => ['font' => ['family' => 'inherit']]
                    ],
                    'y' => [
                        'beginAtZero' => true, 
                        'ticks' => ['stepSize' => 1, 'font' => ['family' => 'inherit']],
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.05)', 'borderDash' => [5, 5]]
                    ]
                ]
            ])
        ];

        // 4. Top Questions (Correct/Wrong) Chart
        $questionAnswers = \App\Models\StudentAnswer::whereHas('question', function ($q) use ($isRegularTeacher, $user) {
            $q->where('cycle_id', $this->selected_cycle_id)
                ->where(function ($q2) {
                    $q2->where('is_parent_suggestion', false)
                      ->orWhere(function ($q3) {
                          $q3->where('is_parent_suggestion', true)->where('status', 'approved');
                      });
                })
                ->when($isRegularTeacher, fn($q4) => $q4->where('user_id', $user->id));
        })
            ->selectRaw('question_id, is_correct, count(*) as count')
            ->groupBy('question_id', 'is_correct')
            ->get();

        $qStats = [];
        foreach ($questionAnswers as $ans) {
            if (!isset($qStats[$ans->question_id])) {
                $qStats[$ans->question_id] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            if ($ans->is_correct) {
                $qStats[$ans->question_id]['correct'] = $ans->count;
            } else {
                $qStats[$ans->question_id]['wrong'] = $ans->count;
            }
            $qStats[$ans->question_id]['total'] += $ans->count;
        }

        uasort($qStats, fn($a, $b) => $b['total'] <=> $a['total']);
        $top10 = array_slice($qStats, 0, 10, true);

        $qIds = array_keys($top10);
        $questionsMap = \App\Models\Question::whereIn('id', $qIds)->pluck('content', 'id');

        $labelsTop = [];
        $correctDataTop = [];
        $wrongDataTop = [];

        foreach ($top10 as $qId => $stat) {
            $content = $questionsMap[$qId] ?? 'سؤال #' . $qId;
            $content = \Illuminate\Support\Str::limit($content, 30);
            $labelsTop[] = $content;
            $correctDataTop[] = $stat['correct'];
            $wrongDataTop[] = $stat['wrong'];
        }

        $this->topQuestionsChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelsTop,
                'datasets' => [
                    [
                        'label' => __('lang.correct_answers') ?? 'الإجابات الصحيحة',
                        'data' => $correctDataTop,
                        'backgroundColor' => 'rgba(22, 163, 74, 0.85)',
                        'borderColor' => '#16a34a',
                        'borderWidth' => 1,
                        'borderRadius' => 6,
                        'hoverBackgroundColor' => '#16a34a',
                    ],
                    [
                        'label' => __('lang.wrong_answers') ?? 'الإجابات الخاطئة',
                        'data' => $wrongDataTop,
                        'backgroundColor' => 'rgba(220, 38, 38, 0.85)',
                        'borderColor' => '#dc2626',
                        'borderWidth' => 1,
                        'borderRadius' => 6,
                        'hoverBackgroundColor' => '#dc2626',
                    ]
                ]
            ],
            'options' => array_merge_recursive($commonOptions, [
                'indexAxis' => 'y',
                'interaction' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true, 
                        'stacked' => true,
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.05)', 'borderDash' => [5, 5]],
                        'ticks' => ['stepSize' => 1, 'font' => ['family' => 'inherit']]
                    ],
                    'y' => [
                        'stacked' => true,
                        'grid' => ['display' => false],
                        'ticks' => ['font' => ['family' => 'inherit', 'size' => 12]]
                    ]
                ]
            ])
        ];

        // 5. Top Questions Current Week (Correct/Wrong) Chart
        $selectedCycle = AcademicCycle::find($this->selected_cycle_id);
        $currentWeek = $selectedCycle ? $selectedCycle->active_week : 1;

        $questionAnswersWeek = \App\Models\StudentAnswer::where('week', $currentWeek)
            ->whereHas('question', function ($q) use ($isRegularTeacher, $user) {
                $q->where('cycle_id', $this->selected_cycle_id)
                    ->where(function ($q2) {
                        $q2->where('is_parent_suggestion', false)
                          ->orWhere(function ($q3) {
                              $q3->where('is_parent_suggestion', true)->where('status', 'approved');
                          });
                    })
                    ->when($isRegularTeacher, fn($q4) => $q4->where('user_id', $user->id));
            })
            ->selectRaw('question_id, is_correct, count(*) as count')
            ->groupBy('question_id', 'is_correct')
            ->get();

        $qStatsWeek = [];
        foreach ($questionAnswersWeek as $ans) {
            if (!isset($qStatsWeek[$ans->question_id])) {
                $qStatsWeek[$ans->question_id] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            if ($ans->is_correct) {
                $qStatsWeek[$ans->question_id]['correct'] = $ans->count;
            } else {
                $qStatsWeek[$ans->question_id]['wrong'] = $ans->count;
            }
            $qStatsWeek[$ans->question_id]['total'] += $ans->count;
        }

        uasort($qStatsWeek, fn($a, $b) => $b['total'] <=> $a['total']);
        $top10Week = array_slice($qStatsWeek, 0, 10, true);

        $qIdsWeek = array_keys($top10Week);
        $questionsMapWeek = \App\Models\Question::whereIn('id', $qIdsWeek)->pluck('content', 'id');

        $labelsTopWeek = [];
        $correctDataTopWeek = [];
        $wrongDataTopWeek = [];

        foreach ($top10Week as $qId => $stat) {
            $content = $questionsMapWeek[$qId] ?? 'سؤال #' . $qId;
            $content = \Illuminate\Support\Str::limit($content, 30);
            $labelsTopWeek[] = $content;
            $correctDataTopWeek[] = $stat['correct'];
            $wrongDataTopWeek[] = $stat['wrong'];
        }

        $this->topQuestionsCurrentWeekChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelsTopWeek,
                'datasets' => [
                    [
                        'label' => __('lang.correct_answers') ?? 'الإجابات الصحيحة',
                        'data' => $correctDataTopWeek,
                        'backgroundColor' => 'rgba(22, 163, 74, 0.85)',
                        'borderColor' => '#16a34a',
                        'borderWidth' => 1,
                        'borderRadius' => 6,
                        'hoverBackgroundColor' => '#16a34a',
                    ],
                    [
                        'label' => __('lang.wrong_answers') ?? 'الإجابات الخاطئة',
                        'data' => $wrongDataTopWeek,
                        'backgroundColor' => 'rgba(220, 38, 38, 0.85)',
                        'borderColor' => '#dc2626',
                        'borderWidth' => 1,
                        'borderRadius' => 6,
                        'hoverBackgroundColor' => '#dc2626',
                    ]
                ]
            ],
            'options' => array_merge_recursive($commonOptions, [
                'indexAxis' => 'y',
                'interaction' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true, 
                        'stacked' => true,
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.05)', 'borderDash' => [5, 5]],
                        'ticks' => ['stepSize' => 1, 'font' => ['family' => 'inherit']]
                    ],
                    'y' => [
                        'stacked' => true,
                        'grid' => ['display' => false],
                        'ticks' => ['font' => ['family' => 'inherit', 'size' => 12]]
                    ]
                ]
            ])
        ];
    }

    #[Computed]
    public function totalQuestions(): int
    {
        if (! $this->selected_cycle_id) {
            return 0;
        }

        $user = auth()->user();
        $isRegularTeacher = $user->hasRole('teacher') && !$user->is_substitute;

        return Question::where('cycle_id', $this->selected_cycle_id)
            ->where(function ($q) {
                $q->where('is_parent_suggestion', false)
                  ->orWhere(function ($q2) {
                      $q2->where('is_parent_suggestion', true)->where('status', 'approved');
                  });
            })
            ->when($isRegularTeacher, fn($q) => $q->where('user_id', $user->id))
            ->count();
    }

    #[Computed]
    public function recentQuestions()
    {
        if (! $this->selected_cycle_id) {
            return collect();
        }
        $user = auth()->user();
        $isRegularTeacher = $user->hasRole('teacher') && !$user->is_substitute;

        return Question::where('cycle_id', $this->selected_cycle_id)
            ->where(function ($q) {
                $q->where('is_parent_suggestion', false)
                  ->orWhere(function ($q2) {
                      $q2->where('is_parent_suggestion', true)->where('status', 'approved');
                  });
            })
            ->when($isRegularTeacher, fn($q) => $q->where('user_id', $user->id))
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard.dashboard');
    }
}
