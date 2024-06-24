<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoryAttributeSeeder::class,
            ProductSeeder::class,
            // FunnelSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
