# المعمارية

المشروع مبني كواجهة HTML/CSS/JS تقليدية مع API بسيط بـ PHP.

## الطبقات

### 1. واجهة المستخدم

الملفات الأساسية:

- `index.html`
- `styles.css`
- `css/*.css`
- `js/pages/*.js`
- `js/core/*.js`

الواجهة لا تتصل مباشرة بقاعدة البيانات. كل العمليات تمر عبر:

```text
window.wiliDb
```

وهو معرف في:

```text
js/mysql-wilidb.js
```

### 2. طبقة Bridge في JavaScript

`js/mysql-wilidb.js` يوفر نفس أسماء الدوال التي كانت تستعملها نسخة سطح المكتب قدر الإمكان، مثل:

- `listProductsPage`
- `searchProducts`
- `createSale`
- `createPurchase`
- `getFinanceSummary`
- `exportSaleCartPdf`

هذه الدوال ترسل JSON إلى:

```text
api/index.php
```

### 3. PHP API

`api/index.php` يستقبل:

```json
{
  "action": "products.page",
  "data": {}
}
```

ثم ينفذ الدالة المناسبة ويرجع:

```json
{
  "ok": true,
  "data": {}
}
```

أو:

```json
{
  "ok": false,
  "error": "message"
}
```

### 4. MySQL

الاتصال مضبوط في:

```text
api/config.php
```

القيم الحالية:

```text
host: 127.0.0.1
port: 3306
database: wchnqnhuwj_wili_demo
user: root
password:
```

## بدء التشغيل

عند فتح التطبيق:

1. `mysql-wilidb.js` يكون جاهزا.
2. `app-init.js` يبدأ التطبيق.
3. إذا وجد الرابط `?user=...&database=...` ينفذ دخول مباشر عبر `auth.demoEnter`.
4. يتم تحميل الإعدادات واللغة.
5. تعرض الواجهة الرئيسية.

## مبدأ مهم

أي عملية قراءة أو كتابة تجارية يجب أن تمر بـ `workspace_id()` في PHP حتى لا تختلط بيانات المستخدمين.

