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
            $controller = $this->makeController($request);
            // $this->makeModel();
            // $this->makeRepository();
            // $this->makeService();
            // $this->makeProvider();
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
            $controllerContent = str_replace('{ModuleTemplate}', $replace['ModuleTemplate'], $controllerContent);
            $controllerContent = str_replace('{moduleTemplate}', $replace['moduleTemplate'], $controllerContent);
            $controllerContent = str_replace('{foreignKey}', $replace['foreignKey'], $controllerContent);
            $controllerContent = str_replace('{moduleView}', $replace['moduleView'], $controllerContent);
            $controllerContent = str_replace('{tableName}', $replace['tableName'], $controllerContent);
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
