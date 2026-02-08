<div class="flex flex-col gap-8">
    <div class="self-end flex gap-4">
        <a href="{{ route('db.table.data.row.show', ['table' => $table, 'id' => $row->id]) }}"
            @class([
                'hover:text-blue-600 dark:text-gray-400',
                'font-semibold text-blue-500' => request()->routeIs(
                    'db.table.data.row.show'),
            ])>
            Details
        </a>
        <a href="{{ route('db.table.data.row.edit', ['table' => $table, 'id' => $row->id]) }}"
            @class([
                'hover:text-blue-600 dark:text-gray-400',
                'font-semibold text-blue-500' => request()->routeIs(
                    'db.table.data.row.edit'),
            ])>
            Edit
        </a>
        <form action="{{ route('db.table.data.row.delete', ['table' => $table, 'id' => $row->id]) }}" method="POST"
            class="inline" onsubmit="return confirm('Are you sure you want to delete this row?');">

            @csrf
            @method('DELETE')

            <button type="submit" class="text-red-500 hover:underline">Delete</button>
        </form>
    </div>

    <div>
        {{ $slot }}
    </div>

</div>
