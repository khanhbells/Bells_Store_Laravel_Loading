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
        try {
            $database = $this->makeDatabase($request);
            $controller = $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $makeView = $this->makeView($request);
            if ($request->input('module_type') == 'catalogue') {
                $makeRule = $this->makeRule($request);
            }
            $makeRoute = $this->makeRoute($request);
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
            $module = $this->convertModuleNameToTableName($payload['name']);
            $moduleExtract = explode('_', $module);
            $this->makeMainTable($request, $module, $payload);
            if ($payload['module_type'] !== 'difference') {
                $this->makeLanguageTable($request, $module);
                if (count($moduleExtract) == 1) {
                    $this->makeRelationTable($request, $module);
                }
            }
            ARTISAN::call('migrate');
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage() . '-' . $e->getLine();
            die();
            return false;
        }
    }
    public function makeRelationTable($request, $module)
    {
        $moduleExtract = explode('_', $module);
        $tableName = $module . '_catalogue_' . $moduleExtract[0];
        $schema = $this->relationSchema($tableName, $module);
        $migrationRelationFile = $this->createMigrationFile($schema, $tableName);
        $migrationRelationFileName = date('Y_m_d_His', time() + 10) . '_create_' . $tableName . '_table.php';
        $migrationRelationPath = database_path('migrations\\' . $migrationRelationFileName);
        FILE::put($migrationRelationPath, $migrationRelationFile);
    }
    public function makeLanguageTable($request, $module)
    {
        $foreignKey = $module . '_id';
        $pivotTableName = $module . '_language';
        $pivotSchema = $this->pivotSchema($module);
        $dropPivotTable = $module . '_language';
        $migrationPivot = $this->createMigrationFile($pivotSchema, $dropPivotTable);
        $migrationPivotFileName = date('Y_m_d_His', time() + 10) . '_create_' . $pivotTableName . '_table.php';
        $migrationPivotPath = database_path('migrations\\' . $migrationPivotFileName);
        FILE::put($migrationPivotPath, $migrationPivot);
    }
    public function makeMainTable($request, $module, $payload)
    {

        $moduleExtract = explode('_', $module);
        $tableName = $module . 's';
        $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationPath = database_path('migrations\\' . $migrationFileName);
        $migrationTemplate = $this->createMigrationFile($payload['schema'], $tableName);
        FILE::put($migrationPath, $migrationTemplate);
    }
    public function relationSchema($tableName = '', $module = '')
    {
        $schema = <<<SCHEMA
    Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->unsignedBigInteger('{$module}_catalogue_id');
            \$table->unsignedBigInteger('{$module}_id');
            \$table->foreign('{$module}_catalogue_id')->references('id')->on('{$module}_catalogues')->onDelete('cascade');
            \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
        });
