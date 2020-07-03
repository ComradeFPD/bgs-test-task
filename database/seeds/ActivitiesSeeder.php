<?php

use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Activity::class, 15)->create();
        factory(\App\Models\Activity::class, 10)->create([
            'start_at' => \Carbon\Carbon::now()->addDay()
        ]);
    }
}
