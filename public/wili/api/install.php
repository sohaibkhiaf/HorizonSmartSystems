<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';

try {
    $server = pdo_server();
    $server->exec("CREATE DATABASE IF NOT EXISTS `" . WILI_DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = pdo_db();
    install_schema($pdo);
    seed_templates_and_demo($pdo);
    ok([
        'database' => WILI_DB_NAME,
        'installed' => true,
        'login' => ['email' => 'test', 'password' => 'test'],
        'url' => '/wili/'
    ]);
} catch (Throwable $e) {
    fail('install_failed', 500, $e->getMessage());
}

function install_schema(PDO $pdo): void
{
    $sql = [
        "CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            name VARCHAR(190) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS wili_workspaces (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            owner_user_id BIGINT UNSIGNED NULL,
            name VARCHAR(190) NOT NULL,
            template_key VARCHAR(80) NOT NULL DEFAULT '',
            type ENUM('template','user') NOT NULL DEFAULT 'user',
            status ENUM('active','archived','deleted') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_used_at DATETIME NULL,
            INDEX idx_workspaces_owner (owner_user_id, status),
            INDEX idx_workspaces_template (template_key, type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS settings (
            workspace_id BIGINT UNSIGNED NOT NULL,
            setting_key VARCHAR(120) NOT NULL,
            setting_value TEXT NULL,
            PRIMARY KEY (workspace_id, setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS counters (
            workspace_id BIGINT UNSIGNED NOT NULL,
            counter_key VARCHAR(80) NOT NULL,
            counter_value BIGINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (workspace_id, counter_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS products (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            barcode VARCHAR(120) NOT NULL DEFAULT '',
            reference VARCHAR(120) NOT NULL DEFAULT '',
            sku VARCHAR(120) NOT NULL DEFAULT '',
            product_type VARCHAR(30) NOT NULL DEFAULT 'product',
            stock_qty DECIMAL(14,2) NOT NULL DEFAULT 0,
            cost_cents BIGINT NOT NULL DEFAULT 0,
            avg_cost_cents BIGINT NOT NULL DEFAULT 0,
            last_cost_cents BIGINT NOT NULL DEFAULT 0,
            wholesale_price_cents BIGINT NOT NULL DEFAULT 0,
            retail_price_cents BIGINT NOT NULL DEFAULT 0,
            assisted_price_cents BIGINT NOT NULL DEFAULT 0,
            discount_price_cents BIGINT NOT NULL DEFAULT 0,
            expiry_date DATE NULL,
            image_path VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_products_workspace_name (workspace_id, name),
            INDEX idx_products_workspace_barcode (workspace_id, barcode),
            INDEX idx_products_workspace_reference (workspace_id, reference)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS customers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(120) NOT NULL DEFAULT '',
            address TEXT NULL,
            nif VARCHAR(120) NOT NULL DEFAULT '',
            nis VARCHAR(120) NOT NULL DEFAULT '',
            rc VARCHAR(120) NOT NULL DEFAULT '',
            ai VARCHAR(120) NOT NULL DEFAULT '',
            activity VARCHAR(190) NOT NULL DEFAULT '',
            commerce_name VARCHAR(190) NOT NULL DEFAULT '',
            balance_cents BIGINT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_customers_workspace_name (workspace_id, name),
            INDEX idx_customers_workspace_phone (workspace_id, phone)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS suppliers LIKE customers",

        "CREATE TABLE IF NOT EXISTS sales (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            ref VARCHAR(80) NOT NULL,
            customer_id BIGINT UNSIGNED NULL,
            customer_name VARCHAR(255) NOT NULL DEFAULT '',
            date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            total_cents BIGINT NOT NULL DEFAULT 0,
            paid_cents BIGINT NOT NULL DEFAULT 0,
            due_cents BIGINT NOT NULL DEFAULT 0,
            status VARCHAR(40) NOT NULL DEFAULT 'Regle',
            created_by BIGINT UNSIGNED NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sales_workspace_date (workspace_id, date),
            INDEX idx_sales_workspace_ref (workspace_id, ref)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS sale_lines (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            sale_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            qty DECIMAL(14,2) NOT NULL DEFAULT 0,
            unit_price_cents BIGINT NOT NULL DEFAULT 0,
            discount_cents BIGINT NOT NULL DEFAULT 0,
            total_cents BIGINT NOT NULL DEFAULT 0,
            cost_cents BIGINT NOT NULL DEFAULT 0,
            INDEX idx_sale_lines_workspace_sale (workspace_id, sale_id),
            INDEX idx_sale_lines_workspace_product (workspace_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS purchases LIKE sales",
        "CREATE TABLE IF NOT EXISTS purchase_lines (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            purchase_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            qty DECIMAL(14,2) NOT NULL DEFAULT 0,
            unit_cost_cents BIGINT NOT NULL DEFAULT 0,
            total_cents BIGINT NOT NULL DEFAULT 0,
            INDEX idx_purchase_lines_workspace_purchase (workspace_id, purchase_id),
            INDEX idx_purchase_lines_workspace_product (workspace_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS finance_entries (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            entry_type VARCHAR(30) NOT NULL,
            source VARCHAR(120) NOT NULL,
            ref VARCHAR(80) NOT NULL DEFAULT '',
            ref_id VARCHAR(80) NOT NULL DEFAULT '',
            party_key VARCHAR(80) NOT NULL DEFAULT '',
            party_name VARCHAR(255) NOT NULL DEFAULT '',
            amount_cents BIGINT NOT NULL DEFAULT 0,
            status VARCHAR(60) NOT NULL DEFAULT 'Regle',
            pdf_kind VARCHAR(80) NOT NULL DEFAULT '',
            pdf_id VARCHAR(80) NOT NULL DEFAULT '',
            product_id BIGINT UNSIGNED NULL,
            product_name VARCHAR(255) NOT NULL DEFAULT '',
            qty DECIMAL(14,2) NOT NULL DEFAULT 0,
            note TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_finance_workspace_date (workspace_id, date),
            INDEX idx_finance_workspace_ref (workspace_id, ref)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS stock_losses (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            qty DECIMAL(14,2) NOT NULL DEFAULT 0,
            reason VARCHAR(120) NOT NULL DEFAULT '',
            note TEXT NULL,
            cost_cents BIGINT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_stock_losses_workspace_date (workspace_id, created_at),
            INDEX idx_stock_losses_workspace_product (workspace_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS audit_events (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            action VARCHAR(120) NOT NULL,
            entity_type VARCHAR(80) NOT NULL DEFAULT '',
            entity_id VARCHAR(80) NOT NULL DEFAULT '',
            detail TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_audit_workspace_date (workspace_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    foreach ($sql as $statement) {
        $pdo->exec($statement);
    }
    ensure_column($pdo, 'customers', 'reference', "VARCHAR(120) NOT NULL DEFAULT '' AFTER name");
    ensure_column($pdo, 'suppliers', 'reference', "VARCHAR(120) NOT NULL DEFAULT '' AFTER name");
    ensure_index($pdo, 'customers', 'idx_customers_workspace_reference', "CREATE INDEX idx_customers_workspace_reference ON customers (workspace_id, reference)");
    ensure_index($pdo, 'suppliers', 'idx_suppliers_workspace_reference', "CREATE INDEX idx_suppliers_workspace_reference ON suppliers (workspace_id, reference)");
}

function ensure_column(PDO $pdo, string $table, string $column, string $definition): void
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=?");
    $stmt->execute([WILI_DB_NAME, $table, $column]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

function ensure_index(PDO $pdo, string $table, string $index, string $createSql): void
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND INDEX_NAME=?");
    $stmt->execute([WILI_DB_NAME, $table, $index]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec($createSql);
    }
}

function seed_templates_and_demo(PDO $pdo): void
{
    $hash = password_hash('test', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, name) VALUES ('test', ?, 'test')");
    $stmt->execute([$hash]);
    $userId = (int) $pdo->query("SELECT id FROM users WHERE email = 'test'")->fetchColumn();

    $templates = [
        'electric' => 'WILI Electric Template',
        'pizzeria' => 'WILI Pizzeria Template',
        'alimentation' => 'WILI Alimentation Template'
    ];

    foreach ($templates as $key => $name) {
        $stmt = $pdo->prepare("SELECT id FROM wili_workspaces WHERE template_key = ? AND type = 'template' LIMIT 1");
        $stmt->execute([$key]);
        $workspaceId = (int) ($stmt->fetchColumn() ?: 0);
        if (!$workspaceId) {
            $ins = $pdo->prepare("INSERT INTO wili_workspaces (owner_user_id, name, template_key, type) VALUES (NULL, ?, ?, 'template')");
            $ins->execute([$name, $key]);
            $workspaceId = (int) $pdo->lastInsertId();
            seed_workspace_defaults($pdo, $workspaceId, $key);
        }
    }
    $pdo->exec("UPDATE wili_workspaces SET status='archived' WHERE type='template' AND template_key='blank'");

    $stmt = $pdo->prepare("SELECT id FROM wili_workspaces WHERE owner_user_id = ? AND type = 'user' LIMIT 1");
    $stmt->execute([$userId]);
    if (!$stmt->fetchColumn()) {
        $templateId = (int) $pdo->query("SELECT id FROM wili_workspaces WHERE template_key='electric' AND type='template' LIMIT 1")->fetchColumn();
        clone_workspace($pdo, $templateId, $userId, 'Demo Electric');
    }
}

function seed_workspace_defaults(PDO $pdo, int $workspaceId, string $key): void
{
    $settings = [
        'language' => 'fr',
        'business_name' => $key === 'blank' ? 'WILI Demo' : 'WILI ' . ucfirst($key),
        'commerce_name' => $key === 'blank' ? 'WILI Demo' : 'WILI ' . ucfirst($key),
        'allow_negative_stock' => '1',
        'comptoir_layout' => 'classic_no_images',
        'purchase_paid_auto_full' => '1'
    ];
    $stmt = $pdo->prepare("INSERT INTO settings (workspace_id, setting_key, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    foreach ($settings as $k => $v) $stmt->execute([$workspaceId, $k, $v]);
    if ($key === 'blank') return;

    $products = match ($key) {
        'pizzeria' => [
            ['Pizza Margherita', 'PIZ001', 'PIZ001', 100, 35000, 70000],
            ['Pizza Thon', 'PIZ002', 'PIZ002', 80, 45000, 90000],
            ['Boisson 1L', 'B001', 'B001', 60, 8000, 15000],
        ],
        'alimentation' => [
            ['Lait 1L', '613000000001', 'AL001', 120, 9000, 12000],
            ['Sucre 1KG', '613000000002', 'AL002', 90, 11000, 15000],
            ['Cafe 250G', '613000000003', 'AL003', 40, 26000, 36000],
        ],
        default => [
            ['LED 05W SPOT COB BLANC', 'QG33', 'LED3', 45, 28000, 45500],
            ['CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 42, 24000, 45000],
            ['2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', 75, 10000, 15500],
        ],
    };
    $pstmt = $pdo->prepare("INSERT INTO products (workspace_id, name, barcode, reference, sku, stock_qty, cost_cents, avg_cost_cents, last_cost_cents, wholesale_price_cents, retail_price_cents, assisted_price_cents, discount_price_cents) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        [$name, $barcode, $ref, $stock, $cost, $retail] = $p;
        $pstmt->execute([$workspaceId, $name, $barcode, $ref, $ref, $stock, $cost, $cost, $cost, max($cost, $retail - 5000), $retail, $retail, $retail]);
    }
    $pdo->prepare("INSERT INTO customers (workspace_id, name, reference) VALUES (?, 'Client comptoir', 'COMPT')")->execute([$workspaceId]);
    $pdo->prepare("INSERT INTO suppliers (workspace_id, name, reference) VALUES (?, 'Achat divers', 'DIVERS')")->execute([$workspaceId]);
}

function clone_workspace(PDO $pdo, int $templateId, int $ownerUserId, string $name): int
{
    $tpl = $pdo->prepare("SELECT template_key FROM wili_workspaces WHERE id = ?");
    $tpl->execute([$templateId]);
    $templateKey = (string) ($tpl->fetchColumn() ?: 'electric');
    $pdo->prepare("INSERT INTO wili_workspaces (owner_user_id, name, template_key, type) VALUES (?, ?, ?, 'user')")->execute([$ownerUserId, $name, $templateKey]);
    $newId = (int) $pdo->lastInsertId();
    seed_workspace_defaults($pdo, $newId, 'blank');

    foreach (['settings'] as $table) {
        $rows = $pdo->prepare("SELECT * FROM `$table` WHERE workspace_id = ?");
        $rows->execute([$templateId]);
        foreach ($rows->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['workspace_id'] = $newId;
            $cols = array_keys($row);
            $sql = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ") ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)";
            $pdo->prepare($sql)->execute(array_values($row));
        }
    }

    foreach (['products', 'customers', 'suppliers'] as $table) {
        $rows = $pdo->prepare("SELECT * FROM `$table` WHERE workspace_id = ?");
        $rows->execute([$templateId]);
        foreach ($rows->fetchAll(PDO::FETCH_ASSOC) as $row) {
            unset($row['id']);
            $row['workspace_id'] = $newId;
            $cols = array_keys($row);
            $sql = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")";
            $pdo->prepare($sql)->execute(array_values($row));
        }
    }
    return $newId;
}
