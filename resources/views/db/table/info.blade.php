<x-layout.db :tables="$tables" :selectedTable="$table">
    <div class="flex flex-col gap-20">
        {{-- Navigation buttons for table routes (e.g., structure, info) --}}
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
        {{-- Show case information about the current table --}}
        <div class="w-full overflow-x-auto flex flex-col gap-4">
            <h2 class="text-2xl font-semibold">
                Table Info
            </h2>
            <table class="table-auto border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $table === null,
                        ])>{{ $table }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Rows</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $rowCount === 0,
                        ])>{{ $rowCount }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Columns</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => count($columns) === 0,
                        ])>{{ count($columns) }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Engine</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $engine === null,
                        ])>{{ $engine ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Collation</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $collation === null,
                        ])>{{ $collation ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Comment</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $comment === null,
                        ])>{{ $comment ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Create Query</th>
                        <td @class([
                            'border border-gray-300 px-4 py-2',
                            'text-gray-500' => $createQuery === null,
                        ])>
                            <span
                                class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $createQuery ?? 'n/a' }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout.db>
