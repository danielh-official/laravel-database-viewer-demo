{{-- Navigation buttons for table routes (e.g., structure, info) --}}
<div class="flex flex-col gap-20">
    <div class="self-end flex gap-4">
        <a href="{{ route('db.table.data', ['table' => $table]) }}" @class([
            'hover:text-blue-600 dark:text-gray-400',
            'font-semibold text-blue-500' => request()->routeIs('db.table.data'),
        ])>
            Data
        </a>
        <a href="{{ route('db.table.structure', ['table' => $table]) }}" @class([
            'hover:text-blue-600 dark:text-gray-400',
            'font-semibold text-blue-500' => request()->routeIs('db.table.structure'),
        ])>
            Structure
        </a>
        <a href="{{ route('db.table.info', ['table' => $table]) }}" @class([
            'hover:text-blue-600 dark:text-gray-400',
            'font-semibold text-blue-500' => request()->routeIs('db.table.info'),
        ])>
            Info
        </a>
    </div>
    <div class="flex flex-col gap-2">
        {{ $slot }}
    </div>
</div>
