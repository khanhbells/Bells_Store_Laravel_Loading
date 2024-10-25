<?php

namespace App\Services;

use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class SlideService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{
    protected $slideRepository;
    public function __construct(SlideRepository $slideRepository)
    {
        $this->slideRepository = $slideRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->input('publish', -1);
        $perPage = $request->integer('perpage');
        // dd($condition);
        $slides = $this->slideRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'slide/index'],
        );
        return $slides;
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');

            $payload['item'] = $this->handleSlideItem($request, $languageId);
            // dd($payload);
            $slide = $this->slideRepository->create($payload);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function update($id, Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $slide = $this->slideRepository->findById($id);
            $slideItem = $slide->item;
            unset($slideItem[$languageId]);
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');
            $payload['item'] = $this->handleSlideItem($request, $languageId) + $slideItem;
            // dd($payload);
            $slide = $this->slideRepository->update($id, $payload);
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
    public function updateImage($id, $requestSlide, $languageId)
    {
        DB::beginTransaction();

        try {
            $slide = $this->slideRepository->findById($id);
            $slideItem = $slide->item;
            unset($slideItem[$languageId]);
            $payload['item'] = $this->handleSlideItem($requestSlide, $languageId) + $slideItem;
            $slide = $this->slideRepository->update($id, $payload);
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
            $slide = $this->slideRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function handleSlideItem($request, $languageId)
    {
        $slide = $request->input('slide');
        $temp = [];
        foreach ($slide['image'] as $key => $val) {
            $valReplace = $this->formatImage($val);;
            $temp[$languageId][] = [
                'image' => $valReplace,
                'name' => $slide['name'][$key],
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => (isset($slide['window'][$key])) ? $slide['window'][$key] : '',
            ];
        }
        return $temp;
    }

    public function convertSlideArray(array $slide = []): array
    {
        $temp = [];
        $fields = ['image', 'description', 'window', 'canonical', 'name', 'alt'];
        foreach ($slide as $key => $val) {
            foreach ($fields as $field) {
                $temp[$field][] = $val[$field];
            }
        }
        return $temp;
    }

    private function paginateselect()
    {
        return ['id', 'name', 'keyword', 'item', 'publish'];
    }

    public function getSlide($array = [], $language = 1)
    {
        $slides = $this->slideRepository->findByCondition(...$this->getSlideAgrument($array));
        $temp = [];
        foreach ($slides as $key => $val) {
            $temp[$val->keyword]['item'] = $val->item[$language];
            $temp[$val->keyword]['setting'] = $val->setting;
        }
        return $temp;
    }
    private function getSlideAgrument($array)
    {
        return [
            'condition' => [
                config('app.general.defaultPublish'),
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $array,
                'whereInField' => 'keyword'
            ]

        ];
    }
}
