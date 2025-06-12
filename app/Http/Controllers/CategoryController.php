<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Utility\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with("items")->get();
        return ApiResponse::send(200, "Category list retrieved", null, $categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|min:4|max:60",
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors()->all());
        }

        $cred = $validator->validated();
        $cred["slug"] = str_replace(" ", "-", $cred["name"]);

        if (Category::query()->where("slug", $cred["slug"])->exists()) {
            return ApiResponse::send(403, "Category already existed");
        }

        $newCategory = Category::query()->create($cred);

        return ApiResponse::send(200, "Category created", null, $newCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::with('items')->where('slug', $slug)->first();
//        dd($category);

        if (is_null($category)) {
            return ApiResponse::send(404, "Category not found");
        }

        return ApiResponse::send(200, "Category retrieved", null, $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $category = Category::query()->where("slug", $slug)->first();

        if (is_null($category)) {
            return ApiResponse::send(404, "Category not found");
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|min:4|max:60",
        ]);

        if ($validator->fails()) {
            return ApiResponse::send(422, "Validation failed", $validator->errors()->all());
        }

        $cred = $validator->validated();
        $cred["slug"] = str_replace(" ", "-", $cred["name"]);

        if (Category::query()->where("slug", $cred["slug"])->first()) {
            return ApiResponse::send(403, "Category already existed");
        }

        $category->update($cred);

        return ApiResponse::send(200, "Category updated", null, $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $category = Category::query()->where("slug", $slug)->first();

        if (is_null($category)) {
            return ApiResponse::send(404, "Category not found");
        }

        $category->delete();

        return ApiResponse::send(200, "Category removed");
    }
}
