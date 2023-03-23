<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Repositories\Api\NewsDataRepository;
use Illuminate\Http\JsonResponse;

class NewsDataApiController extends Controller
{
    public function __construct(
        protected NewsDataRepository $newsDataRepository
    ) {
    }

    public function getLatestNews(Country $country, Language $language = null, Category $category = null, int $page = 1): JsonResponse
    {
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

        $data = $this->newsDataRepository->getLatestNews($country->code, $languagesParam, $categoriesParam);

        return response()->json($data);
    }
}
