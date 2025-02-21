<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomField;
use App\Mail\SendCloseTicket;
use App\Mail\SendTicket;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Utility;
use App\Models\UserCatgory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Exports\TicketsExport;
use App\Models\Api\Category as ApiCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Priority;
use App\Models\SubCategory;

class TicketController extends Controller
{

    public function __construct()
    {
        $this->middleware('2fa');
    }

    public function index(Request $request)
    {
        $user = \Auth::user();
        if ($user->can('manage-tickets')) {
            if (\Auth::user()->parent == 0) {
                $categories = Category::where('created_by', \Auth::user()->createId())->get()->pluck('name', 'id');
                $categories->prepend('Select Category', 'All');

                $subcategories = SubCategory::where('category_id', \Auth::user()->createId())->get()->pluck('subcategory', 'id');
                $subcategories->prepend('Select subcategory', 'All');

                $priorities = Priority::where('created_by', \Auth::user()->createId())->get()->pluck('name', 'id');
                $priorities->prepend('Select Priority', 'All');

                $statues = Ticket::$statues;
                $tickets = Ticket::select(
                    [
                        'tickets.*',
                        'categories.name as category_name',
                        'categories.color',
                        'sub_categories.subcategory as subcategory_name',
                        'sub_categories.color as sub_color',
                        'priorities.color as priorities_color',
                        'priorities.name as priorities_name',
                    ]
                )
                    ->join('categories', 'categories.id', '=', 'tickets.category')
                    ->join('sub_categories', 'sub_categories.id', '=', 'tickets.subcategory')
                    ->join('priorities', 'priorities.id', '=', 'tickets.priority');

                if ($request->category != 'All' && $request->all() != null) {
                    $tickets->where('category', $request->category);
                }

                if ($request->priority != 'All' && $request->all() != null) {
                    $tickets->where('priority', $request->priority);
                }

                if ($request->status != 'All' && $request->all() != null) {
                    $tickets->where('status', $request->status);
                }
                $tickets = $tickets->orderBy('id', 'desc')->get();
                return view('admin.tickets.index', compact('tickets', 'categories', 'priorities', 'statues'));
            } else {
                $categories1 = User::where('id', auth()->user()->id)->pluck('category_id');
                $categoryIds = explode(',', $categories1->first());

                $categories = \DB::table('categories')
                    ->whereIn('id', $categoryIds)
                    ->pluck('name', 'id');
                $categories->prepend('Select Category', 'All');

                $subcategories = \DB::table('sub_categories')
                    ->whereIn('category_id', $categoryIds)
                    ->pluck('subcategory', 'id');
                $subcategories->prepend('Select Subcategory', 'All');

                $priorities = Priority::where('created_by', \Auth::user()->createId())->get()->pluck('name', 'id');
                $priorities->prepend('Select Priority', 'All');

                $statues = Ticket::$statues;

                $tickets = Ticket::select(
                    [
                        'tickets.*',
                        'categories.name as category_name',
                        'categories.color',
                        'priorities.color as priorities_color',
                        'priorities.name as priorities_name',
                        'sub_categories.subcategory as subcategory_name',
                        'sub_categories.color as sub_color',
                    ]
                )->join('categories', 'categories.id', '=', 'tickets.category')
                    ->join('sub_categories', 'sub_categories.id', '=', 'tickets.subcategory')
                    ->join('priorities', 'priorities.id', '=', 'tickets.priority')
                    ->whereIn('category', $categoryIds);

                if ($request->category != 'All' && $request->category != null) {
                    $tickets->where('category', $request->category);
                }

                if ($request->subcategory != 'All' && $request->subcategory != null) {
                    $tickets->where('subcategory', $request->subcategory);
                }

                if ($request->priority != 'All' && $request->priority != null) {
                    $tickets->where('priority', $request->priority);
                }

                if ($request->status != 'All' && $request->status != null) {
                    $tickets->where('status', $request->status);
                }

                $tickets = $tickets->orderBy('id', 'desc')->get();

                return view('admin.tickets.index', compact('tickets', 'categories', 'subcategories', 'priorities', 'statues'));
            }
        } else {
            return view('403');
        }
    }

