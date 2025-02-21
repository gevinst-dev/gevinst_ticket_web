<?php

namespace App\Http\Controllers;

use App\Events\VerifyReCaptchaToken;
use App\Models\Category;
use App\Models\Conversion;
use App\Models\CustomField;
use App\Models\Faq;
use App\Mail\SendTicket;
use App\Models\UserCatgory;
use App\Mail\SendTicketAdmin;
use App\Mail\SendTicketReply;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Knowledge;
use App\Models\Utility;
use App\Models\Settings;
use App\Models\Priority;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    private  $language;
    public function __construct()
    {
        // $this->middleware('2fa');

        if (!file_exists(storage_path() . "/installed")) {
            return redirect('install');
        }

        $language = Utility::getSettingValByName('DEFAULT_LANG');
        \App::setLocale(isset($language) ? $language : 'en');
    } 

    public function index()
    {
        $this->middleware('2fa');
        if (!file_exists(storage_path() . "/installed")) {
            return redirect('install');
        }
        // if (Auth::user()) {
        //     return redirect()->route('admin.dashboard');
        // }

        $customFields = CustomField::orderBy('order')->get();
        $categories   = Category::get();
        $priorities = Priority::get();

        $setting      = Utility::settings();

        return view('home', compact('categories', 'customFields', 'setting', 'priorities'));
    }

    public function getsub(Request $request)
    {
        $subcategory = SubCategory::where('category_id', '=', $request->category_id)->get();

        return json_encode($subcategory);
    }


    public function search($lang = '')
    {
        $setting      = Utility::settings();

        if ($lang == '') {
            $lang = Utility::getSettingValByName('DEFAULT_LANG') ?? 'en';
        }
        \App::setLocale($lang);
        return view('search', compact('setting'));
    }

    public function faq()
    {
        $setting      = Utility::settings();
        if ($setting['FAQ'] == 'on') {
            $faqs = Faq::get();

            return view('faq', compact('faqs', 'setting'));
        } else {
            return redirect('/');
        }
    }

    public function ticketSearch(Request $request)
    {
        $validation = [
            'ticket_id' => ['required'],
            'email' => ['required'],
        ];

        $this->validate($request, $validation);
        $ticket = Ticket::where('ticket_id', '=', $request->ticket_id)->where('email', '=', $request->email)->first();

        if ($ticket) {
            return redirect()->route('home.view', Crypt::encrypt($ticket->ticket_id));
        } else {
            return redirect()->back()->with('info', __('Invalid Ticket Number'));
        }

        return view('search');
    }


    public function store(Request $request)
    {
        $validation = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'category' => 'required|string|max:255',
            'subcategory' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'status' => 'required|string|max:100',
            'description' => 'required',
            'priority' => 'required|string|max:255',
        ];

        // if (Utility::getSettingValByName('RECAPTCHA_MODULE') == 'yes') {
        //     $validation['g-recaptcha-response'] = 'required';
        // }

        $settings = Utility::settings();
        $validation = [];
        if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes') {
            if ($settings['google_recaptcha_version'] == 'v2-checkbox') {
                $validation['g-recaptcha-response'] = 'required';
            } elseif ($settings['google_recaptcha_version'] == 'v3') {


                $re = event(new VerifyReCaptchaToken($request));
                if (!isset($re[0]['status']) || $re[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]); // Set the key to null

                    $validation['g-recaptcha-response'] = 'required';
                }
            } else {
                $validation = [];
            }
        } else {
            $validation = [];
        }

        // ---------------------------------------------------------------------------------

        $this->validate($request, $validation);

        $post              = $request->all();
        $post['ticket_id'] = time();
        $post['created_by'] = '1';
        $data              = [];
        if ($request->hasfile('attachments')) {
            $errors = [];
            foreach ($request->file('attachments') as $filekey => $file) {
                $name = $file->getClientOriginalName();
                $dir        = ('tickets/' . $post['ticket_id']);
                $path = Utility::multipalFileUpload($request, 'attachments', $name, $dir, $filekey, []);

                if ($path['flag'] == 1) {
                    $data[] = $path['url'];
                } elseif ($path['flag'] == 0) {
                    $errors = __($path['msg']);
                } else {
                    return redirect()->route('tickets.store', \Auth::user()->id)->with('error', __($path['msg']));
                    //return redirect()->back()->with('error', __($path['msg']));

                }
            }
        }
        $post['attachments'] = json_encode($data);
        $ticket              = Ticket::create($post);
        CustomField::saveData($ticket, $request->customField);
        // Send Email to User

        $uArr = [
            'ticket_name' => $ticket->name,
            'email' => $request->email,
            'category' => $request->category,
            'subject' => $request->subject,
            'status' => $request->status,
            'description' => $request->description,
            'ticket_id' => $ticket->ticket_id,
        ];

        // slack //

        $settings  = Utility::non_auth_settings($ticket->created_by);
        if (isset($settings['ticket_notification']) && $settings['ticket_notification'] == 1) {
            $uArr = [
                'name' => $request->name,
                'email' => $request->email,
                'category' => $request->category,
                'subject' => $request->subject,
                'status' => $request->status,
                'description' => $request->description,
                'user_name'  => $request->slug,
            ];
            Utility::send_slack_msg('new_ticket', $uArr, $ticket->created_by);
        }

        // telegram //

        $settings  = Utility::non_auth_settings($ticket->created_by);
        if (isset($settings['telegram_ticket_notification']) && $settings['telegram_ticket_notification'] == 1) {
            $uArr = [
                'name' => $request->name,
                'email' => $request->email,
                'category' => $request->category,
                'subject' => $request->subject,
                'status' => $request->status,
                'description' => $request->description,
                'user_name'  => $request->slug,
            ];
            Utility::send_telegram_msg('new_ticket', $uArr, $ticket->created_by);
        }

        // Send Email to User

        try {
            //Mail Send Agent
            // $userids = User::where('category_id', $request->category)->pluck('user_id');
            $userids = User::where('category_id', $request->category)->pluck('id');
            $agents = User::whereIn('id', $userids)->get();
            Utility::getSMTPDetails(1);
            foreach ($agents as $agent) {
                Mail::to($agent->email)->send(new SendTicketAdmin($agent, $ticket));
            }
            // Mail Send  Ticket User
            Mail::to($ticket->email)->send(new SendTicket($ticket));

            // Mail send to Admin
            $adminUser = User::where('parent', 0)->first();
            Mail::to($adminUser->email)->send(new SendTicket($ticket));

            $module = 'New Ticket';
            $webhook =  Utility::webhookSetting($module, $ticket->created_by);

            if ($webhook) {
                $parameter = json_encode($ticket);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    // return redirect()->back()->with( __('Ticket successfully created!'));

                    return redirect()->back()->with('create_ticket', __('Ticket successfully created!') . ' <a href="' . route('home.view', Crypt::encrypt($ticket->ticket_id)) . '"><b>' . __('Your unique ticket link is this.') . '</b></a> ' . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
                } else {
                    return redirect()->back()->with(__('Webhook call failed.'));
                }
            }
        } catch (\Exception $e) {
            $error_msg = __('E-Mail has been not sent due to SMTP configuration');
        }
        return redirect()->back()->with('create_ticket', __('Ticket created successfully') . ' <a href="' . route('home.view', Crypt::encrypt($ticket->ticket_id)) . '"><b>' . __('Your unique ticket link is this.') . '</b></a> ' . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
    }

    public function view($ticket_id)
    {
        $ticket_id = Crypt::decrypt($ticket_id);
        $ticket    = Ticket::where('ticket_id', '=', $ticket_id)->first();
        if ($ticket) {
            return view('show', compact('ticket'));
        } else {
            return redirect()->back()->with('error', __('Some thing is wrong'));
        }
    }

    // public function reply(Request $request, $ticket_id)
    // {
    //     $ticket = Ticket::where('ticket_id', '=', $ticket_id)->first();
    //     if ($ticket) {
    //         $validation = ['reply_description' => ['required']];
    //         if ($request->hasfile('reply_attachments')) {
    //             $validation['reply_attachments.*'] = 'mimes:zip,rar,jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:204800';
    //         }
    //         $this->validate($request, $validation);

    //         $post                = [];
    //         $post['sender']      = 'user';
    //         $post['ticket_id']   = $ticket->id;
    //         $post['description'] = $request->reply_description;
    //         $data                = [];
    //         if ($request->hasfile('reply_attachments')) {
    //             foreach ($request->file('reply_attachments') as $file) {
    //                 $name = $file->getClientOriginalName();
    //                 $file->storeAs('/tickets/' . $ticket->ticket_id, $name);
    //                 $data[] = $name;
    //             }
    //         }
    //         $post['attachments'] = json_encode($data);
    //         $conversion          = Conversion::create($post);
    //         $ticket->status = 'In Progress';
    //         $ticket->update();

    //         // webhook //
    //         $module = 'New Ticket Reply';
    //         $webhook =  Utility::webhookSetting($module,$ticket->created_by);

    //         if ($webhook) {
    //             $parameter = json_encode($conversion);
    //             // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
    //             $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
    //             if ($status == true) {
    //                 return redirect()->back()->with('success', __('Reply successfully added!') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
    //             } else {
    //                 return redirect()->back()->with('error', __('Webhook call failed.'));
    //             }
    //         }

    //         // slack //
    //         $settings  = Utility::non_auth_settings($ticket->created_by);

    //         if (isset($settings['reply_notification']) && $settings['reply_notification'] == 1) {
    //             $uArr = [
    //                 'name' => $request->name,
    //                 'ticket_id' => $ticket->id,
    //                 'email' => $ticket->email,
    //                 'description' => $request->reply_description,
    //                 //  'user_name'  => \Auth::user(),
    //             ];
    //             Utility::send_slack_msg('new_ticket_reply', $uArr, $ticket->created_by);
    //         }
    //         // telegram //
    //         $settings  = Utility::settings($ticket->created_by);
    //         if (isset($settings['telegram_reply_notification']) && $settings['telegram_reply_notification'] == 1) {
    //             $uArr = [
    //                 'name' => $request->name,
    //                 'ticket_id' => $ticket->id,
    //                 'email' => $ticket->email,
    //                 'description' => $request->reply_description,
    //                 'user_name'  => $request->name,
    //             ];
    //             Utility::send_telegram_msg('new_ticket_reply', $uArr, $ticket->created_by);
    //         }
    //         // Send Email to User
    //         try {


    //             $users = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')->where('model_has_roles.model_type', '=', 'App\Models\User')->where('role_id', '=', 1)->get();
    //             Utility::getSMTPDetails(1);

    //             foreach ($users as $user) {

    //                 // Mail::to($user->email)->send(new SendTicketReply($user, $ticket, $conversion));
    //                 Mail::to($ticket->email)->send(new SendTicketReply($user, $ticket, $conversion));
    //             }
    //         } catch (\Exception $e) {
    //             $error_msg = __('E-Mail has been not sent due to SMTP configuration');
    //         }

    //         return redirect()->back()->with('success', __('Reply added successfully') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
    //     } else {
    //         return redirect()->back()->with('error', __('Something is wrong'));
    //     }
    // }
    public function reply(Request $request, $ticket_id)
    {
        $ticket = Ticket::where('ticket_id', '=', $ticket_id)->first();
        if ($ticket) {
            $validation = ['reply_description' => ['required']];
            if ($request->hasfile('reply_attachments')) {
                $validation['reply_attachments.*'] = 'mimes:zip,rar,jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:204800';
            }
            $this->validate($request, $validation);

            $post                = [];
            $post['sender']      = 'user';
            $post['ticket_id']   = $ticket->id;
            //$post['description'] = $request->reply_description;

            $summernoteContent = $request->reply_description;
            // Extract images and save them
            preg_match_all('/<img[^>]+src="data:image\/[^;]+;base64,([^"]+)"[^>]*data\-filename="([^"]+)"[^>]*>/', $summernoteContent, $matches);
            $data = [];
            foreach ($matches[1] as $index => $base64Image) {
                $imageName = time() . '_summernote_image_' . $index . '.png';
                \Storage::put('/tickets/' . $ticket->ticket_id . '/' . $imageName, base64_decode($base64Image));
                $summernoteContent = str_replace($matches[0][$index], '<img src="/storage/tickets/' . $ticket->ticket_id . '/' . $imageName . '">', $summernoteContent);
            }
            $post['description'] = $summernoteContent;

            $data                = [];
            if ($request->hasfile('reply_attachments')) {
                foreach ($request->file('reply_attachments') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->storeAs('/tickets/' . $ticket->ticket_id, $name);
                    $data[] = $name;
                }
            }
            $post['attachments'] = json_encode($data);
            $conversion          = Conversion::create($post);
            $ticket->status = 'In Progress';
            $ticket->update();

            // webhook //
            $module = 'New Ticket Reply';
            $webhook =  Utility::webhookSetting($module, $ticket->created_by);

            if ($webhook) {
                $parameter = json_encode($conversion);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->back()->with('success', __('Reply successfully added!') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            // slack //
            $settings  = Utility::non_auth_settings($ticket->created_by);

            if (isset($settings['reply_notification']) && $settings['reply_notification'] == 1) {
                $uArr = [
                    'name' => $request->name,
                    'ticket_id' => $ticket->id,
                    'email' => $ticket->email,
                    'description' => $request->reply_description,
                    //  'user_name'  => \Auth::user(),
                ];
                Utility::send_slack_msg('new_ticket_reply', $uArr, $ticket->created_by);
            }
            // telegram //
            $settings  = Utility::settings($ticket->created_by);
            if (isset($settings['telegram_reply_notification']) && $settings['telegram_reply_notification'] == 1) {
                $uArr = [
                    'name' => $request->name,
                    'ticket_id' => $ticket->id,
                    'email' => $ticket->email,
                    'description' => $request->reply_description,
                    'user_name'  => $request->name,
                ];
                Utility::send_telegram_msg('new_ticket_reply', $uArr, $ticket->created_by);
            }
            // Send Email to User
            try {


                $users = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')->where('model_has_roles.model_type', '=', 'App\Models\User')->where('role_id', '=', 1)->get();
                Utility::getSMTPDetails(1);

                foreach ($users as $user) {

                    // Mail::to($user->email)->send(new SendTicketReply($user, $ticket, $conversion));
                    Mail::to($ticket->email)->send(new SendTicketReply($user, $ticket, $conversion));
                }
            } catch (\Exception $e) {
                $error_msg = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Reply added successfully') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }
    // public function reply(Request $request, $ticket_id)
    // {
    //     $ticket = Ticket::where('ticket_id', '=', $ticket_id)->first();
    //     if ($ticket) {
    //         $validation = ['reply_description' => ['required']];
    //         if ($request->hasfile('reply_attachments')) {
    //             $validation['reply_attachments.*'] = 'mimes:zip,rar,jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:204800';
    //         }
    //         $this->validate($request, $validation);

    //         $post                = [];
    //         $post['sender']      = 'user';
    //         $post['ticket_id']   = $ticket->id;
    //         //$post['description'] = $request->reply_description;

    //         $summernoteContent = $request->reply_description;

    //         // Extract images and save them
    //         preg_match_all('/<img[^>]+src="data:image\/[^;]+;base64,([^"]+)"[^>]*data\-filename="([^"]+)"[^>]*>/', $summernoteContent, $matches);

    //         $data = [];
    //         foreach ($matches[1] as $index => $base64Image) {
    //             // Create a unique image name
    //             $imageName = time() . '_summernote_image_' . $index . '.png';

    //             // Store the decoded base64 image as a file
    //             \Storage::put('/tickets/' . $ticket->ticket_id . '/' . $imageName, base64_decode($base64Image));

    //             // Replace the base64 string with the actual image URL in the Summernote content
    //             $imageUrl = '/storage/tickets/' . $ticket->ticket_id . '/' . $imageName;
    //             $summernoteContent = str_replace($matches[0][$index], '<img src="' . $imageUrl . '">', $summernoteContent);
    //         }

    //         // Now save $summernoteContent with proper image paths
    //         $post['description'] = $summernoteContent;

    //         $data                = [];
    //         if ($request->hasfile('reply_attachments')) {
    //             foreach ($request->file('reply_attachments') as $file) {
    //                 $name = $file->getClientOriginalName();
    //                 $file->storeAs('/tickets/' . $ticket->ticket_id, $name);
    //                 $data[] = $name;
    //             }
    //         }
    //         $post['attachments'] = json_encode($data);
    //         $conversion          = Conversion::create($post);
    //         $ticket->status = 'In Progress';
    //         $ticket->update();

    //         // webhook //
    //         $module = 'New Ticket Reply';
    //         $webhook =  Utility::webhookSetting($module,$ticket->created_by);

    //         if ($webhook) {
    //             $parameter = json_encode($conversion);
    //             // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
    //             $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
    //             if ($status == true) {
    //                 return redirect()->back()->with('success', __('Reply successfully added!') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
    //             } else {
    //                 return redirect()->back()->with('error', __('Webhook call failed.'));
    //             }
    //         }

    //         // slack //
    //         $settings  = Utility::non_auth_settings($ticket->created_by);

    //         if (isset($settings['reply_notification']) && $settings['reply_notification'] == 1) {
    //             $uArr = [
    //                 'name' => $request->name,
    //                 'ticket_id' => $ticket->id,
    //                 'email' => $ticket->email,
    //                 'description' => $request->reply_description,
    //                 //  'user_name'  => \Auth::user(),
    //             ];
    //             Utility::send_slack_msg('new_ticket_reply', $uArr, $ticket->created_by);
    //         }
    //         // telegram //
    //         $settings  = Utility::settings($ticket->created_by);
    //         if (isset($settings['telegram_reply_notification']) && $settings['telegram_reply_notification'] == 1) {
    //             $uArr = [
    //                 'name' => $request->name,
    //                 'ticket_id' => $ticket->id,
    //                 'email' => $ticket->email,
    //                 'description' => $request->reply_description,
    //                 'user_name'  => $request->name,
    //             ];
    //             Utility::send_telegram_msg('new_ticket_reply', $uArr, $ticket->created_by);
    //         }
    //         // Send Email to User
    //         try {


    //             $users = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')->where('model_has_roles.model_type', '=', 'App\Models\User')->where('role_id', '=', 1)->get();
    //             Utility::getSMTPDetails(1);

    //             foreach ($users as $user) {

    //                 // Mail::to($user->email)->send(new SendTicketReply($user, $ticket, $conversion));
    //                 Mail::to($ticket->email)->send(new SendTicketReply($user, $ticket, $conversion));
    //             }
    //         } catch (\Exception $e) {
    //             $error_msg = __('E-Mail has been not sent due to SMTP configuration');
    //         }

    //         return redirect()->back()->with('success', __('Reply added successfully') . ((isset($error_msg)) ? '<br> <span class="text-danger">' . $error_msg . '</span>' : ''));
    //     } else {
    //         return redirect()->back()->with('error', __('Something is wrong'));
    //     }
    // }
    public function knowledge(Request $request)
    {
        $setting      = Utility::settings();
        if ($setting['Knowlwdge_Base'] == 'on') {
            $knowledges = Knowledge::select('category')->groupBy('category')->get();
            $knowledges_detail = knowledge::get();

            return view('knowledge', compact('knowledges', 'knowledges_detail', 'setting'));
        } else {
            return redirect('/');
        }
    }

    public function knowledgeDescription(Request $request)
    {
        $setting = Utility::settings();
        $descriptions = knowledge::find($request->id);
        return view('knowledgedesc', compact('descriptions','setting'));
    }
}
