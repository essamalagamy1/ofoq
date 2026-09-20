<div>
	<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
		{{-- Header with Cycle Selection --}}
		<div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-base-100 p-4 rounded-xl shadow-sm border border-base-200">
			<div>
				<h1 class="text-2xl font-bold text-primary">{{ __('lang.dashboard') ?? 'لوحة التحكم' }}</h1>
				<p class="text-sm text-gray-500">{{ __('lang.overview_of_platform') ?? 'نظرة عامة على إحصائيات المنصة' }}</p>
			</div>
			<div class="w-full sm:w-64">
				<x-select 
					wire:model.live="selected_cycle_id" 
					:options="$this->cycles" 
					option-value="id" 
					option-label="name" 
					placeholder="{{ __('lang.select_cycle') ?? 'اختر الدورة الأكاديمية' }}" 
					icon="o-arrow-path" 
				/>
			</div>
		</div>

		{{-- Overview Statistics Cards --}}
		<div class="grid auto-rows-min gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
			@if(auth()->user()->type !== 'teacher')
			<div class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
				<x-stat 
					title="{{ __('lang.total_students') ?? 'إجمالي الطلاب' }}" 
					value="{{ $this->totalStudents }}" 
					icon="o-users" 
					color="text-info" 
				/>
			</div>
			
			<div class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
				<x-stat 
					title="{{ __('lang.total_teachers') ?? 'إجمالي المعلمين' }}" 
					value="{{ $this->totalTeachers }}" 
					icon="o-academic-cap" 
					color="text-success" 
				/>
			</div>
			@endif

			
			<div class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
				<x-stat 
					title="{{ __('lang.total_questions') ?? 'الأسئلة (بالدورة)' }}" 
					value="{{ $this->totalQuestions }}" 
					icon="o-document-text" 
					color="text-primary" 
				/>
			</div>
		</div>

		{{-- Recent Activity Table --}}
		<div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
			<div class="p-4 border-b border-base-200 bg-base-50/50">
				<h2 class="text-lg font-bold text-gray-700 flex items-center gap-2">
					<x-icon name="o-clock" class="w-5 h-5 text-primary" />
					{{ __('lang.recent_questions') ?? 'أحدث الأسئلة المضافة بالدورة' }}
				</h2>
			</div>
			
			<div class="overflow-x-auto">
				<table class="table w-full">
					<thead>
						<tr class="bg-base-200/50 text-gray-600">
							<th class="py-3 px-4 text-start font-semibold">#</th>
							<th class="py-3 px-4 text-start font-semibold">{{ __('lang.question') ?? 'السؤال' }}</th>
							<th class="py-3 px-4 text-start font-semibold">{{ __('lang.teacher') ?? 'المعلم' }}</th>
							<th class="py-3 px-4 text-start font-semibold">{{ __('lang.date') ?? 'التاريخ' }}</th>
						</tr>
					</thead>
					<tbody>
						@forelse($this->recentQuestions as $index => $question)
							<tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
								<td class="py-3 px-4">{{ $index + 1 }}</td>
								<td class="py-3 px-4">
									<div class="max-w-xs truncate" title="{{ $question->content }}">
										{{ Str::limit($question->content, 50) }}
									</div>
								</td>
								<td class="py-3 px-4">
									<div class="flex items-center gap-2">
										<div class="avatar placeholder">
											<div class="bg-neutral text-neutral-content rounded-full w-6">
												<span class="text-xs">{{ Str::substr($question->creator?->name ?? '?', 0, 1) }}</span>
											</div>
										</div>
										<span class="text-sm">{{ $question->creator?->name ?? __('lang.unknown') }}</span>
									</div>
								</td>
								<td class="py-3 px-4 text-sm text-gray-500">
									{{ $question->created_at->diffForHumans() }}
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="py-8 text-center text-gray-500">
									<div class="flex flex-col items-center justify-center gap-2">
										<x-icon name="o-inbox" class="w-12 h-12 text-gray-300" />
										<p>{{ __('lang.no_questions_in_cycle') ?? 'لا توجد أسئلة مضافة في هذه الدورة حتى الآن.' }}</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>