<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            ContactSeeder::class,
            CatalogSeeder::class,
            DocumentSeriesSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
