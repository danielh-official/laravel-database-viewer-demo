@php
    $rowJson = json_encode($row, JSON_PRETTY_PRINT);
    $json = '<pre>' . htmlspecialchars($rowJson) . '</pre>';
@endphp


<x-layout.db :tables="$tables" :selectedTable="$table" title="Details | Row {{ $row->id ?? null }} | Data | {{ $table }} Table">
    <x-layout.db.table :table="$table">
        <x-layout.db.table.data.row :table="$table" :row="$row">
            <div style="justify-items:center;">
                {!! $json !!}
            </div>
        </x-layout.db.table.data.row>
    </x-layout.db.table>
</x-layout.db>
