<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->delete();

        Product::insert([
            [
                'title' => 'Смарт-часы X100',
                'price' => 7490,
                'category' => 'Гаджеты',
                'image' => 'https://picsum.photos/id/1062/800/600',
                'description' => 'Водонепроницаемые, GPS, 7-дневный аккумулятор',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Наушники AirBeat Pro',
                'price' => 5990,
                'category' => 'Аудио',
                'image' => 'https://picsum.photos/id/103/800/600',
                'description' => 'Активное шумоподавление, 24 часа с кейсом, Bluetooth 5.3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Кофемашина Barista Mini',
                'price' => 12990,
                'category' => 'Кухня',
                'image' => 'https://picsum.photos/id/30/800/600',
                'description' => '15 бар, капучинатор, компактная, быстрый нагрев',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Рюкзак CityPack 20L',
                'price' => 3490,
                'category' => 'Аксессуары',
                'image' => 'https://picsum.photos/id/1080/800/600',
                'description' => 'Водостойкая ткань, отделение для ноутбука 15”, скрытый карман',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Смарт-лампа Luma E27',
                'price' => 990,
                'category' => 'Умный дом',
                'image' => 'https://picsum.photos/id/82/800/600',
                'description' => '16 млн цветов, управление через приложение, расписания и сцены',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
