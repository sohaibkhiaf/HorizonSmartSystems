# خطة تحويل WILI Web Demo إلى MySQL Multi-Tenant

> ملاحظة تحديث: تم إلغاء قالب `blank` من الواجهة ومن إنشاء الديمو الجديد. القوالب المعتمدة حاليا هي `electric` و`pizzeria` و`alimentation` فقط. الدخول المباشر المدعوم: `/wili/?user=test2&database=electric`. للوثائق الحالية راجع `README.md` وملفات `docs/`.

## حالة التنفيذ الحالية - 2026-06-24

تم تنفيذ المرحلة الأولى عمليًا داخل:

```text
S:\xampp\htdocs\wili
```

ما تم:

- إنشاء API PHP داخل `api/`.
- إنشاء قاعدة MySQL واحدة باسم `hss_wili_demo`.
- إنشاء الجداول الأساسية: users, workspaces, settings, products, customers, suppliers, sales, purchases, finance, stock losses, audit.
- إضافة user تجريبي:
  - username: `test`
  - password/PIN: `test`
- إنشاء قوالب ثابتة:
  - `electric`
  - `pizzeria`
  - `alimentation`
  - `blank`
- إنشاء مساحة مستخدم أولية من قالب electric باسم `Demo Electric`.
- ربط الواجهة بملف `js/mysql-wilidb.js` بدل `localStorage`.
- إضافة switcher في topbar لاختيار workspace وإنشاء demo جديد من template وإعادة ضبط workspace.
- اختبار Playwright:
  - فتح `http://127.0.0.1/wili/`
  - تسجيل دخول `test/test`
  - عرض المنتجات من MySQL
  - بيع منتج من Comptoir
  - تسجيل BL في `sales`
  - تسجيل حركة في `finance_entries`
  - إنقاص stock في مساحة المستخدم
  - إنشاء workspace جديد من قالب pizzeria باسم `Demo Pizzeria Playwright`

ما بقي للمرحلة التالية:

- إكمال كل العمليات المتقدمة غير المنفذة بعد في web demo مثل returns وquotes وbackup/restore الحقيقي.
- ربط مستخدمي `hssdz.com` الحقيقيين بدل user `test`.
- إضافة import XLSX حقيقي من المتصفح إلى MySQL.
- إضافة إدارة صور المنتجات عبر upload داخل `uploads/`.
- تحسين نظام الطباعة في المتصفح حسب قيود browser printing.
- إضافة حماية CSRF وقيود صلاحيات roles عند ربطها بالموقع الحقيقي.

## الفكرة العامة

نريد نسخة Web Demo من WILI تعمل على XAMPP / MySQL وتخدم مستخدمي موقع `hssdz.com`.

لا ننشئ قاعدة بيانات لكل مستخدم لأن الاستضافة محدودة بعدد قواعد البيانات. الحل هو قاعدة بيانات واحدة فقط، وكل البيانات داخلها تكون مفصولة بواسطة `workspace_id`.

كل مستخدم في الموقع يمكنه إنشاء مساحة تجربة خاصة به من قالب جاهز مثل:

- `pizzeria`
- `alimentation`
- `electric`
- `blank`

القوالب الأصلية تبقى ثابتة ولا تتغير. عند اختيار المستخدم لقالب، يتم نسخ بيانات القالب إلى workspace خاص به داخل نفس الجداول.

## قاعدة البيانات

اسم قاعدة البيانات المقترح:

```sql
hss_wili_demo
```

لا نستخدم قواعد مثل:

```text
wili_pizzeria
wili_electric
wili_user_12
```

بل نستخدم قاعدة واحدة:

```text
hss_wili_demo
```

وكل جدول رئيسي يحتوي على:

```sql
workspace_id
```

## الجداول الأساسية

### users

هذا الجدول يمثل مستخدمي موقع `hssdz.com` أو يكون مربوطًا بجدول المستخدمين الموجود في الموقع.

```sql
users
- id
- email
- password_hash
- name
- created_at
```

إذا كان موقع `hssdz.com` عنده جدول مستخدمين جاهز، لا نكرر المستخدمين. نربط فقط بـ `external_user_id`.

### wili_workspaces

يمثل مساحة WILI الخاصة بكل مستخدم.

