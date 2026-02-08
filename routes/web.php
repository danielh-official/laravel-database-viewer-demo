<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::name('db.')->prefix('db')->group(function () {
    Route::get('/', function () {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        return view('db.home', compact('tables'));
    })->name('home');

    Route::get('sql', function () {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        return view('db.sql', compact('tables'));
    })->name('sql');

    Route::get('/table/{table}', function (Illuminate\Http\Request $request, $table) {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        $columns = \DB::connection()->getSchemaBuilder()->getColumnListing($table);

        $currentPage = $request->input('page', 1);

        $rows = \DB::table($table)->paginate(15, ['*'], 'page', $currentPage);

        return view('db.table', compact('tables', 'table', 'columns', 'rows'));
    })->name('table');
});
