<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // user
        \App\Models\User::factory()->create([
            'name' => 'Invoices',
            'email' => 'admin@invoices.my.id',
            'password' => 'Password',
            'role' => 'admin',
            'is_verified' => 1,
        ]);

        // $user = \App\Models\User::factory()->create([
        //     'name' => 'PT. Tester',
        //     'email' => 'user@gmail.com',
        //     'password' => 'Password',
        //     'role' => 'user',
        //     'is_verified' => 1,
        //     'sales' => "Handoko Sumanto",
        //     "logo" => "as.jpg",
        //     "address" => "Depok Jawa Barat",
        //     "telephone" => "088289317870",
        //     "payment_methode" => "BANK BCA",
        //     "payment_name" => "Handoko Sumanto",
        //     "payment_number" => "203292393889",
        // ]);
        // \App\Models\User::factory(35)->create();

        // product
        // for ($i = 0; $i < 4; $i++) {
        //     \App\Models\Product::factory()->create([
        //         'users_id' => 2,
        //     ]);
        // }
        // \App\Models\Product::factory(50)->create();

        // // invoice
        // for ($i = 0; $i < 4; $i++) {
        //     $invoice = \App\Models\Invoice::factory()->create([
        //         'users_id' => 2,
        //     ]);
        //     \App\Models\InvoiceProduct::factory(2)->create([
        //         'invoices_code' => $invoice->code,
        //     ]);
        // }
        // $invoice = \App\Models\Invoice::factory(100)->create();
        // \App\Models\InvoiceProduct::factory(20)->create();
    }
}
