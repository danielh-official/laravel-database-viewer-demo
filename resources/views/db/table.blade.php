<x-layout.db :tables="$tables" :selectedTable="$table">
    <div class="w-full overflow-x-auto">
        <table class="table-auto border-collapse border border-gray-300">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th class="border border-gray-300 px-4 py-2">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            @if ($row->$column)
                                <td class="border border-gray-300 px-4 py-2 truncate max-w-64">
                                    {{ $row->$column }}
                                </td>
                            @elseif ($row->$column === '')
                                <td class="border border-gray-300 px-4 py-2">-</td>
                            @else
                                <td class="border border-gray-300 px-4 py-2 text-gray-400">NULL</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="my-4">
        {{ $rows->links() }}
    </div>

</x-layout.db>
