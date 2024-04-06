<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\YearRound;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use MStaack\LaravelPostgis\Geometries\Point;

class YearRoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (City::all() as $city) {
            $temperatures = [];
            for ($i = 1; $i < 13; $i++) {
                $temperature = $this->generateTemperature($i);
                $year_round = new YearRound();
                $year_round->month = $i;
                $year_round->average_temperature = $temperature;
                $year_round->city_id = $city->id;
                $year_round->save();

                $temperatures[] = $temperature;
            }
            $city->year_round_rating = $this->calculateCoefficient($temperatures);
            $city->save();
        }
    }

    private function calculateCoefficient($temperatures)
    {
        $availableMonths = 0;

        foreach ($temperatures as $temperature) {
            if ($temperature >= -20) {
                $availableMonths++;
            }
        }

        if ($availableMonths == 12) {
            // Проверяем наличие месяцев с температурой, которая может вызвать краткосрочные ограничения.
            $hasWeatherLimitations = false;
            foreach ($temperatures as $temperature) {
                if ($temperature < 0) { // Условие может быть изменено в зависимости от конкретных ограничений.
                    $hasWeatherLimitations = true;
                    break;
                }
            }

            if ($hasWeatherLimitations) {
                return 9; // возможны кратковременные ограничения
            } else {
                return 10; // доступно круглый год без ограничений
            }
        } elseif ($availableMonths >= 9) {
            return 8;
        } elseif ($availableMonths >= 6) {
            return 7;
        } elseif ($availableMonths >= 3) {
            return 6;
        } elseif ($availableMonths > 1) {
            return 5;
        } elseif ($availableMonths == 1) {
            return 4;
        } else {
            return 0; // Нет доступных месяцев, относится к неопределенной категории.
        }
    }

    private function generateTemperature($month)
    {
        switch ($month) {
            case 1:
                $temperature = rand(-40, -20); // Январь
                break;
            case 2:
                $temperature = rand(-35, -20); // Февраль
                break;
            case 3:
                $temperature = rand(-35, -20); // Март
                break;
            case 4:
                $temperature = rand(-25, -10); // Апрель
                break;
            case 11:
                $temperature = rand(-25, -10); // Ноябрь
                break;
            case 12:
                $temperature = rand(-35, -20); // Декабрь
                break;
            // Добавьте логику для других месяцев по аналогии
            default:
                $temperature = rand(10, 30); // Период с апреля по ноябрь
                break;
        }

        return $temperature;
    }
}