```sql
wili_workspaces
- id
- owner_user_id
- name
- template_key
- type
- status
- created_at
- updated_at
- last_used_at
```

القيم:

```text
type = template | user
status = active | archived | deleted
```

أمثلة:

```text
workspace_id = 1   template electric
workspace_id = 2   template pizzeria
workspace_id = 101 user demo لأحمد
workspace_id = 102 user demo لمحمد
```

### wili_workspace_members

اختياري لاحقًا إذا أردنا أكثر من مستخدم داخل نفس مساحة العمل.

```sql
wili_workspace_members
- workspace_id
- user_id
- role
- created_at
```

### settings

```sql
settings
- workspace_id
- key
- value
```

### products

```sql
products
- id
- workspace_id
- name
- barcode
- reference
- sku
- product_type
- stock_qty
- cost_cents
- retail_price_cents
- wholesale_price_cents
- assisted_price_cents
- expiry_date
- image_path
- image_blob_id
- created_at
- updated_at
```

### customers

```sql
customers
- id
- workspace_id
- name
- phone
- address
- nif
- nis
- rc
- ai
- activity
- commerce_name
- balance_cents
- created_at
- updated_at
```

### suppliers

```sql
suppliers
- id
- workspace_id
- name
- phone
- address
- nif
- nis
- rc
- ai
- activity
- commerce_name
- balance_cents
- created_at
- updated_at
```

### sales

```sql
sales
- id
- workspace_id
- ref
- customer_id
- customer_name
- date
- total_cents
- paid_cents
- due_cents
- status
- created_by
- created_at
```

### sale_lines

```sql
sale_lines
- id
- workspace_id
- sale_id
- product_id
- product_name
- qty
- unit_price_cents
- discount_cents
- total_cents
- cost_cents
```

### purchases

```sql
purchases
- id
- workspace_id
- ref
- supplier_id
- supplier_name
- date
- total_cents
- paid_cents
- due_cents
- status
- created_by
- created_at
```

### purchase_lines

```sql
purchase_lines
- id
- workspace_id
- purchase_id
- product_id
- product_name
- qty
- unit_cost_cents
- total_cents
```

### finance_entries

```sql
finance_entries
- id
- workspace_id
- date
- entry_type
- source
- ref
- ref_id
- party_key
- party_name
- amount_cents
- status
- pdf_kind
- pdf_id
- note
- created_at
```

### stock_losses

```sql
stock_losses
- id
- workspace_id
- product_id
- product_name
- qty
- reason
- note
- cost_cents
- created_at
```

### audit_events

```sql
audit_events
- id
- workspace_id
- user_id
- action
- entity_type
- entity_id
- detail
- created_at
```

### media_files

للصور داخل نسخة الويب.

```sql
media_files
- id
- workspace_id
- original_name
- mime_type
- size_bytes
- storage_path
- created_at
```

الصور لا توضع داخل `localStorage`. في نسخة MySQL/XAMPP الأفضل حفظها في:

```text
S:\xampp\htdocs\wili\uploads\{workspace_id}\...
```

وفي قاعدة البيانات نخزن المسار فقط.

## قاعدة العزل

أي استعلام يجب أن يحتوي على:

```sql
WHERE workspace_id = ?
```

ممنوع أي استعلام عام مثل:

```sql
SELECT * FROM products;
```

الصحيح:

```sql
SELECT * FROM products WHERE workspace_id = ?;
```

وينطبق هذا على:

- products
- customers
- suppliers
- sales
- purchases
- finance
- stock
- returns
- settings
- audit
- media

## القوالب Templates

القوالب تكون Workspaces من نوع `template`.

مثال:

```text
workspace_id = 1
type = template
template_key = electric
name = WILI Electric Template
```

بيانات هذا القالب موجودة في نفس الجداول:

```sql
products.workspace_id = 1
customers.workspace_id = 1
suppliers.workspace_id = 1
...
```

لكن الواجهة العادية لا تسمح للمستخدم بتعديل قوالب `template`.

## إنشاء Demo لمستخدم

عندما يدخل مستخدم جديد ويختار قالب `electric`:

