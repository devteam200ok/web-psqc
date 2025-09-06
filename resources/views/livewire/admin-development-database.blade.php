@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-12">
                <div class="row align-items-center justify-content-between g-3 mb-3">
                    <div class="col-auto">
                        <button wire:click="toggleNewTable" class="btn btn-primary" type="button">
                            <span class="ti ti-{{ $newTable ? 'minus' : 'plus' }} me-2"></span>New Model &
                            Migration
                        </button>
                    </div>
                </div>
                @if ($newTable)
                    <form wire:submit.prevent="createTable">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="tableName" class="form-label">Model Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Model Name</span>
                                        <input type="text" class="form-control" id="tableName" wire:model="tableName"
                                            value="{{ $tableName }}">
                                        <button class="btn btn-primary" type="submit">
                                            <span class="ti ti-plus me-2"></span>
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if ($selectedTable == '')
                    <div>
                        <div class="d-flex mt-3">
                            <h3>Missing Tables</h3>
                            @if ($selectedMissingTable != '')
                                <button wire:click="hideCreateColumn" class="btn btn-secondary ms-auto">Return to
                                    Tables</button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-centered table-sm table-striped nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>name</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missingTables as $missingTable)
                                        <tr class="align-middle">
                                            <td class="ps-2">
                                                <a href="javascript:void(0)"
                                                    wire:click="openCreateColumn('{{ $missingTable }}')">{{ $missingTable }}</a>
                                            </td>
                                            <td class="text-end pe-2">
                                                @if ($selectedMissingTable == '')
                                                    <button wire:click="migrateTable('{{ $missingTable }}')"
                                                        class="btn btn-secondary py-1 px-2" type="button">
                                                        <span class="ti ti-upload me-2"></span>Migrate
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($selectedMissingTable == '')
                            <h3 class="mt-5">Tables</h3>
                            <div class="table-responsive">
                                <table class="table table-centered table-sm table-striped nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>name</th>
                                            <th>collation</th>
                                            <th>type</th>
                                            <th>columns</th>
                                            <th>rows</th>
                                            <th>size</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tables as $table)
                                            <tr class="align-middle">
                                                <td class="ps-2">
                                                    <a href="javascript:void(0)"
                                                        wire:click="showTableColumns('{{ $table['name'] }}')">
                                                        {{ $table['name'] }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $table['collation'] }}
                                                </td>
                                                <td>
                                                    {{ $table['type'] }}
                                                </td>
                                                <td>
                                                    {{ $table['columns'] }}
                                                </td>
                                                <td>
                                                    {{ $table['rows'] }}
                                                </td>
                                                <td>
                                                    {{ $table['size'] / 1024 }} KB
                                                </td>
                                                <td class="text-end pe-2">
                                                    <button wire:click="deleteTable('{{ $table['name'] }}')"
                                                        class="btn btn-danger py-1 px-2" type="button">
                                                        <span class="ti ti-trash me-2"></span>Delete
                                                    </button>
                                                    <button wire:click="dropTable('{{ $table['name'] }}')"
                                                        class="btn btn-secondary py-1 px-2" type="button">
                                                        <span class="ti ti-reload me-2"></span>Drop
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="mt-5">
                                <h3>{{ $selectedMissingTable }}</h3>
                            </div>
                            <form wire:submit.prevent="createColumn">
                                <div class="row">
                                    <div class="col-xl-3 mb-2">
                                        <label for="columnName" class="form-label">Column Name</label>
                                        <input type="text" class="form-control" id="columnName"
                                            wire:model="columnName" value="{{ $columnName }}">
                                    </div>
                                    <div class="col-xl-3 mb-2">
                                        <label for="columnType" class="form-label">Column Type</label>
                                        <input type="text" class="form-control" id="columnType"
                                            wire:model="columnType" value="{{ $columnType }}">
                                    </div>
                                    <div class="col-xl-3 mb-2">
                                        <label for="columnNullable" class="form-label">Nullable</label>
                                        <input type="text" class="form-control" id="columnNullable"
                                            wire:model="columnNullable" value="{{ $columnNullable }}">
                                    </div>
                                    <div class="col-xl-3 mb-2">
                                        <label for="columnDefault" class="form-label">Default</label>
                                        <input type="text" class="form-control" id="columnDefault"
                                            wire:model="columnDefault" value="{{ $columnDefault }}">
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <button class="btn btn-primary ms-auto" type="submit">
                                        <span class="ti ti-plus me-2"></span>
                                        Add
                                    </button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-centered table-sm table-striped nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>name</th>
                                            <th>type</th>
                                            <th>nullable</th>
                                            <th>default</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($migrationRows as $migrationRow)
                                            @php
                                                if (strpos($migrationRow, "->default('')") !== false) {
                                                    $migrationRow = str_replace("->default('')", '', $migrationRow);
                                                }
                                                if (strpos($migrationRow, '->nullable()') !== false) {
                                                    $migrationRow = str_replace('->nullable()', '', $migrationRow);
                                                    $nullable = 'true';
                                                } else {
                                                    $nullable = 'false';
                                                }

                                                preg_match(
                                                    "/->(\w+)\('(\w+)'\)(->nullable\(\))?(->default\('(.*?)'\))?;/",
                                                    $migrationRow,
                                                    $matches,
                                                );

                                                $migrationRow = [
                                                    'name' => $matches[2],
                                                    'type' => $matches[1],
                                                    'default' => $matches[5] ?? '',
                                                ];

                                            @endphp
                                            <tr class="align-middle">
                                                <td class="ps-2">
                                                    {{ $migrationRow['name'] }}
                                                </td>
                                                <td>
                                                    {{ $migrationRow['type'] }}
                                                </td>
                                                <td>
                                                    {{ $nullable }}
                                                </td>
                                                <td>
                                                    {{ $migrationRow['default'] }}
                                                </td>
                                                <td class="text-end pe-2">
                                                    <button
                                                        wire:click="deleteMigrationRow('{{ $migrationRow['name'] }}')"
                                                        class="btn btn-secondary py-1 px-2" type="button">
                                                        <span class="ti ti-trash me-2"></span>Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @else
                    <div>
                        <h3 class="mt-3">{{ $selectedTable }}</h3>
                        <div class="row my-3">
                            <div class="col-xl-6 mb-2">
                                <input type="text" class="form-control" wire:model.live="search"
                                    placeholder="Search">
                            </div>
                            <div class="col-xl-6 d-flex mb-2">
                                <button wire:click="hideTableColumns" class="btn btn-secondary ms-auto">Return
                                    to
                                    Tables</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table table-centered table-bordered table-sm table-striped nowrap w-100">
                                <thead>
                                    <tr>
                                        @foreach ($tableColumns as $column)
                                            <th class="ps-2">
                                                {{ $column['Field'] }}
                                                <br>
                                                <small>{{ $column['Type'] }}</small>
                                            </th>
                                        @endforeach
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($tableColumns as $column)
                                            @php
                                                $field = $column['Field'];
                                            @endphp
                                            <td class="ps-2">
                                                @php
                                                    $inputType = 'text';
                                                    if (strpos($column['Type'], 'int') !== false) {
                                                        $inputType = 'number';
                                                    }
                                                    if (strpos($column['Type'], 'date') !== false) {
                                                        $inputType = 'date';
                                                    }
                                                    if (strpos($column['Type'], 'time') !== false) {
                                                        $inputType = 'time';
                                                    }
                                                    if (strpos($column['Type'], 'color') !== false) {
                                                        $inputType = 'color';
                                                    }
                                                    if (strpos($column['Type'], 'text') !== false) {
                                                        $inputType = 'textarea';
                                                    }
                                                @endphp
                                                @if ($inputType == 'textarea')
                                                    <textarea class="form-control m-0" wire:model="newRow.{{ $field }}"></textarea>
                                                @else
                                                    <input type="{{ $inputType }}"
                                                        class="form-control m-0"
                                                        wire:model="newRow.{{ $field }}"
                                                        @if ($field == 'id' || $field == 'created_at' || $field == 'updated_at') disabled @endif>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-end pe-2">
                                            <button wire:click="createTableRow"
                                                class="btn btn-secondary py-1 px-2" type="button">
                                                <span class="ti ti-plus me-2"></span>Create
                                            </button>
                                        </td>
                                    </tr>
                                    @foreach ($tableData as $row)
                                        <tr>
                                            @foreach ($tableColumns as $column)
                                                @php
                                                    $field = $column['Field'];
                                                @endphp
                                                <td class="ps-2">{{ $row->$field }}</td>
                                            @endforeach
                                            <td class="text-end pe-2">
                                                <button wire:click="deleteTableRow('{{ $row->id }}')"
                                                    class="btn btn-secondary py-1 px-2" type="button">
                                                    <span class="ti ti-trash me-2"></span>Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mb-2">
                            {{ $tableData->onEachSide(0)->links() }}
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
