<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendCloseTicket;
use App\Mail\SendTicket;
use Illuminate\Support\Facades\Mail;
use App\Models\Api\User;
use App\Models\Api\Category;
use App\Models\Api\Ticket;
use App\Models\Api\Faq;


class FaqController extends Controller
{
    use ApiResponser;

    public function indexs(Request $request)
    {
   
   
        $faqs = Faq::query();
        
        
        if($request->search){

            $faqs->where('title', 'like', "%{$request->search}%");
        }
        
              $faqs = $faqs->paginate(10);
        
    
     
        $data = [
            'faq'=>$faqs,
        ];  

        return $this->success($data);
    }

    public function store(Request $request)
    {

        if($request->id == null){

            $validation = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required'],
            ];
            $request->validate($validation);
    
            $post = [
                'title' => $request->title,
                'description' => $request->description,
            ];
    
            $faq = Faq::create($post);
    
            $data = [
                'faq' =>$faq
            ];
    
            return $this->success($data);

        }else{

            $faq = Faq::find($request->id);
            
            $faq->title = $request->title;
            $faq->description = $request->description;
            
            $faq->save();

             $data = [
                'faq' =>$faq
            ];
    
            return $this->success($data);
        }
    }

    public function destroy(Request $request)
    {
        $faq = Faq::find($request->id);
        $faq->delete();

        $data = [
            'faq'=>[],
        ]; 

        return $this->success($data);
    }


}