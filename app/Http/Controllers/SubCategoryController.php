<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        if($user->parent==0){
            $subcategories = SubCategory::with('category')->get();
            return view('admin.subcategory.index', compact('subcategories'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        return view('admin.subcategory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $category = Category::findOrFail($request->category_id);
        $category->subCategories()->create([
            'subcategory' => $request->subcategory,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.subcategory')->with('success', __('Subcategory created successfully!'));
    }
        
    public function destroy($id)
    {
        $subcategory = SubCategory::find($id);
        $subcategory->delete();

        return redirect()->route('admin.subcategory')->with('success', __('Category deleted successfully'));
    }

    public function edit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.subcategory.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $subcategory = SubCategory::findOrFail($id);

        $subcategory->category_id = $request->category_id;
        $subcategory->subcategory = $request->subcategory;
        $subcategory->color = $request->color;

        $subcategory->save();

        return redirect()->route('admin.subcategory')->with('success', __('Subcategory updated successfully!'));
    }
}
