<?php

namespace App\Repositories\Api;

use NewsdataIO\NewsdataApi;

class NewsDataRepository
{
    protected NewsdataApi $newsData;

    public function __construct()
    {
        $this->newsData = new NewsdataApi(env('NEWS_DATA_API_KEY'));
    }

    public function getLatestNews(string $countryCode, string $languages, string $categories, string $nextPage = null): object|array
    {
        $params = [
            'country'  => $countryCode,
            'category' => $categories,
            'language' => $languages,
            'page'     => $nextPage,
        ];

        return $this->newsData->get_latest_news($params);
    }
}