1. نتحقق من تسجيل دخوله في `hssdz.com`.
2. ننشئ سجلًا جديدًا في `wili_workspaces`.
3. ننسخ بيانات template إلى workspace الجديد.
4. كل IDs الجديدة ترتبط بالـ workspace الجديد.
5. نعيد mapping بين المنتجات والعملاء والموردين والعمليات.

مثال:

```text
template workspace_id = 1
user workspace_id = 205
```

ننسخ:

```sql
products where workspace_id = 1
```

إلى:

```sql
products where workspace_id = 205
```

## Reset Demo

زر `Reset demo` يعمل هكذا:

1. يحذف بيانات workspace الخاصة بالمستخدم فقط.
2. ينسخ القالب الأصلي من جديد.
3. لا يمس template.
4. لا يمس workspaces مستخدمين آخرين.

## Blank Demo

إذا اختار المستخدم `blank`:

1. ننشئ workspace فارغ.
2. ننشئ settings افتراضية فقط.
3. لا ننسخ منتجات أو عمليات.
4. يبدأ المستخدم يملأ البيانات بنفسه.

## API المقترحة

ننشئ API PHP داخل:

```text
S:\xampp\htdocs\wili\api
```

الملفات المقترحة:

```text
api/
- config.php
- db.php
- auth.php
- workspaces.php
- products.php
- customers.php
- suppliers.php
- sales.php
- purchases.php
- finance.php
- reports.php
- print.php
- media.php
```

## الاتصال من الواجهة

بدل `browser-wilidb.js` الحالي الذي يستخدم `localStorage`، ننشئ:

```text
js/mysql-wilidb.js
```

وهذا الملف يعرّف نفس الواجهات:

```js
window.wiliDb.listProducts()
window.wiliDb.createSale()
window.wiliDb.getFinanceReport()
...
```

لكن بدل التخزين المحلي، يستدعي:

```js
fetch("/wili/api/products.php?action=list")
```

أو API موحدة:

```js
fetch("/wili/api/index.php", {
  method: "POST",
  body: JSON.stringify({
    action: "products.list",
    workspace_id: currentWorkspaceId
  })
})
```

الأفضل استعمال API موحدة في البداية لتسريع التطوير:

```text
api/index.php
```

مع dispatch حسب `action`.

## Session المستخدم

إذا كان `hssdz.com` و`/wili` على نفس الدومين، نستعمل PHP Session أو Token.

المطلوب:

```text
user_id
email
name
current_workspace_id
```

لا نثق بـ `workspace_id` القادم من الواجهة وحده. يجب التأكد في backend أن:

```sql
workspace.owner_user_id = session.user_id
```

أو أن المستخدم عضو في workspace.

## الطباعة

الطباعة في الويب تكون HTML Print وليس Electron PDF.

الخطة:

1. API يرجع HTML document جاهز للطباعة.
2. الواجهة تفتح modal أو iframe.
3. زر Print يستعمل:

```js
iframe.contentWindow.print()
```

أو صفحة:

```text
/wili/print/sale.php?id=...
```

مع CSS خاص:

```css
@media print {
  body { margin: 0; }
}
```

أنواع الطباعة:

- Ticket caisse
- Bon de livraison
- Facture vente
- Bon reception
- Facture achat
- Recu paiement client
- Recu paiement fournisseur
- Bon retour client
- Bon retour fournisseur
- Barcode labels

## الصور

الصور تعمل عبر upload:

```text
POST /wili/api/media.php
```

وتخزن في:

```text
uploads/{workspace_id}/{file_id}.jpg
```

داخل MySQL:

```sql
media_files
```

وفي product:

```sql
image_blob_id
image_path
```

## الحماية

يجب منع:

- وصول مستخدم لبيانات مستخدم آخر.
- تعديل template من المستخدم العادي.
- تمرير workspace_id يدوي ليس ملكه.
- رفع ملفات غير صور.
- رفع ملفات كبيرة جدًا.

كل API يجب أن تعمل:

1. تحقق login.
2. تحقق workspace ownership.
3. تحقق role إذا احتجنا roles لاحقًا.
4. تنفيذ query مع `workspace_id`.

## الفهارس Indexes

مهمة جدًا لأن كل الجداول فيها `workspace_id`.

أمثلة:

