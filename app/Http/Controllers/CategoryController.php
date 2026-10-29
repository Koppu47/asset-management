<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount('assets')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'code.required' => 'กรุณากรอกรหัสย่อหมวดหมู่',
            'code.unique' => 'รหัสย่อหมวดหมู่นี้มีอยู่ในระบบแล้ว',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "เพิ่มหมวดหมู่ '{$validated['name']}' เรียบร้อยแล้ว");
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'code.required' => 'กรุณากรอกรหัสย่อหมวดหมู่',
            'code.unique' => 'รหัสย่อหมวดหมู่นี้มีอยู่ในระบบแล้ว',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "อัปเดตข้อมูลหมวดหมู่ '{$category->name}' เรียบร้อยแล้ว");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        Gate::authorize('delete', $category);

        // ป้องกันการลบหากยังมีครุภัณฑ์สังกัดหมวดหมู่นี้อยู่
        if ($category->assets()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', "ไม่สามารถลบหมวดหมู่ '{$category->name}' ได้ เนื่องจากยังมีครุภัณฑ์สังกัดอยู่ {$category->assets()->count()} รายการ");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "ลบหมวดหมู่ '{$categoryName}' เรียบร้อยแล้ว");
    }
}
