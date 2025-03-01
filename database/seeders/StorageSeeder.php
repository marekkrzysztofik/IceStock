<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        DB::table('storages')->insertOrIgnore([
            ['shop_id' => 1, 'name' => 'Mroźnia 1 - Chwarzno', 'type' => 'storage'],
            ['shop_id' => 1, 'name' => 'Mroźnia 2 - Chwarzno', 'type' => 'storage'],
            ['shop_id' => 1, 'name' => 'Witryna - Chwarzno', 'type' => 'display'],

            ['shop_id' => 2, 'name' => 'Mroźnia 1 - Karwiny', 'type' => 'storage'],
            ['shop_id' => 2, 'name' => 'Witryna - Karwiny', 'type' => 'display'],

            ['shop_id' => 3, 'name' => 'Mroźnia 1 - Gdańsk', 'type' => 'storage'],
            ['shop_id' => 3, 'name' => 'Witryna - Gdańsk', 'type' => 'display'],

            ['shop_id' => 4, 'name' => 'Mroźnia 1 - Rewa', 'type' => 'storage'],
            ['shop_id' => 4, 'name' => 'Mroźnia 2 - Rewa', 'type' => 'storage'],
            ['shop_id' => 4, 'name' => 'Witryna - Rewa', 'type' => 'display'],
        ]);
    }
}
