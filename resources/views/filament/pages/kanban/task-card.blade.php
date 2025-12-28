@props(['record'])

<div {{ $attributes->merge(['class' => 'p-4 rounded-lg shadow bg-white border']) }}>
    {{-- Header: Title + Priority --}}
    <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-lg">{{ $record->title }}</h3>
        <span class="text-sm px-2 py-1 rounded
            @if($record->priority === 'low') bg-green-100 text-green-800
            @elseif($record->priority === 'medium') bg-yellow-100 text-yellow-800
            @elseif($record->priority === 'high') bg-red-100 text-red-800
            @endif
        ">{{ ucfirst($record->priority) }}</span>
    </div>

    {{-- Description --}}
    @if($record->description)
        <p class="text-sm text-gray-700 mb-2">{{ $record->description }}</p>
    @endif

    {{-- Status Badge --}}
    <span class="inline-block text-xs font-semibold px-2 py-1 rounded
        @if($record->status === 'todo') bg-gray-200 text-gray-800
        @elseif($record->status === 'in_progress') bg-blue-200 text-blue-800
        @elseif($record->status === 'done') bg-green-200 text-green-800
        @elseif($record->status === 'canceled') bg-red-200 text-red-800
        @endif
    ">
        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
    </span>

    {{-- Canceled Reason --}}
    @if($record->status === 'canceled' && $record->canceled_reason)
        <p class="text-red-700 text-xs mt-1 italic">Alasan: {{ $record->canceled_reason }}</p>
    @endif

    {{-- Team / Individu --}}
    @if ($record->team)
        <span class="inline-block text-xs font-semibold px-2 py-1 rounded bg-purple-200 text-purple-800 mt-2">
            Tim: {{ $record->team->name }}
        </span>
    @else
        <span class="inline-block text-xs font-semibold px-2 py-1 rounded bg-blue-200 text-blue-800 mt-2">
            Individu
        </span>
    @endif

    {{-- Footer: Owner + Deadline --}}
    <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
        <span>Owner: {{ $record->user->name ?? 'N/A' }}</span>
        @if($record->deadline)
            <span>Deadline: {{ \Carbon\Carbon::parse($record->deadline)->format('d M Y') }}</span>
        @endif
    </div>
</div>
