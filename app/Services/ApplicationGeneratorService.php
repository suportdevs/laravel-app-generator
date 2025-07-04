<?php

namespace App\Services;

use App\Models\GenerateApplication;

class ApplicationGeneratorService
{
    public function generate(GenerateApplication $application)
    {
        // 1. Create directory structure
        $this->createDirectoryStructure($application);

        // 2. Generate config files
        $this->generateConfigFiles($application);

        // 3. Generate Models
        $this->generateModels($application);

        // 4. Generate Migrations
        $this->generateMigrations($application);

        // 5. Generate Controllers
        $this->generateControllers($application);

        // 6. Generate Views
        $this->generateViews($application);

        // 7. Generate Routes
        $this->generateRoutes($application);

        // 8. Zip the application
        return $this->zipApplication($application);
    }

    protected function createDirectoryStructure(GenerateApplication $application)
    {
        $basePath = storage_path('app/generated/'.$application->slug);

        $dirs = [
            '/app/Models',
            '/app/Http/Controllers',
            '/database/migrations',
            '/resources/views',
            '/routes',
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($basePath.$dir)) {
                mkdir($basePath.$dir, 0755, true);
            }
        }
    }

    protected function generateModels(GenerateApplication $application)
    {
        foreach ($application->entities as $entity) {
            $stub = file_get_contents(resource_path('stubs/model.stub'));

            $replacements = [
                '{{namespace}}' => 'App\Models',
                '{{class}}' => $entity->name,
                '{{table}}' => $entity->table_name,
                '{{fillable}}' => $this->generateFillable($entity),
                '{{casts}}' => $this->generateCasts($entity),
                '{{relationships}}' => $this->generateRelationships($entity),
            ];

            $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

            file_put_contents(
                storage_path('app/generated/'.$application->slug.'/app/Models/'.$entity->name.'.php'),
                $content
            );
        }
    }

    protected function generateConfigFiles(GenerateApplication $application)
    {
        // Generate basic app.php config
        $appConfig = [
            'name' => $application->name,
            'env' => 'local',
            'debug' => true,
            'url' => 'http://localhost',
            'timezone' => 'UTC',
            'locale' => 'en',
            'key' => 'base64:'.base64_encode(random_bytes(32)),
            'cipher' => 'AES-256-CBC',
        ];

        file_put_contents(
            storage_path('app/generated/'.$application->slug.'/config/app.php'),
            "<?php\n\nreturn ".var_export($appConfig, true).";\n"
        );

        // Generate database.php config
        $dbConfig = [
            'default' => 'mysql',
            'connections' => [
                'mysql' => [
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => strtolower(str_replace(' ', '_', $application->name)),
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]
            ]
        ];

        file_put_contents(
            storage_path('app/generated/'.$application->slug.'/config/database.php'),
            "<?php\n\nreturn ".var_export($dbConfig, true).";\n"
        );

        // Generate auth.php config if authentication is enabled
        if ($application->config['with_auth'] ?? false) {
            $authConfig = config('auth');
            file_put_contents(
                storage_path('app/generated/'.$application->slug.'/config/auth.php'),
                "<?php\n\nreturn ".var_export($authConfig, true).";\n"
            );
        }
    }

    protected function generateMigrations(GenerateApplication $application)
    {
        foreach ($application->entities as $entity) {
            $stub = file_get_contents(resource_path('stubs/migration.stub'));

            $replacements = [
                '{{table}}' => $entity->table_name,
                '{{fields}}' => $this->generateFields($entity),
            ];

            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            $migrationName = date('Y_m_d_His').'_create_'.$entity->table_name.'_table.php';

            file_put_contents(
                storage_path('app/generated/'.$application->slug.'/database/migrations/'.$migrationName),
                $content
            );
        }

        // Generate pivot tables for many-to-many relationships
        foreach ($application->relationships as $relationship) {
            if ($relationship['type'] === 'belongsToMany') {
                $table1 = $relationship['entity1']->table_name;
                $table2 = $relationship['entity2']->table_name;
                $pivotTable = $relationship['pivot_table'] ?? $this->generatePivotTableName($table1, $table2);

                $stub = file_get_contents(resource_path('stubs/migration.stub'));

                $fields = "\$table->foreignId('".str_singular($table1)."_id');\n";
                $fields .= "            \$table->foreignId('".str_singular($table2)."_id');\n";
                $fields .= "            \$table->primary(['".str_singular($table1)."_id', '".str_singular($table2)."_id']);";

                $replacements = [
                    '{{table}}' => $pivotTable,
                    '{{fields}}' => $fields,
                ];

                $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

                $migrationName = date('Y_m_d_His').'_create_'.$pivotTable.'_table.php';

                file_put_contents(
                    storage_path('app/generated/'.$application->slug.'/database/migrations/'.$migrationName),
                    $content
                );
            }
        }
    }

    protected function generateControllers(GenerateApplication $application)
    {
        $namespace = 'App\Http\Controllers';
        $routePrefix = $application->config['route_prefix'] ?? '';
        $viewPrefix = $application->config['view_prefix'] ?? '';

        foreach ($application->entities as $entity) {
            $stub = file_get_contents(resource_path('stubs/controller.stub'));

            $replacements = [
                '{{namespace}}' => $namespace,
                '{{class}}' => $entity->name.'Controller',
                '{{model}}' => $entity->name,
                '{{modelVariable}}' => strtolower($entity->name),
                '{{collection}}' => str_plural(strtolower($entity->name)),
                '{{validationRules}}' => $this->generateValidationRules($entity),
                '{{routePrefix}}' => $routePrefix ? $routePrefix.'.' : '',
                '{{viewPrefix}}' => $viewPrefix ? $viewPrefix.'.' : '',
            ];

            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            // Create controller directory if needed
            $controllerDir = storage_path('app/generated/'.$application->slug.'/app/Http/Controllers');
            if (!file_exists($controllerDir)) {
                mkdir($controllerDir, 0755, true);
            }

            file_put_contents(
                $controllerDir.'/'.$entity->name.'Controller.php',
                $content
            );
        }

        // Generate API controllers if enabled
        if ($application->config['with_api'] ?? false) {
            $this->generateApiControllers($application);
        }
    }

    protected function generatePivotTableName($table1, $table2)
    {
        $tables = [str_singular($table1), str_singular($table2)];
        sort($tables);
        return implode('_', $tables);
    }

    protected function generateApiControllers(GenerateApplication $application)
    {
        $namespace = 'App\Http\Controllers\Api';
        $routePrefix = $application->config['api_prefix'] ?? 'api';

        foreach ($application->entities as $entity) {
            $stub = file_get_contents(resource_path('stubs/api_controller.stub'));

            $replacements = [
                '{{namespace}}' => $namespace,
                '{{class}}' => $entity->name.'Controller',
                '{{model}}' => $entity->name,
                '{{modelVariable}}' => strtolower($entity->name),
            ];

            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            // Create API controller directory
            $apiControllerDir = storage_path('app/generated/'.$application->slug.'/app/Http/Controllers/Api');
            if (!file_exists($apiControllerDir)) {
                mkdir($apiControllerDir, 0755, true);
            }

            file_put_contents(
                $apiControllerDir.'/'.$entity->name.'Controller.php',
                $content
            );
        }
    }

    protected function generateRoutes(GenerateApplication $application)
    {
        // Generate web.php routes
        $webRoutes = "<?php\n\nuse Illuminate\Support\Facades\Route;\n";

        // Authentication routes if enabled
        if ($application->config['with_auth'] ?? false) {
            $webRoutes .= "Auth::routes();\n\n";
        }

        foreach ($application->entities as $entity) {
            $controller = $entity->name.'Controller';
            $routePrefix = $application->config['route_prefix'] ?? '';

            if ($routePrefix) {
                $webRoutes .= "Route::prefix('$routePrefix')->group(function() {\n";
                $webRoutes .= "    Route::resource('".strtolower($entity->name)."', $controller::class);\n";
                $webRoutes .= "});\n\n";
            } else {
                $webRoutes .= "Route::resource('".strtolower($entity->name)."', $controller::class);\n";
            }
        }

        // Home route
        $webRoutes .= "\nRoute::get('/', function() {\n";
        $webRoutes .= "    return view('welcome');\n";
        $webRoutes .= "});\n";

        file_put_contents(
            storage_path('app/generated/'.$application->slug.'/routes/web.php'),
            $webRoutes
        );

        // Generate api.php routes if API is enabled
        if ($application->config['with_api'] ?? false) {
            $apiRoutes = "<?php\n\nuse Illuminate\Support\Facades\Route;\n";

            foreach ($application->entities as $entity) {
                $controller = 'Api\\'.$entity->name.'Controller';
                $routePrefix = $application->config['api_prefix'] ?? 'api';

                $apiRoutes .= "Route::apiResource('$routePrefix/".strtolower($entity->name)."', $controller::class);\n";
            }

            file_put_contents(
                storage_path('app/generated/'.$application->slug.'/routes/api.php'),
                $apiRoutes
            );
        }
    }

    protected function generateViews(GenerateApplication $application)
{
    $viewPrefix = $application->config['view_prefix'] ?? '';
    $viewPath = $viewPrefix ? $viewPrefix.'/' : '';

    // Create base layout
    $this->generateLayoutViews($application);

    foreach ($application->entities as $entity) {
        $entityViewPath = storage_path('app/generated/'.$application->slug.'/resources/views/'.$viewPath.strtolower($entity->name));

        if (!file_exists($entityViewPath)) {
            mkdir($entityViewPath, 0755, true);
        }

        // Generate index view
        $indexStub = file_get_contents(resource_path('stubs/view_index.stub'));
        $indexContent = str_replace(
            [
                '{{model}}',
                '{{modelPlural}}',
                '{{modelVariable}}',
                '{{collection}}',
                '{{tableHeaders}}',
                '{{tableBody}}',
                '{{routePrefix}}',
            ],
            [
                $entity->name,
                str_plural($entity->name),
                strtolower($entity->name),
                str_plural(strtolower($entity->name)),
                $this->generateTableHeaders($entity),
                $this->generateTableBody($entity),
                $viewPrefix ? $viewPrefix.'.' : '',
            ],
            $indexStub
        );

        file_put_contents($entityViewPath.'/index.blade.php', $indexContent);

        // Generate form view (used by create and edit)
        $formStub = file_get_contents(resource_path('stubs/view_form.stub'));
        $formContent = str_replace(
            '{{formFields}}',
            $this->generateFormFields($entity),
            $formStub
        );

        file_put_contents($entityViewPath.'/create.blade.php', "@extends('layouts.app')\n\n@section('content')\n".$formContent."\n@endsection");
        file_put_contents($entityViewPath.'/edit.blade.php', "@extends('layouts.app')\n\n@section('content')\n".$formContent."\n@endsection");

        // Generate show view
        $showContent = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container\">\n    <h1>{{ \$".strtolower($entity->name)."->name }}</h1>\n    <div class=\"card\">\n        <div class=\"card-body\">\n            ".$this->generateShowFields($entity)."\n        </div>\n    </div>\n</div>\n@endsection";
        file_put_contents($entityViewPath.'/show.blade.php', $showContent);
    }
}

