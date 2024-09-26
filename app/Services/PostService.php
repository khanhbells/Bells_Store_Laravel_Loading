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
        $condition['post_catalogue_id'] = $request->input('post_catalogue_id');
        $condition['where'] = [
            ['tb2.language_id', '=', $this->language]
        ];
        $perPage = $request->integer('perpage');
        $posts = $this->postRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'post/index'],
            [
                'posts.id',
                'DESC'
            ],
            [
                ['post_language as tb2', 'tb2.post_id', '=', 'posts.id'],
                ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id']
            ],
            ['post_catalogues'],
            $this->whereRaw($request)
        );
        return $posts;
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            // dd($request);
            $post = $this->createPost($request);
            if ($post->id > 0) {
                $this->uploadLanguageForPost($post, $request);
                $this->updateCatalogueForpost($post, $request);
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
            // dd($post);
            if ($this->uploadPost($post, $request)) {
                $this->uploadLanguageForPost($post, $request);
                $this->updateCatalogueForPost($post, $request);
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
            Log::error($e->getMessage());
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
    private function createPost($request)
    {
        $payload = $request->only($this->payload());
        // Kiểm tra xem album có được truyền không
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        // Kiểm tra xem image có được truyền không
        if (isset($payload['image'])) {
            $payload['image'] = $this->formatImage($payload['image']);
        }
        // dd($payload['album'], $payload['image']);
        $payload['user_id'] = Auth::id();
        // Tạo bản ghi
        $post = $this->postRepository->create($payload);
        return $post;
    }

    private function formatAlbum($albumArray)
    {
        foreach ($albumArray as &$image) {
            if (strpos($image, 'http://localhost:81/laravelversion1.com/public') !== false) {
                $image = str_replace('http://localhost:81/laravelversion1.com/public', '', $image);
            } elseif (strpos($image, '/laravelversion1.com/public') !== false) {
                $image = str_replace('/laravelversion1.com/public', '', $image);
            }
        }
        return (!empty($albumArray)) ? json_encode($albumArray) : ''; // Mã hóa lại thành chuỗi JSON
    }
    private function formatImage($image)
    {
        if (strpos($image, 'http://localhost:81/laravelversion1.com/public') !== false) {
            return str_replace('http://localhost:81/laravelversion1.com/public', '', $image);
        } elseif (strpos($image, '/laravelversion1.com/public') !== false) {
            return str_replace('/laravelversion1.com/public', '', $image);
        }
        return $image;
    }
    private function formatLanguagePayload($payload, $postId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->language;
        $payload['post_id'] = $postId;
        return $payload;
    }
    private function uploadPost($post, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($payload['album']);
        $payload['image'] = $this->formatImage($payload['image']);
        // dd($payload);
        // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'

        return $this->postRepository->update($post->id, $payload);
    }
    private function uploadLanguageForPost($post, $request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $post->id);
        $post->languages()->detach([$this->language, $post->id]);
        return $this->postRepository->createPivot($post, $payload, 'languages');
    }
    private function updateCatalogueForpost($post, $request)
    {
        $post->post_catalogues()->sync($this->catalogue($request));
    }
    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->post_catalogue_id]));
        } else {
            return [$request->post_catalogue_id];
        }
    }
    private function whereRaw($request)
    {
        $rawCondition = [];
        if ($request->integer('post_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id
                        FROM post_catalogues
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id =?)
                    )',
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }
    private function paginateselect()
    {
        return ['posts.id', 'posts.publish', 'posts.image', 'posts.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'post_catalogue_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}