SCHEMA;
        return $schema;
    }
    private function pivotSchema($module)
    {
        $pivotSchema = <<<SCHEMA
Schema::create('{$module}_language', function (Blueprint \$table) {
            \$table->unsignedBigInteger('{$module}_id');
            \$table->unsignedBigInteger('language_id');
            \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
            \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->longText('content')->nullable();
            \$table->string('meta_title')->nullable();
            \$table->string('meta_keyword')->nullable();
            \$table->text('meta_description')->nullable();
            \$table->string('canonical')->nullable();
            \$table->timestamps();
        });
SCHEMA;
        return $pivotSchema;
    }
    private function createMigrationFile($schema, $dropTable = '')
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
        {$schema}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$dropTable}');
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
                case 'catalogue':
                    $this->createTemplateController($payload['name'], 'PostCatalogueController');
                    return true;
                case 'detail':
                    $this->createTemplateController($payload['name'], 'PostController');
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
        $module = $this->convertModuleNameToTableName($name);
        $controllerName = $name . 'Controller.php';
        $templateControllerPath = base_path('app\\Template\\controllers\\' . $controllerFile . '.php');
        $controllerContent = file_get_contents($templateControllerPath);
        $extractModule = explode('_', $module);
        $replace = [
            'class' => ucfirst($extractModule[0]),
            'module' => $extractModule[0]
        ];
        foreach ($replace as $key => $val) {
            $controllerContent = str_replace('{' . $key . '}', $replace[$key], $controllerContent);
        }
        $controllerPath = base_path('app\\Http\\Controllers\\Backend\\' . $controllerName);
        FILE::put($controllerPath, $controllerContent);
        return true;
    }
    //Khoi tao model
    private function makeModel($request)
    {
        $payload = $request->only('name', 'module_type');
        switch ($payload['module_type']) {
            case 'catalogue':
                $this->createModelTemplate($payload['name'], 'PostCatalogue');
                return true;
            case 'detail':
                $modelDetail = $this->createModelTemplate($payload['name'], 'Post');
                $postCatalogueLanguage = $payload['name'] . 'CatalogueLanguage';
                $modelDetailLanguage = $this->createModelTemplate($postCatalogueLanguage, 'PostCatalogueLanguage');
                return true;
            default:
                echo 123;
                die();
        }
    }
    public function createModelTemplate($name, $modelFile)
    {
        try {
            $modelName = $name . '.php';
            $templateModelPath = base_path('app\\Template\\models\\' . $modelFile . '.php');
            $modelContent = file_get_contents($templateModelPath);
            $module = $this->convertModuleNameToTableName($name);
            $extractModule = explode('_', $module);
            $replace = [
                'class' => ucfirst($extractModule[0]),
                'module' => $extractModule[0]
            ];
            foreach ($replace as $key => $val) {
                $modelContent = str_replace('{' . $key . '}', $replace[$key], $modelContent);
            }
            $this->createModelFile($modelName, $modelContent);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function createModelFile($modelName, $modelContent)
    {

        $modelPath = base_path('app\\Models\\' . $modelName);
        FILE::put($modelPath, $modelContent);
    }
    //Khoi tao repository
    public function makeRepository($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            $module = $this->convertModuleNameToTableName($payload['name']);
            $extractModule = explode('_', $module);
            $optionCatalogue = [
                'RepositoryInterface' => 'PostCatalogueRepositoryInterface',
                'Repository' => 'PostCatalogueRepository'
            ];
            $optionDetail = [
                'RepositoryInterface' => 'PostRepositoryInterface',
                'Repository' => 'PostRepository'
            ];
            switch ($payload['module_type']) {
                case 'catalogue':
                    $repository = $this->initializeServiceLayer($payload['name'], $optionCatalogue, 'Repository', 'Repositories', 'repositories');
                    $layerContentRepository = $this->layerContentAll($repository);
                    $replace = $this->replaceAll($extractModule);
                    $content = $this->allContent($layerContentRepository['allInterfaceContent'], $layerContentRepository['allContent'], $replace);
                    if (!FILE::exists($repository['layerInterfacePath']) && !FILE::exists($repository['layerPathPut'])) {
                        FILE::put($repository['layerInterfacePath'], $content['allInterfaceContent']);
                        FILE::put($repository['layerPathPut'], $content['allContent']);
                    }
                    return true;
                case 'detail':
                    $repository = $this->initializeServiceLayer($payload['name'], $optionDetail, 'Repository', 'Repositories', 'repositories');
                    $layerContentRepository = $this->layerContentAll($repository);
                    $replace = $this->replaceAll($extractModule);
                    $content = $this->allContent($layerContentRepository['allInterfaceContent'], $layerContentRepository['allContent'], $replace);
                    if (!FILE::exists($repository['layerInterfacePath']) && !FILE::exists($repository['layerPathPut'])) {
                        FILE::put($repository['layerInterfacePath'], $content['allInterfaceContent']);
                        FILE::put($repository['layerPathPut'], $content['allContent']);
                    }
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
    //Khoi tao Service
    public function makeService($request)
    {
        try {
            $payload = $request->only('name', 'module_type');
            $module = $this->convertModuleNameToTableName($payload['name']);
            $extractModule = explode('_', $module);
            $optionCatalogue = [
                'ServiceInterface' => 'PostCatalogueServiceInterface',
                'Service' => 'PostCatalogueService'
            ];
            $optionDetail = [
                'ServiceInterface' => 'PostServiceInterface',
                'Service' => 'PostService'
            ];
            switch ($payload['module_type']) {
                case 'catalogue':
                    $service = $this->initializeServiceLayer($payload['name'], $optionCatalogue, 'Service', 'Services', 'services');
                    $layerContentService = $this->layerContentAll($service);
                    $replace = $this->replaceAll($extractModule);
                    $content = $this->allContent($layerContentService['allInterfaceContent'], $layerContentService['allContent'], $replace);
                    if (!FILE::exists($service['layerInterfacePath']) && !FILE::exists($service['layerPathPut'])) {
                        FILE::put($service['layerInterfacePath'], $content['allInterfaceContent']);
                        FILE::put($service['layerPathPut'], $content['allContent']);
                    }
                    return true;
                case 'detail':
                    $service = $this->initializeServiceLayer($payload['name'], $optionDetail, 'Service', 'Services', 'services');
                    $layerContentService = $this->layerContentAll($service);
                    $replace = $this->replaceAll($extractModule);
                    $content = $this->allContent($layerContentService['allInterfaceContent'], $layerContentService['allContent'], $replace);
                    if (!FILE::exists($service['layerInterfacePath']) && !FILE::exists($service['layerPathPut'])) {
                        FILE::put($service['layerInterfacePath'], $content['allInterfaceContent']);
                        FILE::put($service['layerPathPut'], $content['allContent']);
                    }
                    return true;
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
    // PHUONG THUC CHUNG CHO CA REPOSITORY VA SERVICES
    public function replaceAll($extractModule)
    {
        $replace = [
            'class' => ucfirst($extractModule[0]),
            'module' => $extractModule[0]
        ];
        return $replace;
    }
    public function allContent($allInterfaceContent, $allContent, $replace)
    {
        $allInterfaceContent = str_replace('{class}', $replace['class'], $allInterfaceContent);
        foreach ($replace as $key => $val) {
            $allContent = str_replace('{' . $key . '}', $replace[$key], $allContent);
        }
        return [
            'allInterfaceContent' => $allInterfaceContent,
            'allContent' => $allContent
        ];
    }
    public function layerContentAll($all)
    {
        $allInterfaceContent = $all['layerInterfaceContent'];
        $allContent = $all['layerContent'];
        return [
            'allInterfaceContent' => $allInterfaceContent,
            'allContent' => $allContent
        ];
    }
    public function initializeServiceLayer($name, $layerFile = [], $layer = '', $folder = '', $file = '')
    {
        try {
            $layerInterfaceName = $name . $layer . 'Interface.php';
            $layerName = $name . $layer . '.php';
            $layerInterfaceRead = base_path('app\\Template\\' . $file . '\\' . $layerFile[$layer . 'Interface'] . '.php');
            $layerPathRead = base_path('app\\Template\\' . $file . '\\' . $layerFile[$layer] . '.php');
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
    // ----------------------------------------------------------------------------


    //Khoi tao provider
    public function makeProvider($request)
    {
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
        return true;
    }
    //Khoi tao request
    public function makeRequest($request)
    {
        // dd($request);
        $name = $request->input('name');
        $requestArray = ['Store' . $name . 'Request', 'Update' . $name . 'Request', 'Delete' . $name . 'Request'];
        $requestTemplate = ['RequestTemplateStore', 'RequestTemplateUpdate', 'RequestTemplateDelete'];
        if ($request->input('module_type') != 'catalogue') {
            unset($requestArray[2]);
            unset($requestTemplate[2]);
        }
        foreach ($requestTemplate as $key => $val) {
            $requestPath = base_path('app/Template/requests/' . $val . '.php');
            $requestContent = file_get_contents($requestPath);
            $requestContent = str_replace('{Module}', $name, $requestContent);
            $requestPut = base_path('app/Http/Requests/' . $requestArray[$key] . '.php');
            FILE::put($requestPut, $requestContent);
        }
        return true;
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
