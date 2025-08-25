<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'nom' => 'Admin Principal',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');
        // Assigner les permissions à l'admin
         $superadmin = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'nom' => 'Super admin',
            'password' => Hash::make('password'),
        ]);
        $superadmin->assignRole('super admin');

        // Utilisateur gestionnaire
        $gestionnaire = User::firstOrCreate([
            'email' => 'gestionnaire@example.com',
        ], [
            'nom' => 'Jean Gestionnaire',
            'password' => Hash::make('password'),
        ]);
        $gestionnaire->assignRole('gestionnaire');

    }
    }

