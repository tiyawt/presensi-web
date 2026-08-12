<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = ['7A', '7B', '7C', '8A', '8B', '8C', '8D', '9A', '9B', '9C'];

        foreach ($classrooms as $name) {
            DB::table('classrooms')->insert([
                'name' => $name,
                'slug' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}