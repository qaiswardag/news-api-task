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

        $cacheKey = 'news:index:' . $request->country . ':lang:' . $request->language . ':cat:' . md5($request->category ?? '');
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

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

        $result = [
            'data' => $response->json()['results'] ?? [],
        ];

        // Cache for 10 minutes
        cache()->put($cacheKey, $result, 600);

        return response()->json($result);
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
    public function show(string $countryCode, ?string $nextPageToken = null)
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

        $params = [
            'apikey'   => $key,
            'country'  => $countryCode,
            'language' => implode(',', $languages),
            'category' => implode(',', $categories),
        ];

        if ($nextPageToken !== null) {
            $params['page'] = $nextPageToken;
        }

        $cacheKey = 'news:' . $countryCode . ':page:' . ($nextPageToken ?? '1') . ':cat:' . md5(implode(',', $categories));
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        $response = Http::get($url, $params);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to fetch news data',
                'error'   => $response->body(),
            ], 500);
        }

        $json = $response->json();

        $result = [
            'country'  => $countryCode,
            'nextPage' => $json['nextPage'] ?? null,
            'data'     => $json['results'] ?? [],
        ];

        // Cache for 10 minutes
        cache()->put($cacheKey, $result, 600);

        return response()->json($result);
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
