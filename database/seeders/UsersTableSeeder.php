<?php

namespace Database\Seeders;

use App\Models\Settings;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'Admin']);
        }

        $adminPermissions = [
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'lang-manage',
            'lang-change',
            'lang-create',
            'manage-tickets',
            'create-tickets',
            'edit-tickets',
            'delete-tickets',
            'manage-category',
            'create-category',
            'edit-category',
            'delete-category',
            'reply-tickets',
            'manage-setting',
            'manage-faq',
            'create-faq',
            'edit-faq',
            'show-faq',
            'delete-faq',
            'manage-knowledge',
            'create-knowledge',
            'edit-knowledge',
            'delete-knowledge',
            'manage-knowledgecategory',
            'create-knowledgecategory',
            'show-knowledgecategory',
            'edit-knowledgecategory',
            'delete-knowledgecategory',
            'manage-company-settings',
            'manage dashboard',
        ];



        foreach ($adminPermissions as $ap) {
            $checkPermission = Permission::where('name', $ap)->first();
            if (!$checkPermission) {
                $permission = Permission::create(['name' => $ap]);
            } else {
                $permission = $checkPermission;
            }
            $adminRole->givePermissionTo($permission);
        }

        $adminUser = User::where('name', 'Admin')->first();
        if (!$adminUser) {
            $adminUser = User::create(
                [
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('1234'),
                ]
            );
        }

        $adminUser->assignRole($adminRole);

        $agentRole = Role::where('name', 'Agent')->first();
        if (!$agentRole) {
            $agentRole = Role::create(['name' => 'Agent']);
        }

        $agentPermissions = [
            'view-users',
            'lang-change',
            'manage-tickets',
            'edit-tickets',
            'reply-tickets',
            'manage dashboard',
        ];

        foreach ($agentPermissions as $ep) {
            $permission = Permission::where('name', $ep)->first();
            if (!$permission) {
                $permission = Permission::firstOrCreate(['name' => $ep]);
            }
            $agentRole->givePermissionTo($permission);
        }

        $editorUser = User::where('name', 'Agent')->first();

        if (!$editorUser) {
            $editorUser = User::create(
                [
                    'name' => 'Agent',
                    'email' => 'agent@example.com',
                    'password' => Hash::make('1234'),
                    'parent' => 1,
                ]
            );
        }

        $editorUser->assignRole($agentRole);

        Utility::defaultEmail();
        Utility::userDefaultData();
        Utility::languagecreate();

        $data = [
            ['name' => 'local_storage_validation', 'value' => 'jpg,jpeg,png,xlsx,xls,csv,pdf', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'wasabi_storage_validation', 'value' => 'jpg,jpeg,png,xlsx,xls,csv,pdf', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 's3_storage_validation', 'value' => 'jpg,jpeg,png,xlsx,xls,csv,pdf', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'local_storage_max_upload_size', 'value' => 2048000, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'wasabi_max_upload_size', 'value' => 2048000, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 's3_max_upload_size', 'value' => 2048000, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'storage_setting', 'value' => 'local', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()]

        ];
        // DB::table('settings')->insert($data);
        foreach ($data as $item) {
            Settings::updateOrCreate(
                ['name' => $item['name']], // Conditions to check for existing records
                [
                    'value' => $item['value'],
                    'created_by' => $item['created_by'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
