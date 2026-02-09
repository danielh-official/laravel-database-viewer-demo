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

    Route::post('sql/execute', function (Illuminate\Http\Request $request) {
        $tables = \DB::connection()->getSchemaBuilder()->getTables();

        $query = $request->input('query');

        $results = \DB::select($query);

        return view('db.sql', compact('tables', 'results', 'query'));
    })->name('sql.execute');

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

        Route::get('/insert', function ($table) {
            $tables = \DB::connection()->getSchemaBuilder()->getTables();

            $columns = Schema::getColumns($table);

            return view('db.table.insert', compact('tables', 'table', 'columns'));
        })->name('insert');

        Route::post('/insert', function (\Illuminate\Http\Request $request, $table) {
            $columns = Schema::getColumns($table);

            $data = $request->only(array_map(fn ($column) => $column['name'], $columns));

            $validationRules = [];

            $foreignKeys = Schema::getForeignKeys($table);

            // Check if any of the columns are foreign keys and validate the referenced IDs
            foreach ($foreignKeys as $foreignKey) {
                $referencedTable = $foreignKey['foreign_table'];
                $referencedColumn = $foreignKey['foreign_columns'][0];
                $column = $foreignKey['columns'][0];
                $value = $data[$column] ?? null;

                if (isset($validationRules[$column])) {
                    $validationRules[$column][] = ["exists:{$referencedTable},{$referencedColumn}"];
                } else {
                    $validationRules[$column] = ["exists:{$referencedTable},{$referencedColumn}"];
                }
            }

            $indexes = collect(Schema::getIndexes($table));

            // Check if any of the columns have a unique constraint and validate the values
            foreach ($columns as $column) {
                $columnName = $column['name'];
                $indexes->filter(function ($index) use ($columnName) {
                    $indexColumnName = $index['columns'][0];

                    return $indexColumnName === $columnName && $index['unique'];
                })->each(function ($index) use ($columnName, $table, &$validationRules) {
                    if (isset($validationRules[$columnName])) {
                        $validationRules[$columnName][] = ["unique:{$table},{$columnName}"];
                    } else {
                        $validationRules[$columnName] = ["unique:{$table},{$columnName}"];
                    }
                });
            }

            $request->validate($validationRules);

            \DB::table($table)->insert($data);

            return redirect()->route('db.table.data', ['table' => $table]);
        })->name('store');

        Route::name('data.row.')->prefix('data/row/{id}')->group(function () {
            Route::get('/', function ($table, $id) {
                $tables = \DB::connection()->getSchemaBuilder()->getTables();

                $row = \DB::table($table)->whereId($id)->orWhere('key', $id)->first();

                return view('db.table.data.row.show', compact('tables', 'table', 'row'));
            })->name('show');

            Route::get('/edit', function ($table, $id) {
                $tables = \DB::connection()->getSchemaBuilder()->getTables();

                $columns = Schema::getColumns($table);

                $row = \DB::table($table)->whereId($id)->orWhere('key', $id)->first();

                return view('db.table.data.row.edit', compact('tables', 'table', 'columns', 'row'));
            })->name('edit');

            Route::patch('/update', function ($table, $id, \Illuminate\Http\Request $request) {
                $columns = Schema::getColumns($table);

                $data = $request->only(array_map(fn ($column) => $column['name'], $columns));

                $validationRules = [];

                $foreignKeys = Schema::getForeignKeys($table);

                // Check if any of the columns are foreign keys and validate the referenced IDs
                foreach ($foreignKeys as $foreignKey) {
                    $referencedTable = $foreignKey['foreign_table'];
                    $referencedColumn = $foreignKey['foreign_columns'][0];
                    $column = $foreignKey['columns'][0];
                    $value = $data[$column] ?? null;

                    if (isset($validationRules[$column])) {
                        $validationRules[$column][] = ["exists:{$referencedTable},{$referencedColumn}"];
                    } else {
                        $validationRules[$column] = ["exists:{$referencedTable},{$referencedColumn}"];
                    }
                }

                $indexes = collect(Schema::getIndexes($table));

                // Check if any of the columns have a unique constraint and validate the values
                foreach ($columns as $column) {
                    $columnName = $column['name'];
                    $indexes->filter(function ($index) use ($columnName) {
                        $indexColumnName = $index['columns'][0];

                        return $indexColumnName === $columnName && $index['unique'];
                    })->each(function ($index) use ($columnName, $table, &$validationRules, $id) {
                        if (isset($validationRules[$columnName])) {
                            $validationRules[$columnName][] = [
                                \Illuminate\Validation\Rule::unique($table, $columnName)->ignore($id),
                            ];
                        } else {
                            $validationRules[$columnName] = [
                                \Illuminate\Validation\Rule::unique($table, $columnName)->ignore($id),
                            ];
                        }
                    });
                }

                unset($validationRules['id']);

                $request->validate($validationRules);

                \DB::table($table)->where('id', $id)->orWhere('key', $id)->update($data);

                return redirect()->route('db.table.data.row.show', ['table' => $table, 'id' => $id]);
            })->name('update');

            Route::delete('/delete', function ($table, $id) {
                \DB::table($table)->where('id', $id)->orWhere('key', $id)->delete();

                return redirect()->back();
            })->name('delete');
        });
    });
});
