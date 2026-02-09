@php
    function sql_to_html_input_type($sql_type)
    {
        // Standardize type name by removing length/constraints and converting to lowercase
        $type = strtolower(explode('(', $sql_type)[0]);

        switch ($type) {
            // Text types
            case 'varchar':
            case 'char':
            case 'text':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
                return 'text';

            // Integer types
            case 'int':
            case 'integer':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'bigint':
                return 'number';

            // Float/Decimal types
            case 'float':
            case 'double':
            case 'decimal':
            case 'numeric':
                return 'number';

            // Date/Time types
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime-local';
            case 'time':
                return 'time';
            case 'year':
                return 'number'; // A 4-digit number input is suitable for a year

            // Boolean/Enum types
            case 'boolean':
            case 'bool':
                return 'checkbox'; // Consider a checkbox or select for better UX
            case 'enum':
                return 'select'; // Enums often work best as a <select> dropdown

            // Other types
            case 'email': // If you use a custom "email" SQL type
                return 'email';
            case 'url': // If you use a custom "url" SQL type
                return 'url';

            // Default to text if no specific mapping is found
            default:
                return 'text';
        }
    }
@endphp

<x-layout.db :tables="$tables" :selectedTable="$table" title="Insert | {{ $table }} Table">
    <x-layout.db.table :table="$table">
        <h2 class="text-2xl font-semibold">
            Insert Data
        </h2>
        <form action="{{ route('db.table.store', ['table' => $table]) }}" method="POST">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($columns as $column)
                    @if (!($column['auto_increment'] ?? false))
                        <div class="mb-4">
                            <label for="{{ $column['name'] }}" class="block text-sm font-medium text-gray-700">
                                {{ $column['name'] }}
                                @if (!($column['nullable'] ?? false))
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="{{ sql_to_html_input_type($column['type']) }}" name="{{ $column['name'] }}"
                                id="{{ $column['name'] }}" @required(!($column['nullable'] ?? false))
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                autocomplete="off" />
                            @error($column['name'])
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-end">
                <button type="submit"
                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                    Insert
                </button>
            </div>
        </form>
    </x-layout.db.table>
</x-layout.db>
