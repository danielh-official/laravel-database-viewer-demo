<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::name('db.')->prefix('db')->group(function () {
    Route::get('/', function () {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        $connectionDetails = \DB::getConfig();

        return view('db.home', compact('tables', 'connectionDetails'));
    })->name('home');

    Route::get('sql', function () {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        return view('db.sql', compact('tables'));
    })->name('sql');

    Route::name('table.')->prefix('/table/{table}')->group(function () {
        Route::get('/data', function (Illuminate\Http\Request $request, $table) {
            $tables = \DB::connection()->getSchemaBuilder()->getTables();

            $columns = \DB::connection()->getSchemaBuilder()->getColumnListing($table);

            $currentPage = $request->input('page', 1);

            $query = \DB::table($table);

            $showSortForm = $request->input('show_sort_form', false);

            $sorts = collect($request->array('sort'));

            $sorts = $sorts->filter(fn ($value) => ! empty($value));

            foreach ($columns as $column) {
                if (isset($sorts[$column])) {
                    $query->orderBy($column, $sorts[$column]);
                }
            }

            $rows = $query->paginate(15, ['*'], 'page', $currentPage)->withQueryString();

            return view('db.table.data', compact('tables', 'table', 'columns', 'rows', 'sorts', 'showSortForm'));
        })->name('data');

        Route::get('/structure', function ($table) {
            $tables = \DB::connection()->getSchemaBuilder()->getTables();

            $columns = Schema::getColumns($table);

            $foreignKeys = Schema::getForeignKeys($table);

            $indexes = Schema::getIndexes($table);

            return view('db.table.structure', compact('tables', 'table', 'columns', 'foreignKeys', 'indexes'));
        })->name('structure');

        Route::get('/info', function ($table) {
            $tables = \DB::connection()->getSchemaBuilder()->getTables();

            $columns = Schema::getColumns($table);

            $foreignKeys = Schema::getForeignKeys($table);

            $indexes = Schema::getIndexes($table);

            $rowCount = \DB::table($table)->count();

            if (\DB::getDriverName() === 'mysql') {
                $databaseType = 'mysql';
                $engine = \DB::selectOne('SHOW TABLE STATUS WHERE Name = ?', [$table])->Engine;
                $collation = \DB::selectOne('SHOW TABLE STATUS WHERE Name = ?', [$table])->Collation;
                $comment = \DB::selectOne('SHOW TABLE STATUS WHERE Name = ?', [$table])->Comment;
            } else {
                $databaseType = \DB::getDriverName();
                $engine = null;
                $collation = null;

                if (\DB::getDriverName() === 'pgsql') {
                    $collation = \DB::selectOne('SELECT pg_catalog.pg_get_userbyid(datdba) AS collation FROM pg_catalog.pg_database WHERE datname = ?', [\DB::getDatabaseName()])->collation;
                }

                if (\DB::getDriverName() === 'sqlite') {
                    $collation = \DB::selectOne('PRAGMA encoding')->encoding;
                }

                $comment = null;

                if (\DB::getDriverName() === 'pgsql') {
                    $comment = \DB::selectOne('SELECT obj_description(oid) AS comment FROM pg_class WHERE relname = ?', [$table])->comment;
                }
            }

            $createQuery = null;

            if (\DB::getDriverName() === 'sqlite') {
                $createQuery = \DB::selectOne('SELECT sql FROM sqlite_schema WHERE name = ?', [$table])->sql;
            }

            if (\DB::getDriverName() === 'mysql') {
                $createQuery = \DB::selectOne("SHOW CREATE TABLE `$table`")->{'Create Table'};
            }

            if (\DB::getDriverName() === 'pgsql') {
                $createQuery = \DB::selectOne('SELECT pg_get_tabledef(oid) AS create_table FROM pg_class WHERE relname = ?', [$table])->create_table;
            }

            return view('db.table.info', compact('tables', 'table', 'columns', 'foreignKeys', 'indexes', 'rowCount', 'engine', 'collation', 'comment', 'createQuery'));
        })->name('info');
    });
});
