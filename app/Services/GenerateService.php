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
            // $database = $this->makeDatabase($request);
            // $controller = $this->makeController($request);
            // $model = $this->makeModel($request);
            // $repository = $this->makeRepository($request);
            // $service = $this->makeService($request);
            // $provider = $this->makeProvider($request);



            // $this->makeRequest();
            // $this->makeView();
            // $this->makeRoute();
            // $this->makeRule();
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
        $payload = $request->only('name', 'module_type');
        switch ($payload['module_type']) {
            case 1:
                $this->createTemplateController($payload['name'], 'TemplateCatalogueController');
                break;
            case 2:
                $this->createTemplateController($payload['name'], 'TemplateController');
                break;
            default:
                // $this->createSingleController();
                break;
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
        $payload = $request->only('name', 'module_type');
        switch ($payload['module_type']) {
            case 1:
                $this->createModelTemplate($payload['name'], 'TemplateCatalogueModel');
                break;
            case 2:
                $this->createModelTemplate($payload['name'], 'TemplateModel');
                break;
            default:
                // $this->createSingleController();
                break;
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
                die();
                break;
            case 2:
                echo 123;
                // $this->createRepositoryTemplate($payload['name'], 'TemplateModel');
                break;
            default:
                // $this->createSingleController();
                break;
        }
    }

    //Khoi tao Service
    public function makeService($request)
    {
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
                die();
                break;
            case 2:
                echo 123;
                // $this->createRepositoryTemplate($payload['name'], 'TemplateModel');
                break;
            default:
                break;
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
        $payload = $request->only('name', 'module_type');
        $name = $payload['name'];
        switch ($payload['module_type']) {
            case 1:
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
                break;
            case 2:
                echo 123;
                die();
                // $this->createRepositoryTemplate($payload['name'], 'TemplateModel');
                break;
            default:
                break;
        }
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
