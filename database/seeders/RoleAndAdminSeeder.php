<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // রোল তৈরি
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // সুপার অ্যাডমিন অ্যাকাউন্ট (Salon Owner)
        $admin = User::firstOrCreate(
            ['email' => 'admin@salon.com'],
            [
                'name' => 'Salon Owner',
                'password' => Hash::make('password123'),
                'status' => true,
            ]
        );
        $admin->syncRoles([$adminRole]);

        // মোট ৮ জন এমপ্লয়ি তালিকা
        $employees = [
            ['name' => 'Ratul Ahmed',    'email' => 'ratul@salon.com'],
            ['name' => 'Shakil Hossain', 'email' => 'shakil@salon.com'],
            ['name' => 'Arif Khan',      'email' => 'arif@salon.com'],
            ['name' => 'Tanvir Hasan',   'email' => 'tanvir@salon.com'],
            ['name' => 'Mehedi Hasan',   'email' => 'mehedi@salon.com'],
            ['name' => 'Sabbir Rahman',  'email' => 'sabbir@salon.com'],
            ['name' => 'Imran Nazir',    'email' => 'imran@salon.com'],
            ['name' => 'Kamrul Islam',   'email' => 'kamrul@salon.com'],
        ];

        foreach ($employees as $emp) {
            $employeeUser = User::firstOrCreate(
                ['email' => $emp['email']],
                [
                    'name' => $emp['name'],
                    'password' => Hash::make('password123'),
                    'status' => true,
                ]
            );
            $employeeUser->syncRoles([$employeeRole]);
        }

        // সেলুনের প্রাথমিক সার্ভিসসমূহ
        $services = [
            ['name' => 'Haircut', 'price' => 150.00],
            ['name' => 'Beard Trim / Shaving', 'price' => 80.00],
            ['name' => 'Facial Treatment', 'price' => 500.00],
            ['name' => 'Hair Color', 'price' => 600.00],
            ['name' => 'Head Massage', 'price' => 200.00],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
