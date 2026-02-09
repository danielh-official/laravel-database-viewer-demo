@php
    $rowJson = json_encode($row, JSON_PRETTY_PRINT);
    $json = '<pre style="white-space: pre-wrap; word-break: break-word;">' . htmlspecialchars($rowJson) . '</pre>';
@endphp

<x-layout.db :tables="$tables" :selectedTable="$table"
    title="Details | Row {{ $row->id ?? ($row->key ?? null) }} | Data | {{ $table }} Table">
    <x-layout.db.table :table="$table">
        <x-layout.db.table.data.row :table="$table" :row="$row">
            <div style="justify-items:center;">
                {!! $json !!}
            </div>
        </x-layout.db.table.data.row>
    </x-layout.db.table>
</x-layout.db>
