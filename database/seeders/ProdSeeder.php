<?php
namespace Database\Seeders;

use App\Models\Pak;
use Illuminate\Database\Seeder;

class ProdSeeder extends Seeder
{
    public function run()
    {
        foreach (config('paks') as $slug => $name) {
            Pak::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
