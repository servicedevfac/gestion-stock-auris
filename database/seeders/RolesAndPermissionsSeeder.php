<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Reset
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
        'voir produit',
        'gérer produit',
        // Vente permissions
        'voir vente',
        'gérer vente',
        'créer vente',
        // Stock permissions
        'voir stock',
        'gérer stock',
        // Client permissions
        'gérer client',
        'voir client',
        // User permissions
        'voir user',
        'gérer user',
        // Recu permissions
        'exporter excel',
        // Permissions pour les rôles
        'gérer rôles',
        'voir role',
        // Permissions pour les permissions
        'voir permission',
        'gérer permission',
        // Permissions spécifiques
        'voir paramètre',
        'gérer paramètre',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Créer les rôles et leurs permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $vendeur = Role::firstOrCreate(['name' => 'vendeur']);
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->givePermissionTo($permissions);
        $vendeur->givePermissionTo([
            'voir produit',
            'voir vente',
            'créer vente',
            'créer vente',
            'voir stock',
            'gérer client',
            'voir client',
            'exporter excel',
        ]);
        $admin->givePermissionTo([
            'voir produit',
            'gérer produit',
            'voir vente',
            'gérer vente',
            'voir stock',
            'gérer stock',
            'gérer client',
            'voir client',
            'voir user',
            'gérer user',
            'exporter excel',
            ]);
    }
}


