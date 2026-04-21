<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserMigrateSeeder extends Seeder
{
    public function run(): void
    {
        $legacyUsers = DB::table('muser')->get();
        $defaultPassword = Hash::make('password');

        foreach ($legacyUsers as $legacyUser) {
            try {
                $formatLegacyName = str_replace(' ', '', strtolower($legacyUser->nama));
                $formatLegacyEmail = $formatLegacyName.'@ns.inox';

                $profileData = [
                    'name' => trim($legacyUser->nama),
                    'approver_name' => $legacyUser->namettdpo,
                    'approver_title' => $legacyUser->jabttdpo,
                    'branch_code' => $legacyUser->kdcab,
                    'position' => $legacyUser->jbt,
                    'updated_at' => now(),
                ];

                $existingUser = DB::table('users')
                    ->where('email', $formatLegacyEmail)
                    ->first();

                if ($existingUser) {
                    DB::table('users')
                        ->where('id', $existingUser->id)
                        ->update($profileData);

                    $userId = $existingUser->id;
                } else {
                    $insertData = array_merge($profileData, [
                        'email' => $formatLegacyEmail,
                        'password' => $defaultPassword,
                        'created_at' => now(),
                    ]);

                    $userId = DB::table('users')->insertGetId($insertData);
                }

                if ($legacyUser->super === 'T') {
                    $roleExists = DB::table('model_has_roles')
                        ->where('model_id', $userId)
                        ->where('model_type', 'App\Models\User')
                        ->where('role_id', 1)
                        ->exists();

                    if (! $roleExists) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => 1,
                            'model_type' => 'App\Models\User',
                            'model_id' => $userId,
                        ]);
                    }
                }
            } catch (Throwable $e) {
            }
        }
    }
}
