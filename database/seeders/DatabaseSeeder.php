<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // USERS 
        User::factory()->create([
            'name' => 'Sohaib KHIAF',
            'email' => 'sohaibkhiaf@gmail.com',
            'role' => 'ADMIN',
        ]);
        User::factory()->create([
            'name' => 'Mohamed Amine SALEM',
            'email' => 'salemamine@gmail.com',
            'role' => 'OPERATOR',
        ]);

        // ACHIEVEMENTS
        DB::table('achievements')->insert([
            [
                'title' => 'Wili',
                'type' => 'software',

                'short_description' => 'A smart stock and business management system built to help companies manage products, inventory, sales, and daily operations efficiently.',

                'description' => 'Wili is a professional management system designed for businesses that need better control over their stock, products, sales, and internal operations. It provides a clean interface, organized workflows, and practical tools to simplify daily management tasks.',

                'image' => 'wili_pc.png',
                'project_url' => null,

                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'HORIZON PC',
                'type' => 'ecommerce',

                'short_description' => 'A modern ecommerce website for HORIZON PC, designed to showcase digital products, subscriptions, software, and gaming services online.',

                'description' => 'The HORIZON PC ecommerce website is a clean and responsive online store built to present products such as game keys, gift cards, software, subscriptions, and digital services. It helps customers browse products easily and place orders through a smooth and professional shopping experience.',

                'image' => 'horizon_pc_store.png',
                'project_url' => 'https://horizon-pc.com',

                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // PRODUCTS
        $products = [
            [
                'name' => 'ChatGPT Plus Subscription 1 Month',
                'description' => 'Monthly ChatGPT Plus subscription account for AI writing, coding, research, and productivity tasks.',
                'price' => 6500,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'Gemini Pro Subscription 1 Month',
                'description' => 'Google Gemini Pro subscription account for AI assistance, productivity, and creative work.',
                'price' => 6000,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'Adobe Creative Cloud 1 Month',
                'description' => 'Adobe Creative Cloud subscription for Photoshop, Illustrator, Premiere Pro, and other creative tools.',
                'price' => 7500,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'Netflix Premium 1 Month',
                'description' => 'Netflix Premium account access for movies, series, and entertainment streaming.',
                'price' => 2200,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'Spotify Premium 1 Month',
                'description' => 'Spotify Premium subscription for ad-free music, offline listening, and high-quality audio.',
                'price' => 1800,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'YouTube Premium 1 Month',
                'description' => 'YouTube Premium account access with ad-free videos, background play, and YouTube Music.',
                'price' => 2500,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
            [
                'name' => 'Microsoft Office 2021 Professional Plus',
                'description' => 'Microsoft Office 2021 Professional Plus activation key for Word, Excel, PowerPoint, Outlook, and more.',
                'price' => 4500,
                'type' => 'key',
                'category' => 'software',
            ],
            [
                'name' => 'Windows 11 Pro Key',
                'description' => 'Windows 11 Pro activation key for professional use, security features, and modern productivity.',
                'price' => 3500,
                'type' => 'key',
                'category' => 'software',
            ],
            [
                'name' => 'Windows 10 Pro Key',
                'description' => 'Windows 10 Pro activation key for business, gaming, and daily computer use.',
                'price' => 3000,
                'type' => 'key',
                'category' => 'software',
            ],
            [
                'name' => 'Steam Gift Card 10 USD',
                'description' => 'Steam wallet gift card for buying PC games, software, and in-game content on Steam.',
                'price' => 2700,
                'type' => 'key',
                'category' => 'gift_cards',
            ],
            [
                'name' => 'Steam Gift Card 20 USD',
                'description' => 'Steam wallet gift card for purchasing games, DLCs, and digital content.',
                'price' => 5200,
                'type' => 'key',
                'category' => 'gift_cards',
            ],
            [
                'name' => 'PlayStation Store Gift Card 10 USD',
                'description' => 'PlayStation Store gift card for games, add-ons, subscriptions, and digital content.',
                'price' => 2800,
                'type' => 'key',
                'category' => 'gift_cards',
            ],
            [
                'name' => 'Xbox Gift Card 10 USD',
                'description' => 'Xbox gift card for buying games, subscriptions, and digital content from the Microsoft Store.',
                'price' => 2700,
                'type' => 'key',
                'category' => 'gift_cards',
            ],
            [
                'name' => 'EA Sports FC 25',
                'description' => 'EA Sports FC 25 game key for football fans with clubs, tournaments, and online matches.',
                'price' => 9500,
                'type' => 'key',
                'category' => 'games',
            ],
            [
                'name' => 'Grand Theft Auto V',
                'description' => 'GTA V game key with story mode, online multiplayer, missions, vehicles, and open-world gameplay.',
                'price' => 4500,
                'type' => 'key',
                'category' => 'games',
            ],
            [
                'name' => 'Minecraft Java & Bedrock Edition',
                'description' => 'Minecraft Java and Bedrock Edition account/key for creative building, survival, and multiplayer.',
                'price' => 6200,
                'type' => 'key',
                'category' => 'games',
            ],
            [
                'name' => 'Red Dead Redemption 2',
                'description' => 'Red Dead Redemption 2 game key with an immersive open-world western adventure.',
                'price' => 6800,
                'type' => 'key',
                'category' => 'games',
            ],
            [
                'name' => 'Fortnite V-Bucks 1000',
                'description' => 'Fortnite V-Bucks digital code for skins, battle passes, emotes, and in-game items.',
                'price' => 2500,
                'image' => 'products/fortnite-vbucks.jpg',
                'type' => 'key',
                'category' => 'in_game_currency',
            ],
            [
                'name' => 'PUBG Mobile UC Pack',
                'description' => 'PUBG Mobile UC pack for buying outfits, crates, royal pass, and in-game items.',
                'price' => 2200,
                'type' => 'key',
                'category' => 'in_game_currency',
            ],
            [
                'name' => 'Discord Nitro 1 Month',
                'description' => 'Discord Nitro subscription for profile customization, server boosts, HD streaming, and bigger uploads.',
                'price' => 3000,
                'type' => 'account',
                'category' => 'subscriptions',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['name' => $product['name']],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'type' => $product['type'],
                    'category' => $product['category'],
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

    }
}
