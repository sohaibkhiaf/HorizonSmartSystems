# قاعدة البيانات والـ Workspaces

## قاعدة البيانات

اسم قاعدة البيانات:

```text
hss_wili_demo
```

يتم إنشاؤها من:

```text
api/install.php
```

## الجداول الرئيسية

### users

يحفظ مستخدمي demo:

- `id`
- `email`
- `password_hash`
- `name`

في demo الحالي يمكن إنشاء مستخدم تلقائيا من الرابط.

### wili_workspaces

يمثل كل قاعدة بيانات منطقية داخل MySQL:

- `id`
- `owner_user_id`
- `name`
- `template_key`
- `type`
- `status`

الأنواع:

- `template`: قالب جاهز.
- `user`: نسخة خاصة بمستخدم.

### settings

إعدادات كل workspace:

- `workspace_id`
- `setting_key`
- `setting_value`

### products

المنتجات والمخزون والأسعار.

### customers / suppliers

العملاء والموردون.

### sales / sale_lines

المبيعات وتفاصيلها.

### purchases / purchase_lines

المشتريات وتفاصيلها.

### finance_entries

الأرشيف المالي والحركات المالية.

### stock_losses

خسائر المخزون.

### audit_events

تتبع العمليات.

## القوالب الجاهزة

القوالب النشطة حاليا:

```text
electric
pizzeria
alimentation
```

تم إلغاء `blank` من الواجهة ومن إنشاء القوالب الجديدة. إذا كان موجودا سابقا في قاعدة قديمة يتم أرشفته بواسطة `install.php`.

## اختيار القالب من الرابط

الرابط:

```text
/wili/?user=test2&database=electric
```

ينشئ أو يفتح:

```text
user: test2
workspace template_key: electric
```

إذا كان للمستخدم workspace سابق بنفس القالب يتم فتحه. إذا لم يكن موجودا يتم نسخه من template.

## قاعدة العزل

كل استعلام تجاري يجب أن يحتوي:

```sql
WHERE workspace_id = ?
```

بدون هذا الشرط يمكن أن تختلط بيانات المستخدمين.

