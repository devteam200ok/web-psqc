<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminDevelopmentDatabase extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $newTable = false;
    public $tableName;
    public $tableColumns = [];
    public $selectedTable = '';
    public $selectedMissingTable = '';
    public $newRow = [];
    public $search = '';

    public $columnName = '';
    public $columnType = 'string';
    public $columnNullable = true;
    public $columnDefault = '';
    public $migrationRows = [];

    public function deleteMigrationRow($columnName)
    {
        $table = $this->selectedMissingTable;
        $migrationPath = database_path('migrations');
        if (!file_exists($migrationPath)) {
            throw new Exception("Directory does not exist: {$migrationPath}");
        }
    
        $migrationFiles = scandir($migrationPath);
        $migrationFile = array_filter($migrationFiles, function ($file) use ($table) {
            return preg_match("/create_{$table}_table\.php/", $file);
        });
    
        if (empty($migrationFile)) {
            throw new Exception("Migration file for table {$table} not found");
        }
    
        $migrationFile = current($migrationFile);
        $migrationFile = str_replace('.php', '', $migrationFile);
        $migrationFilePath = database_path("migrations/{$migrationFile}.php");
    
        if (!file_exists($migrationFilePath)) {
            throw new Exception("File does not exist: {$migrationFilePath}");
        }
    
        $migrationContent = file_get_contents($migrationFilePath);
    
        // Improved regular expression to match more accurately
        // This pattern assumes the column definitions start at the beginning of a new line, possibly preceded by whitespace
        $pattern = "/\\\$table->[\w]+\(\\s*['\"]" . preg_quote($columnName, '/') . "['\"]\\s*[^;]*\);/m";
        $migrationContent = preg_replace($pattern, '', $migrationContent);
    
        // Save the migration file
        file_put_contents($migrationFilePath, $migrationContent);
    
        $this->openCreateColumn($table);
    }
    

    public function createColumn()
    {
        $table = $this->selectedMissingTable;
        $column = $this->columnName;
        $type = $this->columnType;
        $nullable = $this->columnNullable;
        $default = $this->columnDefault;
    
        $migrationPath = database_path('migrations');
        if (!file_exists($migrationPath)) {
            throw new Exception("Directory does not exist: {$migrationPath}");
        }
    
        $migrationFiles = scandir($migrationPath);
        $migrationFile = array_filter($migrationFiles, function ($file) use ($table) {
            return preg_match("/create_{$table}_table\.php/", $file);
        });
    
        if (empty($migrationFile)) {
            throw new Exception("Migration file for table {$table} not found");
        }
    
        $migrationFile = current($migrationFile);
        $migrationFile = str_replace('.php', '', $migrationFile);
        $migrationFilePath = database_path("migrations/{$migrationFile}.php");
    
        if (!file_exists($migrationFilePath)) {
            throw new Exception("File does not exist: {$migrationFilePath}");
        }
    
        $migrationContent = file_get_contents($migrationFilePath);
    
        // add this line to the migration file before $table->timestamps();
        $migrationContent = preg_replace('/\$table->timestamps\(\);/', "\$table->{$type}('{$column}')" . ($nullable == 'true' ? '->nullable()' : '') . ($default ? "->default('{$default}')" : '') . ";\n            \$table->timestamps();", $migrationContent);
    
        // save the migration file
        file_put_contents($migrationFilePath, $migrationContent);

        $this->openCreateColumn($table);
    }

    public function openCreateColumn($table)
    {
        $this->selectedMissingTable = $table;
        
        $migrationPath = database_path('migrations');
        if (!file_exists($migrationPath)) {
            throw new Exception("Directory does not exist: {$migrationPath}");
        }
    
        $migrationFiles = scandir($migrationPath);
        $migrationFile = array_filter($migrationFiles, function ($file) use ($table) {
            return preg_match("/create_{$table}_table\.php/", $file);
        });
    
        if (empty($migrationFile)) {
            throw new Exception("Migration file for table {$table} not found");
        }
    
        $migrationFile = current($migrationFile);
        $migrationFile = str_replace('.php', '', $migrationFile);
        $migrationFilePath = database_path("migrations/{$migrationFile}.php");
    
        if (!file_exists($migrationFilePath)) {
            throw new Exception("File does not exist: {$migrationFilePath}");
        }
    
        $migrationContent = file_get_contents($migrationFilePath);

        // get the rows between "$table->id();" and "$table->timestamps();" from the migration file
        preg_match('/\$table->id\(\);(.*?)\$table->timestamps\(\);/s', $migrationContent, $matches);
        $rows = $matches[1];
        $rows = explode("\n", $rows);
        $rows = array_map('trim', $rows);
        $rows = array_filter($rows, function ($row) {
            return !empty($row);
        });
        $this->migrationRows = $rows;
    }

    public function hideCreateColumn()
    {
        $this->selectedMissingTable = '';
        $this->migrationRows = [];
    }

    public function updatedSearch()
    {
        $this->goToPage(1);
    }

    public function toggleNewTable()
    {
        $this->newTable = !$this->newTable;
    }

    public function createTable()
    {
        \Artisan::call('make:model', ['name' => $this->tableName, '-m' => true]);
        $this->newTable = false;
        $this->tableName = '';
    }

    public function showTableColumns($table)
    {
        $columns = \DB::select("SHOW COLUMNS FROM $table");
        $columns = array_map(function ($column) {
            return (array) $column;
        }, $columns);
        $this->tableColumns = $columns;
        $this->selectedTable = $table;
    }

    public function hideTableColumns()
    {
        $this->selectedTable = '';
    }

    public function deleteTableRow($id)
    {
        \DB::table($this->selectedTable)->where('id', $id)->delete();
    }
    
    public function createTableRow()
    {
        // insert created_at and updated_at
        $this->newRow['created_at'] = now();
        $this->newRow['updated_at'] = now();
        $this->newRow = array_filter($this->newRow, function ($key) {
            return $key != 'id';
        }, ARRAY_FILTER_USE_KEY);

        \DB::table($this->selectedTable)->insert($this->newRow);
        $this->newRow = [];
    }

    public function deleteTable($table)
    {
        $this->dropTable($table);
        // find migration file
        $migrationFiles = scandir(database_path('migrations'));
        $migrationFile = array_filter($migrationFiles, function ($file) use ($table) {
            return preg_match("/create_{$table}_table\.php/", $file);
        });
        // delete the migration file
        if (count($migrationFile) > 0) {
            $migrationFile = current($migrationFile);
            $migrationFile = str_replace('.php', '', $migrationFile);
            unlink(database_path("migrations/{$migrationFile}.php"));
        }
    
        $table = explode('_', $table);
        $table = array_map('ucfirst', $table);
        $table = implode('', $table);
        $modelName = Str::singular($table);
    
        unlink(app_path("Models/{$modelName}.php"));
    }    

    public function dropTable($table)
    {
        \DB::statement("DROP TABLE $table");
        // delete the row from the migrations table
        \DB::table('migrations')->where('migration', 'like', "%create_{$table}_table%")->delete();
    }

    public function migrateTable($table)
    {
        // search for the migration file with the table name
        $migrationFiles = scandir(database_path('migrations'));
        $migrationFile = array_filter($migrationFiles, function ($file) use ($table) {
            return preg_match("/create_{$table}_table\.php/", $file);
        });

        // migrate the migration file
        if (count($migrationFile) > 0) {
            $migrationFile = current($migrationFile);
            $migrationFile = str_replace('.php', '', $migrationFile);
            \Artisan::call('migrate', ['--path' => "database/migrations/{$migrationFile}.php"]);
        }

    }

    public function render()
    {

        // list of the tables in the database
        $tables = \DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);

        // get each table's rows and size and collation and type
        foreach ($tables as $key => $table) {
            $tableInfo = [];
            $tableInfo['name'] = $table;
            $tableInfo['rows'] = \DB::select("SELECT COUNT(*) as count FROM $table");
            $tableInfo['rows'] = current($tableInfo['rows'])->count;
            $tableInfo['size'] = \DB::select("SHOW TABLE STATUS LIKE '$table'");
            $tableInfo['size'] = current($tableInfo['size'])->Data_length;
            $tableInfo['collation'] = \DB::select("SHOW TABLE STATUS LIKE '$table'");
            $tableInfo['collation'] = current($tableInfo['collation'])->Collation;
            $tableInfo['type'] = \DB::select("SHOW TABLE STATUS LIKE '$table'");
            $tableInfo['type'] = current($tableInfo['type'])->Engine;
            $tableInfo['columns'] = \DB::select("SHOW COLUMNS FROM $table");
            $tableInfo['columns'] = count($tableInfo['columns']);
            $tables[$key] = $tableInfo;
        }

        $migrationFiles = scandir(database_path('migrations'));
        $migrationTableNames = array_filter($migrationFiles, function ($file) {
            return preg_match('/\.php$/', $file);
        });
        
        $migrationTableNames = array_map(function ($file) {
            $file = str_replace('.php', '', $file);
            $parts = explode('_', $file);
            $createIndex = array_search('create', $parts);
            if ($createIndex === false) {
                return null;
            }
            $tableNameParts = array_slice($parts, $createIndex + 1, -1);
            return implode('_', $tableNameParts);
        }, $migrationTableNames);
        
        $migrationTableNames = array_filter($migrationTableNames, function ($name) {
            return !is_null($name);
        });
        
        $actualTablesResult = DB::select('SHOW TABLES');
        $actualTables = array_map(function ($table) {
            return reset($table);
        }, $actualTablesResult);
        
        $missingTables = array_diff($migrationTableNames, $actualTables);

        if($this->selectedTable != ''){
            if($this->search != ''){

                $query = \DB::table($this->selectedTable);
                foreach($this->tableColumns as $column){
                    $query->orWhere($column['Field'], 'like', '%'.$this->search.'%');
                }
                $tableData = $query->orderby('id', 'desc')->paginate(10);
                               
            } else {
                $tableData = \DB::table($this->selectedTable)
                ->orderby('id', 'desc')
                ->paginate(10);
            }
            return view('livewire.admin-development-database')
                ->with('tables', $tables)
                ->with('missingTables', $missingTables)
                ->with('tableData', $tableData)
                ->layout('layouts.admin');
        } else {
            return view('livewire.admin-development-database')
                ->with('tables', $tables)
                ->with('missingTables', $missingTables)
                ->layout('layouts.admin');
        }
    }
}
