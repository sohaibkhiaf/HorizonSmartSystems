# WILI Web Demo

هذه نسخة ويب من WILI تعمل داخل XAMPP على:

```text
http://127.0.0.1/wili/
```

الهدف منها هو تقديم Demo قابل للتجربة من المتصفح باستعمال MySQL واحدة اسمها:

```text
hss_wili_demo
```

كل مستخدم يحصل على بياناته الخاصة داخل نفس قاعدة البيانات عن طريق `workspace_id`. لا يتم إنشاء قاعدة بيانات MySQL جديدة لكل مستخدم.

## رابط الديمو

يمكن فتح صفحة تسجيل الدخول مع بيانات جاهزة هكذا:

```text
http://127.0.0.1:8000/wili/?user=test2&&database=electric
```

أو حسب Apache/XAMPP المحلي:

```text
http://127.0.0.1/wili/?user=test2&&database=electric
```

المعنى:

- `user=test2`: اسم المستخدم/العميل داخل demo.
- `database=electric`: القالب الجاهز الذي سيتم نسخه لهذا المستخدم.
- `pin`: اختياري. إذا لم يرسل في الرابط يتم استعمال `test`.

التطبيق لا يدخل مباشرة إلى Comptoir. يفتح صفحة Login أولا، ويملأ user و PIN، وبعد الضغط على `Entrer` يفتح الـ workspace المطلوب.

القوالب الجاهزة الحالية:

- `electric`
- `pizzeria`
- `alimentation`

لا يوجد قالب `blank`.

## الملفات المهمة

- `index.html`: واجهة التطبيق.
- `styles.css`: تنسيقات عامة قديمة/رئيسية.
- `css/workspace-demo.css`: تنسيق switcher الخاص بالـ workspaces.
- `js/mysql-wilidb.js`: طبقة الاتصال بين الواجهة و PHP API.
- `js/app-init.js`: بدء التطبيق والدخول المباشر من الرابط.
- `api/index.php`: API الرئيسي.
- `api/install.php`: إنشاء الجداول والقوالب التجريبية.
- `api/config.php`: إعدادات MySQL.

## الوثائق

- `docs/PROJECT_OVERVIEW.md`
- `docs/ARCHITECTURE.md`
- `docs/DATABASE_AND_WORKSPACES.md`
- `docs/API_REFERENCE.md`
- `docs/DEMO_FLOW.md`
- `docs/MAINTENANCE_AND_TESTING.md`
