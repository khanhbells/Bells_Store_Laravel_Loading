<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;
    protected $routerRepository;
    public function __construct(AttributeRepository $attributeRepository, RouterRepository $routerRepository)
    {
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'AttributeController';
    }
    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->input('publish', -1),
            'attribute_catalogue_id' => $request->input('attribute_catalogue_id'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $paginationConfig = [
            'path' => 'attribute/index'
        ];
        $orderBy = ['attributes.id', 'DESC'];
        $relations = ['attribute_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        $joins = [
            ['attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id'],
            ['attribute_catalogue_attribute as tb3', 'attributes.id', '=', 'tb3.attribute_id']
        ];
        $attributes = $this->attributeRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );
        return $attributes;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            // dd($request);
            $attribute = $this->createAttribute($request);
            if ($attribute->id > 0) {
                $this->uploadLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForattribute($attribute, $request);
                $this->createRouter($attribute, $request, $this->controllerName, $languageId);
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
    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $attribute = $this->attributeRepository->findById($id);
            // dd($attribute);
            if ($this->uploadAttribute($attribute, $request)) {
                $this->uploadLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->updateRouter($attribute, $request, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->forceDelete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controller\Frontend\AttributeCatalogueController']
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
    private function createAttribute($request)
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
        $attribute = $this->attributeRepository->create($payload);
        return $attribute;
    }



    private function uploadAttribute($attribute, $request)
    {
        $payload = $request->only($this->payload());
        if (isset($payload['album'])) {
            $payload['album'] = $this->formatAlbum($payload['album']);
        }
        $payload['image'] = $this->formatImage($payload['image']);
        // dd($payload);
        // Kiểm tra và xử lý cả hai trường hợp ảnh có tiền tố 'http://localhost:81/laravelversion1.com/public' hoặc '/laravelversion1.com/public'

        return $this->attributeRepository->update($attribute->id, $payload);
    }
    private function uploadLanguageForAttribute($attribute, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attribute->id, $languageId);
        $attribute->languages()->detach([$languageId, $attribute->id]);
        return $this->attributeRepository->createPivot($attribute, $payload, 'languages');
    }
    private function formatLanguagePayload($payload, $attributeId, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_id'] = $attributeId;
        return $payload;
    }
    private function updateCatalogueForattribute($attribute, $request)
    {
        $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }
    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->attribute_catalogue_id]));
        } else {
            return [$request->attribute_catalogue_id];
        }
    }
    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('attribute_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.attribute_catalogue_id IN (
                        SELECT id
                        FROM attribute_catalogues
                        WHERE lft >= (SELECT lft FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM attribute_catalogues as pc WHERE pc.id =?)
                    )',
                    [$request->integer('attribute_catalogue_id'), $request->integer('attribute_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }
    private function paginateselect()
    {
        return ['attributes.id', 'attributes.publish', 'attributes.image', 'attributes.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'attribute_catalogue_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
