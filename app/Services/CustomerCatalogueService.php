<?php

namespace App\Services;

use App\Services\Interfaces\CustomerCatalogueServiceInterface;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class CustomerCatalogueService
 * @package App\Services
 */
class CustomerCatalogueService implements CustomerCatalogueServiceInterface
{
    protected $CustomerCatalogueRepository;
    protected $CustomerRepository;
    public function __construct(CustomerCatalogueRepository $CustomerCatalogueRepository, CustomerRepository $CustomerRepository)
    {
        $this->CustomerCatalogueRepository = $CustomerCatalogueRepository;
        $this->CustomerRepository = $CustomerRepository;
    }

    public function paginate($request)
    {
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1)
        ];
        $perPage = $request->integer('perpage');
        // dd($condition);
        $customerCatalogues = $this->CustomerCatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'customer/catalogue/index'],
            ['id', 'DESC'],
            [],
            ['customers']
        );
        // dd($customerCatalogues);
        return $customerCatalogues;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $customer = $this->CustomerCatalogueRepository->create($payload);
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
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $customer = $this->CustomerCatalogueRepository->update($id, $payload);
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
            $customer = $this->CustomerCatalogueRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function setPermission($request)
    {
        DB::beginTransaction();
        try {
            $permissions = $request->input('permission', []); // Nếu không có quyền nào được gửi lên, trả về mảng rỗng
            //$permissions[1]
            // Duyệt qua tất cả các nhóm customerCatalogue có trong hệ thống
            $customerCatalogues = $this->CustomerCatalogueRepository->all();
            foreach ($customerCatalogues as $customerCatalogue) {
                if (isset($permissions[$customerCatalogue->id])) {
                    // Nếu có quyền nào đó được chọn cho customerCatalogue, đồng bộ chúng
                    $customerCatalogue->permissions()->sync($permissions[$customerCatalogue->id]);
                } else {
                    // Nếu không có quyền nào được chọn (hoặc tất cả đã bị bỏ tích), xóa hết các quyền liên kết
                    $customerCatalogue->permissions()->detach();
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $customer = $this->CustomerCatalogueRepository->update($post['modelId'], $payload);
            $this->changeCustomerStatus($post, $payload[$post['field']]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $flag = $this->CustomerCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            $this->changeCustomerStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function changeCustomerStatus($post, $value)
    {
        DB::beginTransaction();
        try {
            $array = [];
            if (isset($post['modelId'])) {
                $array[] = $post['modelId'];
            } else {
                $array = $post['id'];
            }
            $payload[$post['field']] = $value;
            $this->CustomerRepository->updateByWhereIn('customer_catalogue_id', $array, $payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function paginateselect()
    {
        return ['id', 'name', 'description', 'publish'];
    }
}
