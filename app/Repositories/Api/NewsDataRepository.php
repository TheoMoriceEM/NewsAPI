<?php

namespace App\Repositories\Api;

use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use NewsdataIO\NewsdataApi;

class NewsDataRepository
{
    protected NewsdataApi $newsData;

    public function __construct()
    {
        $this->newsData = new NewsdataApi(env('NEWS_DATA_API_KEY'));
    }

    public function getLatestNews(Country $country, Language $language, Category $category): object|array
    {
        $params = [
            'country'  => $country->code,
            'category' => $category->name,
            'language' => $language->language,
        ];

        return $this->newsData->get_latest_news($params);
    }
}
