<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'country' => ['required', 'string', 'max:4'],
            'language' => ['required', 'string', 'max:4'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $key = config('services.newsdata.key');
        $url = config('services.newsurl.key');

        $response = Http::get($url, [
            'apikey' => $key,
            'country' => $request->country,
            'language' => $request->language,
            'category' => $request->category,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to fetch news data',
                'error' => $response->body()
            ], 500);
        }

        return response()->json([
            'data' => $response->json()['results'] ?? [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $countryCode, int $page = 1)
    {
        $country = Country::where('code', $countryCode)
            ->with(['categories', 'languages'])
            ->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $key = config('services.newsdata.key');
        $url = config('services.newsurl.key');

        $categories = array_filter($country->categories->pluck('name')->toArray());
        $languages = array_filter($country->languages->pluck('language')->toArray());

        if (empty($languages)) {
            return response()->json([
                'message' => 'No languages found for this country.'
            ], 400);
        }

        if (empty($categories)) {
            return response()->json([
                'message' => 'No categories found for this country.'
            ], 400);
        }

        $categoryParam = implode(',', $categories);
        $languageParam = implode(',', $languages);

        // return response()->json([
        //     'country' => $countryCode,
        //     'page'    => $page,
        //     'languageParam'    => $languageParam,
        //     'categoryParam'    => $categoryParam,
        // ]);

        $response = Http::get($url, [
            'apikey'   => $key,
            'country'  => $countryCode,
            'language' => $languageParam,
            'category' => $categoryParam,
            'page'     => $page,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to fetch news data',
                'error'   => $response->body()
            ], 500);
        }

        return response()->json([
            'country' => $countryCode,
            'page'    => $page,
            'data'    => $response->json()['results'] ?? [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
