<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FaqKnwlController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\KnowledgebaseCategoryController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailTemplateLangController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\NotificationTemplatesController;
use App\Http\Controllers\AiTemplateController;
use App\Http\Controllers\Api\TicketController as ApiTicketController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\GoogleAuthenticationController;
use App\Http\Controllers\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


require __DIR__.'/auth.php';



Route::any('/cookie-consent', [SettingsController::class,'CookieConsent'])->name('cookie-consent');

Route::controller(HomeController::class)->group(function(){

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('home', 'store')->name('home.store');
    Route::get('search/{lang?}', 'search')->name('search');
    Route::post('search', 'ticketSearch')->name('ticket.search');
    Route::get('tickets/{id}', 'view')->name('home.view');
    Route::post('ticket/{id}', 'reply')->name('home.reply');
    Route::get('faq', 'faq')->name('faq');
    Route::get('knowledge', 'knowledge')->name('knowledge');
    Route::get('knowledgedesc', 'knowledgeDescription')->name('knowledgedesc');

});

// ----------------------------------------------------------------------------------------------------------------------------------

// 2FA Google Authenticated

Route::group(['middleware' => ['web', 'auth', 'verified']], function () {
    Route::post('/generateSecret', [GoogleAuthenticationController::class,'generate2faSecret'])->name('generate2faSecret');
    Route::post('/enable2fa', [GoogleAuthenticationController::class,'enable2fa'])->name('enable2fa');
    Route::post('/disable2fa', [GoogleAuthenticationController::class,'disable2fa'])->name('disable2fa');
});

Route::middleware(['web'])->group(function ()
{
    Route::post('/2faVerify', function () {
        return redirect(request()->get('2fa_referrer'));
    })->name('2faVerify')->middleware('2fa');

});

// ---------------------------------------------------------------------------------------------------------------------------------

Route::get('getsub', [HomeController::class, 'getsub'])->name('get.ticket.sub');



Route::post('disable-language',[LanguageController::class,'disableLang'])->name('disablelanguage')->middleware(['auth','XSS']);

