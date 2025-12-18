@php
    $statusColors = [
        'todo' => 'border-blue-400',
        'in_progress' => 'border-yellow-400',
        'done' => 'border-green-400',
        'canceled' => 'border-red-400 bg-red-50 opacity-80',
    ];

    $priorityBadge = [
        'low' => 'bg-gray-200 text-gray-700',
        'medium' => 'bg-blue-200 text-blue-800',
        'high' => 'bg-red-200 text-red-800',
    ];
@endphp

<div class="rounded-xl border-l-4 p-4 shadow-sm bg-white {{ $statusColors[$record->status] ?? '' }}">
    <div class="flex justify-between items-start gap-2">
        <h3 class="font-semibold text-sm text-gray-800">
            {{ $record->title }}
        </h3>

        <span class="text-xs px-2 py-1 rounded-full {{ $priorityBadge[$record->priority] }}">
            {{ ucfirst($record->priority) }}
        </span>
    </div>

    @if($record->description)
        <p class="text-xs text-gray-500 mt-1 line-clamp-2">
            {{ $record->description }}
        </p>
    @endif

    <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
        @if($record->deadline)
            <span>
                📅 {{ \Carbon\Carbon::parse($record->deadline)->format('d M Y') }}
            </span>
        @endif

        @if($record->status === 'canceled')
            <span class="text-red-600 font-medium">
                Canceled
            </span>
        @endif
    </div>
</div>
