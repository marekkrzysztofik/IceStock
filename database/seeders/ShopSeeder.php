<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shops')->insertOrIgnore([
            ['name' => 'Chwarzno', 'address' => 'Gdynia, Chwarzno'],
            ['name' => 'Karwiny', 'address' => 'Gdynia, Karwiny'],
            ['name' => 'Gdańsk', 'address' => 'Gdańsk, Centrum'],
            ['name' => 'Rewa', 'address' => 'Rewa, Nadmorska'],
        ]);
    }
}
