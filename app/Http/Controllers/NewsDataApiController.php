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

        // If no language is specified in the route, take 5 of them at most from the DB for the NewsData API request (since the languages parameter is limited to 5)
        if ($language) {
            $languagesParam = $language->language;
        } else {
            $languages      = $country->languages()->limit(5)->get();
            $languagesParam = $languages->map(function (Language $language) {
                return $language->language;
            })->implode(','); // Format the languages as a comma-separated string, as required by the NewsData API
        }

        // If no category is specified in the route, take 5 of them at most from the DB for the NewsData API request (since the categories parameter is limited to 5)
        if ($category) {
            $categoriesParam = $category->name;
        } else {
            $categories      = $country->categories()->limit(5)->get();
            $categoriesParam = $categories->map(function (Category $category) {
                return $category->name;
            })->implode(','); // Format the categories as a comma-separated string, as required by the NewsData API
        }

        /**
         * The NewsData API doesn't require a page number parameter, but a random hash which is to be retrieved from the previous page's request.
         * Recursive calls from the first page to get to the next one each time until we reach the requested one would be a disaster performance-wise,
         * that's why we're using Redis to store hashes at every successful request instead.
         * When a certain page is requested, we check if the corresponding hash is stored in the cache :
         * if it's not, we decrement the requested page number until we get to the highest possible page or to the first one.
         */
        if ($page > 1) {
            do {
                $nextPage = Redis::get("$country->code:$page");
                if (!$nextPage) {
                    $page--;
                }
            } while (!$nextPage && $page > 1);
        }

        $data = $this->newsDataRepository->getLatestNews($country->code, $languagesParam, $categoriesParam, $nextPage);

        if ($data->status === $this->newsDataRepository::STATUS_SUCCESS) {
            Redis::set($country->code . ':' . $page + 1, $data->nextPage); // Storing the hash in order to be able to access the next page in a later request
        }

        return response()->json($data);
    }
}
