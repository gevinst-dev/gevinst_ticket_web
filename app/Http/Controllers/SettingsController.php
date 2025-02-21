<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Mail\EmailTest;
use App\Models\Setting;
use App\Models\Settings;
use App\Models\Utility;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            // $lang         = $user->languages();
            $customFields = CustomField::orderBy('order')->get();
            $setting      = Utility::settings();
            $webhooks = Webhook::where('created_by', \Auth::user()->id)->get();
            $timezones               = config('timezones');
            return view('admin.users.setting', compact('customFields', 'setting','webhooks','timezones'));
        }
        else
        {
            return view('403');
        }

    }

    public function store(Request $request)
    {
        $user = \Auth::user();
        $post = [];
        if($user->can('manage-setting'))
        {
            if($request->favicon)
            {

                $request->validate(
                    [
                        'favicon' => 'image',
                        ]
                    );
                $favicon = 'favicon.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'favicon',$favicon,$dir,$validation);
                if($path['flag'] == 1){
                    $favicon = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }
            if(!empty($request->logo))
            {

                $request->validate(
                    [
                        'logo' => 'image',
                        ]
                    );
                $logoName = 'logo-dark.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'logo',$logoName,$dir,$validation);
                if($path['flag'] == 1){
                    $logo = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            if($request->white_logo)
            {

                $request->validate(
                    [
                        'white_logo' => 'image',
                        ]
                );
                $lightlogoName = 'logo-light.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'white_logo',$lightlogoName,$dir,$validation);

                if($path['flag'] == 1){
                    $white_logo = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $rules = [
                'APP_NAME' => 'required|string|max:50',
                'default_language' => 'required|string|max:50',
                'footer_text' => 'required|string|max:50',
            ];

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $arrEnv = [
                'APP_NAME' => $request->APP_NAME,
            ];

            $this->setEnvironmentValue($arrEnv);
            $default_language = $request->has('default_language') ? $request-> default_language : 'en';
            $post['DEFAULT_LANG'] = $default_language;
            $site_rtl = $request->has('site_rtl') ? $request->site_rtl : 'off';
            $post['SITE_RTL'] = $site_rtl;
            $post['SITE_RTL'] = $request->has('site_rtl') && $site_rtl == "on" ? $request-> site_rtl : 'off';

            $footer_text = $request->has('footer_text') ? $request-> footer_text : '';
            $post['footer_text'] = $footer_text;
 
            $faq = $request->has('faq') ? $request-> faq : 'off';
            $post['FAQ'] = $faq;
            $post['FAQ'] = $request->has('faq') && $faq == "on" ? $request-> faq : '';


            $knowledge_base = $request->has('knowledge') ? $request-> knowledge : 'off';
            $post['Knowlwdge_Base'] = $knowledge_base;

            if (isset($request->color_flag) && $request->color_flag == 'false' ) {
                $post['color'] = $request->color;
            } else {
                $post['color'] = $request->custom_color;
            }
            $post['color_flag'] = $request->color_flag;


            $cust_theme_bg = (!empty($request->cust_theme_bg)) ? 'on' : 'off';
            $post['cust_theme_bg'] = $cust_theme_bg;

            $cust_darklayout = !empty($request->cust_darklayout) ? 'on' : 'off';
            $post['cust_darklayout'] = $cust_darklayout;


            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach ($post as $key => $data) {
                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                        [$data, $key, Auth::user()->id, $created_at, $updated_at,]
                    );
                }
            }
            return redirect()->back()->with('success', __('Setting updated successfully'));

            Artisan::call('config:cache');
	        Artisan::call('config:clear');

        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function emailSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post = $request->all();

            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {

                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                    $data,
                                                                                                                                                                                                                    $key,
                                                                                                                                                                                                                    \Auth::user()->createId(),
                                                                                                                                                                                                                    $created_at,
                                                                                                                                                                                                                    $updated_at,
                                                                                                                                                                                                        ]
                );
            }
            return redirect()->back()->with('success', __('Email Settings updated successfully'));

            Artisan::call('config:cache');
	        Artisan::call('config:clear');


        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }



    public function recaptchaSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [];

            if($request->recaptcha_module == 'yes')
            {
                $rules['google_recaptcha_key'] = 'required|string|max:50';
                $rules['google_recaptcha_secret'] = 'required|string|max:50';
                $rules['google_recaptcha_version'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $post = [];

            // $enable_recaptcha = (!empty($request->recaptcha_module)) ? 'yes' : 'no';
            // $post['RECAPTCHA_MODULE'] = $enable_recaptcha;

            // $google_recaptcha_key = $request->has('google_recaptcha_key') ? $request->google_recaptcha_key : '';
            // $post['NOCAPTCHA_SITEKEY'] = $google_recaptcha_key;

            // $google_recaptcha_secret = $request->has('google_recaptcha_secret') ? $request-> google_recaptcha_secret : '';
            // $post['NOCAPTCHA_SECRET'] = $google_recaptcha_secret;

            // // ---------------------------------------------

            // $google_recaptcha_version = $request->has('google_recaptcha_version') ? $request->google_recaptcha_version : '';
            // $post['google_recaptcha_version'] = $google_recaptcha_version;

            $enable_recaptcha = (!empty($request->recaptcha_module)) ? 'yes' : 'no';
            $post['RECAPTCHA_MODULE'] = $enable_recaptcha;

            $google_recaptcha_key = $request->has('google_recaptcha_key') ? $request->google_recaptcha_key : '';
            $post['NOCAPTCHA_SITEKEY'] = $google_recaptcha_key;

            $google_recaptcha_secret = $request->has('google_recaptcha_secret') ? $request->google_recaptcha_secret : '';
            $post['NOCAPTCHA_SECRET'] = $google_recaptcha_secret;

            $google_recaptcha_version = $request->has('google_recaptcha_version') ? $request->google_recaptcha_version : '';
            $post['google_recaptcha_version'] = $google_recaptcha_version;
            // ------------------------



            if(isset($post) && !empty($post) && count($post) > 0)
            {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach($post as $key => $data)
                {

                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [$data, $key, Auth::user()->id, $created_at, $updated_at, ]
                    );
                }
            }
            return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }
    public function pusherSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [];

            if($request->enable_chat == 'yes')
            {
                $rules['pusher_app_id']      = 'required|string|max:50';
                $rules['pusher_app_key']     = 'required|string|max:50';
                $rules['pusher_app_secret']  = 'required|string|max:50';
                $rules['pusher_app_cluster'] = 'required|string|max:50';
            }

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $post = [];

            $enable_chat = (!empty($request->enable_chat)) ? 'yes' : 'no';
            $post['CHAT_MODULE'] = $enable_chat;

            $pusher_app_id = $request->has('pusher_app_id') ? $request-> pusher_app_id : '';
            $post['PUSHER_APP_ID'] = $pusher_app_id;

            $pusher_app_key = $request->has('pusher_app_key') ? $request-> pusher_app_key : '';
            $post['PUSHER_APP_KEY'] = $pusher_app_key;

            $pusher_app_secret = $request->has('pusher_app_secret') ? $request-> pusher_app_secret : '';
            $post['PUSHER_APP_SECRET'] = $pusher_app_secret;

            $pusher_app_cluster = $request->has('pusher_app_cluster') ? $request-> pusher_app_cluster : '';
            $post['PUSHER_APP_CLUSTER'] = $pusher_app_cluster;


            if(isset($post) && !empty($post) && count($post) > 0)
            {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach($post as $key => $data)
                {

                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [$data, $key, Auth::user()->id, $created_at, $updated_at, ]
                    );
                }
            }
            return redirect()->back()->with('success', __('Pusher Settings updated successfully'));
        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }



    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";

        return file_put_contents($envFile, $str) ? true : false;
    }

    public function testEmail(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $data                      = [];
            $data['mail_driver']       = $request->mail_driver;
            $data['mail_host']         = $request->mail_host;
            $data['mail_port']         = $request->mail_port;
            $data['mail_username']     = $request->mail_username;
            $data['mail_password']     = $request->mail_password;
            $data['mail_encryption']   = $request->mail_encryption;
            $data['mail_from_address'] = $request->mail_from_address;
            $data['mail_from_name']    = $request->mail_from_name;

            return view('admin.users.test_email', compact('data'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'email' => 'required|email',
                               'mail_driver' => 'required',
                               'mail_host' => 'required',
                               'mail_port' => 'required',
                               'mail_username' => 'required',
                               'mail_password' => 'required',
                               'mail_from_address' => 'required',
                               'mail_from_name' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try
        {
            config(
                [
                    'mail.driver' => $request->mail_driver,
                    'mail.host' => $request->mail_host,
                    'mail.port' => $request->mail_port,
                    'mail.encryption' => $request->mail_encryption,
                    'mail.username' => $request->mail_username,
                    'mail.password' => $request->mail_password,
                    'mail.from.address' => $request->mail_from_address,
                    'mail.from.name' => $request->mail_from_name,
                ]
            );
            Mail::to($request->email)->send(new EmailTest());
        }
        catch(\Exception $e)
        {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return response()->json(
            [
                'is_success' => true,
                'message' => __('Email send Successfully'),
            ]
        );
    }


    public function storeCustomFields(Request $request)
    {
        $rules      = [
            'fields' => 'required|present|array',
        ];
        $attributes = [];

        if($request->fields)
        {
            foreach($request->fields as $key => $val)
            {
                $rules['fields.' . $key . '.name']      = 'required|max:255';
                $attributes['fields.' . $key . '.name'] = __('Field Name');
            }
        }

        $validator = \Validator::make($request->all(), $rules, [], $attributes);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $field_ids = CustomField::orderBy('order')->pluck('id')->toArray();

        $order = 0;

        foreach($request->fields as $key => $field)
        {
            $fieldObj = new CustomField();
            if(isset($field['id']) && !empty($field['id']))
            {
                $fieldObj = CustomField::find($field['id']);
                if(($key = array_search($fieldObj->id, $field_ids)) !== false)
                {
                    unset($field_ids[$key]);
                }
            }
            $fieldObj->name        = $field['name'];
            $fieldObj->placeholder = $field['placeholder'];
            if(isset($field['type']) && !empty($field['type']))
            {
                if(isset($fieldObj->id) && $fieldObj->id > 6)
                {
                    $fieldObj->type = $field['type'];
                }
                elseif(!isset($fieldObj->id))
                {
                    $fieldObj->type = $field['type'];
                }
            }
            $fieldObj->width  = (isset($field['width'])) ? $field['width'] : '12';
            $fieldObj->status = 1;
            if(isset($field['is_required']))
            {
                if(isset($fieldObj->id) && $fieldObj->id > 6)
                {
                    $fieldObj->is_required = $field['is_required'];
                }
                elseif(!isset($fieldObj->id))
                {
                    $fieldObj->is_required = $field['is_required'];
                }
            }
            $fieldObj->created_by = Auth::id();
            $fieldObj->order      = $order++;
            $fieldObj->save();
        }

        if(!empty($field_ids) && count($field_ids) > 0)
        {
            CustomField::whereIn('id', $field_ids)->where('status', 1)->delete();
        }

        return redirect()->back()->with('success', __('Fields Saves Successfully.!'));
    }


    public function saveCompanySettings(Request $request)
    {
        if(\Auth::user()->can('manage-company-settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'company_name' => 'required|string|max:50',
                ]
            );

            // $arrEnv = [
            //     'APP_TIMEZONE' => $request->timezone,
            // ];
            // $this->setEnvironmentValue($arrEnv);

            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->createId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storageSettingStore(Request $request)
    {

        if(isset($request->storage_setting) && $request->storage_setting == 'local')
        {
            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;

        }

        if(isset($request->storage_setting) && $request->storage_setting == 's3')
        {
            $request->validate(
                [
                    's3_key'                  => 'required',
                    's3_secret'               => 'required',
                    's3_region'               => 'required',
                    's3_bucket'               => 'required',
                    's3_url'                  => 'required',
                    's3_endpoint'             => 'required',
                    's3_max_upload_size'      => 'required',
                    's3_storage_validation'   => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['s3_key']                     = $request->s3_key;
            $post['s3_secret']                  = $request->s3_secret;
            $post['s3_region']                  = $request->s3_region;
            $post['s3_bucket']                  = $request->s3_bucket;
            $post['s3_url']                     = $request->s3_url;
            $post['s3_endpoint']                = $request->s3_endpoint;
            $post['s3_max_upload_size']         = $request->s3_max_upload_size;
            $s3_storage_validation              = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation']      = $s3_storage_validation;
        }

        if(isset($request->storage_setting) && $request->storage_setting == 'wasabi')
        {
            $request->validate(
                [
                    'wasabi_key'                    => 'required',
                    'wasabi_secret'                 => 'required',
                    'wasabi_region'                 => 'required',
                    'wasabi_bucket'                 => 'required',
                    'wasabi_url'                    => 'required',
                    'wasabi_root'                   => 'required',
                    'wasabi_max_upload_size'        => 'required',
                    'wasabi_storage_validation'     => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['wasabi_key']                 = $request->wasabi_key;
            $post['wasabi_secret']              = $request->wasabi_secret;
            $post['wasabi_region']              = $request->wasabi_region;
            $post['wasabi_bucket']              = $request->wasabi_bucket;
            $post['wasabi_url']                 = $request->wasabi_url;
            $post['wasabi_root']                = $request->wasabi_root;
            $post['wasabi_max_upload_size']     = $request->wasabi_max_upload_size;
            $wasabi_storage_validation          = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation']  = $wasabi_storage_validation;
        }

        foreach($post as $key => $data)
        {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');

    }


    public function saveSEOSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'meta_keywords' => 'required',
                'meta_description' => 'required',
                'meta_image' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        if ($request->meta_image) {
            $img_name = time() . '_' . 'meta_image.png';
            $dir = 'uploads/metaevent/';
            $validation = [
                'max:' . '20480',
            ];
            $path = Utility::upload_file($request, 'meta_image', $img_name, $dir, $validation);
            if ($path['flag'] == 1) {
                $logo_dark = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['meta_image']  = $img_name;
        }
        $post['meta_keywords']            = $request->meta_keywords;
        $post['meta_description']            = $request->meta_description;
        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->id,
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s'),
                ]
            );
        }
        return redirect()->back()->with('success', 'SEO setting successfully updated.');
    }



    public function slack(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'slack_webhook' => 'required',



            ]
        );

       $post = [];
       $post['slack_webhook'] = $request->input('slack_webhook');
       $post['user_notification'] = $request->has('user_notification')?$request->input('user_notification'):0;
       $post['ticket_notification'] = $request->has('ticket_notification')?$request->input('ticket_notification'):0;
       $post['reply_notification'] = $request->has('reply_notification')?$request->input('reply_notification'):0;

       if(isset($post) && !empty($post) && count($post) > 0)
       {
           $created_at = $updated_at = date('Y-m-d H:i:s');


           foreach($post as $key => $data)
           {
               \DB::insert(
                   'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                   [
                       $data,
                       $key,
                       \Auth::user()->id,
                       date('Y-m-d H:i:s'),
                       date('Y-m-d H:i:s'),
                   ]
               );
           }
      }
      return redirect()->back()->with('success', 'Settings updated successfully.');
   }

    public function telegram(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'telegram_accestoken' => 'required',
                'telegram_chatid' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

       $post = [];
       $post['telegram_accestoken'] = $request->input('telegram_accestoken');
       $post['telegram_chatid'] = $request->input('telegram_chatid');
       $post['telegram_user_notification'] = $request->has('telegram_user_notification')?$request->input('telegram_user_notification'):0;
       $post['telegram_ticket_notification'] = $request->has('telegram_ticket_notification')?$request->input('telegram_ticket_notification'):0;
       $post['telegram_reply_notification'] = $request->has('telegram_reply_notification')?$request->input('telegram_reply_notification'):0;


       if(isset($post) && !empty($post) && count($post) > 0)
       {
           $created_at = $updated_at = date('Y-m-d H:i:s');

           foreach($post as $key => $data)
           {
               \DB::insert(
                   'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                   [
                       $data,
                       $key,
                       \Auth::user()->id,
                       date('Y-m-d H:i:s'),
                       date('Y-m-d H:i:s'),
                   ]
               );
           }
       }

       return redirect()->back()->with('success', __('Settings updated successfully.'));
    }



    public function chatgptkey(Request $request)
    {
       if (\Auth::user()->parent == 0) {
           $user = \Auth::user();

           if (isset($request->is_enabled) && $request->is_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'chatgpt_key' => 'required',
                    'chat_gpt_model' => 'required',

                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_enabled'] = $request->is_enabled;
            $post['chatgpt_key'] = $request->chatgpt_key;
            $post['chat_gpt_model'] = $request->chat_gpt_model;
                } else {
                    $post['is_enabled'] = 'off';
                }

               unset($post['_token']);

               $created_at = date('Y-m-d H:i:s');
               $updated_at = date('Y-m-d H:i:s');

               foreach($post as $key => $data)
               {
                   \DB::insert(
                       'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                       $data,
                                                                                                                                                                                                                       $key,
                                                                                                                                                                                                                       \Auth::user()->createId(),
                                                                                                                                                                                                                       $created_at,
                                                                                                                                                                                                                       $updated_at,
                                                                                                                                                                                                           ]
                   );
               }
           return redirect()->back()->with('success', __('Chatgpykey successfully saved.'));
       } else {
           return redirect()->back()->with('error', __('Permission denied.'));
       }
    }


    public function saveCookieSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_description' => 'required',
                'contactus_url' => 'required',
            ]
        );
        $post = $request->all();
        unset($post['_token']);
        if ($request->enable_cookie) {
            $post['enable_cookie'] = 'on';
        } else {
            $post['enable_cookie'] = 'off';
        }
        if ($request->cookie_logging) {
            $post['cookie_logging'] = 'on';
        } else {
            $post['cookie_logging'] = 'off';
        }
        $post['cookie_title']            = $request->cookie_title;
        $post['cookie_description']            = $request->cookie_description;
        $post['strictly_cookie_title']            = $request->strictly_cookie_title;
        $post['strictly_cookie_description']            = $request->strictly_cookie_description;
        $post['more_information_description']            = $request->more_information_description;
        $post['contactus_url']            = $request->contactus_url;
        $settings = Utility::settings();
        foreach ($post as $key => $data) {
            if (in_array($key, array_keys($settings))) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->id,
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }
        return redirect()->back()->with('success', 'Cookie setting successfully saved.');
    }

    public function CookieConsent(Request $request)
    {
        $settings = Utility::settings();
        if ($request['cookie']) {
            if ($settings['enable_cookie'] == "on" && $settings['cookie_logging'] == "on") {
                $allowed_levels = ['necessary', 'analytics', 'targeting'];
                $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                    return in_array($level, $allowed_levels);
                });
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                // Generate new CSV line
                $browser_name = $whichbrowser->browser->name ?? null;
                $os_name = $whichbrowser->os->name ?? null;
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                $ip = '49.36.83.154';
                // $ip = $_SERVER['REMOTE_ADDR']; // your ip address here
                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                $date = (new \DateTime())->format('Y-m-d');
                $time = (new \DateTime())->format('H:i:s') . ' UTC';
                $new_line = implode(',', [$ip, $date, $time, json_encode($request['cookie']), $device_type, $browser_language, $browser_name, $os_name, isset($query) ? $query['country'] : '', isset($query) ? $query['region'] : '', isset($query) ? $query['regionName'] : '', isset($query) ? $query['city'] : '', isset($query) ? $query['zip'] : '', isset($query) ? $query['lat'] : '', isset($query) ? $query['lon'] : '']);
                if (!file_exists(storage_path() . '/uploads/sample/data.csv')) {
                    $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name';
                    file_put_contents(storage_path() . '/uploads/sample/data.csv', $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
                file_put_contents(storage_path() . '/uploads/sample/data.csv', $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);
                return response()->json('success');
            }
            return response()->json('error');
        } else {
            return redirect()->back();
        }
    }
}

function get_device_type($user_agent)
{
    $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
    $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
    if (preg_match_all($mobile_regex, $user_agent)) {
        return 'mobile';
    } else {
        if (preg_match_all($tablet_regex, $user_agent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }
}
