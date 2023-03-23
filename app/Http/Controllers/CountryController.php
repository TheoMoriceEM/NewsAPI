<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
use App\Models\Category;
use App\Models\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): CountryCollection
    {
        return new CountryCollection(Country::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    /**
     * Attach or detach a category
     * @param Country $country
     * @param Category $category
     * @return array
     */
    public function toggleCategory(Country $country, Category $category): array
    {
        return $country->categories()->toggle($category);
    }
}
