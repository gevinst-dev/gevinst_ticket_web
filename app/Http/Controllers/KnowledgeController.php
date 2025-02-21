<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use Illuminate\Http\Request;
use App\Models\Knowledgebasecategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->can('manage-knowledge')) {

            $knowledges = Knowledge::get();

            return view('admin.knowledge.index', compact('knowledges'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->can('create-knowledge')) {
            $category = Knowledgebasecategory::get();
            return view('admin.knowledge.create', compact('category'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->can('create-knowledge')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'category' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }
            $knowledge = new  Knowledge();
            $knowledge->title = $request->title;
            $knowledge->description = $request->description;
            $knowledge->category = $request->category;
            $knowledge->save();
            return redirect()->route('admin.knowledge')->with('success',  __('Knowledge created successfully'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function show($id){
        if(Auth::user()->can('show-knowledgecategory')){
            $knowledge = Knowledge::with('getCategoryInfo')->find($id);
          if($knowledge){
            return view('admin.knowledge.show',compact('knowledge'));
          }else{
             return redirect()->back()->with('error','Knowledgebase Not Found.');    
          }
        }else{
         return redirect()->back()->with('error','Permission Denied.');
        }
     }


    public function edit($id)
    {
        $userObj = Auth::user();
        if ($userObj->can('edit-knowledge')) {
            $knowledge = Knowledge::find($id);
            $category = Knowledgebasecategory::get();
            return view('admin.knowledge.edit', compact('knowledge', 'category'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }


    public function update(Request $request, $id)
    {
        $userObj = Auth::user();
        if ($userObj->can('edit-knowledge')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'category' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }

            $knowledge = Knowledge::where('id', $id)->first();
            if ($knowledge) {
                $knowledge->title = $request->title;
                $knowledge->description = $request->description;
                $knowledge->category = $request->category;
                $knowledge->save();
                return redirect()->route('admin.knowledge')->with('success', __('Knowledge updated successfully'));
            } else {
                return redirect()->back()->with('error', 'Knowledge Not Found.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }


    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->can('delete-knowledge')) {
            $knowledge = Knowledge::find($id);
            if ($knowledge) {
                $knowledge->delete();
                return redirect()->route('admin.knowledge')->with('success', __('Knowledge deleted successfully'));
            } else {
                return redirect()->back()->with('error', 'Knowledge Not Found.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }
}
