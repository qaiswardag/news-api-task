<?php

namespace App\Http\Controllers;

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
    public function show(string $id)
    {
        //
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
