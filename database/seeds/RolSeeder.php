<?php

use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 6,
            'usuario' => 'alex',
            'password' => bcrypt('alex'),
            'condicion' => 1,
            'idrol' => 1
        ]);
        DB::table('users')->insert([
            'id' => 6,
            'usuario' => 'ale',
            'password' => bcrypt('alex'),
            'condicion' => 1,
            'idrol' => 2
        ]);
        DB::table('users')->insert([
            'id' => 6,
            'usuario' => 'alejandro',
            'password' => bcrypt('alex'),
            'condicion' => 1,
            'idrol' => 3
        ]);
    }
}
