<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\UserCatgory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-category'))
        {
            $categories = Category::where('created_by',\Auth::user()->createId())->get();
            $categoriesWithUsers = $categories->map(function ($category) {
                $users = User::whereRaw("FIND_IN_SET(?, category_id)", [$category->id])->get();
                $category->users = $users;
                return $category;
            });
            return view('admin.category.index', compact('categoriesWithUsers'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        $users = User::where('parent',\Auth::user()->createId())->get()->pluck('name','id');
        $users->users  = explode(',', $user->users);
        if($user->can('create-category'))
        {
            return view('admin.category.create',compact('users'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        if($user->can('create-category'))
        {
            $validation = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'color' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ];
            $request->validate($validation);

            $category = new Category();
            $category->name = $request->name;
            $category->color = $request->color;
            $category->created_by = \Auth::user()->createId();

            $category->save();

            // if(!empty($request->users)){
            //     foreach($request->users as $value)
            //     {
            //         $user = User::find($value);
            //         // $user->category_id = $category->id;
            //         $user->category_id = !empty($category->id) ? implode(',', $category->id) : '';
            //         $user->save();
            //     }
            // }
            if (!empty($request->users)) {
                foreach ($request->users as $value) {
                    $user = User::find($value);

                    $existingCategoryIds = $user->category_id;

                    if (!empty($existingCategoryIds)) {
                        $categoryIdsArray = explode(',', $existingCategoryIds);
                        if (!in_array($category->id, $categoryIdsArray)) {
                            $categoryIdsArray[] = $category->id;
                        }
                        $user->category_id = implode(',', $categoryIdsArray);
                    } else {
                        $user->category_id = $category->id;
                    }
                    $user->save();
                }
            } 
            return redirect()->route('admin.category')->with('success', __('Category created successfully'));
        }
        else
        {
            return view('403');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if(\Auth::user()->can('edit-category'))
        {
            $userObj = \Auth::user();
            $category = Category::find($id);
            // if ($category){
                $users = User::where('parent',\Auth::user()->createId())->get()->pluck('name','id');
                $users->prepend(__('Select User'), '');
                $users->users  = explode(',', $userObj->users);

                $catgoryuser = User::whereRaw("FIND_IN_SET(?, category_id)", [$category->id])->get()->pluck('id');

                return view('admin.category.edit', compact('category','users','catgoryuser'));
            // }else{

            // }
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */

    // public function update(Request $request, $id)
    // {


    //     $userObj = \Auth::user();
    //     if($userObj->can('edit-category'))
    //     {
    //         $category        = Category::find($id);

    //         $category->name  = $request->name;
    //         $category->color = $request->color;

    //         if(!empty($request->users)){
    //             UserCatgory::where('category_id',$category->id)->delete();
    //             foreach($request->users as $value)
    //             {
    //                 $usercategory = UserCatgory::create([
    //                 'user_id' => $value,
    //                 'category_id' => $category->id,
    //                 ]);
    //             }
    //         }
    //         $category->save();

    //         return redirect()->route('admin.category')->with('success', __('Category updated successfully'));
    //     }
    //     else
    //     {
    //         return view('403');
    //     }
    // }

    public function update(Request $request, $id)
    {
        $userObj = \Auth::user();

        if ($userObj->can('edit-category')) {
            $category = Category::find($id);

            $category->name = $request->name;
            $category->color = $request->color;

            if (!empty($request->users)) {
                foreach ($request->users as $value) {
                    $user = User::find($value);

                    $existingCategoryIds = $user->category_id;

                    if (!empty($existingCategoryIds)) {
                        $categoryIdsArray = explode(',', $existingCategoryIds);

                        if (!in_array($category->id, $categoryIdsArray)) {
                            $categoryIdsArray[] = $category->id;
                        }
                        $user->category_id = implode(',', $categoryIdsArray);
                    } else {
                        $user->category_id = $category->id;
                    }
                    $user->save();
                }
            }

            $category->save();

            $catgoryusers = User::whereRaw("FIND_IN_SET(?, category_id)", [$category->id]);
            if (!empty($request->users)) {

                $catgoryusers = $catgoryusers->whereNotIn('id', $request->users);
            }

            $catgoryusers = $catgoryusers->get();

            $categoryIdToRemove = $category->id;
            foreach ($catgoryusers as $catgoryuser) {
                $categoryIds = explode(',', $catgoryuser->category_id);

                $categoryIds = array_filter($categoryIds, function ($categoryId) use ($categoryIdToRemove) {
                    return $categoryId != $categoryIdToRemove;
                });

                $categoryIds = array_values($categoryIds);

                $catgoryuser->category_id = implode(',', $categoryIds);

                $catgoryuser->save();
            }

            return redirect()->route('admin.category')->with('success', __('Category updated successfully'));
        } else {
            return view('403');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \Auth::user();
        if($user->can('delete-category'))
        {
            $category = Category::find($id);
            $category->delete();

            return redirect()->route('admin.category')->with('success', __('Category deleted successfully'));
        }
        else
        {
            return view('403');
        }
    }
}
