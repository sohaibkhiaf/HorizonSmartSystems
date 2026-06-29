<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';

session_name(WILI_SESSION_NAME);
session_start();

try {
    $payload = read_json_body();
    $action = (string)($payload['action'] ?? $_GET['action'] ?? '');
    $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
    if ($action === '') fail('missing_action');

    $public = ['app.getState', 'auth.hasUsers', 'auth.login', 'auth.demoEnter', 'auth.bootstrapFirstAdmin', 'workspaces.templates', 'settings.get', 'products.page'];
    if (!in_array($action, $public, true)) {
        require_login();
    }

    switch ($action) {
        case 'app.getState':
            ok(['activated' => true, 'mode' => 'mysql', 'database' => WILI_DB_NAME]);
        case 'auth.hasUsers':
            ok(['has_users' => ((int)db_one("SELECT COUNT(*) c FROM users")['c']) > 0]);
        case 'auth.login':
            login($data);
        case 'auth.demoEnter':
            demo_enter($data);
        case 'auth.bootstrapFirstAdmin':
            bootstrap_first_admin($data);
        case 'auth.logout':
            session_destroy();
            ok(['logged_out' => true]);
        case 'auth.me':
            ok(['user' => current_user(), 'workspace' => current_workspace()]);

        case 'workspaces.templates':
            ok(db_all("SELECT id, name, template_key FROM wili_workspaces WHERE type='template' AND status='active' AND template_key<>'blank' ORDER BY template_key"));
        case 'workspaces.mine':
            ok(my_workspaces());
        case 'workspaces.current':
            ok(current_workspace());
        case 'workspaces.switch':
            switch_workspace((int)($data['workspace_id'] ?? 0));
        case 'workspaces.createFromTemplate':
            create_workspace_from_template((string)($data['template_key'] ?? 'electric'), (string)($data['name'] ?? 'Demo WILI'));
        case 'workspaces.reset':
            reset_workspace();

        case 'settings.get':
            if (!isset($_SESSION['user_id'])) ok(default_settings());
            ok(settings_for(workspace_id()));
        case 'settings.update':
            update_settings($data);

        case 'products.list':
            ok(products_list($data));
        case 'products.page':
            if (!isset($_SESSION['user_id'])) ok([]);
            ok(products_page($data));
        case 'products.summary':
            ok(products_summary());
        case 'products.search':
            ok(products_search((string)($data['query'] ?? ''), (int)($data['limit'] ?? 100)));
        case 'products.lookup':
            ok(product_lookup((string)($data['query'] ?? '')));
        case 'products.create':
            ok(product_save(null, $data));
        case 'products.update':
            ok(product_save((int)($data['id'] ?? 0), $data['payload'] ?? $data));
        case 'products.delete':
            db_exec("DELETE FROM products WHERE workspace_id=? AND id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            audit('product.delete', 'product', (string)($data['id'] ?? ''));
            ok(['deleted' => true]);

        case 'customers.list':
            ok(parties_list('customers', $data));
        case 'customers.page':
            ok(parties_page('customers', $data));
        case 'customers.create':
            ok(party_save('customers', null, $data));
        case 'customers.update':
            ok(party_save('customers', (int)($data['id'] ?? 0), $data['payload'] ?? $data));
        case 'customers.delete':
            db_exec("DELETE FROM customers WHERE workspace_id=? AND id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            ok(['deleted' => true]);

        case 'suppliers.list':
            ok(parties_list('suppliers', $data));
        case 'suppliers.page':
            ok(parties_page('suppliers', $data));
        case 'suppliers.create':
            ok(party_save('suppliers', null, $data));
        case 'suppliers.update':
            ok(party_save('suppliers', (int)($data['id'] ?? 0), $data['payload'] ?? $data));
        case 'suppliers.delete':
            db_exec("DELETE FROM suppliers WHERE workspace_id=? AND id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            ok(['deleted' => true]);

        case 'sales.list':
            ok(sales_list((int)($data['limit'] ?? 1000)));
        case 'sales.create':
            ok(create_sale($data));
        case 'sales.update':
            ok(update_sale($data));
        case 'sales.details':
            ok(sale_details((string)($data['id'] ?? '')));
        case 'sales.delete':
            db_exec("DELETE FROM sale_lines WHERE workspace_id=? AND sale_id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            db_exec("DELETE FROM sales WHERE workspace_id=? AND id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            ok(['deleted' => true]);
        case 'sales.payment.add':
            ok(add_sale_payment($data));
        case 'sales.dues':
            ok(db_all("SELECT * FROM sales WHERE workspace_id=? AND (?='' OR customer_id=?) AND due_cents>0 ORDER BY date DESC", [workspace_id(), (string)($data['customer_id'] ?? ''), (int)($data['customer_id'] ?? 0)]));

        case 'purchases.list':
            ok(purchases_list((int)($data['limit'] ?? 1000)));
        case 'purchases.create':
            ok(create_purchase($data));
        case 'purchases.update':
            ok(update_purchase($data));
        case 'purchases.details':
            ok(purchase_details((string)($data['id'] ?? '')));
        case 'purchases.delete':
            db_exec("DELETE FROM purchase_lines WHERE workspace_id=? AND purchase_id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            db_exec("DELETE FROM purchases WHERE workspace_id=? AND id=?", [workspace_id(), (int)($data['id'] ?? 0)]);
            ok(['deleted' => true]);
        case 'purchases.payment.add':
            ok(add_purchase_payment($data));
        case 'purchases.dues':
            ok(array_map('purchase_row_public', db_all("SELECT * FROM purchases WHERE workspace_id=? AND (?='' OR customer_id=?) AND due_cents>0 ORDER BY date DESC", [workspace_id(), (string)($data['supplier_id'] ?? ''), (int)($data['supplier_id'] ?? 0)])));

        case 'stock.loss.add':
            ok(add_stock_loss($data));
        case 'stock.loss.list':
            ok(db_all("SELECT * FROM stock_losses WHERE workspace_id=? ORDER BY created_at DESC LIMIT " . limit_sql((int)($data['limit'] ?? 500)), [workspace_id()]));
        case 'products.trace':
            ok(product_trace((int)($data['product_id'] ?? 0)));
        case 'products.traceAll':
            ok([]);

        case 'finance.entries.list':
        case 'finance.report':
            ok(finance_report((int)($data['limit'] ?? 1000)));
        case 'finance.summary':
            ok(finance_summary());
        case 'finance.entry.add':
            ok(add_manual_finance($data));
        case 'debts.summary':
            ok(debts_summary());
        case 'audit.list':
            ok(db_all("SELECT * FROM audit_events WHERE workspace_id=? ORDER BY created_at DESC LIMIT " . limit_sql((int)($data['limit'] ?? 500)), [workspace_id()]));
        case 'reports.daily':
            ok(daily_report());
        case 'media.upload':
            ok(upload_media($data));

        case 'pdf.saleCart':
            ok(document_payload('Ticket de caisse', cart_html($data), 'ticket.html', 'ticket'));
        case 'pdf.saleInvoice':
            $sale = sale_details((string)($data['id'] ?? ''));
            $saleTitle = ((string)($data['document_mode'] ?? '') === 'facture') ? 'FACTURE DE VENTE' : sale_doc_badge($sale, false);
            ok(document_payload($saleTitle, sale_html($sale, (string)($data['document_mode'] ?? 'bon')), 'sale.html', 'document'));
        case 'pdf.purchase':
            $purchase = purchase_details((string)($data['id'] ?? $data['purchase_id'] ?? ''));
            $purchaseTitle = ((string)($data['document_mode'] ?? '') === 'facture') ? "FACTURE D'ACHAT" : purchase_doc_badge($purchase, false);
            ok(document_payload($purchaseTitle, purchase_html($purchase, (string)($data['document_mode'] ?? 'bon')), 'purchase.html', 'document'));
        case 'pdf.simple':
            ok(document_payload((string)($data['title'] ?? 'Document'), simple_document_html($data), 'document.html'));

        default:
            fail('unknown_action: ' . $action, 404);
    }
} catch (Throwable $e) {
    fail('server_error', 500, $e->getMessage());
}

function require_login(): void
{
    if (!isset($_SESSION['user_id'])) fail('not_authenticated', 401);
}

function current_user(): array
{
    $user = db_one("SELECT id, email, name FROM users WHERE id=?", [(int)($_SESSION['user_id'] ?? 0)]);
    if (!$user) fail('not_authenticated', 401);
    return [
        'id' => (string)$user['id'],
        'username' => $user['email'],
        'display_name' => $user['name'],
        'role' => 'admin',
        'email' => $user['email'],
    ];
}

function workspace_id(): int
{
    $wid = (int)($_SESSION['workspace_id'] ?? 0);
    if ($wid > 0) return $wid;
    $workspaces = my_workspaces();
    if (!$workspaces) fail('workspace_required', 409);
    $_SESSION['workspace_id'] = (int)$workspaces[0]['id'];
    return (int)$workspaces[0]['id'];
}

function current_workspace(): array
{
    $wid = workspace_id();
    $row = db_one("SELECT * FROM wili_workspaces WHERE id=? AND owner_user_id=? AND type='user' AND status='active'", [$wid, (int)$_SESSION['user_id']]);
    if (!$row) fail('workspace_forbidden', 403);
    return $row;
}

function my_workspaces(): array
{
    return db_all("SELECT id, name, template_key, type, status, created_at, updated_at FROM wili_workspaces WHERE owner_user_id=? AND type='user' AND status='active' AND template_key<>'blank' ORDER BY updated_at DESC", [(int)$_SESSION['user_id']]);
}

function normalize_template_key(string $value): string
{
    $key = strtolower(trim($value));
    $key = (string)preg_replace('/[\s_-]+/', '', $key);
    $aliases = [
        'electric' => 'electric',
        'electrique' => 'electric',
        'electricity' => 'electric',
        'pizzeria' => 'pizzeria',
        'pizza' => 'pizzeria',
        'alimentation' => 'alimentation',
        'allimatation' => 'alimentation',
        'aliment' => 'alimentation',
        'grocery' => 'alimentation',
    ];
    return $aliases[$key] ?? '';
}

function template_label(string $templateKey): string
{
    return match ($templateKey) {
        'electric' => 'Electric',
        'pizzeria' => 'Pizzeria',
        'alimentation' => 'Alimentation',
        default => ucfirst($templateKey),
    };
}

function login(array $data): never
{
    $email = trim((string)($data['username'] ?? $data['email'] ?? ''));
    $pin = (string)($data['pin'] ?? $data['password'] ?? '');
    $templateKey = normalize_template_key((string)($data['database'] ?? $data['db'] ?? $data['template_key'] ?? $data['template'] ?? ''));
    $user = db_one("SELECT * FROM users WHERE email=?", [$email]);
    if (!$user) {
        if ($templateKey === '' || $email === '' || $pin === '') fail('invalid_credentials', 401);
        $userId = db_insert("INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)", [$email, password_hash($pin, PASSWORD_DEFAULT), $email]);
    } else {
        if (!password_verify($pin, (string)$user['password_hash'])) fail('invalid_credentials', 401);
        $userId = (int)$user['id'];
    }
    $_SESSION['user_id'] = $userId;
    if ($templateKey !== '') {
        $_SESSION['workspace_id'] = ensure_user_workspace_for_template($userId, $templateKey);
    } else {
        $workspaces = my_workspaces();
        if (!$workspaces) {
            $_SESSION['workspace_id'] = ensure_user_workspace_for_template($userId, 'electric');
            $workspaces = my_workspaces();
        }
        $_SESSION['workspace_id'] = (int)($workspaces[0]['id'] ?? ($_SESSION['workspace_id'] ?? 0));
    }
    ok(current_user());
}

function demo_enter(array $data): never
{
    $email = trim((string)($data['user'] ?? $data['username'] ?? $data['email'] ?? ''));
    $email = substr($email, 0, 190);
    if ($email === '') fail('user_required');

    $templateKey = normalize_template_key((string)($data['database'] ?? $data['db'] ?? $data['template_key'] ?? $data['template'] ?? 'electric'));
    if ($templateKey === '') fail('template_not_found');

    $user = db_one("SELECT id FROM users WHERE email=?", [$email]);
    if ($user) {
        $userId = (int)$user['id'];
    } else {
        $userId = db_insert("INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)", [$email, password_hash('test', PASSWORD_DEFAULT), $email]);
    }

    $_SESSION['user_id'] = $userId;

    $workspaceId = ensure_user_workspace_for_template($userId, $templateKey);
    $_SESSION['workspace_id'] = $workspaceId;
    db_exec("UPDATE wili_workspaces SET last_used_at=NOW() WHERE id=?", [$workspaceId]);
    ok(['user' => current_user(), 'workspace' => current_workspace()]);
}

function ensure_user_workspace_for_template(int $userId, string $templateKey): int
{
    $templateKey = normalize_template_key($templateKey);
    if ($templateKey === '') fail('template_not_found');
    $workspace = db_one("SELECT id FROM wili_workspaces WHERE owner_user_id=? AND type='user' AND status='active' AND template_key=? ORDER BY updated_at DESC, id DESC LIMIT 1", [$userId, $templateKey]);
    if ($workspace) {
        return (int)$workspace['id'];
    }
    $template = db_one("SELECT id FROM wili_workspaces WHERE type='template' AND status='active' AND template_key=? LIMIT 1", [$templateKey]);
    if (!$template) fail('template_not_found');
    return clone_workspace_api((int)$template['id'], $userId, 'Demo ' . template_label($templateKey));
}

function bootstrap_first_admin(array $data): never
{
    $email = trim((string)($data['username'] ?? $data['email'] ?? ''));
    if ($email === '') fail('email_required');
    $exists = db_one("SELECT id FROM users WHERE email=?", [$email]);
    if ($exists) fail('user_exists');
    $id = db_insert("INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)", [$email, password_hash((string)($data['pin'] ?? 'test'), PASSWORD_DEFAULT), (string)($data['display_name'] ?? $email)]);
    $_SESSION['user_id'] = $id;
    create_workspace_from_template('electric', 'Demo Electric');
}

function switch_workspace(int $workspaceId): never
{
    $row = db_one("SELECT id FROM wili_workspaces WHERE id=? AND owner_user_id=? AND type='user' AND status='active'", [$workspaceId, (int)$_SESSION['user_id']]);
    if (!$row) fail('workspace_forbidden', 403);
    $_SESSION['workspace_id'] = $workspaceId;
    db_exec("UPDATE wili_workspaces SET last_used_at=NOW() WHERE id=?", [$workspaceId]);
    ok(current_workspace());
}

function create_workspace_from_template(string $templateKey, string $name): never
{
    $templateKey = normalize_template_key($templateKey);
    if ($templateKey === '') fail('template_not_found');
    $template = db_one("SELECT id FROM wili_workspaces WHERE type='template' AND status='active' AND template_key=? LIMIT 1", [$templateKey]);
    if (!$template) fail('template_not_found');
    $newId = clone_workspace_api((int)$template['id'], (int)$_SESSION['user_id'], $name ?: ('Demo ' . $templateKey));
    $_SESSION['workspace_id'] = $newId;
    ok(current_workspace());
}

function reset_workspace(): never
{
    $workspace = current_workspace();
    $templateKey = normalize_template_key((string)($workspace['template_key'] ?? '')) ?: 'electric';
    $template = db_one("SELECT id FROM wili_workspaces WHERE type='template' AND status='active' AND template_key=? LIMIT 1", [$templateKey]);
    if (!$template) fail('template_not_found');
    clear_workspace((int)$workspace['id']);
    copy_workspace_data((int)$template['id'], (int)$workspace['id']);
    ok(current_workspace());
}

function clone_workspace_api(int $templateId, int $ownerUserId, string $name): int
{
    $tpl = db_one("SELECT template_key FROM wili_workspaces WHERE id=?", [$templateId]);
    $templateKey = normalize_template_key((string)($tpl['template_key'] ?? '')) ?: 'electric';
    $newId = db_insert("INSERT INTO wili_workspaces (owner_user_id, name, template_key, type) VALUES (?, ?, ?, 'user')", [$ownerUserId, $name, $templateKey]);
    copy_workspace_data($templateId, $newId);
    return $newId;
}

function clear_workspace(int $workspaceId): void
{
    foreach (['settings','counters','products','customers','suppliers','sale_lines','sales','purchase_lines','purchases','finance_entries','stock_losses','audit_events'] as $table) {
        db_exec("DELETE FROM `$table` WHERE workspace_id=?", [$workspaceId]);
    }
}

function copy_workspace_data(int $from, int $to): void
{
    foreach (['settings','products','customers','suppliers'] as $table) {
        $rows = db_all("SELECT * FROM `$table` WHERE workspace_id=?", [$from]);
        foreach ($rows as $row) {
            unset($row['id']);
            $row['workspace_id'] = $to;
            $cols = array_keys($row);
            $sql = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")";
            if ($table === 'settings') $sql .= " ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)";
            db_exec($sql, array_values($row));
        }
    }
}

function settings_for(int $workspaceId): array
{
    $rows = db_all("SELECT setting_key, setting_value FROM settings WHERE workspace_id=?", [$workspaceId]);
    $settings = default_settings();
    foreach ($rows as $row) $settings[$row['setting_key']] = $row['setting_value'];
    return $settings;
}

function default_settings(): array
{
    return [
        'language' => 'fr',
        'business_name' => 'WILI Demo',
        'commerce_name' => 'WILI Demo',
        'allow_negative_stock' => '1',
        'comptoir_layout' => 'classic_no_images',
        'purchase_paid_auto_full' => '1',
        'ui_font_size' => 'medium',
        'ui_font_bold' => false,
    ];
}

function update_settings(array $patch): never
{
    $wid = workspace_id();
    foreach ($patch as $key => $value) {
        if (is_array($value) || is_object($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        db_exec("INSERT INTO settings (workspace_id, setting_key, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)", [$wid, (string)$key, (string)$value]);
    }
    ok(settings_for($wid));
}

function products_list(array $data): array
{
    return db_all("SELECT * FROM products WHERE workspace_id=? ORDER BY name LIMIT " . limit_sql((int)($data['limit'] ?? 500)), [workspace_id()]);
}

function products_page(array $data): array
{
    $q = '%' . trim((string)($data['q'] ?? $data['query'] ?? '')) . '%';
    $limit = limit_sql((int)($data['limit'] ?? 100));
    $offset = max(0, (int)($data['offset'] ?? 0));
    return db_all("SELECT * FROM products WHERE workspace_id=? AND (?='%%' OR name LIKE ? OR barcode LIKE ? OR reference LIKE ? OR sku LIKE ?) ORDER BY name LIMIT $limit OFFSET $offset", [workspace_id(), $q, $q, $q, $q, $q]);
}

function products_search(string $query, int $limit): array
{
    $tokens = preg_split('/\s+/', trim($query)) ?: [];
    $where = ["workspace_id=?"];
    $params = [workspace_id()];
    foreach ($tokens as $token) {
        if ($token === '') continue;
        $like = '%' . $token . '%';
        $where[] = "(name LIKE ? OR barcode LIKE ? OR reference LIKE ? OR sku LIKE ?)";
        array_push($params, $like, $like, $like, $like);
    }
    return db_all("SELECT * FROM products WHERE " . implode(' AND ', $where) . " ORDER BY name LIMIT " . limit_sql($limit ?: 100), $params);
}

function product_lookup(string $query): ?array
{
    return db_one("SELECT * FROM products WHERE workspace_id=? AND (barcode=? OR reference=? OR sku=?) LIMIT 1", [workspace_id(), $query, $query, $query]);
}

function products_summary(): array
{
    $row = db_one("SELECT COUNT(*) total_products, COALESCE(SUM(stock_qty),0) total_stock_qty, COALESCE(SUM(stock_qty * cost_cents),0) stock_value_cents FROM products WHERE workspace_id=?", [workspace_id()]);
    return $row ?: ['total_products' => 0, 'total_stock_qty' => 0, 'stock_value_cents' => 0];
}

function product_save(?int $id, array $payload): array
{
    $wid = workspace_id();
    $name = trim((string)($payload['name'] ?? 'Produit'));
    $barcode = trim((string)($payload['barcode'] ?? ''));
    $reference = trim((string)($payload['reference'] ?? ''));
    $stock = (float)($payload['stock_qty'] ?? $payload['stock'] ?? 0);
    $cost = cents_value($payload['cost_cents'] ?? null, $payload['cost'] ?? 0);
    $retail = cents_value($payload['retail_price_cents'] ?? null, $payload['retail_price'] ?? $payload['retail'] ?? 0);
    $wholesale = cents_value($payload['wholesale_price_cents'] ?? null, $payload['wholesale_price'] ?? $payload['wholesale'] ?? 0);
    $assist = cents_value($payload['assisted_price_cents'] ?? null, $payload['assisted_price'] ?? $payload['discount_price'] ?? 0) ?: $retail;
    if ($id) {
        db_exec("UPDATE products SET name=?, barcode=?, reference=?, sku=?, product_type=?, stock_qty=?, cost_cents=?, avg_cost_cents=?, last_cost_cents=?, wholesale_price_cents=?, retail_price_cents=?, assisted_price_cents=?, discount_price_cents=?, expiry_date=NULLIF(?,''), image_path=? WHERE workspace_id=? AND id=?", [$name,$barcode,$reference,$reference,(string)($payload['product_type'] ?? 'product'),$stock,$cost,$cost,$cost,$wholesale ?: $retail,$retail,$assist,$assist,(string)($payload['expiry_date'] ?? ''),(string)($payload['image_path'] ?? ''),$wid,$id]);
    } else {
        $id = db_insert("INSERT INTO products (workspace_id,name,barcode,reference,sku,product_type,stock_qty,cost_cents,avg_cost_cents,last_cost_cents,wholesale_price_cents,retail_price_cents,assisted_price_cents,discount_price_cents,expiry_date,image_path) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NULLIF(?,''),?)", [$wid,$name,$barcode,$reference,$reference,(string)($payload['product_type'] ?? 'product'),$stock,$cost,$cost,$cost,$wholesale ?: $retail,$retail,$assist,$assist,(string)($payload['expiry_date'] ?? ''),(string)($payload['image_path'] ?? '')]);
    }
    return db_one("SELECT * FROM products WHERE workspace_id=? AND id=?", [$wid, $id]) ?: [];
}

function parties_list(string $table, array $data): array
{
    return db_all("SELECT * FROM `$table` WHERE workspace_id=? ORDER BY name LIMIT " . limit_sql((int)($data['limit'] ?? 1000)), [workspace_id()]);
}

function parties_page(string $table, array $data): array
{
    $q = '%' . trim((string)($data['q'] ?? $data['query'] ?? '')) . '%';
    $limit = limit_sql((int)($data['limit'] ?? 100));
    $offset = max(0, (int)($data['offset'] ?? 0));
    $rows = db_all("SELECT * FROM `$table` WHERE workspace_id=? AND (?='%%' OR name LIKE ? OR phone LIKE ? OR reference LIKE ?) ORDER BY name LIMIT $limit OFFSET $offset", [workspace_id(), $q, $q, $q, $q]);
    $total = db_one("SELECT COUNT(*) c FROM `$table` WHERE workspace_id=? AND (?='%%' OR name LIKE ? OR phone LIKE ? OR reference LIKE ?)", [workspace_id(), $q, $q, $q, $q])['c'] ?? count($rows);
    return ['rows' => $rows, 'total' => (int)$total];
}

function party_save(string $table, ?int $id, array $payload): array
{
    $wid = workspace_id();
    $name = trim((string)($payload['name'] ?? ''));
    if ($name === '') $name = $table === 'suppliers' ? 'Fournisseur' : 'Client';
    $values = [$name,(string)($payload['phone'] ?? ''),(string)($payload['address'] ?? ''),(string)($payload['nif'] ?? ''),(string)($payload['nis'] ?? ''),(string)($payload['rc'] ?? ''),(string)($payload['ai'] ?? ''),(string)($payload['activity'] ?? ''),(string)($payload['commerce_name'] ?? $payload['customer_commerce_name'] ?? ''),(string)($payload['reference'] ?? '')];
    if ($id) {
        db_exec("UPDATE `$table` SET name=?, phone=?, address=?, nif=?, nis=?, rc=?, ai=?, activity=?, commerce_name=?, reference=? WHERE workspace_id=? AND id=?", [...$values, $wid, $id]);
    } else {
        $id = db_insert("INSERT INTO `$table` (workspace_id,name,phone,address,nif,nis,rc,ai,activity,commerce_name,reference) VALUES (?,?,?,?,?,?,?,?,?,?,?)", [$wid, ...$values]);
    }
    return db_one("SELECT * FROM `$table` WHERE workspace_id=? AND id=?", [$wid, $id]) ?: [];
}

function create_sale(array $payload): array
{
    $wid = workspace_id();
    $customerId = (int)($payload['customer_id'] ?? 0);
    $customer = $customerId ? db_one("SELECT * FROM customers WHERE workspace_id=? AND id=?", [$wid, $customerId]) : null;
    $lines = normalize_sale_lines($payload);
    $total = (int)($payload['total_cents'] ?? array_sum(array_column($lines, 'total_cents')));
    $paid = (int)($payload['paid_cents'] ?? $payload['amount_paid_cents'] ?? $total);
    $due = max(0, $total - $paid);
    $docPrefix = $customerId ? 'BL' : 'VCC';
    $ref = next_ref($docPrefix);
    $saleId = db_insert("INSERT INTO sales (workspace_id, ref, customer_id, customer_name, total_cents, paid_cents, due_cents, status, created_by) VALUES (?,?,?,?,?,?,?,?,?)", [$wid,$ref,$customerId ?: null,(string)($customer['name'] ?? $payload['customer_name'] ?? ''),$total,$paid,$due,$due > 0 ? 'Non regle' : 'Regle',(int)$_SESSION['user_id']]);
    foreach ($lines as $line) {
        db_exec("INSERT INTO sale_lines (workspace_id,sale_id,product_id,product_name,qty,unit_price_cents,discount_cents,total_cents,cost_cents) VALUES (?,?,?,?,?,?,?,?,?)", [$wid,$saleId,$line['product_id'],$line['product_name'],$line['qty'],$line['unit_price_cents'],$line['discount_cents'],$line['total_cents'],$line['cost_cents']]);
        db_exec("UPDATE products SET stock_qty=stock_qty-? WHERE workspace_id=? AND id=? AND product_type<>'service'", [$line['qty'],$wid,$line['product_id']]);
    }
    add_finance('income', $docPrefix === 'VCC' ? 'Encaissement VCC' : 'Encaissement BL', $ref, (string)($customer['id'] ?? ''), (string)($customer['name'] ?? ''), $paid, 'sale_invoice', (string)$saleId);
    audit('sale.create', 'sale', (string)$saleId);
    return sale_details((string)$saleId) ?: [];
}

function update_sale(array $payload): array
{
    return create_sale($payload);
}

function normalize_sale_lines(array $payload): array
{
    $items = is_array($payload['items'] ?? null) ? $payload['items'] : (is_array($payload['lines'] ?? null) ? $payload['lines'] : []);
    $out = [];
    foreach ($items as $item) {
        $pid = (int)($item['product_id'] ?? 0);
        $p = db_one("SELECT * FROM products WHERE workspace_id=? AND id=?", [workspace_id(), $pid]);
        if (!$p) continue;
        $qty = (float)($item['qty'] ?? 0);
        if ($qty <= 0) continue;
        $unit = (int)($item['unit_price_cents'] ?? $p['retail_price_cents']);
        $discount = (int)($item['discount_cents'] ?? 0);
        $out[] = ['product_id'=>$pid,'product_name'=>$p['name'],'qty'=>$qty,'unit_price_cents'=>$unit,'discount_cents'=>$discount,'total_cents'=>max(0, (int)round($qty*$unit)-$discount),'cost_cents'=>(int)$p['cost_cents']];
    }
    return $out;
}

function sales_list(int $limit): array
{
    return db_all("SELECT * FROM sales WHERE workspace_id=? ORDER BY date DESC LIMIT " . limit_sql($limit ?: 1000), [workspace_id()]);
}

function sale_details(string $id): ?array
{
    $sale = db_one("SELECT * FROM sales WHERE workspace_id=? AND (id=? OR ref=?) LIMIT 1", [workspace_id(), (int)$id, $id]);
    if (!$sale) return null;
    $sale['lines'] = db_all("SELECT * FROM sale_lines WHERE workspace_id=? AND sale_id=?", [workspace_id(), (int)$sale['id']]);
    $sale['items'] = $sale['lines'];
    return $sale;
}

function add_sale_payment(array $payload): array
{
    $sale = sale_details((string)($payload['sale_id'] ?? ''));
    if (!$sale) fail('sale_not_found');
    $amount = (int)($payload['amount_cents'] ?? 0);
    $paid = (int)$sale['paid_cents'] + $amount;
    $due = max(0, (int)$sale['total_cents'] - $paid);
    db_exec("UPDATE sales SET paid_cents=?, due_cents=?, status=? WHERE workspace_id=? AND id=?", [$paid,$due,$due>0?'Non regle':'Regle',workspace_id(),(int)$sale['id']]);
    $ref = next_ref('PAY');
    add_finance('income', 'Reglement manuel', $ref, (string)($sale['customer_id'] ?? ''), (string)($sale['customer_name'] ?? ''), $amount, 'sale_payment', $ref);
    return ['id'=>$ref,'ref'=>$ref,'amount_cents'=>$amount];
}

function create_purchase(array $payload): array
{
    $wid = workspace_id();
    $supplierId = (int)($payload['supplier_id'] ?? 0);
    $supplier = $supplierId ? db_one("SELECT * FROM suppliers WHERE workspace_id=? AND id=?", [$wid, $supplierId]) : null;
    $lines = normalize_purchase_lines($payload);
    $total = (int)($payload['total_cents'] ?? array_sum(array_column($lines, 'total_cents')));
    $paid = (int)($payload['paid_cents'] ?? $payload['amount_paid_cents'] ?? $total);
    $due = max(0, $total - $paid);
    $ref = next_ref('BR');
    $purchaseId = db_insert("INSERT INTO purchases (workspace_id, ref, customer_id, customer_name, total_cents, paid_cents, due_cents, status, created_by) VALUES (?,?,?,?,?,?,?,?,?)", [$wid,$ref,$supplierId ?: null,(string)($supplier['name'] ?? $payload['supplier_name'] ?? ''),$total,$paid,$due,$due > 0 ? 'Non regle' : 'Regle',(int)$_SESSION['user_id']]);
    foreach ($lines as $line) {
        db_exec("INSERT INTO purchase_lines (workspace_id,purchase_id,product_id,product_name,qty,unit_cost_cents,total_cents) VALUES (?,?,?,?,?,?,?)", [$wid,$purchaseId,$line['product_id'],$line['product_name'],$line['qty'],$line['unit_cost_cents'],$line['total_cents']]);
        db_exec("UPDATE products SET stock_qty=stock_qty+?, cost_cents=?, last_cost_cents=? WHERE workspace_id=? AND id=?", [$line['qty'],$line['unit_cost_cents'],$line['unit_cost_cents'],$wid,$line['product_id']]);
    }
    add_finance('outcome', 'Paiement BR', $ref, (string)($supplier['id'] ?? ''), (string)($supplier['name'] ?? ''), $paid, 'purchase_invoice', (string)$purchaseId);
    audit('purchase.create', 'purchase', (string)$purchaseId);
    return purchase_details((string)$purchaseId) ?: [];
}

function update_purchase(array $payload): array
{
    return create_purchase($payload);
}

function normalize_purchase_lines(array $payload): array
{
    $items = is_array($payload['items'] ?? null) ? $payload['items'] : (is_array($payload['lines'] ?? null) ? $payload['lines'] : []);
    $out = [];
    foreach ($items as $item) {
        $pid = (int)($item['product_id'] ?? 0);
        $p = db_one("SELECT * FROM products WHERE workspace_id=? AND id=?", [workspace_id(), $pid]);
        if (!$p) continue;
        $qty = (float)($item['qty'] ?? 0);
        if ($qty <= 0) continue;
        $unit = (int)($item['unit_cost_cents'] ?? $item['unit_price_cents'] ?? $p['cost_cents']);
        $out[] = ['product_id'=>$pid,'product_name'=>$p['name'],'qty'=>$qty,'unit_cost_cents'=>$unit,'total_cents'=>(int)round($qty*$unit)];
    }
    return $out;
}

function purchases_list(int $limit): array
{
    return array_map('purchase_row_public', db_all("SELECT * FROM purchases WHERE workspace_id=? ORDER BY date DESC LIMIT " . limit_sql($limit ?: 1000), [workspace_id()]));
}

function purchase_details(string $id): ?array
{
    $purchase = db_one("SELECT * FROM purchases WHERE workspace_id=? AND (id=? OR ref=?) LIMIT 1", [workspace_id(), (int)$id, $id]);
    if (!$purchase) return null;
    $purchase = purchase_row_public($purchase);
    $purchase['lines'] = db_all("SELECT * FROM purchase_lines WHERE workspace_id=? AND purchase_id=?", [workspace_id(), (int)$purchase['id']]);
    $purchase['items'] = $purchase['lines'];
    return $purchase;
}

function purchase_row_public(array $row): array
{
    $row['supplier_id'] = $row['customer_id'] ?? null;
    $row['supplier_name'] = $row['customer_name'] ?? '';
    return $row;
}

function add_purchase_payment(array $payload): array
{
    $purchase = purchase_details((string)($payload['purchase_id'] ?? ''));
    if (!$purchase) fail('purchase_not_found');
    $amount = (int)($payload['amount_cents'] ?? 0);
    $paid = (int)$purchase['paid_cents'] + $amount;
    $due = max(0, (int)$purchase['total_cents'] - $paid);
    db_exec("UPDATE purchases SET paid_cents=?, due_cents=?, status=? WHERE workspace_id=? AND id=?", [$paid,$due,$due>0?'Non regle':'Regle',workspace_id(),(int)$purchase['id']]);
    $ref = next_ref('PAY');
    add_finance('outcome', 'Reglement manuel', $ref, (string)($purchase['supplier_id'] ?? ''), (string)($purchase['supplier_name'] ?? ''), $amount, 'purchase_payment', $ref);
    return ['id'=>$ref,'ref'=>$ref,'amount_cents'=>$amount];
}

function add_stock_loss(array $payload): array
{
    $p = db_one("SELECT * FROM products WHERE workspace_id=? AND id=?", [workspace_id(), (int)($payload['product_id'] ?? 0)]);
    if (!$p) fail('product_not_found');
    $qty = (float)($payload['qty'] ?? 0);
    $cost = (int)round($qty * (int)$p['cost_cents']);
    db_exec("UPDATE products SET stock_qty=stock_qty-? WHERE workspace_id=? AND id=?", [$qty, workspace_id(), (int)$p['id']]);
    $id = db_insert("INSERT INTO stock_losses (workspace_id, product_id, product_name, qty, reason, note, cost_cents) VALUES (?,?,?,?,?,?,?)", [workspace_id(), (int)$p['id'], $p['name'], $qty, (string)($payload['reason'] ?? ''), (string)($payload['note'] ?? ''), $cost]);
    add_finance('outcome', 'Perte stock', (string)$id, '', '', $cost, '', '');
    return db_one("SELECT * FROM stock_losses WHERE workspace_id=? AND id=?", [workspace_id(), $id]) ?: [];
}

function finance_report(int $limit): array
{
    return db_all("SELECT * FROM finance_entries WHERE workspace_id=? ORDER BY date DESC LIMIT " . limit_sql($limit ?: 1000), [workspace_id()]);
}

function finance_summary(): array
{
    $row = db_one("SELECT COALESCE(SUM(CASE WHEN entry_type='income' THEN amount_cents ELSE 0 END),0) in_cents, COALESCE(SUM(CASE WHEN entry_type='outcome' THEN amount_cents ELSE 0 END),0) out_cents, COUNT(*) operations FROM finance_entries WHERE workspace_id=?", [workspace_id()]);
    $in = (int)($row['in_cents'] ?? 0);
    $out = (int)($row['out_cents'] ?? 0);
    return ['income_cents'=>$in,'outcome_cents'=>$out,'balance_cents'=>$in-$out,'operations'=>(int)($row['operations'] ?? 0)];
}

function add_manual_finance(array $payload): array
{
    $type = in_array((string)($payload['entry_type'] ?? 'income'), ['expense','outcome'], true) ? 'outcome' : 'income';
    $ref = next_ref('PAY');
    $id = add_finance($type, 'Reglement manuel', $ref, (string)($payload['party_key'] ?? ''), (string)($payload['party_name'] ?? ''), (int)($payload['amount_cents'] ?? 0), 'finance_entry', $ref, (string)($payload['note'] ?? ''));
    return db_one("SELECT * FROM finance_entries WHERE workspace_id=? AND id=?", [workspace_id(), $id]) ?: [];
}

function debts_summary(): array
{
    $customers = db_all("SELECT * FROM sales WHERE workspace_id=? AND due_cents>0 ORDER BY date DESC", [workspace_id()]);
    $suppliers = array_map('purchase_row_public', db_all("SELECT * FROM purchases WHERE workspace_id=? AND due_cents>0 ORDER BY date DESC", [workspace_id()]));
    return [
        'customers' => $customers,
        'suppliers' => $suppliers,
        'customer_due_cents' => array_sum(array_column($customers, 'due_cents')),
        'supplier_due_cents' => array_sum(array_column($suppliers, 'due_cents')),
    ];
}

function daily_report(): array
{
    $sales = db_one("SELECT COALESCE(SUM(total_cents),0) v FROM sales WHERE workspace_id=?", [workspace_id()])['v'] ?? 0;
    $purchases = db_one("SELECT COALESCE(SUM(total_cents),0) v FROM purchases WHERE workspace_id=?", [workspace_id()])['v'] ?? 0;
    $profit = db_one("SELECT COALESCE(SUM(total_cents - (qty * cost_cents)),0) v FROM sale_lines WHERE workspace_id=?", [workspace_id()])['v'] ?? 0;
    $fin = finance_summary();
    return ['sales_total_cents'=>(int)$sales,'purchases_total_cents'=>(int)$purchases,'profit_cents'=>(int)$profit,'cash_delta_cents'=>(int)$fin['balance_cents']];
}

function product_trace(int $productId): array
{
    $sales = db_all("SELECT s.date, 'Vente' type, s.ref, l.qty qty_out, 0 qty_in, l.total_cents amount_cents FROM sale_lines l JOIN sales s ON s.id=l.sale_id WHERE l.workspace_id=? AND l.product_id=?", [workspace_id(), $productId]);
    $purchases = db_all("SELECT p.date, 'Achat' type, p.ref, 0 qty_out, l.qty qty_in, l.total_cents amount_cents FROM purchase_lines l JOIN purchases p ON p.id=l.purchase_id WHERE l.workspace_id=? AND l.product_id=?", [workspace_id(), $productId]);
    $losses = db_all("SELECT created_at date, 'Perte' type, id ref, qty qty_out, 0 qty_in, cost_cents amount_cents FROM stock_losses WHERE workspace_id=? AND product_id=?", [workspace_id(), $productId]);
    $rows = array_merge($sales, $purchases, $losses);
    usort($rows, fn($a, $b) => strcmp((string)$b['date'], (string)$a['date']));
    return $rows;
}

function upload_media(array $payload): array
{
    $dataUrl = trim((string)($payload['data_url'] ?? ''));
    if ($dataUrl === '') fail('image_required');
    if (!preg_match('/^data:(image\/(?:png|jpeg|webp|gif));base64,(.+)$/i', $dataUrl, $matches)) {
        fail('image_type_not_supported');
    }

    $mime = strtolower((string)$matches[1]);
    $raw = base64_decode((string)$matches[2], true);
    if ($raw === false || $raw === '') fail('image_decode_failed');

    $maxBytes = 5 * 1024 * 1024;
    if (strlen($raw) > $maxBytes) fail('image_too_large_max_5mb');

    $detected = '';
    if (function_exists('finfo_buffer')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detected = strtolower((string)$finfo->buffer($raw));
    }
    if ($detected !== '' && $detected !== $mime && !($mime === 'image/jpeg' && $detected === 'image/pjpeg')) {
        fail('image_type_not_supported');
    }

    $ext = match ($mime) {
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => 'img',
    };

    $workspaceId = workspace_id();
    $dir = rtrim(WILI_UPLOAD_DIR, "\\/") . DIRECTORY_SEPARATOR . $workspaceId;
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fail('upload_dir_failed', 500);
    }

    $name = 'product-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $path = $dir . DIRECTORY_SEPARATOR . $name;
    if (file_put_contents($path, $raw) === false) fail('image_save_failed', 500);

    audit('media.upload', 'media', $name);
    return [
        'image_path' => 'uploads/' . $workspaceId . '/' . $name,
        'mime_type' => $mime,
        'size_bytes' => strlen($raw),
    ];
}

function add_finance(string $type, string $source, string $ref, string $partyKey, string $partyName, int $amount, string $pdfKind = '', string $pdfId = '', string $note = ''): int
{
    return db_insert("INSERT INTO finance_entries (workspace_id, entry_type, source, ref, ref_id, party_key, party_name, amount_cents, pdf_kind, pdf_id, note) VALUES (?,?,?,?,?,?,?,?,?,?,?)", [workspace_id(), $type, $source, $ref, $ref, $partyKey, $partyName, max(0, $amount), $pdfKind, $pdfId, $note]);
}

function next_ref(string $prefix): string
{
    $wid = workspace_id();
    db_exec("INSERT INTO counters (workspace_id, counter_key, counter_value) VALUES (?, ?, 0) ON DUPLICATE KEY UPDATE counter_value=counter_value+1", [$wid, $prefix]);
    $value = (int)(db_one("SELECT counter_value FROM counters WHERE workspace_id=? AND counter_key=?", [$wid, $prefix])['counter_value'] ?? 1);
    if ($value <= 0) {
        $value = 1;
        db_exec("UPDATE counters SET counter_value=1 WHERE workspace_id=? AND counter_key=?", [$wid, $prefix]);
    }
    return $prefix . '/' . $value . '/' . date('Y');
}

function audit(string $action, string $entityType = '', string $entityId = '', string $detail = ''): void
{
    db_exec("INSERT INTO audit_events (workspace_id, user_id, action, entity_type, entity_id, detail) VALUES (?,?,?,?,?,?)", [workspace_id(), (int)($_SESSION['user_id'] ?? 0), $action, $entityType, $entityId, $detail]);
}

function document_payload(string $title, string $bodyHtml, string $name, string $kind = 'document'): array
{
    $html = "<!doctype html><html><head><meta charset='utf-8'><title>" . h($title) . "</title><style>"
        . "html,body{background:#fff;color:#111;margin:0;padding:0}"
        . "body{font-family:Segoe UI,Tahoma,Arial,sans-serif}"
        . "@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}"
        . "</style></head><body class='wili-print-" . h($kind) . "'>" . $bodyHtml . "</body></html>";
    return ['file_path' => 'data:text/html;charset=utf-8,' . rawurlencode($html), 'suggested_name' => $name, 'editable_html' => $html];
}

function web_document_settings(): array
{
    $settings = isset($_SESSION['user_id']) ? settings_for(workspace_id()) : default_settings();
    $name = trim((string)($settings['store_name'] ?? $settings['commerce_name'] ?? $settings['business_name'] ?? ''));
    if ($name === '') $name = 'WILI';
    return [
        'store_name' => $name,
        'store_activity' => (string)($settings['store_activity'] ?? $settings['activity'] ?? ''),
        'store_address' => (string)($settings['store_address'] ?? $settings['address'] ?? ''),
        'store_phone' => (string)($settings['store_phone'] ?? $settings['phone'] ?? ''),
        'store_email' => (string)($settings['store_email'] ?? $settings['email'] ?? ''),
        'store_nif' => (string)($settings['store_nif'] ?? $settings['nif'] ?? ''),
        'store_nis' => (string)($settings['store_nis'] ?? $settings['nis'] ?? ''),
        'store_rc' => (string)($settings['store_rc'] ?? $settings['rc'] ?? ''),
        'store_ai' => (string)($settings['store_ai'] ?? $settings['ai'] ?? ''),
    ];
}

function store_meta_html(array $settings): string
{
    $rows = [];
    foreach (['store_activity', 'store_address'] as $key) {
        $text = trim((string)($settings[$key] ?? ''));
        if ($text !== '') $rows[] = '<div class="meta">' . h($text) . '</div>';
    }
    if (trim((string)($settings['store_phone'] ?? '')) !== '') $rows[] = '<div class="meta">Tel: ' . h((string)$settings['store_phone']) . '</div>';
    if (trim((string)($settings['store_email'] ?? '')) !== '') $rows[] = '<div class="meta">Email: ' . h((string)$settings['store_email']) . '</div>';
    $legal = [];
    foreach (['store_nif' => 'NIF', 'store_nis' => 'NIS', 'store_rc' => 'RC', 'store_ai' => 'AI'] as $key => $label) {
        $text = trim((string)($settings[$key] ?? ''));
        if ($text !== '') $legal[] = $label . ': ' . h($text);
    }
    if ($legal) $rows[] = '<div class="meta">' . implode(' | ', $legal) . '</div>';
    return implode("\n", $rows);
}

function document_body(array $args): string
{
    $settings = web_document_settings();
    $badge = (string)($args['badge'] ?? 'DOCUMENT');
    $metaRowsHtml = (string)($args['metaRowsHtml'] ?? '');
    $tableHeadHtml = (string)($args['tableHeadHtml'] ?? '');
    $tableBodyHtml = (string)($args['tableBodyHtml'] ?? '');
    $totalsRowsHtml = (string)($args['totalsRowsHtml'] ?? '');
    $beforeTableHtml = (string)($args['beforeTableHtml'] ?? '');
    $extraSectionsHtml = (string)($args['extraSectionsHtml'] ?? '');
    $storeName = trim((string)($settings['store_name'] ?? 'WILI')) ?: 'WILI';
    return "
      <style>
        @page{size:A4;margin:12mm}
        body{font-family:Segoe UI,Tahoma,sans-serif;font-size:12px;color:#111;background:#fff}
        .inv{max-width:740px;margin:0 auto;padding:10px}
        .head{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:start;border-bottom:2px solid #111;padding-bottom:8px}
        .brand{font-size:22px;font-weight:800;line-height:1.2}
        .meta{font-size:12px;line-height:1.35;color:#1f2937}
        .badge{border:1px solid #111;padding:4px 8px;font-weight:700}
        .section{margin-top:10px}
        .small{font-size:12px;color:#374151;line-height:1.35}
        .kv-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:4px 12px;margin-top:5px}
        table{width:100%;border-collapse:collapse;margin-top:8px}
        th,td{border:1px solid #d1d5db;padding:6px;vertical-align:top}
        th{background:#f3f4f6;text-align:left}
        .tot{margin-top:10px;border:1px solid #d1d5db;padding:8px}
        .tot-row{display:flex;justify-content:space-between;line-height:1.55}
        .tot-row.big{font-size:14px;font-weight:800}
        .empty-row{text-align:center;color:#64748b}
      </style>
      <div class='inv'>
        <div class='head'>
          <div>
            <div class='brand'>" . h($storeName) . "</div>
            " . store_meta_html($settings) . "
          </div>
          <div class='badge'>" . h($badge) . "</div>
        </div>
        " . ($metaRowsHtml !== '' ? "<div class='section small'>{$metaRowsHtml}</div>" : "") . "
        {$beforeTableHtml}
        <table>
          <thead>{$tableHeadHtml}</thead>
          <tbody>{$tableBodyHtml}</tbody>
        </table>
        " . ($totalsRowsHtml !== '' ? "<div class='tot'>{$totalsRowsHtml}</div>" : "") . "
        {$extraSectionsHtml}
      </div>
    ";
}

function sale_doc_type(?array $sale): string
{
    if (!$sale) return 'BL';
    $ref = strtoupper((string)($sale['ref'] ?? ''));
    if (str_starts_with($ref, 'VCC/')) return 'VCC';
    if (!trim((string)($sale['customer_id'] ?? '')) && !trim((string)($sale['customer_name'] ?? ''))) return 'VCC';
    return 'BL';
}

function sale_doc_badge(?array $sale, bool $facture): string
{
    if ($facture) return 'FACTURE DE VENTE';
    return sale_doc_type($sale) === 'VCC' ? 'VCC - VENTE CLIENT COMPTOIR' : 'BL - BON DE LIVRAISON';
}

function sale_html(?array $sale, string $documentMode = 'bon'): string
{
    if (!$sale) return document_body(['badge' => 'VENTE INTROUVABLE', 'tableHeadHtml' => '<tr><th>Erreur</th></tr>', 'tableBodyHtml' => '<tr><td>Vente introuvable</td></tr>']);
    $isFacture = strtolower($documentMode) === 'facture';
    $taxRate = $isFacture ? 19.0 : 0.0;
    $rows = '';
    foreach (($sale['lines'] ?? []) as $line) {
        $qty = (float)($line['qty'] ?? 0);
        $unit = (int)($line['unit_price_cents'] ?? 0);
        $lineHt = (int)($line['total_cents'] ?? round($qty * $unit));
        if ($isFacture) {
            $tax = (int)round($lineHt * $taxRate / 100);
            $rows .= '<tr><td>' . h((string)($line['product_name'] ?? '-')) . '</td><td style="text-align:center">' . h(qty_fmt($qty)) . '</td><td style="text-align:right">' . money_fmt($unit) . '</td><td style="text-align:right">' . money_fmt($lineHt) . '</td><td style="text-align:right">' . number_format($taxRate, 2, '.', '') . '%</td><td style="text-align:right">' . money_fmt($tax) . '</td><td style="text-align:right">' . money_fmt($lineHt + $tax) . '</td></tr>';
        } else {
            $rows .= '<tr><td>' . h((string)($line['product_name'] ?? '-')) . '</td><td style="text-align:center">' . h(qty_fmt($qty)) . '</td><td style="text-align:right">' . money_fmt($unit) . '</td><td style="text-align:right">' . money_fmt($lineHt) . '</td></tr>';
        }
    }
    if ($rows === '') $rows = '<tr><td colspan="' . ($isFacture ? '7' : '4') . '" class="empty-row">Aucune ligne</td></tr>';
    $total = (int)($sale['total_cents'] ?? 0);
    $paid = (int)($sale['paid_cents'] ?? 0);
    $due = (int)($sale['due_cents'] ?? max(0, $total - $paid));
    $tax = $isFacture ? (int)round($total * $taxRate / 100) : 0;
    $totalDisplay = $isFacture ? $total + $tax : $total;
    $paidDisplay = $isFacture ? (int)round($paid * (1 + $taxRate / 100)) : $paid;
    $dueDisplay = max(0, $totalDisplay - $paidDisplay);
    $customerDisplay = trim((string)($sale['customer_name'] ?? '')) ?: '-';
    $meta = '<div><strong>Type:</strong> ' . h(sale_doc_type($sale)) . '</div>'
        . '<div><strong>Ref:</strong> ' . h((string)($sale['ref'] ?? '-')) . '</div>'
        . '<div><strong>Date:</strong> ' . h((string)($sale['date'] ?? $sale['created_at'] ?? '')) . '</div>'
        . '<div><strong>Client:</strong> ' . h($customerDisplay) . '</div>';
    $totals = $isFacture
        ? '<div class="tot-row"><span>Sous-total HT</span><span>' . money_fmt($total) . '</span></div><div class="tot-row"><span>Remise HT</span><span>0.00</span></div><div class="tot-row"><span>TVA (19.00%)</span><span>' . money_fmt($tax) . '</span></div><div class="tot-row big"><span>Total TTC</span><span>' . money_fmt($totalDisplay) . '</span></div><div class="tot-row"><span>Paye TTC</span><span>' . money_fmt($paidDisplay) . '</span></div><div class="tot-row"><span>Reste TTC</span><span>' . money_fmt($dueDisplay) . '</span></div>'
        : '<div class="tot-row"><span>Sous-total</span><span>' . money_fmt($total) . '</span></div><div class="tot-row"><span>Remise</span><span>0.00</span></div><div class="tot-row big"><span>Total</span><span>' . money_fmt($total) . '</span></div><div class="tot-row"><span>Paye</span><span>' . money_fmt($paid) . '</span></div><div class="tot-row"><span>Reste</span><span>' . money_fmt($due) . '</span></div>';
    $movements = sale_movement_rows($sale);
    $extra = '<div class="section small"><strong>Suivi paiements / reste</strong></div><table><thead><tr><th>Date / Heure</th><th>Mouvement</th><th>Ref</th><th>Total facture</th><th>Montant mouvement</th><th>Reste apres</th><th>Note</th></tr></thead><tbody>' . $movements . '</tbody></table>';
    return document_body([
        'badge' => sale_doc_badge($sale, $isFacture),
        'metaRowsHtml' => $meta,
        'tableHeadHtml' => $isFacture ? '<tr><th>Designation</th><th>Qte livree</th><th>Prix unitaire HT</th><th>Montant HT</th><th>Taux TVA</th><th>TVA</th><th>Total TTC</th></tr>' : '<tr><th>Produit</th><th>Qte</th><th>Prix</th><th>Total</th></tr>',
        'tableBodyHtml' => $rows,
        'totalsRowsHtml' => $totals,
        'extraSectionsHtml' => $extra,
    ]);
}

function purchase_doc_type(?array $purchase): string
{
    if (!$purchase) return 'BR';
    $ref = strtoupper((string)($purchase['ref'] ?? ''));
    if (str_starts_with($ref, 'FC/')) return 'FC';
    return 'BR';
}

function purchase_doc_badge(?array $purchase, bool $facture): string
{
    if ($facture) return "FACTURE D'ACHAT";
    return purchase_doc_type($purchase) === 'FC' ? 'FC - FACTURE FOURNISSEUR' : 'BR - BON DE RECEPTION';
}

function purchase_html(?array $purchase, string $documentMode = 'bon'): string
{
    if (!$purchase) return document_body(['badge' => 'ACHAT INTROUVABLE', 'tableHeadHtml' => '<tr><th>Erreur</th></tr>', 'tableBodyHtml' => '<tr><td>Achat introuvable</td></tr>']);
    $isFacture = strtolower($documentMode) === 'facture';
    $taxRate = $isFacture ? 19.0 : 0.0;
    $rows = '';
    foreach (($purchase['lines'] ?? []) as $line) {
        $qty = (float)($line['qty'] ?? 0);
        $unit = (int)($line['unit_cost_cents'] ?? 0);
        $lineHt = (int)($line['total_cents'] ?? round($qty * $unit));
        if ($isFacture) {
            $tax = (int)round($lineHt * $taxRate / 100);
            $rows .= '<tr><td>' . h((string)($line['product_name'] ?? '-')) . '</td><td style="text-align:center">' . h(qty_fmt($qty)) . '</td><td style="text-align:right">' . money_fmt($unit) . '</td><td style="text-align:right">' . money_fmt($lineHt) . '</td><td style="text-align:right">' . money_fmt($tax) . '</td><td style="text-align:right">' . money_fmt($lineHt + $tax) . '</td></tr>';
        } else {
            $rows .= '<tr><td>' . h((string)($line['product_name'] ?? '-')) . '</td><td style="text-align:center">' . h(qty_fmt($qty)) . '</td><td style="text-align:right">' . money_fmt($unit) . '</td><td style="text-align:right">' . money_fmt($lineHt) . '</td></tr>';
        }
    }
    if ($rows === '') $rows = '<tr><td colspan="' . ($isFacture ? '6' : '4') . '" class="empty-row">Aucune ligne</td></tr>';
    $total = (int)($purchase['total_cents'] ?? 0);
    $paid = (int)($purchase['paid_cents'] ?? 0);
    $tax = $isFacture ? (int)round($total * $taxRate / 100) : 0;
    $totalDisplay = $isFacture ? $total + $tax : $total;
    $paidDisplay = $isFacture ? (int)round($paid * (1 + $taxRate / 100)) : $paid;
    $dueDisplay = max(0, $totalDisplay - $paidDisplay);
    $supplierDisplay = trim((string)($purchase['supplier_name'] ?? $purchase['customer_name'] ?? '')) ?: '-';
    $meta = '<div><strong>Type:</strong> ' . h(purchase_doc_type($purchase)) . '</div>'
        . '<div><strong>Ref:</strong> ' . h((string)($purchase['ref'] ?? '-')) . '</div>'
        . '<div><strong>Date:</strong> ' . h((string)($purchase['date'] ?? $purchase['created_at'] ?? '')) . '</div>'
        . '<div><strong>Fournisseur:</strong> ' . h($supplierDisplay) . '</div>';
    $totals = '<div class="tot-row"><span>Sous-total HT</span><span>' . money_fmt($total) . '</span></div><div class="tot-row"><span>Remise HT</span><span>0.00</span></div>'
        . ($isFacture ? '<div class="tot-row"><span>TVA (19.00%)</span><span>' . money_fmt($tax) . '</span></div>' : '')
        . '<div class="tot-row big"><span>' . ($isFacture ? 'Total TTC' : 'Total HT') . '</span><span>' . money_fmt($totalDisplay) . '</span></div>'
        . '<div class="tot-row"><span>' . ($isFacture ? 'Paye TTC' : 'Paye HT') . '</span><span>' . money_fmt($paidDisplay) . '</span></div>'
        . '<div class="tot-row"><span>' . ($isFacture ? 'Reste TTC' : 'Reste HT') . '</span><span>' . money_fmt($dueDisplay) . '</span></div>';
    $extra = '<div class="section small"><strong>Suivi paiements / reste</strong></div><table><thead><tr><th>Date / Heure</th><th>Mouvement</th><th>Ref</th><th>Total facture</th><th>Montant mouvement</th><th>Reste apres</th><th>Note</th></tr></thead><tbody>' . purchase_movement_rows($purchase) . '</tbody></table>';
    return document_body([
        'badge' => purchase_doc_badge($purchase, $isFacture),
        'metaRowsHtml' => $meta,
        'tableHeadHtml' => $isFacture ? '<tr><th>Produit</th><th>Qte</th><th>Prix unitaire (HT)</th><th>Prix total (HT)</th><th>TVA (19%)</th><th>Prix apres TVA (TTC)</th></tr>' : '<tr><th>Produit</th><th>Qte</th><th>Cout unitaire HT</th><th>Total ligne HT</th></tr>',
        'tableBodyHtml' => $rows,
        'totalsRowsHtml' => $totals,
        'extraSectionsHtml' => $extra,
    ]);
}

function cart_html(array $payload): string
{
    $settings = web_document_settings();
    $lines = is_array($payload['lines'] ?? null) ? $payload['lines'] : (is_array($payload['items'] ?? null) ? $payload['items'] : []);
    $totals = is_array($payload['totals'] ?? null) ? $payload['totals'] : [];
    $rows = '';
    $subtotal = (int)($totals['subtotal_cents'] ?? 0);
    $discount = (int)($totals['discount_cents'] ?? 0);
    $total = (int)($totals['total_cents'] ?? 0);
    foreach ($lines as $line) {
        $name = (string)($line['product_name'] ?? $line['name'] ?? $line['product_id'] ?? '-');
        $qty = (float)($line['qty'] ?? $line['quantity'] ?? 0);
        $unit = (int)($line['unit_price_cents'] ?? $line['unit_cents'] ?? 0);
        $lineTotal = (int)($line['line_total_cents'] ?? $line['total_cents'] ?? round($qty * $unit));
        $rows .= '<div class="line-name">' . h($name) . '</div><div class="line-row"><span>' . h(qty_fmt($qty)) . ' x ' . money_fmt($unit) . '</span><span>' . money_fmt($lineTotal) . '</span></div>';
        if (!$subtotal) $subtotal += $lineTotal;
    }
    if (!$total) $total = max(0, $subtotal - $discount);
    $discountRow = $discount > 0 ? '<div class="line-row"><span>Remise</span><span>- ' . money_fmt($discount) . '</span></div>' : '';
    return "
      <style>
        @page{size:58mm auto;margin:0}
        html,body{width:58mm;max-width:58mm;padding:0!important;margin:0!important;overflow:hidden;background:#fff}
        body{font-family:Consolas,'Courier New',monospace;font-size:12px;color:#111}
        .ticket{width:100%;max-width:100%;margin:0;padding:1.8mm 1.8mm 2.2mm;box-sizing:border-box}
        .center{text-align:center}.store{font-weight:700;font-size:17px;line-height:1.2;margin-bottom:2px}
        .meta{font-size:11px;line-height:1.3}.sep{border-top:1px dashed #444;margin:2mm 0}
        .line-name{font-size:12px;line-height:1.2;margin-top:1.1mm}
        .line-row{display:flex;justify-content:space-between;font-size:12px;gap:2mm}
        .total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;margin-top:2mm;gap:2mm}
        .footer{margin-top:2.2mm;font-size:11px;text-align:center;line-height:1.35}
      </style>
      <div class='ticket'>
        <div class='center store'>" . h((string)$settings['store_name']) . "</div>
        " . (trim((string)$settings['store_address']) !== '' ? "<div class='center meta'>" . h((string)$settings['store_address']) . "</div>" : "") . "
        " . (trim((string)$settings['store_phone']) !== '' ? "<div class='center meta'>Tel: " . h((string)$settings['store_phone']) . "</div>" : "") . "
        " . (trim((string)$settings['store_nif']) !== '' ? "<div class='center meta'>NIF: " . h((string)$settings['store_nif']) . "</div>" : "") . "
        <div class='sep'></div>
        <div class='meta'>Ticket de caisse</div>
        <div class='meta'>" . h(date('Y-m-d H:i:s')) . "</div>
        <div class='sep'></div>
        " . ($rows !== '' ? $rows : "<div class='meta'>Aucune ligne</div>") . "
        <div class='sep'></div>
        <div class='line-row'><span>Sous-total</span><span>" . money_fmt($subtotal) . "</span></div>
        {$discountRow}
        <div class='total'><span>TOTAL</span><span>" . money_fmt($total) . "</span></div>
        <div class='footer'>Merci pour votre visite</div>
      </div>
    ";
}

function sale_movement_rows(array $sale): string
{
    $date = (string)($sale['date'] ?? $sale['created_at'] ?? date('Y-m-d H:i:s'));
    $ref = (string)($sale['ref'] ?? '-');
    $total = (int)($sale['total_cents'] ?? 0);
    $paid = max(0, (int)($sale['paid_cents'] ?? 0));
    $running = $total;
    $rows = '<tr><td>' . h($date) . '</td><td>Vente</td><td>' . h($ref) . '</td><td style="text-align:right">' . money_fmt($total) . '</td><td style="text-align:right">0.00</td><td style="text-align:right">' . money_fmt($running) . '</td><td>-</td></tr>';
    if ($paid > 0) {
        $running = max(0, $running - $paid);
        $payRef = finance_ref_for_document('sale_invoice', (string)($sale['id'] ?? ''), $ref);
        $note = sale_doc_type($sale) === 'VCC' ? 'Encaissement VCC' : 'Encaissement BL';
        $rows .= '<tr><td>' . h($date) . '</td><td>Paiement</td><td>' . h($payRef) . '</td><td style="text-align:right">' . money_fmt($total) . '</td><td style="text-align:right">' . money_fmt($paid) . '</td><td style="text-align:right">' . money_fmt($running) . '</td><td>' . h($note) . '</td></tr>';
    }
    return $rows;
}

function purchase_movement_rows(array $purchase): string
{
    $date = (string)($purchase['date'] ?? $purchase['created_at'] ?? date('Y-m-d H:i:s'));
    $ref = (string)($purchase['ref'] ?? '-');
    $total = (int)($purchase['total_cents'] ?? 0);
    $paid = max(0, (int)($purchase['paid_cents'] ?? 0));
    $running = $total;
    $rows = '<tr><td>' . h($date) . '</td><td>Achat</td><td>' . h($ref) . '</td><td style="text-align:right">' . money_fmt($total) . '</td><td style="text-align:right">0.00</td><td style="text-align:right">' . money_fmt($running) . '</td><td>-</td></tr>';
    if ($paid > 0) {
        $running = max(0, $running - $paid);
        $payRef = finance_ref_for_document('purchase_invoice', (string)($purchase['id'] ?? ''), $ref);
        $rows .= '<tr><td>' . h($date) . '</td><td>Paiement</td><td>' . h($payRef) . '</td><td style="text-align:right">' . money_fmt($total) . '</td><td style="text-align:right">' . money_fmt($paid) . '</td><td style="text-align:right">' . money_fmt($running) . '</td><td>Paiement BR</td></tr>';
    }
    return $rows;
}

function finance_ref_for_document(string $pdfKind, string $pdfId, string $fallback): string
{
    if ($pdfId !== '') {
        $row = db_one("SELECT ref FROM finance_entries WHERE workspace_id=? AND pdf_kind=? AND pdf_id=? ORDER BY id ASC LIMIT 1", [workspace_id(), $pdfKind, $pdfId]);
        if ($row && trim((string)($row['ref'] ?? '')) !== '') return (string)$row['ref'];
    }
    return $fallback;
}

function simple_document_html(array $payload): string
{
    $title = (string)($payload['title'] ?? 'Document');
    unset($payload['title']);
    if (!$payload) return '<p>' . h($title) . '</p>';
    return '<table><tbody>' . simple_rows_html($payload) . '</tbody></table>';
}

function simple_rows_html(array $payload, string $prefix = ''): string
{
    $rows = '';
    foreach ($payload as $key => $value) {
        $label = trim($prefix . ' ' . (string)$key);
        if (is_array($value)) {
            $rows .= '<tr><th colspan="2">' . h($label) . '</th></tr>' . simple_rows_html($value, '');
        } else {
            $rows .= '<tr><td>' . h($label) . '</td><td>' . h((string)$value) . '</td></tr>';
        }
    }
    return $rows;
}

function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function money_fmt(int $cents): string { return number_format($cents / 100, 2, '.', ''); }
function qty_fmt(float $qty): string { return rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.') ?: '0'; }
function limit_sql(int $limit): int { return max(1, min(10000, $limit)); }
function cents_value(mixed $centsValue, mixed $decimalValue): int { return $centsValue !== null && $centsValue !== '' ? (int)$centsValue : (int)round(((float)$decimalValue) * 100); }
