<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'country_code' => ['required', 'exists:countries,code', 'max:255'],
            'category_id' => ['required', 'exists:categories,id', 'integer'],
        ]);

        $country = Country::where('code', $request->country_code)->first();

        // Check if category is already attached
        $alreadyAttached = $country->categories()->where('categories.id', $request->category_id)->exists();

        if ($alreadyAttached) {
            return response()->json(['message' => 'Category already attached to country'], 200);
        }

        // Attach category if not already attached
        $country->categories()->attach($request->category_id);

        return response()->json(['message' => 'Category successfully added to country'], 201);
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
    public function destroy(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'exists:countries,code', 'max:255'],
            'category_id' => ['required', 'exists:categories,id', 'integer'],
        ]);

        $country = Country::where('code', $request->country_code)->first();

        if ($country->categories()->where('categories.id', $request->category_id)->exists()) {
            $country->categories()->detach($request->category_id);
            return response()->json([
                'message' => "Category with ID {$request->category_id} successfully removed from country '{$country->name}' (code: {$country->code})."
            ], 200);
        }

        return response()->json([
            'message' => "Category with ID {$request->category_id} was not found attached to country '{$country->name}' (code: {$country->code}), so no removal was needed."
        ], 200);
    }
}
