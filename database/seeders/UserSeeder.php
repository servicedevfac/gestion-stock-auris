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

        // Utilisateur vendeur
        $vendeur = User::firstOrCreate([
            'email' => 'vendeur@example.com',
        ], [
            'nom' => 'Jean Vendeur',
            'password' => Hash::make('password'),
        ]);
        $vendeur->assignRole('vendeur');
        
    }
    }

