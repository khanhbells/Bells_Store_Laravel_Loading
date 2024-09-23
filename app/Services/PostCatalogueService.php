<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $nestedset;
    protected $language;
    public function __construct(PostCatalogueRepository $postCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->language = $this->currentLanguage();
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $condition['where'] = [
            ['tb2.language_id', '=', $this->language]
        ];
        $perPage = $request->integer('perpage');
        // dd($condition);
        $postCatalogues = $this->postCatalogueRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'post/catalogue/index'],
            [
                'post_catalogues.lft',
                'ASC'
            ],
            [
                [
                    'post_catalogue_language as tb2',
                    'tb2.post_catalogue_id',
                    '=',
                    'post_catalogues.id'
                ]
            ],
        );
        return $postCatalogues;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only($this->payload());
            $payload['user_id'] = Auth::id();
            // Loại bỏ phần '/laravelversion1.com' khỏi đường dẫn của ảnh
            if (isset($payload['image'])) {
                $payload['image'] = str_replace('/laravelversion1.com/public', '', $payload['image']);
            }

            // Tạo bản ghi
            $postCatalogue = $this->postCatalogueRepository->create($payload);
            if ($postCatalogue->id > 0) {
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                // dd($payloadLanguage);
                $payloadLanguage['language_id'] = $this->language;
                $payloadLanguage['post_catalogue_id'] = $postCatalogue->id;

                $language = $this->postCatalogueRepository->createLanguagePivot($postCatalogue, $payloadLanguage);
                // dd($language);
            }
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Ghi lỗi vào log
            Log::error($e->getMessage());

            // Trả về mã lỗi 500
            abort(500, 'Đã xảy ra lỗi trong quá trình tạo bản ghi.');
            return false;
        }
    }
    // --------------------------------------------------------------------------------
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            $payload = $request->only($this->payload());
            // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'
            if (isset($payload['image'])) {
                if (strpos($payload['image'], 'http://localhost:81/laravelversion1.com/public') !== false) {
                    // Nếu ảnh có URL đầy đủ (khi người dùng không sửa ảnh)
                    $payload['image'] = str_replace('http://localhost:81/laravelversion1.com/public', '', $payload['image']);
                } elseif (strpos($payload['image'], '/laravelversion1.com/public') !== false) {
                    // Nếu ảnh chỉ có tiền tố tương đối (khi người dùng sửa ảnh)
                    $payload['image'] = str_replace('/laravelversion1.com/public', '', $payload['image']);
                }
            }
            // dd($payload['image']); // Kiểm tra xem đường dẫn ảnh đã được chỉnh sửa đúng chưa
            // dd($payload);
            $flag = $this->postCatalogueRepository->update($id, $payload);

            if ($flag) {
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['language_id'] = $this->language;
                $payloadLanguage['post_catalogue_id'] = $id;
                $postCatalogue->languages()->detach([$payloadLanguage['language_id'], $id]);
                $reponse = $this->postCatalogueRepository->createLanguagePivot($postCatalogue, $payloadLanguage);
                $this->nestedset->Get('level ASC, order ASC');
                $this->nestedset->Recursive(0, $this->nestedset->Set());
                $this->nestedset->Action();
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->postCatalogueRepository->forceDelete($id);
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();
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
            $postCatalogue = $this->postCatalogueRepository->update($post['modelId'], $payload);
            // $this->changeUserStatus($post, $payload[$post['field']]);
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
            $flag = $this->postCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    // private function changeUserStatus($post, $value)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $array = [];
    //         if (isset($post['modelId'])) {
    //             $array[] = $post['modelId'];
    //         } else {
    //             $array = $post['id'];
    //         }
    //         $payload[$post['field']] = $value;
    //         $this->UserRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
    //         DB::commit();
    //         return true;
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         echo $e->getMessage();
    //         die();
    //         return false;
    //     }
    // }
    private function paginateselect()
    {
        return ['post_catalogues.id', 'post_catalogues.publish', 'post_catalogues.image', 'post_catalogues.level', 'post_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['parent_id', 'follow', 'publish', 'image'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
