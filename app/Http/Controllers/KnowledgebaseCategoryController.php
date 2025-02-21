<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Knowledgebasecategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KnowledgebaseCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->can('manage-knowledge')) {
            $knowledges_category = Knowledgebasecategory::get();
            return view('admin.knowledgecategory.index', compact('knowledges_category'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
        return view('admin.knowledgecategory.index');
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->can('create-faq')) {
            return view('admin.knowledgecategory.create');
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->can('create-faq')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);

            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }
            $knowledgebasecategory = new Knowledgebasecategory();
            $knowledgebasecategory->title = $request->title;
            $knowledgebasecategory->save();

            return redirect()->route('admin.knowledgecategory')->with('success',  __('KnowledgeBase Category created successfully'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function edit($id)
    {
        $userObj = Auth::user();
        if ($userObj->can('edit-faq')) {
            $knowledge_category = Knowledgebasecategory::find($id);
            if ($knowledge_category) {
                return view('admin.knowledgecategory.edit', compact('knowledge_category'));
            } else {
                return redirect()->back()->with('error', 'KnowledgeBase Not Found.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function update(Request $request, $id)
    {
        $userObj = Auth::user();
        if ($userObj->can('edit-faq')) {
            $knowledge_category = Knowledgebasecategory::where('id', $id)->first();
            if ($knowledge_category) {
                $knowledge_category->title = $request->title;
                $knowledge_category->save();
                return redirect()->route('admin.knowledgecategory')->with('success', __('KnowledgeBase Category updated successfully'));
            } else {
                return redirect()->back()->with('error', 'KnowledgeBase Not Found.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->can('delete-faq')) {
            $knowledge_category = Knowledgebasecategory::find($id);
            if ($knowledge_category) {
                $knowledge_category->delete();
                return redirect()->route('admin.knowledgecategory')->with('success', __('KnowledgeBase Category deleted successfully'));
            } else {
                return redirect()->back()->with('error', 'KnowledgeBase Not Found.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }
}
