<?php

namespace App\Services;

use App\Services\Interfaces\GenerateServiceInterface;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PhpParser\Node\Stmt\Return_;

/**
 * Class GenerateService
 * @package App\Services
 */
class GenerateService extends BaseService implements GenerateServiceInterface
{
    protected $generateRepository;
    public function __construct(GenerateRepository $generateRepository)
    {
        $this->generateRepository = $generateRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $perPage = $request->integer('perpage');
        // dd($condition);
        $generates = $this->generateRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'generate/index'],
        );
        // dd($generates);
        return $generates;
    }
    private function paginateselect()
    {
        return ['id', 'name', 'schema'];
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $database = $this->makeDatabase($request);
            $controller = $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $makeView = $this->makeView($request);
            if ($request->input('module_type') == 1) {
                $makeRule = $this->makeRule($request);
            }
            $makeRoute = $this->makeRoute($request);
            die();



            // $this->makeLang();
            $payload = $request->except(['_token', 'send']);
            $payload['user_id'] = Auth::id();
            $generate = $this->generateRepository->create($payload);
            // dd($payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }


    //khoi tao migration
    private function makeDatabase($request)
    {
        try {
            $payload = $request->only('schema', 'name', 'module_type');
            $tableName = $this->convertModuleNameToTableName($payload['name']) . 's';
            $payload['name'] = $tableName;
            $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
            $migrationPath = database_path('migrations\\' . $migrationFileName);
            $migrationTemplate = $this->createMigrationFile($payload);
            FILE::put($migrationPath, $migrationTemplate);
            if ($payload['module_type'] !== 3) {
                $foreignKey = $this->convertModuleNameToTableName($payload['name']) . '_id';
                $pivotTableName = $this->convertModuleNameToTableName($payload['name']) . '_language';
                $pivotSchema = $this->pivotSchema($pivotTableName, $tableName, $foreignKey);
                $migrationPivotTemplate = $this->createMigrationFile([
                    'schema' => $pivotSchema,
                    'name' => $pivotTableName,
                ]);
                $migrationPivotFileName = date('Y_m_d_His', time() + 10) . '_create_' . $pivotTableName . '_table.php';
                $migrationPivotPath = database_path('migrations\\' . $migrationPivotFileName);
                FILE::put($migrationPivotPath, $migrationPivotTemplate);
            }
            ARTISAN::call('migrate');
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function pivotSchema($createTable = '', $tableName = '', $foreignKey = '')
    {
        $pivotSchema = <<<SCHEMA
Schema::create('{$createTable}', function (Blueprint \$table) {
            \$table->unsignedBigInteger('{$foreignKey}');
            \$table->unsignedBigInteger('language_id');
            \$table->foreign('{$foreignKey}')->references('id')->on('{$tableName}')->onDelete('cascade');
            \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            \$table->string('name');
            \$table->text('description');
            \$table->longText('content');
            \$table->string('meta_title');
            \$table->string('meta_keyword');
            \$table->text('meta_description');
        });
SCHEMA;
        return $pivotSchema;
    }
    private function createMigrationFile($payload)
    {

        $migrationTemplate = <<<MIGRATION
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {$payload['schema']}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$payload['name']}');
    }
};
MIGRATION;
        return $migrationTemplate;
    }
    private function convertModuleNameToTableName($name)
    {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $temp;
    }


    //Khoi tao controller
    private function makeController($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            switch ($payload['module_type']) {
                case 1:
                    $this->createTemplateController($payload['name'], 'TemplateCatalogueController');
                    return true;
                case 2:
                    $this->createTemplateController($payload['name'], 'TemplateController');
                    return true;
                default:
                    // $this->createSingleController();
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function createTemplateController($name, $controllerFile)
    {
        try {
            $controllerName = $name . 'Controller.php';
            $templateControllerPath = base_path('app\\Template\\' . $controllerFile . '.php');
            $controllerContent = file_get_contents($templateControllerPath);
            $replace = [
                'ModuleTemplate' => $name,
                'moduleTemplate' => lcfirst($name),
                'foreignKey' => $this->convertModuleNameToTableName($name) . '_id',
                'tableName' => $this->convertModuleNameToTableName($name) . 's',
                'moduleView' => str_replace('_', '.', $this->convertModuleNameToTableName($name))
            ];
            foreach ($replace as $key => $val) {
                $controllerContent = str_replace('{' . $key . '}', $replace[$key], $controllerContent);
            }
            $controllerPath = base_path('app\\Http\\Controllers\\Backend\\' . $controllerName);
            FILE::put($controllerPath, $controllerContent);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    //Khoi tao model
    private function makeModel($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            switch ($payload['module_type']) {
                case 1:
                    $this->createModelTemplate($payload['name'], 'TemplateCatalogueModel');
                    return true;
                case 2:
                    $this->createModelTemplate($payload['name'], 'TemplateModel');
                    break;
                default:
                    // $this->createSingleController();
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function createModelTemplate($name, $modelFile)
    {
        try {
            $modelName = $name . '.php';
            $templateModelPath = base_path('app\\Template\\' . $modelFile . '.php');
            $modelContent = file_get_contents($templateModelPath);
            $module = $this->convertModuleNameToTableName($name);
            $extractModule = explode('_', $module);
            $replace = [
                'ModuleTemplate' => $name,
                'foreignKey' => $module . '_id',
                'tableName' => $module . 's',
                'relation' => $extractModule[0],
                'pivotModel' => $name . 'Language',
                'relationPivot' => $module . '_' . $extractModule[0],
                'pivotTable' => $module . '_language',
                'module' => $module,
                'relationModel' => ucfirst($extractModule[0])
            ];
            foreach ($replace as $key => $val) {
                $modelContent = str_replace('{' . $key . '}', $replace[$key], $modelContent);
            }
            $modelPath = base_path('app\\Models\\' . $modelName);
            FILE::put($modelPath, $modelContent);
            die();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    //Khoi tao repository
    public function makeRepository($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            $option = [
                'RepositoryInterface' => 'TemplateRepositoryInterface',
                'Repository' => 'TemplateRepository'
            ];
            switch ($payload['module_type']) {
                case 1:
                    $repository = $this->initializeServiceLayer($payload['name'], $option, 'Repository', 'Repositories');
                    $repositoryInterfaceContent = $repository['layerInterfaceContent'];
                    $repositoryContent = $repository['layerContent'];
                    $module = $this->convertModuleNameToTableName($payload['name']);
                    $replace = [
                        'Module' => $payload['name'],
                        'tableName' => $module . 's',
                        'pivotTableName' => $module . '_language',
                        'foreignKey' => $module . '_id'
                    ];
                    $repositoryInterfaceContent = str_replace('{Module}', $replace['Module'], $repositoryInterfaceContent);
                    foreach ($replace as $key => $val) {
                        $repositoryContent = str_replace('{' . $key . '}', $replace[$key], $repositoryContent);
                    }
                    FILE::put($repository['layerInterfacePath'], $repositoryInterfaceContent);
                    FILE::put($repository['layerPathPut'], $repositoryContent);
                    return true;
                    break;
                case 2:
                    echo 123;
                    // $this->createRepositoryTemplate($payload['name'], 'TemplateModel');
                    break;
                default:
                    // $this->createSingleController();
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    //Khoi tao Service
    public function makeService($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            $option = [
                'ServiceInterface' => 'TemplateServiceInterface',
                'Service' => 'TemplateService'
            ];
            switch ($payload['module_type']) {
                case 1:
                    $service = $this->initializeServiceLayer($payload['name'], $option, 'Service', 'Services');
                    // dd($service);
                    $serviceInterfaceContent = $service['layerInterfaceContent'];
                    $serviceContent = $service['layerContent'];
                    $module = $this->convertModuleNameToTableName($payload['name']);
                    $replace = [
                        'Module' => $payload['name'],
                        'tableName' => $module . 's',
                        'pivotTableName' => $module . '_language',
                        'foreignKey' => $module . '_id',
                        'module' => lcfirst($payload['name']),
                        'moduleView' => str_replace('_', '.', $module)
                    ];
                    $serviceInterfaceContent = str_replace('{Module}', $replace['Module'], $serviceInterfaceContent);
                    foreach ($replace as $key => $val) {
                        $serviceContent = str_replace('{' . $key . '}', $replace[$key], $serviceContent);
                    }
                    FILE::put($service['layerInterfacePath'], $serviceInterfaceContent);
                    FILE::put($service['layerPathPut'], $serviceContent);
                    return true;
                    break;
                case 2:
                    echo 123;
                    // $this->createRepositoryTemplate($payload['name'], 'TemplateModel');
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function initializeServiceLayer($name, $layerFile = [], $layer = '', $folder = '')
    {
        try {
            $layerInterfaceName = $name . $layer . 'Interface.php';
            $layerName = $name . $layer . '.php';
            $layerInterfaceRead = base_path('app\\Template\\' . $layerFile[$layer . 'Interface'] . '.php');
            $layerPathRead = base_path('app\\Template\\' . $layerFile[$layer] . '.php');
            $layerInterfaceContent = file_get_contents($layerInterfaceRead);
            $layerContent = file_get_contents($layerPathRead);
            $layerInterfacePath = base_path('app\\' . $folder . '\\Interfaces\\' . $layerInterfaceName);
            $layerPathPut = base_path('app\\' . $folder . '\\' . $layerName);
            return [
                'layerInterfaceContent' => $layerInterfaceContent,
                'layerInterfacePath' => $layerInterfacePath,
                'layerContent' => $layerContent,
                'layerPathPut' => $layerPathPut
            ];
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    //Khoi tao provider
    public function makeProvider($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            $name = $payload['name'];
            $provider = [
                'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
                'repositoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php')
            ];
            foreach ($provider as $key => $val) {
                $content = file_get_contents($val);
                $insertLine = ($key == 'providerPath') ? "'App\\Services\\Interfaces\\{$name}ServiceInterface' =>
        'App\\Services\\{$name}Service'," : "'App\\Repositories\\Interfaces\\{$name}RepositoryInterface' =>
        'App\\Repositories\\{$name}Repository',";
                $position = strpos($content, '];');
                if ($position !== false) {
                    $newContent = substr_replace($content, '    ' . "//$name" . "\n" . "        " . $insertLine . "\n" . "    ", $position, 0);
                }
                FILE::put($val, $newContent);
            }
            die();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    //Khoi tao request
    public function makeRequest($request)
    {
        try {
            // dd($request);
            $name = $request->input('name');
            $requestArray = ['Store' . $name . 'Request', 'Update' . $name . 'Request', 'Delete' . $name . 'Request'];
            $requestTemplate = ['RequestTemplateStore', 'RequestTemplateUpdate', 'RequestTemplateDelete'];
            if ($request->input('module_type') != 1) {
                unset($requestArray[2]);
                unset($requestTemplate[2]);
            }
            foreach ($requestTemplate as $key => $val) {
                $requestPath = base_path('app/Template/' . $val . '.php');
                $requestContent = file_get_contents($requestPath);
                $requestContent = str_replace('{Module}', $name, $requestContent);
                $requestPut = base_path('app/Http/Requests/' . $requestArray[$key] . '.php');
                FILE::put($requestPut, $requestContent);
            }
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    //Khoi tao view
    public function makeView($request)
    {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $extractModule = explode('_', $module);
            $basePath = resource_path("views/backend/{$extractModule[0]}");
            $folderPath = (count($extractModule) == 2) ? "$basePath/{$extractModule[1]}" : "$basePath/{$extractModule[0]}";
            $componentPath = "$folderPath/component";
            $this->createDirectory($folderPath);
            $this->createDirectory($componentPath);
            $sourcePath = base_path('app/Template/views/' . ((count($extractModule) == 2) ? 'catalogue' : 'post') . '/');
            $viewPath = (count($extractModule) == 2) ? "{$extractModule[0]}.{$extractModule[1]}" : $extractModule[0];
            $replacement = [
                'view' => $viewPath,
                'module' => lcfirst($name),
                'Module' => $name
            ];
            $fileArray = ['store.blade.php', 'index.blade.php', 'delete.blade.php'];
            $componentFile = ['aside.blade.php', 'filter.blade.php', 'table.blade.php'];
            $this->CopAndReplaceContent($sourcePath, $folderPath, $fileArray, $replacement);
            $this->CopAndReplaceContent("{$sourcePath}component/", $componentPath, $componentFile, $replacement);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function createDirectory($path)
    {
        if (!FILE::exists($path)) {
            FILE::makeDirectory($path, 0755, true);
        }
    }
    public function CopAndReplaceContent(string $sourcePath, string $destinationPath, array $fileArray, array $replacement)
    {
        foreach ($fileArray as $key => $val) {
            $sourceFile = $sourcePath . $val;
            $content = file_get_contents($sourceFile);
            $destination = "{$destinationPath}/{$fileArray[$key]}";
            foreach ($replacement as $keyReplace => $valReplace) {
                $content = str_replace('{' . $keyReplace . '}', $valReplace, $content);
            }
            if (!FILE::exists($destination)) {
                FILE::put($destination, $content);
            }
        }
    }
    //Khoi tao rule
    public function makeRule($request)
    {
        $name = $request->input('name');
        $destination = base_path('app/Rules/Check' . $name . 'ChildrenRule.php');
        $ruleTemplate = base_path('app/Template/RuleTemplate.php');
        $content = file_get_contents($ruleTemplate);
        $content = str_replace('{Module}', $name, $content);
        if (!FILE::exists($destination)) {
            FILE::put($destination, $content);
        }
        return true;
    }
    // Khoi tao route
    public function makeRoute($request)
    {
        $name = $request->input('name');
        $module = $this->convertModuleNameToTableName($name);
        $moduleExtract = explode('_', $module);
        $routesPath = base_path('routes/web.php');
        $content = file_get_contents($routesPath);
        $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/{$moduleExtract[1]}" : $moduleExtract[0];
        $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.{$moduleExtract[1]}" : $moduleExtract[0];
        $routeGroup = <<<ROUTE
        Route::group(['prefix' => '{$routeUrl}'], function () {
                Route::get('index', [{$name}Controller::class, 'index'])->name('{$routeName}.index');
                Route::get('create', [{$name}Controller::class, 'create'])->name('{$routeName}.create');
                Route::post('store', [{$name}Controller::class, 'store'])->name('{$routeName}.store');
                Route::get('{id}/edit', [{$name}Controller::class, 'edit'])->where(['id' => '[0-9]+'])->name('{$routeName}.edit');
                Route::post('{id}/update', [{$name}Controller::class, 'update'])->where(['id' => '[0-9]+'])->name('{$routeName}.update');
                Route::get('{id}/delete', [{$name}Controller::class, 'delete'])->where(['id' => '[0-9]+'])->name('{$routeName}.delete');
                Route::post('{id}/destroy', [{$name}Controller::class, 'destroy'])->where(['id' => '[0-9]+'])->name('{$routeName}.destroy');
            });
            //NEW MODULE
        ROUTE;
        $useController = <<<ROUTE
        use App\Http\Controllers\Backend\\{$name}Controller;
        //USE CONTROLLER
        ROUTE;
        $content = str_replace('//NEW MODULE', $routeGroup, $content);
        $content = str_replace('//USE CONTROLLER', $useController, $content);
        FILE::put($routesPath, $content);
        return true;
    }







    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $generate = $this->generateRepository->update($id, $payload);
            // dd($payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $generate = $this->generateRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
}
