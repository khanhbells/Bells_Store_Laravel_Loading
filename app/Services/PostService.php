<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class PostService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    protected $language;
    public function __construct(PostRepository $postRepository)
    {
        $this->language = $this->currentLanguage();
        $this->postRepository = $postRepository;
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
        $posts = $this->postRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'post/catalogue/index'],
            [
                'posts.id',
                'DESC'
            ],
            [
                [
                    'post_language as tb2',
                    'tb2.post_id',
                    '=',
                    'posts.id'
                ]
            ],
        );
        return $posts;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only($this->payload());
            $payload['user_id'] = Auth::id();
            // Xử lý album - loại bỏ '/laravelversion1.com/public' khỏi các đường dẫn
            if (isset($payload['album'])) {
                $albumArray = $payload['album'];
                foreach ($albumArray as &$image) {
                    $image = str_replace('/laravelversion1.com/public', '', $image); // Loại bỏ tiền tố
                }
                $payload['album'] = json_encode($albumArray); // Mã hóa lại thành chuỗi JSON
            }

            // Loại bỏ phần '/laravelversion1.com' khỏi đường dẫn của ảnh chính (nếu có)
            if (isset($payload['image'])) {
                $payload['image'] = str_replace('/laravelversion1.com/public', '', $payload['image']);
            }
            // Tạo bản ghi
            $post = $this->postRepository->create($payload);
            if ($post->id > 0) {
                $payloadLanguage = $request->only($this->payloadLanguage());
                dd($payloadLanguage);
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->language;
                $payloadLanguage['post_id'] = $post->id;
                $language = $this->postRepository->createLanguagePivot($post, $payloadLanguage);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            // In ra lỗi để debug
            dd($e);
            // Ghi lỗi vào log
            Log::error($e->getMessage());
            // Trả về mã lỗi 500
            abort(500, 'Đã xảy ra lỗi trong quá trình tạo bản ghi.');
        }
    }
    // --------------------------------------------------------------------------------
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $post = $this->postRepository->findById($id);
            $payload = $request->only($this->payload());
            if (isset($payload['album'])) {
                $albumArray = $payload['album']; // Mảng chứa các ảnh
                foreach ($albumArray as &$image) {
                    // Kiểm tra và xử lý đường dẫn ảnh
                    if (strpos($image, 'http://localhost:81/laravelversion1.com/public') !== false) {
                        // Nếu ảnh có URL đầy đủ
                        $image = str_replace('http://localhost:81/laravelversion1.com/public', '', $image);
                    } elseif (strpos($image, '/laravelversion1.com/public') !== false) {
                        // Nếu ảnh chỉ có tiền tố tương đối
                        $image = str_replace('/laravelversion1.com/public', '', $image);
                    }
                }
                $payload['album'] = json_encode($albumArray); // Mã hóa lại thành chuỗi JSON
            }
            // dd($payload['album']);
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
            $flag = $this->postRepository->update($id, $payload);

            if ($flag) {
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['language_id'] = $this->language;
                $payloadLanguage['post_id'] = $id;
                $post->languages()->detach([$payloadLanguage['language_id'], $id]);
                $reponse = $this->postRepository->createLanguagePivot($post, $payloadLanguage);
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
            $post = $this->postRepository->forceDelete($id);
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
            $post = $this->postRepository->update($post['modelId'], $payload);
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
            $flag = $this->postRepository->updateByWhereIn('id', $post['id'], $payload);
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
    private function paginateselect()
    {
        return ['posts.id', 'posts.publish', 'posts.image', 'posts.level', 'posts.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'parent_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
