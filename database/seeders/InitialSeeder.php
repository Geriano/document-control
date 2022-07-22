<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = [
      'superuser' => Role::create([
        'name' => 'superuser',
        'guard_name' => 'web',
      ]),

      'manager' => Role::create([
        'name' => 'manager',
        'guard_name' => 'web',
      ]),

      'assistant manager' => Role::create([
        'name' => 'assistant manager',
        'guard_name' => 'web',
      ]),

      'supervisor' => Role::create([
        'name' => 'supervisor',
        'guard_name' => 'web',
      ]),

      'assisstant supervisor' => Role::create([
        'name' => 'assisstant supervisor',
        'guard_name' => 'web',
      ]),

      'operator' => Role::create([
        'name' => 'operator',
        'guard_name' => 'web',
      ]),

      'mr' => Role::create([
        'name' => 'mr',
        'guard_name' => 'web',
      ]),
    ];
    
    $crud = function (string $key) {
      return [
        'create' => Permission::create([
          'name' => "create {$key}",
          'guard_name' => 'web',
        ]),

        'read' => Permission::create([
          'name' => "read {$key}",
          'guard_name' => 'web',
        ]),

        'update' => Permission::create([
          'name' => "update {$key}",
          'guard_name' => 'web',
        ]),

        'delete' => Permission::create([
          'name' => "delete {$key}",
          'guard_name' => 'web',
        ]),
      ];
    };

    $permissions = [
      'users' => $crud('user'),
      'roles' => $crud('role'),
      'permissions' => $crud('permission'),
      'menus' => $crud('menu'),
      'documents' => $crud('document'),
      'revisions' => $crud('revision'),
      'contents' => $crud('content'),
      'document approvers' => $crud('document approver'),
      'revision approvers' => $crud('revision approver'),
    ];

    $password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    $su = User::create([
      'name' => 'su',
      'email' => 'su@batch.record',
      'username' => 'su',
      'password' => $password,
    ]);

    $manager = User::create([
      'name' => 'manager',
      'email' => 'm1@batch.record',
      'username' => 'm1',
      'password' => $password,
    ]);

    $assisstantManager = User::create([
      'name' => 'assisstant manager',
      'email' => 'am1@batch.record',
      'username' => 'am1',
      'password' => $password,
    ]);

    $supervisor = User::create([
      'name' => 'supervisor',
      'email' => 'spv1@batch.record',
      'username' => 'spv1',
      'password' => $password,
    ]);

    $assisstantSupervisor = User::create([
      'name' => 'assisstant supervisor',
      'email' => 'aspv1@batch.record',
      'username' => 'aspv1',
      'password' => $password,
    ]);

    $operator = User::create([
      'name' => 'operator',
      'email' => 'op1@batch.record',
      'username' => 'op1',
      'password' => $password,
    ]);

    $mr = User::create([
      'name' => 'mr',
      'email' => 'mr1@batch.record',
      'username' => 'mr1',
      'password' => $password,
    ]);

    $su->assignRole($roles['superuser']);
    $manager->assignRole($roles['manager']);
    $assisstantManager->assignRole($roles['assistant manager']);
    $supervisor->assignRole($roles['supervisor']);
    $assisstantSupervisor->assignRole($roles['assisstant supervisor']);
    $operator->assignRole($roles['operator']);
    $mr->assignRole($roles['mr']);

    $roles['mr']->givePermissionTo([
      'create document',
      'read document',
      'update document',
      'delete document',

      'create revision',
      'read revision',
      'update revision',
      'delete revision',
      
      'create document approver',
      'read document approver',
      'update document approver',
      'delete document approver',
      
      'create revision approver',
      'read revision approver',
      'update revision approver',
      'delete revision approver',
    ]);
  }
}