protected function generateTableHeaders($entity)
{
    $headers = '';
    foreach ($entity->fields as $field) {
        if (!in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $headers .= "<th>$label</th>\n                    ";
        }
    }
    return rtrim($headers);
}

protected function generateTableBody($entity)
{
    $body = '';
    foreach ($entity->fields as $field) {
        if (!in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
            $body .= "<td>{{ \$".strtolower($entity->name)."->".$field['name']." }}</td>\n                    ";
        }
    }
    return rtrim($body);
}

protected function generateShowFields($entity)
{
    $fields = '';
    foreach ($entity->fields as $field) {
        if (!in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $fields .= "<div class=\"row mb-3\">\n";
            $fields .= "    <div class=\"col-md-2 font-weight-bold\">$label</div>\n";
            $fields .= "    <div class=\"col-md-10\">{{ \$".strtolower($entity->name)."->".$field['name']." }}</div>\n";
            $fields .= "</div>\n";
        }
    }
    return $fields;
}

    protected function generateLayoutViews($application)
    {
        $layoutsPath = storage_path('app/generated/'.$application->slug.'/resources/views/layouts');
        if (!file_exists($layoutsPath)) {
            mkdir($layoutsPath, 0755, true);
        }

        // Create app.blade.php layout
        $appLayout = <<<'EOT'
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ config('app.name') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    EOT;
        file_put_contents($layoutsPath.'/app.blade.php', $appLayout);

        // Create welcome.blade.php
        $welcomeContent = <<<'EOT'
    @extends('layouts.app')

    @section('content')
        <div class="text-center">
            <h1>Welcome to {{ config('app.name') }}</h1>
            <p class="lead">Your application is ready!</p>
        </div>
    @endsection
    EOT;
        file_put_contents(storage_path('app/generated/'.$application->slug.'/resources/views/welcome.blade.php'), $welcomeContent);
    }

    // Implement similar methods for migrations, controllers, views etc.

    protected function zipApplication(GenerateApplication $application)
    {
        $zip = new \ZipArchive();
        $zipFile = storage_path('app/generated/'.$application->slug.'.zip');

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(storage_path('app/generated/'.$application->slug)),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $relativePath = substr($file->getRealPath(), strlen(storage_path('app/generated/'.$application->slug)) + 1);
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
            }

            $zip->close();
            return $zipFile;
        }

        return false;
    }


    protected function generateFillable($entity)
    {
        $fillable = [];
        foreach ($entity->fields as $field) {
            // Skip timestamps and auto-increment fields
            if (!in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
                $fillable[] = "'".$field['name']."'";
            }
        }

        return implode(",\n        ", $fillable);
    }

    protected function generateCasts($entity)
    {
        $casts = [];
        foreach ($entity->fields as $field) {
            switch ($field['type']) {
                case 'json':
                    $casts[$field['name']] = 'array';
                    break;
                case 'boolean':
                case 'bool':
                    $casts[$field['name']] = 'boolean';
                    break;
                case 'decimal':
                case 'double':
                    $casts[$field['name']] = 'decimal:'.($field['precision'] ?? 2);
                    break;
                case 'date':
                    $casts[$field['name']] = 'date';
                    break;
                case 'datetime':
                    $casts[$field['name']] = 'datetime';
                    break;
                case 'timestamp':
                    $casts[$field['name']] = 'timestamp';
                    break;
            }
        }

        $castStrings = [];
        foreach ($casts as $field => $type) {
            $castStrings[] = "'$field' => '$type'";
        }

        return implode(",\n        ", $castStrings);
    }

    protected function generateFields($entity)
    {
        $fields = [];

        foreach ($entity->fields as $field) {
            $fieldDefinition = '$table->';

            switch ($field['type']) {
                case 'string':
                    $fieldDefinition .= "string('{$field['name']}'";
                    if (isset($field['length']) && $field['length'] != 255) {
                        $fieldDefinition .= ", {$field['length']}";
                    }
                    break;
                case 'text':
                    $fieldDefinition .= "text('{$field['name']}'";
                    break;
                case 'integer':
                    $fieldDefinition .= "integer('{$field['name']}'";
                    break;
                case 'bigInteger':
                    $fieldDefinition .= "bigInteger('{$field['name']}'";
                    break;
                case 'decimal':
                    $precision = $field['precision'] ?? 8;
                    $scale = $field['scale'] ?? 2;
                    $fieldDefinition .= "decimal('{$field['name']}', $precision, $scale)";
                    break;
                case 'boolean':
                    $fieldDefinition .= "boolean('{$field['name']}'";
                    break;
                case 'date':
                    $fieldDefinition .= "date('{$field['name']}'";
                    break;
                case 'datetime':
                    $fieldDefinition .= "dateTime('{$field['name']}'";
                    break;
                case 'timestamp':
                    $fieldDefinition .= "timestamp('{$field['name']}'";
                    break;
                case 'json':
                    $fieldDefinition .= "json('{$field['name']}'";
                    break;
                case 'foreignId':
                    $fieldDefinition .= "foreignId('{$field['name']}'";
                    break;
                default:
                    $fieldDefinition .= "string('{$field['name']}'";
            }

            // Add nullable if needed
            if ($field['nullable'] ?? false) {
                $fieldDefinition .= '->nullable()';
            }

            // Add default value if specified
            if (isset($field['default'])) {
                $default = is_string($field['default'])
                    ? "'".addslashes($field['default'])."'"
                    : $field['default'];
                $fieldDefinition .= "->default($default)";
            }

            // Add index if needed
            if ($field['index'] ?? false) {
                $fieldDefinition .= '->index()';
            }

            // Add unique if needed
            if ($field['unique'] ?? false) {
                $fieldDefinition .= '->unique()';
            }

            $fieldDefinition .= ';';
            $fields[] = $fieldDefinition;
        }

        return implode("\n            ", $fields);
    }

    protected function generateFormFields($entity)
    {
        $fields = [];

        foreach ($entity->fields as $field) {
            if (in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $label = ucwords(str_replace('_', ' ', $field['name']));
            $fieldHtml = '';

            // Field opening
            $fieldHtml .= "<div class=\"form-group\">\n";
            $fieldHtml .= "    <label for=\"{$field['name']}\">$label</label>\n";

            // Field input
            switch ($field['type']) {
                case 'text':
                    $fieldHtml .= "    <textarea name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\">{{ old('{$field['name']}', \$".strtolower($entity->name)."->{$field['name']} ?? null) }}</textarea>\n";
                    break;
                case 'boolean':
                    $fieldHtml .= "    <div class=\"form-check\">\n";
                    $fieldHtml .= "        <input type=\"checkbox\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-check-input\" value=\"1\" {{ (old('{$field['name']}', \$".strtolower($entity->name)."->{$field['name']} ?? false) ? 'checked' : '' }}>\n";
                    $fieldHtml .= "        <label class=\"form-check-label\" for=\"{$field['name']}\">$label</label>\n";
                    $fieldHtml .= "    </div>\n";
                    break;
                case 'select':
                case 'enum':
                    $fieldHtml .= "    <select name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\">\n";
                    foreach ($field['options'] as $value => $label) {
                        $fieldHtml .= "        <option value=\"$value\" {{ (old('{$field['name']}', \$".strtolower($entity->name)."->{$field['name']} ?? null) == '$value' ? 'selected' : '' }}>$label</option>\n";
                    }
                    $fieldHtml .= "    </select>\n";
                    break;
                case 'date':
                    $fieldHtml .= "    <input type=\"date\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" value=\"{{ old('{$field['name']}', isset(\$".strtolower($entity->name).") ? \$".strtolower($entity->name)."->{$field['name']}->format('Y-m-d') : '') }}\">\n";
                    break;
                case 'datetime':
                    $fieldHtml .= "    <input type=\"datetime-local\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" value=\"{{ old('{$field['name']}', isset(\$".strtolower($entity->name).") ? \$".strtolower($entity->name)."->{$field['name']}->format('Y-m-d\TH:i') : '') }}\">\n";
                    break;
                default:
                    $inputType = in_array($field['type'], ['email', 'password', 'number']) ? $field['type'] : 'text';
                    $fieldHtml .= "    <input type=\"$inputType\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" value=\"{{ old('{$field['name']}', \$".strtolower($entity->name)."->{$field['name']} ?? null) }}\">\n";
            }

            // Field closing
            $fieldHtml .= "    @error('{$field['name']}')\n";
            $fieldHtml .= "        <div class=\"text-danger\">{{ \$message }}</div>\n";
            $fieldHtml .= "    @enderror\n";
            $fieldHtml .= "</div>\n";

            $fields[] = $fieldHtml;
        }

        return implode("\n", $fields);
    }

    protected function generateValidationRules($entity)
    {
        $rules = [];

        foreach ($entity->fields as $field) {
            if (in_array($field['name'], ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $fieldRules = [];

            // Type-based rules
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'integer':
                case 'bigInteger':
                    $fieldRules[] = 'integer';
                    break;
                case 'decimal':
                    $fieldRules[] = 'numeric';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date_format:Y-m-d H:i:s';
                    break;
                case 'json':
                    $fieldRules[] = 'json';
                    break;
            }

            // Required rule
            if (!($field['nullable'] ?? false)) {
                $fieldRules[] = 'required';
            }

            // String length rules
            if ($field['type'] === 'string' && isset($field['length'])) {
                $fieldRules[] = 'max:'.$field['length'];
            }

            // Unique rule
            if ($field['unique'] ?? false) {
                $uniqueRule = 'unique:'.$entity->table_name.','.$field['name'];
                if (isset($entity->primaryKey)) {
                    $uniqueRule .= ',\{\$'.$entity->name.'->'.$entity->primaryKey.'}';
                }
                $fieldRules[] = $uniqueRule;
            }

            // Add custom rules if specified
            if (isset($field['rules'])) {
                $fieldRules = array_merge($fieldRules, $field['rules']);
            }

            if (!empty($fieldRules)) {
                $rules[] = "'".$field['name']."' => '".implode('|', $fieldRules)."'";
            }
        }

        return implode(",\n            ", $rules);
    }
}
