<?php

namespace App\Services;

use App\Services\Interfaces\CustomerServiceInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerService implements CustomerServiceInterface
{
    protected $customerRepository;
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $customerCatalogue = $request->integer('customer_catalogue_id');
        if ($customerCatalogue != null) {
            $condition['where'] = [
                ['customer_catalogue_id', '=', $request->integer('customer_catalogue_id')],
            ];
        }
        $perPage = $request->integer('perpage');
        // dd($condition);
        $customers = $this->customerRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'customer/index'],
        );
        // dd($customers);
        return $customers;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {

            $payload = $request->except(['_token', 'send', 're_password']);
            dd($payload);
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            $payload['password'] = Hash::make($payload['password']);
            // dd($payload);
            $customer = $this->customerRepository->create($payload);

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
            // dd($payload);
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            $customer = $this->customerRepository->update($id, $payload);
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
            $customer = $this->customerRepository->forceDelete($id);
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
            $customer = $this->customerRepository->update($post['modelId'], $payload);
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
            $flag = $this->customerRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = '')
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        return $birthday;
    }
    private function paginateselect()
    {
        return [
            'id',
            'email',
            'phone',
            'address',
            'name',
            'publish',
            'customer_catalogue_id',
            'image',
            'source_id'
        ];
    }
}
