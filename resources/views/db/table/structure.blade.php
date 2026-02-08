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
        <div class="w-full overflow-x-auto flex flex-col gap-4">
            {{-- This should be a table with the following fields: Name, Type, Collation, Nullable, Default, Auto Increment, Comment, Generation --}}
            <h2 class="text-2xl font-semibold">
                Table Structure
            </h2>
            <table class="table-auto border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Type</th>
                        <th class="border border-gray-300 px-4 py-2">Collation</th>
                        <th class="border border-gray-300 px-4 py-2">Nullable</th>
                        <th class="border border-gray-300 px-4 py-2">Default</th>
                        <th class="border border-gray-300 px-4 py-2">Auto Increment</th>
                        <th class="border border-gray-300 px-4 py-2">Comment</th>
                        <th class="border border-gray-300 px-4 py-2">Generation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($columns as $column)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $column['name'] }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $column['type'] }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => $column['collation'] === null,
                            ])>{{ $column['collation'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'font-semibold' => empty($column['nullable']),
                            ])>{{ $column['nullable'] ? 'Yes' : 'No' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($column['default']),
                            ])>{{ $column['default'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($column['auto_increment']),
                            ])>{{ $column['auto_increment'] ? 'Yes' : 'No' }}
                            </td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($column['comment']),
                            ])>{{ $column['comment'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($column['generation']),
                            ])>{{ $column['generation'] ?? 'n/a' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 px-4 py-2 text-gray-500 text-center">No columns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Indexes --}}
            <h2 class="text-2xl font-semibold">
                Indexes
            </h2>
            <table class="table-auto border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Type</th>
                        <th class="border border-gray-300 px-4 py-2">Columns</th>
                        <th class="border border-gray-300 px-4 py-2">Unique</th>
                        <th class="border border-gray-300 px-4 py-2">Primary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($indexes as $index)
                        <tr>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($index['name']),
                            ])>{{ $index['name'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($index['type']),
                            ])>{{ $index['type'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($index['columns']),
                            ])>{{ implode(', ', $index['columns']) }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($index['unique']),
                            ])>{{ $index['unique'] ? 'Yes' : 'No' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($index['primary']),
                            ])>{{ $index['primary'] ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 px-4 py-2 text-gray-500 text-center">No indexes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Foreign Keys --}}
            <h2 class="text-2xl font-semibold">
                Foreign Keys
            </h2>
            <table class="table-auto border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Column</th>
                        <th class="border border-gray-300 px-4 py-2">Referenced Table</th>
                        <th class="border border-gray-300 px-4 py-2">Referenced Column</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($foreignKeys as $foreignKey)
                        <tr>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($foreignKey['name']),
                            ])>{{ $foreignKey['name'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($foreignKey['column']),
                            ])>{{ $foreignKey['column'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($foreignKey['referenced_table']),
                            ])>{{ $foreignKey['referenced_table'] ?? 'n/a' }}</td>
                            <td @class([
                                'border border-gray-300 px-4 py-2',
                                'text-gray-500' => empty($foreignKey['referenced_column']),
                            ])>{{ $foreignKey['referenced_column'] ?? 'n/a' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border border-gray-300 px-4 py-2 text-gray-500 text-center">No foreign keys found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout.db>
