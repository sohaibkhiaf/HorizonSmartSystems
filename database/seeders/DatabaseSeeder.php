<?php

namespace Database\Seeders;

use App\Models\CustomerDemo;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // USERS //////////////////////////////////////
        $admin = User::updateOrCreate(
            ['email' => 'sohaibkhiaf@gmail.com'],
            [
                'name' => 'Sohaib KHIAF',
                'role' => 'ADMIN',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'salemamine@gmail.com'],
            [
                'name' => 'Mohamed Amine SALEM',
                'role' => 'OPERATOR',
                'password' => Hash::make('password'),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@hss.dz'],
            [
                'name' => 'Nadia Benali',
                'phone' => '0550 12 34 56',
                'company' => 'Benali Consulting',
                'role' => 'CUSTOMER',
                'password' => Hash::make('password'),
            ]
        );

        // ACHIEVEMENTS //////////////////////////////////////
        DB::table('public_achievements')->insert([
            [
                'en_title' => 'Wili',
                'ar_title' => 'ويلي',
                'fr_title' => 'Wili',

                'en_type' => 'Software',
                'ar_type' => 'برمجية',
                'fr_type' => 'Logiciel',

                'en_short_description' => 'A smart stock and business management system built to help companies manage products, inventory, sales, and daily operations efficiently.',
                'ar_short_description' => 'نظام ذكي لإدارة المخزون والأعمال يساعد الشركات على إدارة المنتجات والمخزون والمبيعات والعمليات اليومية بكفاءة.',
                'fr_short_description' => 'Un système intelligent de gestion de stock et d\'entreprise conçu pour aider les entreprises à gérer les produits, les stocks, les ventes et les opérations quotidiennes efficacement.',

                'en_description' => 'Wili is a professional management system designed for businesses that need better control over their stock, products, sales, and internal operations. It provides a clean interface, organized workflows, and practical tools to simplify daily management tasks.',
                'ar_description' => 'ويلي هو نظام إدارة احترافي مصمم للشركات التي تحتاج إلى تحكم أفضل في المخزون والمنتجات والمبيعات والعمليات الداخلية. يوفر واجهة نظيفة وعمليات منظمة وأدوات عملية لتبسيط المهام اليومية.',
                'fr_description' => 'Wili est un système de gestion professionnel conçu pour les entreprises qui ont besoin d\'un meilleur contrôle de leurs stocks, produits, ventes et opérations internes. Il offre une interface claire, des flux de travail organisés et des outils pratiques pour simplifier les tâches quotidiennes.',

                'image' => null,
                'project_url' => null,

                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'en_title' => 'HORIZON PC',
                'ar_title' => 'هوريزون بي سي',
                'fr_title' => 'HORIZON PC',

                'en_type' => 'Ecommerce',
                'ar_type' => 'متجر إلكتروني',
                'fr_type' => 'E-commerce',

                'en_short_description' => 'A modern ecommerce website for HORIZON PC, designed to showcase digital products, subscriptions, software, and gaming services online.',
                'ar_short_description' => 'موقع تجارة إلكترونية عصري لـ HORIZON PC مصمم لعرض المنتجات الرقمية والاشتراكات والبرمجيات وخدمات الألعاب عبر الإنترنت.',
                'fr_short_description' => 'Un site e-commerce moderne pour HORIZON PC, conçu pour présenter des produits numériques, des abonnements, des logiciels et des services de jeux en ligne.',

                'en_description' => 'The HORIZON PC ecommerce website is a clean and responsive online store built to present products such as game keys, gift cards, software, subscriptions, and digital services. It helps customers browse products easily and place orders through a smooth and professional shopping experience.',
                'ar_description' => 'موقع HORIZON PC هو متجر إلكتروني نظيف ومتجاوب يعرض منتجات مثل مفاتيح الألعاب وبطاقات الهدايا والبرمجيات والاشتراكات والخدمات الرقمية. يساعد العملاء على تصفح المنتجات بسهولة وإجراء الطلبات عبر تجربة شراء سلسة واحترافية.',
                'fr_description' => 'Le site e-commerce HORIZON PC est une boutique en ligne moderne et responsive présentant des produits tels que des clés de jeux, cartes cadeaux, logiciels, abonnements et services numériques. Il permet aux clients de naviguer facilement et de passer commande via une expérience fluide et professionnelle.',

                'image' => null,
                'project_url' => 'https://horizon-pc.com',

                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // STORE SUBSCRIPTIONS //////////////////////////////////////
        $storeSubscriptions = [
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

        foreach ($storeSubscriptions as $subscription) {
            DB::table('store_subscriptions')->updateOrInsert(
                ['name' => $subscription['name']],
                [
                    'description' => $subscription['description'],
                    'price' => $subscription['price'],
                    'type' => $subscription['type'],
                    'category' => $subscription['category'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }


        // PUBLIC SERVICES ///////////////////////////////////
        DB::table('public_services')->insert([
            [
                'ar_name' => 'إنشاء مواقع ويب',
                'fr_name' => 'Création de sites web',
                'en_name' => 'Website Creation',

                'ar_short_description' => 'مواقع ويب حديثة وسريعة ومتجاوبة.',
                'fr_short_description' => 'Sites web modernes, rapides et responsive.',
                'en_short_description' => 'Modern, fast and responsive websites.',

                'ar_description' => 'مواقع ويب حديثة وسريعة ومتجاوبة، مصمَّمة لإبراز نشاطك التجاري.',
                'fr_description' => 'Sites web modernes, rapides et responsive, conçus pour valoriser votre activité.',
                'en_description' => 'Modern, fast and responsive websites designed to showcase your business.',

                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'تطوير تطبيقات مخصصة',
                'fr_name' => 'Création d\'application sur mesure',
                'en_name' => 'Custom Application Development',

                'ar_short_description' => 'تطبيقات مخصصة حسب احتياجاتك.',
                'fr_short_description' => 'Applications sur mesure adaptées à vos besoins.',
                'en_short_description' => 'Tailor-made applications.',

                'ar_description' => 'تطبيقات مُخصَّصة ومُكيَّفة مع احتياجاتك الخاصة.',
                'fr_description' => 'Applications sur mesure adaptées à vos besoins spécifiques.',
                'en_description' => 'Tailor-made applications adapted to your specific needs.',

                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'الإدارة التجارية والرواتب وحلول أخرى',
                'fr_name' => 'Gestion commerciale, paie et autres solutions',
                'en_name' => 'Business Management & ERP Solutions',

                'ar_short_description' => 'حلول لإدارة وتحسين نشاطك.',
                'fr_short_description' => 'Solutions pour gérer et optimiser votre activité.',
                'en_short_description' => 'Integrated business management solutions.',

                'ar_description' => 'حلول متكاملة لإدارة نشاطك وتحسين عملياتك.',
                'fr_description' => 'Solutions intégrées pour gérer votre activité et optimiser vos processus métiers.',
                'en_description' => 'Integrated solutions to manage your business and optimize your processes.',

                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'أنظمة إدارة مخصصة',
                'fr_name' => 'Systèmes de gestion personnalisés',
                'en_name' => 'Custom Management Systems',

                'ar_short_description' => 'برامج لتتبع وإدارة أفضل.',
                'fr_short_description' => 'Logiciels pour un meilleur suivi.',
                'en_short_description' => 'Custom management software.',

                'ar_description' => 'برامج إدارة مُخصَّصة لتتبع أفضل وإنتاجية أعلى.',
                'fr_description' => 'Logiciels de gestion sur mesure pour un meilleur suivi et une productivité accrue.',
                'en_description' => 'Custom management software for better tracking and increased productivity.',

                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'الدعم البرمجي والتقني',
                'fr_name' => 'Assistance logicielle et informatique',
                'en_name' => 'Software & IT Support',

                'ar_short_description' => 'دعم وحلول تقنية.',
                'fr_short_description' => 'Support et dépannage informatique.',
                'en_short_description' => 'Technical support and troubleshooting.',

                'ar_description' => 'المساعدة والإصلاح لبرامجك وأنظمتك المعلوماتية واتصالاتك وشبكاتك.',
                'fr_description' => 'Assistance et dépannage pour vos logiciels, systèmes informatiques, connexion Internet et problèmes réseau.',
                'en_description' => 'Assistance and troubleshooting for your software, computer systems, internet connection and network issues.',

                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // PUBLIC SOFTWARE ////////////////////////////////////////
        DB::table('public_software')->insert([
            [
                'ar_name' => 'ويلي',
                'fr_name' => 'Wili',
                'en_name' => 'Wili',

                'ar_short_description' => 'نظام تسيير المخزون',
                'fr_short_description' => 'Système de gestion de stock',
                'en_short_description' => 'Stock Management System',

                'ar_description' => 'Wili هو نظام لتسيير المخزون يساعد المؤسسات على إدارة المنتجات، المخزون، المبيعات وحركات السلع بطريقة سهلة ومنظمة.',
                'fr_description' => 'Wili est un système de gestion de stock conçu pour aider les entreprises à gérer les produits, l’inventaire, les ventes et les mouvements de stock de manière simple et organisée.',
                'en_description' => 'Wili is a stock management system built to help businesses manage products, inventory, sales and daily stock movements in a simple and organized way.',

                'image' => null,
                'icon' => null,
                'is_published' => true,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
