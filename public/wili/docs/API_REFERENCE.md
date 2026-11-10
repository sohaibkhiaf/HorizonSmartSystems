# مرجع API المختصر

كل الطلبات ترسل إلى:

```text
POST /wili/api/index.php
```

الشكل العام:

```json
{
  "action": "action.name",
  "data": {}
}
```

## Auth

### app.getState

يرجع حالة التطبيق وقاعدة البيانات.

### auth.hasUsers

يتحقق هل يوجد مستخدمون.

### auth.login

دخول عادي:

```json
{
  "action": "auth.login",
  "data": {
    "username": "test",
    "pin": "test"
  }
}
```

### auth.demoEnter

دخول مباشر للديمو عبر API. هذا endpoint متاح لكنه ليس flow الواجهة الافتراضي حاليا، لأن الواجهة تعرض Login أولا:

```json
{
  "action": "auth.demoEnter",
  "data": {
    "user": "test2",
    "database": "electric"
  }
}
```

يرجع:

```json
{
  "user": {},
  "workspace": {}
}
```

## Workspaces

### workspaces.templates

يرجع القوالب الجاهزة فقط:

- `electric`
- `pizzeria`
- `alimentation`

### workspaces.mine

يرجع workspaces الخاصة بالمستخدم الحالي.

### workspaces.switch

تبديل workspace:

```json
{
  "workspace_id": 8
}
```

### workspaces.createFromTemplate

إنشاء workspace جديد من قالب جاهز:

```json
{
  "template_key": "electric",
  "name": "Demo Electric"
}
```

### workspaces.reset

إعادة workspace الحالي من قالبه الأصلي.

## Products

- `products.list`
- `products.page`
- `products.summary`
- `products.search`
- `products.lookup`
- `products.create`
- `products.update`
- `products.delete`

## Customers

- `customers.list`
- `customers.page`
- `customers.create`
- `customers.update`
- `customers.delete`

## Suppliers

- `suppliers.list`
- `suppliers.page`
- `suppliers.create`
- `suppliers.update`
- `suppliers.delete`

## Sales

- `sales.list`
- `sales.create`
- `sales.update`
- `sales.details`
- `sales.delete`
- `sales.payment.add`
- `sales.dues`

## Purchases

- `purchases.list`
- `purchases.create`
- `purchases.update`
- `purchases.details`
- `purchases.delete`
- `purchases.payment.add`
- `purchases.dues`

## Finance

- `finance.entries.list`
- `finance.report`
- `finance.summary`
- `finance.entry.add`
- `debts.summary`

## PDF/HTML Documents

في نسخة الويب، PDF يتحول عمليا إلى HTML قابل للمعاينة والطباعة من المتصفح:

- `pdf.saleCart`
- `pdf.saleInvoice`
- `pdf.purchase`
- `pdf.simple`
