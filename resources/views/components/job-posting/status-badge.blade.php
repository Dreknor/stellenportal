@props(['jobPosting'])

@php
    $classes = match ($jobPosting->status) {
        \App\Models\JobPosting::STATUS_ACTIVE => 'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200',
        \App\Models\JobPosting::STATUS_PAUSED => 'bg-sky-100 text-sky-900 dark:bg-sky-900/40 dark:text-sky-200',
        \App\Models\JobPosting::STATUS_EXPIRED => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $jobPosting->getStatusLabel() }}
</span>
