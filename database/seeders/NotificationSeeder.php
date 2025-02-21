<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NotificationTemplates;
use App\Models\NotificationTemplateLangs;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications = [
            'new_user'=>'New User',
            'new_ticket'=>'New Ticket',
            'new_ticket_reply'=>'New Ticket Reply'
        ];

        $defaultTemplate = [
            'new_user' => [
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "user_name",
                    "Email": "email",
                    "Password": "password"
                    }',
                    'lang' => [
                        'ar' => 'تم تكوين مستخدم جديد بواسطة {user_name}',
                        'da' => 'Ny bruger oprettet af {bruger_navn}.',
                        'de' => 'Neuer Benutzer erstellt von {Benutzername}.',
                        'en' => 'New User created by {user_name}.',
                        'es' => 'Nueva usuario creada por {nombre_usuario}.',
                        'fr' => 'Nouvel utilisateur créé par {Nom_utilisateur}.',
                        'it' => 'Nuovo utente creato da {user_name}.',
                        'ja' => 'によって作成された新しいユーザー{ユーザー名}.',
                        'nl' => 'Nieuwe gebruiker gemaakt door {gebruikersnaam}.',
                        'pl' => 'Nowy użytkownik utworzony przez {nazwa_użytkownika}.',
                        'ru' => 'Новый пользователь, созданный {имя_пользователя}.',
                        'pt' => 'Novo Usuário criado por {user_name}.',
                        'tr' =>  '{ user_name } tarafından oluşturulan yeni Kullanıcı.',
                        'he' => ' משתמש חדש נוצר על ידי {user_name}.',
                        'zh' => '由 {user_name} 创建的新用户。',
                        'pt-br' => 'Novo usuário criado por {user_name}.',



                    ],
            ],

            'new_ticket' => [
                'variables' => '{
                    "App Name": "app_name",
                    "Ticket Name": "ticket_name",
                    "Ticket Id" : "ticket_id",
                    "Email": "email",
                    "Password": "password"
                    }',

                    'lang' => [

                        'ar' => 'تم تكوين بطاقة طلب الخدمة الجديدة من {user_name}',
                        'da' => 'Ny ticket oprettet af {bruger_navn}.',
                        'de' => 'Neues Ticket erstellt von {Benutzername}.',
                        'en' => 'New Ticket created by {user_name}.',
                        'es' => 'Nuevo ticket creado por {nombre_usuario}.',
                        'fr' => 'Nouveau ticket créé par {Nom_utilisateur}.',
                        'it' => 'Nuovo Ticket creato da {user_name}.',
                        'ja' => '新規チケットの作成者.',
                        'nl' => 'Nieuwe ticket gemaakt door {gebruikersnaam}.',
                        'pl' => 'Nowy bilet utworzony przez {nazwa_użytkownika}.',
                        'ru' => 'Новый паспорт, созданный {имя_пользователя}.',
                        'pt' => 'Novo Bilhete criado por {user_name}.',
                        'tr' => '{ user_name } tarafından oluşturulan Yeni Bildirim Formu.',
                        'he' => 'כרטיס חדש שנוצר על-ידי {user_name}.',
                        'zh' => '{user_name} 创建的新凭单。',
                        'pt-br' => 'Novo Ticket criado por {user_name}.',

                    ],
            ],

            'new_ticket_reply' => [
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "user_name",
                    "Ticket Name": "ticket_name",
                    "Ticket Id" : "ticket_id",
                    "Ticket Description" : "ticket_description"
                    }',

                    'lang' => [

                        'ar' => 'رد بطاقة طلب خدمة جديد بواسطة {user_name}',
                        'da' => 'Ny ticket-svar af {bruger_navn}.',
                        'de' => 'Neue Ticket-Antwort von {Benutzername}.',
                        'en' => 'New Ticket Reply by {user_name}.',
                        'es' => 'Nuevo ticket de respuesta {nombre_usuario}.',
                        'fr' => 'Nouvelle réponse au ticket par {Nom_utilisateur}.',
                        'it' => 'Nuovo Ticket Reply by {user_name}.',
                        'ja' => '新規チケットの返信.',
                        'nl' => 'Nieuw ticket antwoord door {gebruikersnaam}.',
                        'pl' => 'Nowa odpowiedź zgłoszenia przez {nazwa_użytkownika}.',
                        'ru' => 'Новый ответ на паспорт по {имя_пользователя}.',
                        'pt' => 'Nova Resposta de Bilhete por {user_name}.',
                        'tr' => 'Yeni Bildirim Formu Yanıtı { user_name } tarafından yanıtlıyor.',
                        'pt-br' => 'Novo ticket Responder por {user_name}.',
                        'zh' => '{ user_name}的新凭单回复。',
                        'he' => 'תשובה כרטיס חדשה על ידי {user_name}.'
                    ],
            ],
        ];

        $user = User::where('parent','Admin')->first();

        foreach($notifications as $k => $n)
        {
            $ntfy = NotificationTemplates::where('slug',$k)->count();
            if($ntfy == 0)
            {
                $new = new NotificationTemplates();
                $new->name = $n;
                $new->slug = $k;
                $new->save();

                foreach($defaultTemplate[$k]['lang'] as $lang => $content)
                {
                    NotificationTemplateLangs::create(
                        [
                            'parent_id' => $new->id,
                            'lang' => $lang,
                            'variables' => $defaultTemplate[$k]['variables'],
                            'content' => $content,
                            'created_by' =>  0,
                        ]
                    );
                }
            }
        }
    }
}
