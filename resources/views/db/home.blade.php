@php
    $connectionDetails = json_encode($connectionDetails, JSON_PRETTY_PRINT);
    $json = '<pre>' . htmlspecialchars($connectionDetails) . '</pre>';
@endphp

<x-layout.db :tables="$tables">
    <div class="p-4 flex flex-col gap-4">
        <span class="font-mono text-sm text-gray-700 dark:text-gray-300">
            {!! $json !!}
        </span>
    </div>
</x-layout.db>
