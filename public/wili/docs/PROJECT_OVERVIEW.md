# شرح عام للمشروع

WILI Web Demo هو تحويل ويب لتجربة WILI POS / Gestion Stock. يعمل داخل المتصفح ويستعمل PHP + MySQL عبر XAMPP.

## الهدف

الفكرة هي أن موقع مثل `hssdz.com` يمكن أن يرسل الزائر إلى رابط Demo جاهز:

```text
/wili/?user=USER_ID&database=electric
```

عند فتح الرابط:

1. يتم إنشاء المستخدم إذا لم يكن موجودا.
2. يتم اختيار قالب جاهز مثل `electric`.
3. يتم إنشاء workspace خاص بالمستخدم من هذا القالب إذا لم يكن موجودا.
4. يدخل المستخدم مباشرة إلى WILI بدون Activation وبدون إنشاء قاعدة MySQL جديدة.

## لماذا Workspace وليس Database لكل مستخدم؟

لأن الاستضافة قد تسمح بعدد محدود من قواعد البيانات. لذلك كل البيانات محفوظة في قاعدة واحدة:

```text
hss_wili_demo
```

والعزل يتم بواسطة:

```text
workspace_id
```

كل جدول تجاري مهم يحتوي `workspace_id`، مثل:

- `products`
- `customers`
- `suppliers`
- `sales`
- `purchases`
- `finance_entries`
- `stock_losses`
- `audit_events`

## القوالب الجاهزة

القالب هو workspace من نوع:

```text
type = template
owner_user_id = NULL
```

القوالب النشطة:

- `electric`
- `pizzeria`
- `alimentation`

القوالب لا تعدل مباشرة من المستخدم. عند اختيار قالب، يتم نسخ بياناته إلى workspace خاص بالمستخدم.

## أهم المزايا الحالية

- Comptoir / Vente comptoir.
- Produits مع بحث سريع.
- Clients و Fournisseurs.
- Achats.
- Finance.
- Sauvegardes و Import XLSX في الواجهة.
- PDF/HTML preview والطباعة من المتصفح.
- Workspaces متعددة لكل مستخدم من قوالب جاهزة.