    public function create()
    {
        $user = \Auth::user();
        if ($user->can('create-tickets')) {
            $customFields = CustomField::where('id', '>', '8')->get();

            $categories = Category::where('created_by', \Auth::user()->createId())->get();



            $priorities = Priority::where('created_by', \Auth::user()->createId())->get();


            return view('admin.tickets.create', compact('categories', 'customFields', 'priorities'));
        } else {
            return view('403');
        }
    }

    public function getsubcategory(Request $request)
    {
        $subcategory = SubCategory::where('category_id', '=', $request->category_id)->get();

        return json_encode($subcategory);
    }


    public function store(Request $request)
    {
        $user = \Auth::user();
        if ($user->can('create-tickets')) {
            $validation = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'category' => 'required|string|max:255',
                'subcategory' => 'required|string|max:255',
                'priority' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'status' => 'required|string|max:100',
                'description' => 'required',
                'priority' => 'required|string|max:255',
            ];

            $this->validate($request, $validation);

            $post              = $request->all();
            $post['ticket_id'] = time();
            $post['created_by'] = \Auth::user()->createId();
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
                    }
                }
            }
            $post['attachments'] = json_encode($data);
            $ticket              = Ticket::create($post);

            CustomField::saveData($ticket, $request->customField);

            // slack //

            $settings  = Utility::settings(\Auth::user()->createId());
            if (isset($settings['ticket_notification']) && $settings['ticket_notification'] == 1) {
                $uArr = [
                    'name' => $request->name,
                    'email' => $user->email,
                    'category' => $request->category,
                    'subject' => $request->subject,
                    'status' => $request->status,
                    'description' => $request->description,
                    'user_name'  => \Auth::user()->name,
                ];
                Utility::send_slack_msg('new_ticket', $uArr);
            }

            // telegram //
            $settings  = Utility::settings(\Auth::user()->createId());
            if (isset($settings['telegram_ticket_notification']) && $settings['telegram_ticket_notification'] == 1) {
                $uArr = [
                    'name' => $request->name,
                    'email' => $user->email,
                    'category' => $request->category,
                    'subject' => $request->subject,
                    'status' => $request->status,
                    'description' => $request->description,
                    'user_name'  => \Auth::user()->name,
                ];
                Utility::send_telegram_msg('new_ticket', $uArr);
            }
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

            //Mail Send Agent
            $userids = User::where('category_id', $request->category)->pluck('id');

            $agents = User::whereIn('id', $userids)->get();
            
            foreach ($agents as $agent) {
                Utility::sendEmailTemplate('new_ticket', [$agent->email], $uArr, \Auth::user());
            }

            // Mail Send  Ticket User
            Utility::sendEmailTemplate('new_ticket', [$request->email], $uArr, \Auth::user());

            //Mail Send Auth User
            Utility::sendEmailTemplate('new_ticket', [\Auth::user()->email], $uArr, \Auth::user());

            //Mail send to Admin
            $adminUser = User::where('parent',0)->first();
            Mail::to($adminUser->email)->send(new SendTicket($ticket));

            // Send Email to
            if (isset($error_msg)) {
                Session::put('smtp_error', '<span class="text-danger ml-2">' . $error_msg . '</span>');
            }
            Session::put('ticket_id', ' <a class="text text-primary" target="_blank" href="' . route('home.view', \Illuminate\Support\Facades\Crypt::encrypt($ticket->ticket_id)) . '"><b>' . __('Your unique ticket link is this.') . '</b></a>');

            $module = 'New Ticket';
            $webhook =  Utility::webhookSetting($module, $user->created_by);

            if ($webhook) {
                $parameter = json_encode($ticket);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->back()->with('success', __('ticket successfully created!'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }
            return redirect()->route('admin.tickets.index')->with('success', __('Ticket created successfully'));
        } else {
            return view('403');
        }
    }

    public function storeNote($ticketID, Request $request)
    {
        $user = \Auth::user();
        if ($user->can('reply-tickets')) {
            $validation = [
                'note' => ['required'],
            ];
            $this->validate($request, $validation);

            $ticket = Ticket::find($ticketID);
            if ($ticket) {
                $ticket->note = $request->note;
                $ticket->save();

                return redirect()->back()->with('success', __('Ticket note saved successfully'));
            } else {
                return view('403');
            }
        } else {
            return view('403');
        }
    }


    public function editTicket($id)
    {
        $user = \Auth::user();
        if ($user->can('edit-tickets')) {
            $ticket = Ticket::find($id);
            if ($ticket) {

                $customFields = CustomField::where('id', '>', '8')->get();
                $ticket->customField = CustomField::getData($ticket);
                $categories = Category::where('created_by', \Auth::user()->createId())->get();
                $priorities = Priority::where('created_by', \Auth::user()->createId())->get();

                $subcategories = SubCategory::where('category_id', $ticket->category)->get();

                return view('admin.tickets.edit', compact('ticket', 'categories', 'subcategories', 'customFields', 'priorities'));
            } else {
                return view('403');
            }
        } else {
            return view('403');
        }
    }

    public function updateTicket(Request $request, $id)
    {
        $user = \Auth::user();
        if ($user->can('edit-tickets')) {
            $ticket = Ticket::find($id);
            if ($ticket) {
                $validation = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'category' => 'required|string|max:255',
                    'subcategory' => 'required|string|max:255',
                    'priority' => 'required|string|max:255',
                    'subject' => 'required|string|max:255',
                    'status' => 'required|string|max:100',
                    'description' => 'required',
                ];

                $this->validate($request, $validation);

                $post = $request->all();
                $post['created_by'] = \Auth::user()->createId();
                if ($request->hasfile('attachments')) {
                    $data = json_decode($ticket->attachments, true);
                    foreach ($request->file('attachments') as $filekey => $file) {
                        $name = $file->getClientOriginalName();
                        $file->storeAs('tickets/' . $ticket->ticket_id, $name);
                        $data[] = $name;
                        $url = '';
                        $dir        = ('tickets/' . $ticket->ticket_id);
                        $path = Utility::multipalFileUpload($request, 'attachments', $name, $dir, $filekey, []);
                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->route('admin.tickets.store', \Auth::user()->id)->with('error', __($path['msg']));
                        }
                    }
                    $post['attachments'] = json_encode($data);
                }
                if ($request->status == 'Resolved') {
                    $ticket->reslove_at = now();
                }
                $ticket->update($post);
                CustomField::saveData($ticket, $request->customField);

                $error_msg = '';
                Utility::getSMTPDetails($user->id);

                if ($ticket->status == 'Closed') {
                    // Send Email to User
                    try {
                        Mail::to($ticket->email)->send(new SendCloseTicket($ticket));
                    } catch (\Exception $e) {
                        $error_msg = "E-Mail has been not sent due to SMTP configuration ";
                    }
                }

                return redirect()->back()->with('success', __('Ticket updated successfully.') . ((isset($error_msg) && !empty($error_msg)) ? '<span class="text-danger">' . $error_msg . '</span>' : ''));
            } else {
                return view('403');
            }
        } else {
            return view('403');
        }
    }

    public function destroy($id)
    {
        $user = \Auth::user();
        if ($user->can('edit-tickets')) {
            $ticket = Ticket::find($id);
            $ticket->delete();

            return redirect()->back()->with('success', __('Ticket deleted successfully'));
        } else {
            return view('403');
        }
    }

    public function attachmentDestroy($ticket_id, $id)
    {
        $user = \Auth::user();
        if ($user->can('edit-tickets')) {
            $ticket      = Ticket::find($ticket_id);
            $attachments = json_decode($ticket->attachments);
            if (isset($attachments[$id])) {
                if (asset(Storage::exists('tickets/' . $ticket->ticket_id . "/" . $attachments[$id]))) {
                    asset(Storage::delete('tickets/' . $ticket->ticket_id . "/" . $attachments[$id]));
                }
                unset($attachments[$id]);
                $ticket->attachments = json_encode(array_values($attachments));
                $ticket->save();

                return redirect()->back()->with('success', __('Attachment deleted successfully'));
            } else {
                return redirect()->back()->with('error', __('Attachment is missing'));
            }
        } else {
            return view('403');
        }
    }

    public function export()
    {
        $name = 'Tickets' . date('Y-m-d i:h:s');
        $data = Excel::download(new TicketsExport(), $name . '.csv');

        return $data;
    }
}
