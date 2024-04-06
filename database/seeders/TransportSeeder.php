<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Transport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonData = file_get_contents(storage_path('/app/public/transports.json'));
        $jsonData = json_decode($jsonData, true);
        foreach ($jsonData as $data) {
            $transport = new Transport();
            $transport->rating = $data['rating'];
            $transport->airport_nearby = $data['airport_nearby'];
            $transport->bus_stations = $data['bus_stations'];
            $transport->road_nearby = $data['road_nearby'];
            $transport->city_id = City::where('title', '=', $data['city'])->first()->id;
            $transport->save();
        }

    }
}
