<x-layout.db :tables="$tables" title="SQL">
    <div class="p-4 text-center">
        <form action="{{ route('db.sql.execute') }}" method="POST">
            @csrf
            <textarea name="query" rows="2" class="w-full p-2 border border-gray-300 rounded"
                placeholder="Enter your SQL query here...">{{ $query ?? '' }}</textarea>
            <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Execute</button>
        </form>
    </div>
    <div class="w-full overflow-x-auto">
        @if (isset($results))
            <table class="table-auto border-collapse border border-gray-300">
                <thead>
                    <tr>
                        @foreach (array_keys((array) $results[0]) as $column)
                            <th class="border border-gray-300 px-4 py-2">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $row)
                        <tr>
                            @foreach ((array) $row as $value)
                                <td class="border border-gray-300 px-4 py-2">{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layout.db>
