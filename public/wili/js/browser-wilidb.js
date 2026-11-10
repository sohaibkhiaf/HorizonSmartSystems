(function () {
  "use strict";

  const DB_KEY = "wili_m_browser_local_db_v1";
  const dbChangedHandlers = new Set();

  const ok = (data) => Promise.resolve({ ok: true, data });
  const fail = (error) => Promise.resolve({ ok: false, error: String(error || "error") });
  const uid = (prefix) => `${prefix}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 8)}`;
  const nowIso = () => new Date().toISOString();
  const cents = (value) => Math.max(0, Math.round(Number(value || 0) * 100));
  const money = (c) => (Number(c || 0) / 100).toFixed(2);
  const normalize = (value) => String(value || "").trim().toLowerCase();
  const arr = (value) => Array.isArray(value) ? value : [];

  function defaultSettings() {
    return {
      language: "fr",
      business_name: "Wili M Web",
      store_name: "Wili M Web",
      commerce_name: "Wili M Web",
      currency: "DZD",
      allow_negative_stock: true,
      stock_negative_allowed: true,
      comptoir_layout: "classic_no_images",
      purchase_paid_auto_full: true,
      direct_payment_enabled: true,
      ui_font_size: "medium",
      ui_font_bold: false
    };
  }

  function seedDb() {
    return {
      activated: true,
      settings: defaultSettings(),
      users: [
        { id: "u_admin", username: "test", display_name: "test", role: "admin", pin: "test", active: 1, created_at: nowIso() }
      ],
      products: [
        productSeed("p1", "LED 05W SPOT COB BLANC", "QG33", "LED3", 45, 280, 455),
        productSeed("p2", "CAR SPOT 1P CARRE NOIR BLANC", "613000000001", "MAX1", 42, 240, 450),
        productSeed("p3", "2GE INTERPT SA-APPARENT-NIVO", "402-010000-200", "402-010000-200", 75, 100, 155)
      ],
      customers: [
        { id: "client_comptoir", name: "Client comptoir", phone: "", address: "", reference: "COMPT", balance_cents: 0, created_at: nowIso() }
      ],
      suppliers: [
        { id: "supplier_divers", name: "Achat divers", phone: "", address: "", reference: "DIVERS", balance_cents: 0, created_at: nowIso() }
      ],
      sales: [],
      sale_returns: [],
      sale_quotes: [],
      sale_payments: [],
      purchases: [],
      purchase_returns: [],
      purchase_payments: [],
      finance: [],
      stock_losses: [],
      audit: [],
      caisse: { open: false, box_id: "main", opened_at: "", opening_cents: 0 },
      caisse_sessions: [],
      counters: { sale: 0, purchase: 0, payment: 0, returnSale: 0, returnPurchase: 0, quote: 0 }
    };
  }

  function productSeed(id, name, barcode, reference, stockQty, cost, retail) {
    return {
      id,
      name,
      barcode,
      reference,
      sku: reference,
      product_type: "product",
      stock_qty: stockQty,
      cost_cents: cents(cost),
      avg_cost_cents: cents(cost),
      last_cost_cents: cents(cost),
      wholesale_price_cents: cents(Math.max(cost, retail - 40)),
      retail_price_cents: cents(retail),
      assisted_price_cents: cents(retail),
      discount_price_cents: cents(retail),
      expiry_date: "",
      image_path: "",
      created_at: nowIso(),
      updated_at: nowIso()
    };
  }

  function loadDb() {
    try {
      const parsed = JSON.parse(localStorage.getItem(DB_KEY) || "null");
      if (parsed && typeof parsed === "object") return { ...seedDb(), ...parsed, settings: { ...defaultSettings(), ...(parsed.settings || {}) } };
    } catch (_err) {}
    const seeded = seedDb();
    localStorage.setItem(DB_KEY, JSON.stringify(seeded));
    return seeded;
  }

  let db = loadDb();

  function saveDb(next = db, emit = true) {
    db = next;
    localStorage.setItem(DB_KEY, JSON.stringify(db));
    if (emit) notifyDbChanged();
  }

  function notifyDbChanged() {
    setTimeout(() => {
      dbChangedHandlers.forEach((handler) => {
        try { handler({ source: "browser-localstorage" }); } catch (_err) {}
      });
    }, 0);
  }

  function audit(action, detail) {
    db.audit.unshift({ id: uid("aud"), date: nowIso(), action, detail: String(detail || ""), user: "browser" });
    db.audit = db.audit.slice(0, 2000);
  }

  function nextRef(type) {
    const year = new Date().getFullYear();
    if (type === "sale") return `BL/${++db.counters.sale}/${year}`;
    if (type === "purchase") return `BR/${++db.counters.purchase}/${year}`;
    if (type === "quote") return `PRO/${++db.counters.quote}/${year}`;
    if (type === "sale_return") return `RC/${++db.counters.returnSale}/${year}`;
    if (type === "purchase_return") return `RF/${++db.counters.returnPurchase}/${year}`;
    return `PAY/${++db.counters.payment}/${year}`;
  }

  function productMatches(product, query) {
    const q = normalize(query);
    if (!q) return true;
    const fields = [product.name, product.barcode, product.reference, product.sku].map(normalize);
    const tokens = q.split(/\s+/).filter(Boolean);
    return tokens.every((token) => fields.some((field) => field.includes(token)));
  }

  function sortRows(rows, sortKey = "name", sortDir = "asc") {
    const dir = String(sortDir) === "desc" ? -1 : 1;
    const key = String(sortKey || "name");
    return rows.slice().sort((a, b) => {
      const av = a && a[key];
      const bv = b && b[key];
      if (typeof av === "number" || typeof bv === "number") return ((Number(av || 0) - Number(bv || 0)) * dir);
      return String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" }) * dir;
    });
  }

  function pageRows(rows, payload = {}) {
    const limit = Math.max(1, Math.min(10000, Number(payload.limit || 100)));
    const offset = Math.max(0, Number(payload.offset || 0));
    return { rows: rows.slice(offset, offset + limit), total: rows.length, limit, offset };
  }

  function findProduct(id) {
    return db.products.find((p) => String(p.id) === String(id));
  }

  function findCustomer(id) {
    return db.customers.find((c) => String(c.id) === String(id));
  }

  function findSupplier(id) {
    return db.suppliers.find((s) => String(s.id) === String(id));
  }

  function contactMatches(row, q) {
    const n = normalize(q);
    if (!n) return true;
    return [row.name, row.phone, row.reference, row.id].map(normalize).some((field) => field.includes(n));
  }

  function normalizeProductPayload(payload = {}, existing = {}) {
    const retail = payload.retail_price_cents ?? payload.retail_cents ?? cents(payload.retail_price || payload.retail || payload.price || 0);
    const wholesale = payload.wholesale_price_cents ?? payload.wholesale_cents ?? cents(payload.wholesale_price || payload.wholesale || 0);
    const cost = payload.cost_cents ?? cents(payload.cost || payload.purchase_price || 0);
    return {
      ...existing,
      id: existing.id || uid("p"),
      name: String(payload.name || payload.designation || existing.name || "").trim() || "Produit",
      barcode: String(payload.barcode || existing.barcode || "").trim(),
      reference: String(payload.reference || payload.ref || existing.reference || "").trim(),
      sku: String(payload.sku || payload.reference || existing.sku || "").trim(),
      product_type: String(payload.product_type || existing.product_type || "product"),
      stock_qty: Number(payload.stock_qty ?? payload.stock ?? existing.stock_qty ?? 0),
      cost_cents: Number(cost || existing.cost_cents || 0),
      avg_cost_cents: Number(cost || existing.avg_cost_cents || existing.cost_cents || 0),
      last_cost_cents: Number(cost || existing.last_cost_cents || existing.cost_cents || 0),
      wholesale_price_cents: Number(wholesale || existing.wholesale_price_cents || retail || 0),
      retail_price_cents: Number(retail || existing.retail_price_cents || 0),
      assisted_price_cents: Number(payload.assisted_price_cents || payload.discount_price_cents || existing.assisted_price_cents || retail || 0),
      discount_price_cents: Number(payload.discount_price_cents || existing.discount_price_cents || retail || 0),
      expiry_date: String(payload.expiry_date || payload.expiration_date || existing.expiry_date || ""),
      image_path: String(payload.image_path || existing.image_path || ""),
      created_at: existing.created_at || nowIso(),
      updated_at: nowIso()
    };
  }

  function normalizeContactPayload(payload = {}, existing = {}, type = "contact") {
    return {
      ...existing,
      id: existing.id || uid(type === "supplier" ? "sup" : "cust"),
      name: String(payload.name || payload.display_name || existing.name || "").trim() || (type === "supplier" ? "Fournisseur" : "Client"),
      phone: String(payload.phone || payload.telephone || existing.phone || "").trim(),
      address: String(payload.address || existing.address || "").trim(),
      reference: String(payload.reference || payload.ref || existing.reference || "").trim(),
      nif: String(payload.nif || existing.nif || ""),
      nis: String(payload.nis || existing.nis || ""),
      rc: String(payload.rc || existing.rc || ""),
      ai: String(payload.ai || existing.ai || ""),
      activity: String(payload.activity || existing.activity || ""),
      commerce_name: String(payload.commerce_name || payload.customer_commerce_name || existing.commerce_name || ""),
      balance_cents: Number(existing.balance_cents || 0),
      created_at: existing.created_at || nowIso(),
      updated_at: nowIso()
    };
  }

  function saleLines(payload) {
    return arr(payload.items || payload.lines).map((line) => {
      const p = findProduct(line.product_id) || {};
      const qty = Math.max(0, Number(line.qty || line.quantity || 0));
      const unit = Math.max(0, Number(line.unit_price_cents || line.unit_cents || p.retail_price_cents || 0));
      const discount = Math.max(0, Number(line.discount_cents || 0));
      return {
        id: uid("sl"),
        product_id: String(line.product_id || ""),
        product_name: String(p.name || line.name || "-"),
        barcode: String(p.barcode || line.barcode || ""),
        reference: String(p.reference || line.reference || ""),
        qty,
        unit_price_cents: unit,
        discount_cents: discount,
        total_cents: Math.max(0, Math.round(qty * unit) - discount),
        cost_cents: Number(p.cost_cents || p.avg_cost_cents || 0)
      };
    }).filter((line) => line.product_id && line.qty > 0);
  }

  function purchaseLines(payload) {
    return arr(payload.items || payload.lines).map((line) => {
      const p = findProduct(line.product_id) || {};
      const qty = Math.max(0, Number(line.qty || line.quantity || 0));
      const unit = Math.max(0, Number(line.unit_cost_cents || line.unit_price_cents || line.unit_cents || p.cost_cents || 0));
      return {
        id: uid("pl"),
        product_id: String(line.product_id || ""),
        product_name: String(p.name || line.name || "-"),
        barcode: String(p.barcode || line.barcode || ""),
        reference: String(p.reference || line.reference || ""),
        qty,
        unit_cost_cents: unit,
        total_cents: Math.round(qty * unit)
      };
    }).filter((line) => line.product_id && line.qty > 0);
  }

  function addFinance(kind, source, ref, party, amountCents, extra = {}) {
    const entry = {
      id: uid("fin"),
      date: nowIso(),
      entry_type: kind,
      source,
      ref,
      ref_id: extra.ref_id || ref,
      party_key: party && party.id ? party.id : "",
      party_name: party && party.name ? party.name : "",
      amount_cents: Math.max(0, Number(amountCents || 0)),
      status: extra.status || "Regle",
      pdf_kind: extra.pdf_kind || "",
      pdf_id: extra.pdf_id || "",
      product_id: extra.product_id || "",
      product_name: extra.product_name || "",
      qty: Number(extra.qty || 0),
      note: extra.note || ""
    };
    db.finance.unshift(entry);
    return entry;
  }

  function buildHtmlDoc(title, body) {
    return `<!doctype html><html><head><meta charset="utf-8"><title>${escapeHtml(title)}</title><style>body{font-family:Arial,sans-serif;padding:24px;color:#0f172a}h1{font-size:22px}.muted{color:#64748b}table{width:100%;border-collapse:collapse;margin-top:14px}th,td{border-bottom:1px solid #e2e8f0;padding:8px;text-align:left}.total{font-size:22px;font-weight:800;text-align:right;margin-top:18px}</style></head><body><h1>${escapeHtml(title)}</h1>${body}</body></html>`;
  }

  function escapeHtml(value) {
    return String(value == null ? "" : value).replace(/[&<>"']/g, (ch) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[ch]));
  }

  function docResult(title, html, suggestedName = "wili-document.html") {
    const dataUrl = `data:text/html;charset=utf-8,${encodeURIComponent(html)}`;
    return ok({ file_path: dataUrl, suggested_name: suggestedName, editable_html: html });
  }

  function saleDoc(saleId, title = "Bon de Livraison") {
    const sale = db.sales.find((s) => String(s.id) === String(saleId) || String(s.ref) === String(saleId));
    if (!sale) return fail("sale_not_found");
    const rows = sale.lines.map((l) => `<tr><td>${escapeHtml(l.product_name)}</td><td>${l.qty}</td><td>${money(l.unit_price_cents)}</td><td>${money(l.total_cents)}</td></tr>`).join("");
    return docResult(title, buildHtmlDoc(title, `<p class="muted">Ref: ${escapeHtml(sale.ref)} | Client: ${escapeHtml(sale.customer_name || "-")}</p><table><thead><tr><th>Produit</th><th>Qte</th><th>Prix</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table><div class="total">${money(sale.total_cents)} DZD</div>`), `${sale.ref.replace(/\//g, "-")}.html`);
  }

  function purchaseDoc(purchaseId, title = "Bon de Reception") {
    const purchase = db.purchases.find((s) => String(s.id) === String(purchaseId) || String(s.ref) === String(purchaseId));
    if (!purchase) return fail("purchase_not_found");
    const rows = purchase.lines.map((l) => `<tr><td>${escapeHtml(l.product_name)}</td><td>${l.qty}</td><td>${money(l.unit_cost_cents)}</td><td>${money(l.total_cents)}</td></tr>`).join("");
    return docResult(title, buildHtmlDoc(title, `<p class="muted">Ref: ${escapeHtml(purchase.ref)} | Fournisseur: ${escapeHtml(purchase.supplier_name || "-")}</p><table><thead><tr><th>Produit</th><th>Qte</th><th>Cout</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table><div class="total">${money(purchase.total_cents)} DZD</div>`), `${purchase.ref.replace(/\//g, "-")}.html`);
  }

  function dashboardSummary() {
    const salesTotal = db.sales.reduce((s, x) => s + Number(x.total_cents || 0), 0);
    const purchasesTotal = db.purchases.reduce((s, x) => s + Number(x.total_cents || 0), 0);
    const profit = db.sales.reduce((s, sale) => s + sale.lines.reduce((n, l) => n + (Number(l.total_cents || 0) - Math.round(Number(l.qty || 0) * Number(l.cost_cents || 0))), 0), 0);
    const inTotal = db.finance.filter((f) => f.entry_type !== "outcome").reduce((s, f) => s + Number(f.amount_cents || 0), 0);
    const outTotal = db.finance.filter((f) => f.entry_type === "outcome").reduce((s, f) => s + Number(f.amount_cents || 0), 0);
    return { sales_cents: salesTotal, purchases_cents: purchasesTotal, profit_cents: profit, cash_delta_cents: inTotal - outTotal };
  }

  const api = {
    getState: () => Promise.resolve({ activated: Boolean(db.activated) }),
    activate: () => { db.activated = true; saveDb(); return ok({ activated: true }); },
    applyDisplayProfile: () => ok({}),
    hasUsers: () => ok({ has_users: arr(db.users).length > 0 }),
    bootstrapFirstAdmin: (payload = {}) => {
      if (!String(payload.username || "").trim()) return fail("username_required");
      db.users.push({ id: uid("u"), username: payload.username, display_name: payload.display_name || payload.username, role: "admin", pin: payload.pin || "test", active: 1, created_at: nowIso() });
      saveDb();
      return ok({ created: true });
    },
    login: (payload = {}) => {
      const user = db.users.find((u) => normalize(u.username) === normalize(payload.username) && String(u.pin) === String(payload.pin));
      if (!user) return fail("invalid_credentials");
      const { pin, ...safe } = user;
      return ok(safe);
    },
    listUsers: () => ok(db.users.map(({ pin, ...u }) => u)),
    createUser: (payload = {}) => { db.users.push({ id: uid("u"), username: payload.username, display_name: payload.display_name || payload.username, role: payload.role || "seller", pin: payload.pin || "0000", active: 1, created_at: nowIso() }); saveDb(); return ok(db.users.at(-1)); },
    updateUserRole: (id, role) => { const u = db.users.find((x) => String(x.id) === String(id)); if (!u) return fail("user_not_found"); u.role = role; saveDb(); return ok(u); },
    setUserPin: (id, pin) => { const u = db.users.find((x) => String(x.id) === String(id)); if (!u) return fail("user_not_found"); u.pin = pin; saveDb(); return ok({}); },
    deleteUser: (id) => { db.users = db.users.filter((x) => String(x.id) !== String(id)); saveDb(); return ok({}); },
    onNavigate: () => () => {},
    onDbChanged: (handler) => { if (typeof handler === "function") dbChangedHandlers.add(handler); return () => dbChangedHandlers.delete(handler); }
  };

  const files = {
    saveCopyAs: (sourcePath, suggestedName) => {
      const a = document.createElement("a");
      a.href = sourcePath;
      a.download = suggestedName || "wili-document.html";
      a.click();
      return ok({ saved: true });
    },
    saveEditedPdf: (html, suggestedName) => docResult("Document", html, suggestedName || "wili-document.html"),
    listPrinters: () => ok([]),
    printPdf: () => { window.print(); return ok({ printed: true }); },
    pickProductImage: () => fail("browser_file_picker_not_available"),
    getProductImagesDefaultDir: () => ok("browser-local"),
    pickProductImagesDir: () => fail("browser_directory_picker_not_available"),
    pickXlsxFile: () => fail("browser_xlsx_picker_not_available"),
    readXlsxFile: () => fail("browser_xlsx_reader_not_available")
  };

  const dbApi = {
    getState: () => ok({ mode: "browser-localstorage", storage_key: DB_KEY }),
    getSettings: () => ok(db.settings),
    updateSettings: (patch = {}) => { db.settings = { ...db.settings, ...(patch || {}) }; saveDb(); return ok(db.settings); },
    createBackup: () => {
      const blob = new Blob([JSON.stringify(db, null, 2)], { type: "application/json" });
      const url = URL.createObjectURL(blob);
      const name = `wili-browser-backup-${new Date().toISOString().slice(0, 10)}.json`;
      const a = document.createElement("a");
      a.href = url;
      a.download = name;
      a.click();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
      return ok({ file_path: url, suggested_name: name });
    },
    restoreBackup: () => fail("use_browser_import_json_manually"),
    pickAndRestoreBackup: () => fail("browser_restore_not_available"),
    getArchiveStatus: () => ok({ available: false, mode: "browser" }),
    listArchives: () => ok([]),
    runArchive: () => ok({ archived: 0 }),

    listProducts: (limit = 500) => ok(db.products.slice(0, Number(limit || 500))),
    listProductsPage: (payload = {}) => {
      const q = payload.q || payload.search || "";
      const rows = sortRows(db.products.filter((p) => productMatches(p, q)), payload.sortKey, payload.sortDir);
      return ok(pageRows(rows, payload).rows);
    },
    getProductsSummary: () => ok({ total_products: db.products.length, count: db.products.length, total_stock_qty: db.products.reduce((s, p) => s + Number(p.stock_qty || 0), 0), stock_value_cents: db.products.reduce((s, p) => s + Math.round(Number(p.stock_qty || 0) * Number(p.cost_cents || 0)), 0) }),
    searchProducts: (query, limit = 100) => ok(db.products.filter((p) => productMatches(p, query)).slice(0, Number(limit || 100))),
    findProductByLookup: (query) => {
      const q = normalize(query);
      return ok(db.products.find((p) => [p.barcode, p.reference, p.sku].map(normalize).includes(q)) || null);
    },
    createProduct: (payload) => { const p = normalizeProductPayload(payload); db.products.unshift(p); audit("product_create", p.name); saveDb(); return ok(p); },
    updateProduct: (id, payload) => { const idx = db.products.findIndex((p) => String(p.id) === String(id)); if (idx < 0) return fail("product_not_found"); db.products[idx] = normalizeProductPayload(payload, db.products[idx]); saveDb(); return ok(db.products[idx]); },
    deleteProduct: (id) => { db.products = db.products.filter((p) => String(p.id) !== String(id)); saveDb(); return ok({ deleted: true }); },
    exportProductBarcodePdf: (id) => { const p = findProduct(id); return docResult("Code barre", buildHtmlDoc("Code barre", `<p>${escapeHtml(p?.name || "")}</p><h2>${escapeHtml(p?.barcode || p?.reference || id)}</h2>`), "barcode.html"); },
    adjustStock: (payload = {}) => { const p = findProduct(payload.product_id); if (!p) return fail("product_not_found"); p.stock_qty = Number(p.stock_qty || 0) + Number(payload.qty || 0); saveDb(); return ok(p); },
    recordStockLoss: (payload = {}) => { const p = findProduct(payload.product_id); if (!p) return fail("product_not_found"); const qty = Number(payload.qty || 0); p.stock_qty = Number(p.stock_qty || 0) - qty; const row = { id: uid("loss"), date: nowIso(), product_id: p.id, product_name: p.name, qty, reason: payload.reason || "", note: payload.note || "", cost_cents: Math.round(qty * Number(p.cost_cents || 0)) }; db.stock_losses.unshift(row); addFinance("outcome", "Perte stock", row.id, null, row.cost_cents, { product_id: p.id, product_name: p.name, qty }); saveDb(); return ok(row); },
    listStockLosses: (limit = 500) => ok(db.stock_losses.slice(0, Number(limit || 500))),
    getProductTrace: (productId) => ok(productTrace(productId)),
    getProductsTraceAll: () => ok(db.products.flatMap((p) => productTrace(p.id))),

    listCustomers: (limit = 1000) => ok(db.customers.slice(0, Number(limit || 1000))),
    listCustomersPage: (payload = {}) => ok(pageRows(sortRows(db.customers.filter((c) => contactMatches(c, payload.q || "")), payload.sortKey, payload.sortDir), payload)),
    createCustomer: (payload) => { const c = normalizeContactPayload(payload, {}, "customer"); db.customers.unshift(c); saveDb(); return ok(c); },
    updateCustomer: (id, payload) => { const idx = db.customers.findIndex((c) => String(c.id) === String(id)); if (idx < 0) return fail("customer_not_found"); db.customers[idx] = normalizeContactPayload(payload, db.customers[idx], "customer"); saveDb(); return ok(db.customers[idx]); },
    deleteCustomer: (id) => { db.customers = db.customers.filter((c) => String(c.id) !== String(id)); saveDb(); return ok({ deleted: true }); },

    listSuppliers: (limit = 1000) => ok(db.suppliers.slice(0, Number(limit || 1000))),
    listSuppliersPage: (payload = {}) => ok(pageRows(sortRows(db.suppliers.filter((s) => contactMatches(s, payload.q || "")), payload.sortKey, payload.sortDir), payload)),
    createSupplier: (payload) => { const s = normalizeContactPayload(payload, {}, "supplier"); db.suppliers.unshift(s); saveDb(); return ok(s); },
    updateSupplier: (id, payload) => { const idx = db.suppliers.findIndex((s) => String(s.id) === String(id)); if (idx < 0) return fail("supplier_not_found"); db.suppliers[idx] = normalizeContactPayload(payload, db.suppliers[idx], "supplier"); saveDb(); return ok(db.suppliers[idx]); },
    deleteSupplier: (id) => { db.suppliers = db.suppliers.filter((s) => String(s.id) !== String(id)); saveDb(); return ok({ deleted: true }); },

    listSales: (limit = 1000) => ok(db.sales.slice(0, Number(limit || 1000))),
    listSalesQuotes: () => ok(db.sale_quotes),
    createSaleQuote: (payload = {}) => { const q = { id: uid("quote"), ref: nextRef("quote"), date: nowIso(), ...payload, lines: saleLines(payload) }; q.total_cents = q.lines.reduce((s, l) => s + l.total_cents, 0); db.sale_quotes.unshift(q); saveDb(); return ok(q); },
    getSaleQuoteDetails: (id) => ok(db.sale_quotes.find((q) => String(q.id) === String(id)) || null),
    deleteSaleQuote: (id) => { db.sale_quotes = db.sale_quotes.filter((q) => String(q.id) !== String(id)); saveDb(); return ok({}); },
    convertSaleQuote: (payload = {}) => dbApi.createSale({ ...(db.sale_quotes.find((q) => String(q.id) === String(payload.quote_id)) || {}), ...payload }),
    createSale: (payload = {}) => {
      const customer = findCustomer(payload.customer_id) || null;
      const lines = saleLines(payload);
      const total = Math.max(0, Number(payload.total_cents || lines.reduce((s, l) => s + l.total_cents, 0)));
      const paid = Math.max(0, Number(payload.paid_cents ?? payload.amount_paid_cents ?? total));
      const sale = { id: uid("sale"), ref: nextRef("sale"), date: nowIso(), customer_id: customer?.id || payload.customer_id || "", customer_name: customer?.name || payload.customer_name || "", lines, total_cents: total, paid_cents: paid, due_cents: Math.max(0, total - paid), status: total - paid > 0 ? "Non regle" : "Regle" };
      lines.forEach((l) => { const p = findProduct(l.product_id); if (p && p.product_type !== "service") p.stock_qty = Number(p.stock_qty || 0) - Number(l.qty || 0); });
      db.sales.unshift(sale);
      addFinance("income", "Encaissement BL", sale.ref, customer, paid, { pdf_kind: "sale_invoice", pdf_id: sale.id, status: sale.status });
      saveDb();
      return ok(sale);
    },
    updateSale: (payload = {}) => { const idx = db.sales.findIndex((s) => String(s.id) === String(payload.id)); if (idx < 0) return dbApi.createSale(payload); db.sales[idx] = { ...db.sales[idx], ...payload, updated_at: nowIso() }; saveDb(); return ok(db.sales[idx]); },
    listSalesDues: (customerId) => ok(db.sales.filter((s) => (!customerId || String(s.customer_id) === String(customerId)) && Number(s.due_cents || 0) > 0)),
    addSalePayment: (payload = {}) => { const sale = db.sales.find((s) => String(s.id) === String(payload.sale_id) || String(s.ref) === String(payload.sale_id)); if (!sale) return fail("sale_not_found"); const amount = Math.max(0, Number(payload.amount_cents || 0)); sale.paid_cents = Number(sale.paid_cents || 0) + amount; sale.due_cents = Math.max(0, Number(sale.total_cents || 0) - sale.paid_cents); sale.status = sale.due_cents > 0 ? "Non regle" : "Regle"; const pay = { id: uid("pay"), sale_id: sale.id, ref: nextRef("payment"), amount_cents: amount, date: nowIso(), note: payload.note || "" }; db.sale_payments.unshift(pay); addFinance("income", "Reglement manuel", pay.ref, findCustomer(sale.customer_id), amount, { pdf_kind: "sale_payment", pdf_id: pay.id }); saveDb(); return ok(pay); },
    addSaleReturn: (payload = {}) => addSaleReturn(payload),
    addSaleReturnAll: (payload = {}) => addSaleReturn(payload),
    addSaleReturnGeneral: (payload = {}) => addSaleReturn(payload),
    settleSaleReturn: (payload = {}) => ok({ id: payload.return_id, settled: true }),
    getSalesTraceBulk: (payload = {}) => ok(arr(payload.sale_ids).flatMap((id) => saleTrace(id))),
    getSaleDetails: (id) => ok(db.sales.find((s) => String(s.id) === String(id) || String(s.ref) === String(id)) || null),
    deleteSale: (id) => { db.sales = db.sales.filter((s) => String(s.id) !== String(id)); saveDb(); return ok({ deleted: true }); },
    exportSaleCartPdf: (payload = {}) => docResult("Ticket de caisse", buildHtmlDoc("Ticket de caisse", `<pre>${escapeHtml(JSON.stringify(payload, null, 2))}</pre>`), "ticket.html"),
    exportSaleQuotePdf: (id) => docResult("Facture Proforma", buildHtmlDoc("Facture Proforma", `<p>${escapeHtml(id)}</p>`), "proforma.html"),
    exportSaleInvoicePdf: (id, options = {}) => saleDoc(id, String(options.document_mode || "") === "facture" ? "Facture de Vente" : "Bon de Livraison"),
    exportSaleReturnPdf: (id) => docResult("Bon de Retour client", buildHtmlDoc("Bon de Retour client", `<p>${escapeHtml(id)}</p>`), "retour-client.html"),
    exportSalesHistoryPdf: () => docResult("Historique ventes", buildHtmlDoc("Historique ventes", `<p>${db.sales.length} ventes</p>`), "ventes.html"),
    exportSalePaymentReceiptPdf: (id) => docResult("Recu paiement client", buildHtmlDoc("Recu paiement client", `<p>${escapeHtml(id)}</p>`), "recu-client.html"),

    listPurchases: (limit = 1000) => ok(db.purchases.slice(0, Number(limit || 1000))),
    createPurchase: (payload = {}) => {
      const supplier = findSupplier(payload.supplier_id) || null;
      const lines = purchaseLines(payload);
      const total = Math.max(0, Number(payload.total_cents || lines.reduce((s, l) => s + l.total_cents, 0)));
      const paid = Math.max(0, Number(payload.paid_cents ?? payload.amount_paid_cents ?? total));
      const purchase = { id: uid("purchase"), ref: nextRef("purchase"), date: nowIso(), supplier_id: supplier?.id || payload.supplier_id || "", supplier_name: supplier?.name || payload.supplier_name || "", lines, total_cents: total, paid_cents: paid, due_cents: Math.max(0, total - paid), status: total - paid > 0 ? "Non regle" : "Regle" };
      lines.forEach((l) => { const p = findProduct(l.product_id); if (p) { p.stock_qty = Number(p.stock_qty || 0) + Number(l.qty || 0); p.cost_cents = Number(l.unit_cost_cents || p.cost_cents || 0); p.last_cost_cents = p.cost_cents; } });
      db.purchases.unshift(purchase);
      addFinance("outcome", "Paiement BR", purchase.ref, supplier, paid, { pdf_kind: "purchase_invoice", pdf_id: purchase.id, status: purchase.status });
      saveDb();
      return ok(purchase);
    },
    updatePurchase: (payload = {}) => { const idx = db.purchases.findIndex((p) => String(p.id) === String(payload.id)); if (idx < 0) return dbApi.createPurchase(payload); db.purchases[idx] = { ...db.purchases[idx], ...payload, updated_at: nowIso() }; saveDb(); return ok(db.purchases[idx]); },
    listPurchaseDues: (supplierId) => ok(db.purchases.filter((p) => (!supplierId || String(p.supplier_id) === String(supplierId)) && Number(p.due_cents || 0) > 0)),
    addPurchasePayment: (payload = {}) => { const purchase = db.purchases.find((p) => String(p.id) === String(payload.purchase_id) || String(p.ref) === String(payload.purchase_id)); if (!purchase) return fail("purchase_not_found"); const amount = Math.max(0, Number(payload.amount_cents || 0)); purchase.paid_cents = Number(purchase.paid_cents || 0) + amount; purchase.due_cents = Math.max(0, Number(purchase.total_cents || 0) - purchase.paid_cents); purchase.status = purchase.due_cents > 0 ? "Non regle" : "Regle"; const pay = { id: uid("pay"), purchase_id: purchase.id, ref: nextRef("payment"), amount_cents: amount, date: nowIso(), note: payload.note || "" }; db.purchase_payments.unshift(pay); addFinance("outcome", "Reglement manuel", pay.ref, findSupplier(purchase.supplier_id), amount, { pdf_kind: "purchase_payment", pdf_id: pay.id }); saveDb(); return ok(pay); },
    addPurchaseReturn: (payload = {}) => addPurchaseReturn(payload),
    addPurchaseReturnAll: (payload = {}) => addPurchaseReturn(payload),
    addPurchaseReturnGeneral: (payload = {}) => addPurchaseReturn(payload),
    settlePurchaseReturn: (payload = {}) => ok({ id: payload.return_id, settled: true }),
    getPurchaseTraceBulk: (payload = {}) => ok(arr(payload.purchase_ids).flatMap((id) => purchaseTrace(id))),
    getPurchaseDetails: (id) => ok(db.purchases.find((p) => String(p.id) === String(id) || String(p.ref) === String(id)) || null),
    deletePurchase: (id) => { db.purchases = db.purchases.filter((p) => String(p.id) !== String(id)); saveDb(); return ok({ deleted: true }); },
    exportPurchasePdf: (payload = {}) => purchaseDoc(payload.purchase_id || payload.id || payload, String(payload.document_mode || "") === "facture" ? "Facture Achat" : "Bon de Reception"),
    exportPurchaseReturnPdf: (id) => docResult("Bon de Retour fournisseur", buildHtmlDoc("Bon de Retour fournisseur", `<p>${escapeHtml(id)}</p>`), "retour-fournisseur.html"),
    exportPurchasesHistoryPdf: () => docResult("Historique achats", buildHtmlDoc("Historique achats", `<p>${db.purchases.length} achats</p>`), "achats.html"),
    exportPurchasePaymentReceiptPdf: (id) => docResult("Recu paiement fournisseur", buildHtmlDoc("Recu paiement fournisseur", `<p>${escapeHtml(id)}</p>`), "recu-fournisseur.html"),

    listFinanceEntries: (limit = 1000) => ok(db.finance.slice(0, Number(limit || 1000))),
    getFinanceReport: () => ok(db.finance),
    addFinanceEntry: (payload = {}) => { const type = String(payload.entry_type || "income"); const row = addFinance(type === "expense" || type === "outcome" ? "outcome" : "income", "Reglement manuel", nextRef("payment"), { id: payload.party_key || "", name: payload.party_name || "" }, payload.amount_cents, { note: payload.note || "", pdf_kind: "finance_entry" }); saveDb(); return ok(row); },
    exportFinanceEntryPdf: (entry) => docResult("Reglement", buildHtmlDoc("Reglement", `<pre>${escapeHtml(JSON.stringify(entry, null, 2))}</pre>`), "finance.html"),
    getFinanceSummary: () => ok(dashboardSummary()),
    getCaisseState: () => ok(db.caisse),
    listCaisseSessions: (limit = 100) => ok(db.caisse_sessions.slice(0, Number(limit || 100))),
    openCaisse: (payload = {}) => { db.caisse = { open: true, box_id: payload.box_id || "main", opened_at: nowIso(), opening_cents: Number(payload.opening_cents || 0) }; saveDb(); return ok(db.caisse); },
    closeCaisse: (payload = {}) => { const row = { ...db.caisse, id: uid("cash"), closed_at: nowIso(), closing_cents: Number(payload.closing_cents || 0) }; db.caisse_sessions.unshift(row); db.caisse = { open: false, box_id: "main", opened_at: "", opening_cents: 0 }; saveDb(); return ok(row); },
    getDebtsSummary: () => ok({ customers: db.sales.filter((s) => s.due_cents > 0), suppliers: db.purchases.filter((p) => p.due_cents > 0), customer_due_cents: db.sales.reduce((n, s) => n + Number(s.due_cents || 0), 0), supplier_due_cents: db.purchases.reduce((n, p) => n + Number(p.due_cents || 0), 0) }),
    listAuditEvents: (payload = {}) => ok(pageRows(db.audit, { limit: payload.limit || 500, offset: payload.offset || 0 }).rows),
    exportAuditPdf: () => docResult("Audit", buildHtmlDoc("Audit", `<p>${db.audit.length} actions</p>`), "audit.html"),
    getDailyReport: () => {
      const s = dashboardSummary();
      return ok({
        sales_total_cents: s.sales_cents,
        purchases_total_cents: s.purchases_cents,
        profit_cents: s.profit_cents,
        cash_delta_cents: s.cash_delta_cents,
        sales: db.sales,
        purchases: db.purchases,
        finance: db.finance
      });
    },
    exportDailyReportPdf: () => docResult("Rapport journalier", buildHtmlDoc("Rapport journalier", `<pre>${escapeHtml(JSON.stringify(dashboardSummary(), null, 2))}</pre>`), "rapport.html"),
    exportCustomersDebtPdf: () => docResult("Dettes clients", buildHtmlDoc("Dettes clients", `<p>${db.sales.filter((s) => s.due_cents > 0).length}</p>`), "dettes-clients.html"),
    exportSuppliersDebtPdf: () => docResult("Dettes fournisseurs", buildHtmlDoc("Dettes fournisseurs", `<p>${db.purchases.filter((s) => s.due_cents > 0).length}</p>`), "dettes-fournisseurs.html"),
    exportCustomersPdf: () => docResult("Clients", buildHtmlDoc("Clients", `<p>${db.customers.length} clients</p>`), "clients.html"),
    exportSuppliersPdf: () => docResult("Fournisseurs", buildHtmlDoc("Fournisseurs", `<p>${db.suppliers.length} fournisseurs</p>`), "fournisseurs.html")
  };

  function addSaleReturn(payload) {
    const p = findProduct(payload.product_id);
    const customer = findCustomer(payload.customer_id);
    const qty = Number(payload.qty || 0);
    const unit = Number(payload.unit_price_cents || p?.retail_price_cents || 0);
    const row = { id: uid("rc"), ref: nextRef("sale_return"), date: nowIso(), customer_id: customer?.id || "", customer_name: customer?.name || "", product_id: p?.id || payload.product_id || "", product_name: p?.name || "", qty, amount_cents: Math.round(qty * unit) };
    if (p) p.stock_qty = Number(p.stock_qty || 0) + qty;
    db.sale_returns.unshift(row);
    addFinance("outcome", "Remboursement RC", row.ref, customer, row.amount_cents, { pdf_kind: "sale_return", pdf_id: row.id, product_id: row.product_id, product_name: row.product_name, qty });
    saveDb();
    return ok(row);
  }

  function addPurchaseReturn(payload) {
    const p = findProduct(payload.product_id);
    const supplier = findSupplier(payload.supplier_id);
    const qty = Number(payload.qty || 0);
    const unit = Number(payload.unit_cost_cents || p?.cost_cents || 0);
    const row = { id: uid("rf"), ref: nextRef("purchase_return"), date: nowIso(), supplier_id: supplier?.id || "", supplier_name: supplier?.name || "", product_id: p?.id || payload.product_id || "", product_name: p?.name || "", qty, amount_cents: Math.round(qty * unit) };
    if (p) p.stock_qty = Number(p.stock_qty || 0) - qty;
    db.purchase_returns.unshift(row);
    addFinance("income", "Remboursement RF", row.ref, supplier, row.amount_cents, { pdf_kind: "purchase_return", pdf_id: row.id, product_id: row.product_id, product_name: row.product_name, qty });
    saveDb();
    return ok(row);
  }

  function productTrace(productId) {
    const id = String(productId || "");
    const rows = [];
    db.sales.forEach((s) => s.lines.filter((l) => String(l.product_id) === id).forEach((l) => rows.push({ date: s.date, type: "Vente", ref: s.ref, qty_out: l.qty, qty_in: 0, amount_cents: l.total_cents })));
    db.purchases.forEach((p) => p.lines.filter((l) => String(l.product_id) === id).forEach((l) => rows.push({ date: p.date, type: "Achat", ref: p.ref, qty_in: l.qty, qty_out: 0, amount_cents: l.total_cents })));
    db.stock_losses.filter((l) => String(l.product_id) === id).forEach((l) => rows.push({ date: l.date, type: "Perte", ref: l.id, qty_out: l.qty, qty_in: 0, amount_cents: l.cost_cents }));
    return rows.sort((a, b) => String(b.date).localeCompare(String(a.date)));
  }

  function saleTrace(id) {
    const sale = db.sales.find((s) => String(s.id) === String(id) || String(s.ref) === String(id));
    if (!sale) return [];
    return sale.lines.map((l) => ({ date: sale.date, type: "Vente", ref: sale.ref, product_id: l.product_id, product_name: l.product_name, qty: l.qty, amount_cents: l.total_cents }));
  }

  function purchaseTrace(id) {
    const purchase = db.purchases.find((p) => String(p.id) === String(id) || String(p.ref) === String(id));
    if (!purchase) return [];
    return purchase.lines.map((l) => ({ date: purchase.date, type: "Achat", ref: purchase.ref, product_id: l.product_id, product_name: l.product_name, qty: l.qty, amount_cents: l.total_cents }));
  }

  window.wiliApi = api;
  window.wiliFiles = files;
  window.wiliDebug = { report: () => ok({}), getLogPath: () => ok("browser-console") };
  window.wiliDisplay = {
    listTargets: () => ok([]),
    listWindows: () => ok([]),
    openWindows: () => ok([]),
    closeWindow: () => ok({}),
    closeAllWindows: () => ok({}),
    listScanners: () => ok([]),
    bindScanner: () => ok({}),
    unbindScanner: () => ok({}),
    disconnectScanner: () => ok({}),
    listSerialPorts: () => ok([]),
    getSerialState: () => ok({ connected: false }),
    connectSerial: () => fail("serial_not_available_in_browser"),
    disconnectSerial: () => ok({})
  };
  window.wiliDb = dbApi;
  window.WILI_BROWSER_DB = {
    export: () => JSON.parse(JSON.stringify(db)),
    reset: () => { localStorage.removeItem(DB_KEY); db = seedDb(); saveDb(); location.reload(); }
  };
}());
