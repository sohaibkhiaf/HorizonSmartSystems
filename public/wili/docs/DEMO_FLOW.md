# سيناريو الديمو

## الرابط المطلوب

```text
http://127.0.0.1:8000/wili/?user=test2&&database=electric
```

أو:

```text
http://127.0.0.1/wili/?user=test2&&database=electric
```

## ماذا يحدث عند فتح الرابط؟

1. المتصفح يفتح `index.html`.
2. `js/app-init.js` يقرأ الرابط.
3. إذا وجد `user` و`database` يفتح صفحة Login.
4. يتم ملء خانة المستخدم تلقائيا.
5. يتم ملء PIN تلقائيا. القيمة الافتراضية هي `test`.
6. لا يدخل التطبيق مباشرة. يجب الضغط على `Entrer`.

## ماذا يحدث بعد الضغط على Entrer؟

1. الواجهة ترسل `username`, `pin`, و`database` إلى `auth.login`.
2. إذا المستخدم غير موجود وكان `database` صحيحا، يتم إنشاؤه.
3. إذا workspace بهذا القالب غير موجود للمستخدم، ينسخه من template.
4. يتم حفظ `user_id` و`workspace_id` في session.
5. تفتح واجهة WILI على workspace المطلوب.

## أمثلة روابط

```text
/wili/?user=ahmed&database=electric
/wili/?user=karim&database=pizzeria
/wili/?user=sara&database=alimentation
```

كل مستخدم يحصل على بيانات مستقلة.

يمكن تمرير PIN مخصص:

```text
/wili/?user=ahmed&database=electric&pin=1234
```

## aliases مقبولة

### electric

- `electric`
- `electrique`
- `electricity`

### pizzeria

- `pizzeria`
- `pizza`

### alimentation

- `alimentation`
- `allimatation`
- `aliment`
- `grocery`

## ما الذي ألغي؟

تم إلغاء:

```text
blank
vide
empty
```

إذا أرسل الرابط هذه القيم فلن يتم إنشاء workspace.

## اختيار workspace من داخل التطبيق

بعد الدخول يظهر switcher في الأعلى:

- اختيار workspace موجود.
- اختيار قالب جاهز.
- إنشاء demo جديد.
- Reset للـ workspace الحالي من قالبه.

القوالب المعروضة لا تحتوي على `blank`.
