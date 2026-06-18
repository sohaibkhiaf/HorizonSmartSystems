<?php

namespace Database\Seeders;


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
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'salemamine@gmail.com'],
            [
                'name' => 'Mohamed Amine SALEM',
                'role' => 'operator',
                'password' => Hash::make('password'),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@hss.dz'],
            [
                'name' => 'Salah Benali',
                'phone' => '0550123456',
                'company' => 'Benali Consulting',
                'role' => 'customer',
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

                'ar_description' => 'نقدم خدمة إنشاء مواقع ويب احترافية وعصرية مصممة بعناية لتلبية احتياجات نشاطك التجاري وتعزيز حضورك الرقمي. نقوم بتطوير مواقع سريعة، متجاوبة مع جميع الأجهزة (الهاتف، الحاسوب، اللوحيات)، وسهلة الاستخدام لضمان أفضل تجربة للمستخدم. نركز على تصميم واجهات جذابة وتجربة مستخدم متقدمة، بالإضافة إلى تحسين محركات البحث (SEO) لضمان ظهور موقعك في نتائج البحث. سواء كنت تحتاج إلى موقع تعريفي، متجر إلكتروني، منصة خدمات أو نظام مخصص، فإننا نوفر حلولًا متكاملة من التصميم إلى الإطلاق والدعم المستمر.',

                'fr_description' => 'Nous proposons des services complets de création de sites web modernes et professionnels, conçus pour répondre parfaitement aux besoins de votre activité et renforcer votre présence en ligne. Nos sites sont rapides, sécurisés, entièrement responsives et optimisés pour offrir une تجربة utilisateur fluide sur tous les appareils. Nous accordons une grande importance au design, à l’ergonomie ainsi qu’au référencement naturel (SEO) afin d’améliorer votre visibilité sur les moteurs de recherche. Que ce soit pour un site vitrine, un e-commerce ou une plateforme personnalisée, nous vous accompagnons de la conception jusqu’au déploiement et au suivi technique.',

                'en_description' => 'We provide comprehensive website creation services tailored to elevate your business presence online. Our websites are modern, fast, secure, and fully responsive, ensuring seamless performance across all devices including smartphones, tablets, and desktops. We focus on clean design, intuitive user experience, and SEO optimization to help your business stand out in search results. Whether you need a corporate website, an e-commerce platform, or a custom web solution, we guide you through every stage—from planning and design to development, launch, and ongoing support.',

                'type' => 'main',
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

                'ar_description' => 'نقوم بتطوير تطبيقات مخصصة تلبي احتياجاتك الفريدة بدقة، سواء كانت تطبيقات ويب، تطبيقات مكتبية أو أنظمة داخلية. نحلل متطلباتك بعناية ونصمم حلولًا تقنية فعالة تساعدك على تحسين الأداء، تقليل التكاليف، وزيادة الإنتاجية. نعتمد على أحدث التقنيات لضمان الأمان، السرعة، وقابلية التوسع، مع توفير تجربة استخدام سلسة وواجهة سهلة. خدماتنا تشمل التصميم، التطوير، الاختبار، النشر، والدعم المستمر.',

                'fr_description' => 'Nous développons des applications sur mesure parfaitement adaptées à vos besoins spécifiques, qu’il s’agisse d’applications web, de logiciels métiers ou de systèmes internes. Nous analysons vos exigences en profondeur afin de concevoir des solutions efficaces qui optimisent vos processus et améliorent votre productivité. Nos applications sont sécurisées, évolutives et conçues avec des technologies modernes, tout en offrant une expérience utilisateur fluide et intuitive.',

                'en_description' => 'We specialize in developing custom applications tailored precisely to your business requirements. Whether you need web apps, internal systems, or specialized tools, we design and build scalable, secure, and high-performance solutions. Our process includes deep requirement analysis, user-focused design, robust development, and continuous improvement. The goal is to streamline your operations, enhance efficiency, and provide long-term value through technology.',

                'type' => 'main',
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

                'ar_description' => 'نوفر حلولًا متكاملة لإدارة الأعمال تشمل إدارة المبيعات، المخزون، الموارد البشرية، والرواتب، مما يساعدك على التحكم الكامل في نشاطك التجاري. تتيح لك هذه الأنظمة تتبع العمليات بدقة، تحسين الكفاءة، واتخاذ قرارات مبنية على بيانات دقيقة. حلولنا مرنة وقابلة للتخصيص لتناسب مختلف أنواع الشركات، مع واجهات سهلة الاستخدام وتقارير مفصلة.',

                'fr_description' => 'Nous proposons des solutions complètes de gestion d’entreprise incluant la gestion commerciale, la paie, les ressources humaines et le suivi des opérations. Ces systèmes vous permettent de centraliser vos données, améliorer votre productivité et prendre des décisions stratégiques basées sur des informations fiables. Nos solutions sont flexibles, évolutives et adaptées à tous types d’entreprises.',

                'en_description' => 'We deliver integrated business management solutions including sales tracking, inventory management, payroll systems, and HR tools. These systems help you gain full control over your operations, improve efficiency, and make data-driven decisions. Our ERP solutions are customizable, scalable, and designed to simplify complex business processes while providing powerful reporting capabilities.',

                'type' => 'main',
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

                'ar_description' => 'نقوم بتصميم وتطوير أنظمة إدارة مخصصة تساعدك على تنظيم أعمالك وتتبع كل تفاصيل نشاطك بدقة. سواء كنت بحاجة إلى نظام لإدارة العملاء، المشاريع، الطلبات أو العمليات الداخلية، فإننا نقدم حلولًا فعالة وسهلة الاستخدام تعزز إنتاجيتك وتقلل الأخطاء البشرية.',

                'fr_description' => 'Nous développons des systèmes de gestion personnalisés pour vous aider à structurer et suivre efficacement votre activité. Nos solutions sont conçues pour améliorer la productivité, automatiser les tâches et offrir une meilleure visibilité sur vos العمليات.',

                'en_description' => 'We build custom management systems that help you track, organize, and optimize your operations. Whether it’s CRM, project tracking, or workflow automation, our solutions are designed to simplify your work and improve productivity with intuitive interfaces and reliable performance.',

                'type' => 'main',
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

                'ar_description' => 'نقدم خدمات دعم تقني متكاملة تشمل صيانة الأنظمة، إصلاح الأعطال، حل مشاكل الشبكات والاتصال، وضمان استمرارية عملك دون انقطاع. فريقنا جاهز للتدخل السريع وتقديم حلول فعالة للحفاظ على استقرار أنظمتك.',

                'fr_description' => 'Nous assurons un support informatique complet incluant la maintenance, le dépannage, la gestion des réseaux et la résolution des problèmes techniques afin de garantir la continuité de votre activité.',

                'en_description' => 'We provide reliable IT support services including troubleshooting, system maintenance, network management, and performance optimization to ensure your systems run smoothly and efficiently at all times.',

                'type' => 'main',
                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'توفير البرمجيات الدولية',
                'fr_name' => 'Acquisition de logiciels internationaux',
                'en_name' => 'International Software Procurement',

                'ar_short_description' => 'توفير البرمجيات الدولية حسب احتياجاتك.',
                'fr_short_description' => 'Fourniture de logiciels internationaux selon vos besoins.',
                'en_short_description' => 'International software procurement according to your needs.',

                'ar_description' => 'نوفر البرمجيات والحلول الدولية الأصلية وفق احتياجاتك وميزانيتك، مع المرافقة في الاختيار والتفعيل.',
                'fr_description' => 'Fourniture de logiciels internationaux et de solutions professionnelles selon vos besoins et votre budget, avec accompagnement dans le choix et l’activation.',
                'en_description' => 'Supply of international software and professional solutions according to your needs and budget, including guidance for selection and activation.',

                'type' => 'other',
                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'توفير الاشتراكات',
                'fr_name' => 'Fourniture d\'abonnements',
                'en_name' => 'Subscription Services',

                'ar_short_description' => 'اشتراكات رقمية وبرمجيات عالمية.',
                'fr_short_description' => 'Abonnements numériques et logiciels internationaux.',
                'en_short_description' => 'Digital subscriptions and software services.',

                'ar_description' => 'توفير وإدارة اشتراكات الذكاء الاصطناعي والخدمات الرقمية مثل ChatGPT وGemini وAdobe وMicrosoft وSteam Cards وغيرها.',
                'fr_description' => 'Fourniture et gestion d’abonnements IA et de services numériques tels que ChatGPT, Gemini, Adobe, Microsoft, Steam Cards et bien d’autres.',
                'en_description' => 'Provision and management of AI subscriptions and digital services such as ChatGPT, Gemini, Adobe, Microsoft, Steam Cards and more.',

                'type' => 'other',
                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'استضافة المواقع الإلكترونية',
                'fr_name' => 'Hébergement de sites web',
                'en_name' => 'Website Hosting',

                'ar_short_description' => 'استضافة آمنة وسريعة لمواقعك.',
                'fr_short_description' => 'Hébergement sécurisé et performant.',
                'en_short_description' => 'Secure and high-performance hosting.',

                'ar_description' => 'استضافة احترافية وآمنة وعالية الأداء لمواقعك وتطبيقاتك مع قابلية التوسع والدعم التقني.',
                'fr_description' => 'Hébergement professionnel, sécurisé et performant pour vos sites web et applications avec évolutivité et assistance technique.',
                'en_description' => 'Professional, secure and scalable hosting for your websites and applications with technical support.',

                'type' => 'other',
                'is_published' => true,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'ar_name' => 'الاستشارة والمرافقة',
                'fr_name' => 'Conseil et accompagnement',
                'en_name' => 'Consulting & Support',

                'ar_short_description' => 'مرافقة استراتيجية وتقنية لمشاريعك.',
                'fr_short_description' => 'Conseil stratégique et accompagnement.',
                'en_short_description' => 'Strategic consulting and project support.',

                'ar_description' => 'نقدم الاستشارة التقنية والاستراتيجية ونرافقك في جميع مراحل تنفيذ وتطوير مشاريعك الرقمية.',
                'fr_description' => 'Nous vous accompagnons à chaque étape de vos projets grâce à un conseil stratégique et technique adapté à vos objectifs.',
                'en_description' => 'We provide strategic and technical consulting and support throughout every stage of your digital projects.',

                'type' => 'other',
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

        // HERO /////////////////////////////////////////////
        DB::table('public_heroes')->insert([
            [
                'id' => 1 ,
                'ar_title' => 'إنشاء مواقع إلكترونية، برمجيات، أنظمة ERP وحلول معلوماتية مصممة للمؤسسات الجزائرية',
                'fr_title' => 'Développement de sites web, de logiciels, de systèmes ERP et de solutions informatiques conçus pour les entreprises algériennes',
                'en_title' => 'Development of websites, software, ERP systems, and IT solutions tailored for Algerian businesses.',

                'facebook_url' => 'https://www.facebook.com/HORIZON.INFORMATIQUE.BISKRA', 
                'whatsapp_url' => 'https://wa.me/+213561655100', 

                'image' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
