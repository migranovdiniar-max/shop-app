<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->delete();

        Product::insert([
            [
                'title' => 'Рюкзак городской',
                'price' => 3990,
                'category' => 'Аксессуары',
                'image' => 'https://ir.ozone.ru/s3/multimedia-1-k/7153961816.jpg',
                'description' => 'Удобный городской рюкзак для повседневного использования.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Беспроводные наушники',
                'price' => 8990,
                'category' => 'Электроника',
                'image' => 'https://pcdn.goldapple.ru/p/p/19000460390/web/696d674d61696e5064705f30373038386136366231643534343162616133343130336331626431613934378de0665c69fbb80.jpg',
                'description' => 'Шумоподавление, чистый звук, длительное время работы.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Кофемашина',
                'price' => 24990,
                'category' => 'Техника',
                'image' => 'https://a.allegroimg.com/s1024/0c3c9b/229edb32408087cedc889ab3c5a4',
                'description' => 'Автоматическая кофемашина для дома и офиса.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Настольная лампа',
                'price' => 2990,
                'category' => 'Дом',
                'image' => 'https://avatars.mds.yandex.net/i?id=879ac303430145dc9dfa2d780703e689_l-9151820-images-thumbs&n=13',
                'description' => 'Современная LED-лампа с регулировкой яркости.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Смарт-часы',
                'price' => 11990,
                'category' => 'Гаджеты',
                'image' => 'https://images-eu.ssl-images-amazon.com/images/I/41hxM-Hs2YL.jpg',
                'description' => 'Пульс, шаги, уведомления, влагозащита.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
