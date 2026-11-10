# الصيانة والاختبار

## تشغيل installer

لإنشاء الجداول والقوالب:

```powershell
S:\xampp\php\php.exe S:\xampp\htdocs\wili\api\install.php
```

أو من المتصفح:

```text
http://127.0.0.1/wili/api/install.php
```

## فحص PHP

```powershell
S:\xampp\php\php.exe -l S:\xampp\htdocs\wili\api\index.php
S:\xampp\php\php.exe -l S:\xampp\htdocs\wili\api\install.php
```

## فحص JavaScript

```powershell
node --check S:\xampp\htdocs\wili\js\mysql-wilidb.js
node --check S:\xampp\htdocs\wili\js\app-init.js
```

## اختبار الدخول المباشر

```powershell
$body = @{ action = 'auth.demoEnter'; data = @{ user = 'test2'; database = 'electric' } } | ConvertTo-Json -Depth 5
Invoke-RestMethod -Uri 'http://127.0.0.1/wili/api/index.php' -Method Post -ContentType 'application/json' -Body $body
```

يجب أن يرجع:

- `ok = true`
- `user`
- `workspace`
- `template_key = electric`

## اختبار القوالب

```powershell
$body = @{ action = 'workspaces.templates'; data = @{} } | ConvertTo-Json -Depth 5
Invoke-RestMethod -Uri 'http://127.0.0.1/wili/api/index.php' -Method Post -ContentType 'application/json' -Body $body
```

يجب ألا يظهر `blank`.

## نقاط يجب الانتباه لها

- لا تضف استعلاما تجاريا بدون `workspace_id`.
- لا تنشئ قاعدة MySQL لكل مستخدم.
- لا ترجع قالب `blank` في الواجهة.
- إذا أضيف قالب جديد، أضفه في:
  - `api/install.php`
  - `normalize_template_key` داخل `api/index.php`
  - `normalizeTemplateKey` داخل `js/mysql-wilidb.js`