Route::name('admin.')->prefix('admin')->middleware(['auth'])->group(function() {

    // Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::get('dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Route::resource('users', 'UserController', ['names' => ['index' => 'users' ]]);
    Route::resource('users', UserController::class, ['names' => ['index' => 'users']]);

    Route::get('lang/clear', [LanguageController::class, 'clear'])->name('lang.clear');
    Route::get('lang/create', [LanguageController::class, 'create'])->name('lang.create');
    Route::post('lang/create', [LanguageController::class, 'store'])->name('lang.store');
    Route::get('lang/{lang}', [LanguageController::class, 'manageLanguage'])->name('lang.index');
    Route::post('lang/{lang}', [LanguageController::class, 'storeData'])->name('lang.store.data');
    Route::get('lang/change/{lang}', [LanguageController::class, 'update'])->name('lang.update');
    Route::delete('lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');

    Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category', [CategoryController::class, 'index'])->name('category');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/{id}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('category/{id}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');

// ---------------------------------------------------------------------------------------------------------------------
    // subcategory
    Route::get('subcategory', [SubCategoryController::class, 'index'])->name('subcategory');
    Route::get('subcategory/create', [SubCategoryController::class, 'create'])->name('subcategory.create');
    Route::post('subcategory/store', [SubCategoryController::class, 'store'])->name('subcategory.store');
    Route::delete('subcategory/{id}/destroy', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');

    Route::get('subcategory/{id}/edit', [SubCategoryController::class, 'edit'])->name('subcategory.edit');

    Route::put('subcategory/{id}/update', [SubCategoryController::class, 'update'])->name('subcategory.update');

// ---------------------------------------------------------------------------------------------------------------------
    // Message Route

    Route::get('chat', [MessageController::class, 'index'])->name('chats');
    Route::get('message/{id}', [MessageController::class, 'getMessage'])->name('message');
    Route::delete('delete-user-message/{id}', [MessageController::class, 'deleteUserMessage'])->name('delete.user.message');
    Route::post('message', [MessageController::class, 'sendMessage']);

    // End Message Route
    Route::post('/custom-fields', [SettingsController::class, 'storeCustomFields'])->name('custom-fields.store');
});

Route::any('users-reset-password/{id}', [UserController::class, 'userPassword'])->name('user.reset');
Route::post('users-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');
Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');


Route::name('admin.')->prefix('admin')->middleware(['auth','XSS'])->group(function() {

    Route::get('ticket/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('ticket', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('ticket', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('ticket/{id}/edit', [TicketController::class, 'editTicket'])->name('tickets.edit');
    Route::delete('ticket/{id}/destroy', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::delete('ticket-attachment/{tid}/destroy/{id}', [TicketController::class, 'attachmentDestroy'])->name('tickets.attachment.destroy');
    Route::put('ticket/{id}/update', [TicketController::class, 'updateTicket'])->name('tickets.update');

    Route::get('faq/create', [FaqController::class, 'create'])->name('faq.create');
    Route::post('faq', [FaqController::class, 'store'])->name('faq.store');
    Route::get('faq', [FaqController::class, 'index'])->name('faq');
    Route::get('faq/{id}/edit', [FaqController::class, 'edit'])->name('faq.edit');
    Route::get('faq/show/{id}',[FaqController::class,'show'])->name('show.faq');
    Route::delete('faq/{id}/destroy', [FaqController::class, 'destroy'])->name('faq.destroy');
    Route::post('faq/{id}/update', [FaqController::class, 'update'])->name('faq.update');

    Route::post('ticket/{id}/conversion', [ConversionController::class, 'store'])->name('conversion.store');

    Route::post('ticket/{id}/note', [TicketController::class, 'storeNote'])->name('note.store');

    //---------------------------------------------------------------------------------------------------------------------------------

    Route::resource('priority', PriorityController::class)->middleware('auth','XSS');

    Route::resource('webhook', WebhookController::class);

    Route::get('knowledge', [KnowledgeController::class, 'index'])->name('knowledge');
    Route::get('knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
    Route::post('knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
    Route::get('knowledge/{id}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
    Route::delete('knowledge/{id}/destroy', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    Route::post('knowledge/{id}/update', [KnowledgeController::class, 'update'])->name('knowledge.update');
    Route::get('knowledge/show/{id}',[KnowledgeController::class,'show'])->name('show.knowledgebase');

    Route::get('knowledgecategory', [KnowledgebaseCategoryController::class, 'index'])->name('knowledgecategory');
    Route::get('knowledgecategory/create', [KnowledgebaseCategoryController::class, 'create'])->name('knowledgecategory.create');
    Route::post('knowledgecategory', [KnowledgebaseCategoryController::class, 'store'])->name('knowledgecategory.store');
    Route::get('knowledgecategory/{id}/edit', [KnowledgebaseCategoryController::class, 'edit'])->name('knowledgecategory.edit');
    Route::delete('knowledgecategory/{id}/destroy', [KnowledgebaseCategoryController::class, 'destroy'])->name('knowledgecategory.destroy');
    Route::post('knowledgecategory/{id}/update', [KnowledgebaseCategoryController::class, 'update'])->name('knowledgecategory.update');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/email-settings', [SettingsController::class, 'emailSettingStore'])->name('email.settings.store');
    Route::post('/payment-settings', [SettingsController::class, 'paymentSettingStore'])->name('payment.settings.store');
    Route::post('/pusher-settings', [SettingsController::class, 'pusherSettingStore'])->name('pusher.settings.store');
    Route::post('/recaptcha-settings', [SettingsController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store');
    Route::post('/test', [SettingsController::class, 'testEmail'])->name('test.email');
    Route::post('/test/send', [SettingsController::class, 'testEmailSend'])->name('test.email.send');


});

    Route::get('get-subcategory', [TicketController::class, 'getsubcategory'])->name('get.ticket.subcategory')->middleware(['auth', 'XSS']);


Route::post('storage-settings', [SettingsController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth','XSS']);
Route::post('company-settings', [SettingsController::class, 'saveCompanySettings'])->name('company.settings');
Route::any('domain-setting',[SettingsController::class,'savedomainSettings'])->name('domain.setting');
Route::get('get_message', [MessageController::class, 'getFloatingMessage'])->name('get_message')->middleware(['XSS']);
Route::post('message_form', [MessageController::class, 'store'])->name('chat_form.store')->middleware(['XSS']);
Route::post('floating_message', [MessageController::class, 'sendFloatingMessage'])->name('floating_message')->middleware(['XSS']);
Route::post('setting/seo', [SettingsController::class, 'saveSEOSettings'])->name('seo.settings');
Route::post('cookie-setting', [SettingsController::class, 'saveCookieSettings'])->name('cookie.setting');


Route::post('chatgptkey-setting', [SettingsController::class, 'chatgptkey'])->name('settings.chatgptkey');

Route::get('generate/{template_name}',[AiTemplateController::class,'create'])->name('generate')->middleware(['XSS','auth']);

Route::post('generate/keywords/{id}',[AiTemplateController::class,'getKeywords'])->name('generate.keywords')->middleware(['XSS','auth']);

Route::post('generate/response',[AiTemplateController::class,'AiGenerate'])->name('generate.response');

Route::get('grammar/{template}',[AiTemplateController::class,'grammar'])->name('grammar');

Route::post('grammar/response',[AiTemplateController::class,'grammarProcess'])->name('grammar.response');

//==================================== Slack ====================================//
Route::any('setting/slack', [SettingsController::class, 'slack'])->name('slack.setting');

//==================================== Telegram ====================================//
Route::any('setting/telegram', [SettingsController::class, 'telegram'])->name('telegram.setting');


//====================================  User Log ====================================//
Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS']);
Route::post('/profile/{id}', [UserController::class, 'editprofile'])->name('update.profile')->middleware(['auth', 'XSS']);
Route::get('/user-log', [UserController::class, 'userlog'])->name('userlog')->middleware(['auth', 'XSS']);
Route::delete('/user-log-delete/{id}', [UserController::class, 'userlogDestroy'])->name('userlog.destroy')->middleware('auth','XSS');
Route::get('/view-user-log/{id}', [UserController::class, 'userlogview'])->name('userlog.display')->middleware('XSS', 'auth');

 //====================================  Notification ====================================//
 Route::resource('notification-templates', NotificationTemplatesController::class)->middleware(['auth','XSS',]);
 Route::get('notification-templates/{id?}/{lang?}/', [NotificationTemplatesController::class, 'index'])->name('notifications-templates.index');
 Route::get('notification_templates_lang/{id}/{lang?}', [NotificationTemplatesController::class, 'manageNotificationLang'])->name('manage.notification.language')->middleware(['auth','XSS']);

//Export
Route::get('export/tickets', [TicketController::class, 'export'])->name('tickets.export');

// Email Templates

Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth','XSS']);
Route::post('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
Route::post('email_template_status', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);

Route::resource('email_template', EmailTemplateController::class)->middleware(['auth','XSS']);

Route::resource('email_template_lang', EmailTemplateLangController::class)->middleware(['auth','XSS']);

Route::get('/config-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Clear Cache successfully.');
});

