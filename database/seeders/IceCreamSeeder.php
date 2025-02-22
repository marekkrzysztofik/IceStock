<?php

namespace Database\Seeders;

use App\Models\IceCream;
use Illuminate\Database\Seeder;


class IceCreamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flavors = [
            'Waniliowe', 'Czekoladowe', 'Truskawkowe', 'Malinowe', 'Mango',
            'Kokosowe', 'Pistacjowe', 'Orzech laskowy', 'Słony karmel', 'Miętowe z czekoladą',
            'Bananowe', 'Borówkowe', 'Tiramisu', 'Jogurtowe', 'Lemoniadowe'
        ];

        foreach ($flavors as $flavor) {
            IceCream::firstOrCreate(['name' => $flavor]);
        }
    }
}