```sql
CREATE INDEX idx_products_workspace_name ON products(workspace_id, name);
CREATE INDEX idx_products_workspace_barcode ON products(workspace_id, barcode);
CREATE INDEX idx_products_workspace_reference ON products(workspace_id, reference);

CREATE INDEX idx_customers_workspace_name ON customers(workspace_id, name);
CREATE INDEX idx_suppliers_workspace_name ON suppliers(workspace_id, name);

CREATE INDEX idx_sales_workspace_date ON sales(workspace_id, date);
CREATE INDEX idx_purchases_workspace_date ON purchases(workspace_id, date);
CREATE INDEX idx_finance_workspace_date ON finance_entries(workspace_id, date);
```

## تنظيف البيانات

لأن demo يمكن أن يكبر، نحتاج تنظيف تلقائي:

- حذف workspaces غير النشطة بعد 30 يوم.
- أو أرشفتها.
- أو تحديد حد لكل مستخدم:
  - عدد منتجات أقصى في demo.
  - عدد عمليات أقصى.
  - حجم صور أقصى.

## مراحل التنفيذ

### المرحلة 1: البنية الأساسية

- إنشاء قاعدة `hss_wili_demo`.
- إنشاء الجداول الأساسية.
- إنشاء `api/config.php`.
- إنشاء `api/db.php`.
- إنشاء API موحدة `api/index.php`.
- إنشاء نظام Session بسيط للتجربة محليًا.

### المرحلة 2: Workspaces و Templates

- إنشاء جدول `wili_workspaces`.
- إدخال قوالب:
  - electric
  - alimentation
  - pizzeria
  - blank
- إنشاء دالة clone template.
- إنشاء API:
  - list templates
  - create workspace from template
  - reset workspace
  - switch workspace

### المرحلة 3: ربط المنتجات والعملاء والموردين

- تحويل:
  - products
  - customers
  - suppliers
- تعديل `window.wiliDb` لاستعمال MySQL API.
- التأكد من البحث والصفحات.
- التأكد من الصور.

### المرحلة 4: العمليات

- sales
- sale_lines
- purchases
- purchase_lines
- stock update
- finance entries
- payments
- returns
- stock losses

### المرحلة 5: التقارير والجورنال

- Journal client
- Journal fournisseur
- Finance archive
- Finance analyse
- Dashboard
- Product trace
- Customer debt
- Supplier debt

### المرحلة 6: الطباعة

- ticket caisse
- BL
- Facture
- BR
- Recu paiement
- Retours
- Barcode labels

### المرحلة 7: التكامل مع hssdz.com

- ربط session المستخدم الحقيقي.
- منع الدخول بدون login.
- ربط workspaces بالمستخدم.
- شاشة اختيار demo template.
- شاشة إدارة demos الخاصة بالمستخدم.

### المرحلة 8: الاختبار

- مستخدم A يختار Electric ويعدل البيانات.
- مستخدم B يختار Electric ويتأكد أن بياناته الأصلية لم تتأثر.
- Reset demo لمستخدم A.
- Blank demo.
- تجربة بيع وشراء ومردودات ومالية.
- تجربة الصور.
- تجربة الطباعة.
- تجربة منع الوصول لـ workspace لا يملكه المستخدم.

## شروط النجاح

يعتبر النظام ناجحًا إذا:

- توجد قاعدة MySQL واحدة فقط.
- كل البيانات مفصولة بـ `workspace_id`.
- كل مستخدم يحصل على نسخة خاصة من template.
- template الأصلي لا يتغير.
- يمكن إنشاء demo فارغ.
- يمكن reset demo.
- كل العمليات الأساسية تعمل:
  - products
  - customers
  - suppliers
  - sales
  - purchases
  - returns
  - finance
  - journals
  - stock trace
  - reports
  - print
- الصور تعمل داخل المتصفح.
- الطباعة تعمل بشكل صحيح.
- لا يمكن لمستخدم رؤية أو تعديل بيانات مستخدم آخر.

## ملاحظة مهمة

نسخة `localStorage` الحالية مناسبة كنموذج أولي سريع، لكنها ليست مناسبة للـ demo الحقيقي على `hssdz.com`.

النسخة الصحيحة للسيناريو المطلوب هي:

```text
HTML/CSS/JS frontend
PHP API
MySQL single database
workspace_id multi-tenancy
uploads per workspace
browser print
```
