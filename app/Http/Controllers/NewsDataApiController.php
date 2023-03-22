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

    public function getLatestNews(Country $country, Language $language, Category $category): JsonResponse
    {
        $data = $this->newsDataRepository->getLatestNews($country, $language, $category);

        return response()->json($data);
    }
}
