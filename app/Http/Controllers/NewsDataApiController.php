<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Repositories\Api\NewsDataRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class NewsDataApiController extends Controller
{
    public function __construct(
        protected NewsDataRepository $newsDataRepository
    ) {
    }

    public function getLatestNews(Country $country, Language $language = null, Category $category = null, int $page = 1): JsonResponse
    {
        $nextPage = null;

        if ($language) {
            $languagesParam = $language->language;
        } else {
            $languages      = $country->languages()->limit(5)->get();
            $languagesParam = $languages->map(function (Language $language) {
                return $language->language;
            })->implode(',');
        }

        if ($category) {
            $categoriesParam = $category->name;
        } else {
            $categories      = $country->categories()->limit(5)->get();
            $categoriesParam = $categories->map(function (Category $category) {
                return $category->name;
            })->implode(',');
        }

        if ($page > 1) {
            do {
                $nextPage = Redis::get("$country->code:$page");
                if (!$nextPage) {
                    $page--;
                }
            } while (!$nextPage && $page > 1);
        }

        $data = $this->newsDataRepository->getLatestNews($country->code, $languagesParam, $categoriesParam, $nextPage);

        Redis::set($country->code . ':' . $page + 1, $data->nextPage);

        return response()->json($data);
    }
}
