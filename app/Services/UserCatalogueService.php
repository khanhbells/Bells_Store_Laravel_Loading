<?php

namespace App\Services;

use App\Services\Interfaces\UserCatalogueServiceInterface;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class UserCatalogueService implements UserCatalogueServiceInterface
{
    protected $UserCatalogueRepository;
    protected $UserRepository;
    public function __construct(UserCatalogueRepository $UserCatalogueRepository, UserRepository $UserRepository)
    {
        $this->UserCatalogueRepository = $UserCatalogueRepository;
        $this->UserRepository = $UserRepository;
    }

    public function paginate($request)
    {
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1)
        ];
        $perPage = $request->integer('perpage');
        // dd($condition);
        $userCatalogues = $this->UserCatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'user/catalogue/index'],
            ['id', 'DESC'],
            [],
            ['users']
        );
        // dd($userCatalogues);
        return $userCatalogues;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except(['_token', 'send']);
            $user = $this->UserCatalogueRepository->create($payload);
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
            $user = $this->UserCatalogueRepository->update($id, $payload);
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
            $user = $this->UserCatalogueRepository->forceDelete($id);
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
            // Duyệt qua tất cả các nhóm userCatalogue có trong hệ thống
            $userCatalogues = $this->UserCatalogueRepository->all();
            foreach ($userCatalogues as $userCatalogue) {
                if (isset($permissions[$userCatalogue->id])) {
                    // Nếu có quyền nào đó được chọn cho userCatalogue, đồng bộ chúng
                    $userCatalogue->permissions()->sync($permissions[$userCatalogue->id]);
                } else {
                    // Nếu không có quyền nào được chọn (hoặc tất cả đã bị bỏ tích), xóa hết các quyền liên kết
                    $userCatalogue->permissions()->detach();
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
            $user = $this->UserCatalogueRepository->update($post['modelId'], $payload);
            $this->changeUserStatus($post, $payload[$post['field']]);
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
            $flag = $this->UserCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            $this->changeUserStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function changeUserStatus($post, $value)
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
            $this->UserRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
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
