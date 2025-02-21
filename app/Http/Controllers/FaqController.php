<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{

    public function index()
    {
        $user = \Auth::user();
        if ($user->can('manage-faq')) {

            $faqs = Faq::get();

            return view('admin.faq.index', compact('faqs'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }



    public function create()
    {
        $user = \Auth::user();
        if ($user->can('create-faq')) {
            return view('admin.faq.create');
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function store(Request $request)
    {
        $user = \Auth::user();
        if ($user->can('create-faq')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }
            $faq = new FAQ();
            $faq->title = $request->title;
            $faq->description = $request->description;
            $faq->save();
            return redirect()->route('admin.faq')->with('success',  __('Faq created successfully'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied');
        }
    }

    public function show($id){
       if(Auth::user()->can('show-faq')){
         $faq = FAQ::find($id);
         if($faq){
           return view('admin.faq.show',compact('faq'));
         }else{
            return redirect()->back()->with('error','FAQ Not Found.');    
         }
       }else{
        return redirect()->back()->with('error','Permission Denied.');
       }
    }

    public function edit($id)
    {
        $userObj = \Auth::user();
        if ($userObj->can('edit-faq')) {
            $faq = Faq::find($id);
            return view('admin.faq.edit', compact('faq'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }


    public function update(Request $request, $id)
    {
        $userObj = \Auth::user();
        if ($userObj->can('edit-faq')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }

            $faq = Faq::where('id', $id)->first();
            if ($faq) {
                $faq->title = $request->title;
                $faq->description = $request->description;
                $faq->save();
                return redirect()->route('admin.faq')->with('success', __('Faq updated successfully'));
            } else {
                return redirect()->back()->with('error', 'FAQ Not Found !!');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }

    public function destroy($id)
    {
        $user = \Auth::user();
        if ($user->can('delete-faq')) {
            $faq = Faq::find($id);
            if ($faq) {
                $faq->delete();
                return redirect()->route('admin.faq')->with('success', __('Faq deleted successfully'));
            } else {
                return redirect()->back()->with('error', 'FAQ Not Found !!');
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }
}
