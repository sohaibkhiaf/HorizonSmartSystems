(function () {
  "use strict";

  const dbChangedHandlers = new Set();
  let installPromise = null;

  const ok = (data) => Promise.resolve({ ok: true, data });
  const fail = (error) => Promise.resolve({ ok: false, error: String(error || "error") });

  async function installDatabase() {
    if (!installPromise) {
      installPromise = fetch("./api/install.php", { credentials: "include" })
        .then(async (response) => {
          const text = await response.text();
          let payload = null;
          try { payload = JSON.parse(text); } catch (_err) {}
          if (!response.ok || !payload || payload.ok === false) {
            throw new Error(payload?.error || text || "install_failed");
          }
          return payload;
        })
        .finally(() => { installPromise = null; });
    }
    return installPromise;
  }

  async function apiCall(action, data, options) {
    const retry = options?.retry !== false;
    const response = await fetch("./api/index.php", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action, data: data || {} })
    });
    let payload = null;
    const text = await response.text();
    try { payload = JSON.parse(text); } catch (_err) {}
    if (payload && payload.ok) return { ok: true, data: payload.data };

    const error = payload?.error || text || `http_${response.status}`;
    if (retry && shouldAutoInstall(error)) {
      await installDatabase();
      return apiCall(action, data, { retry: false });
    }
    return { ok: false, error, detail: payload?.detail };
  }

  function shouldAutoInstall(error) {
    const value = String(error || "").toLowerCase();
    return value.includes("unknown database")
      || value.includes("base table")
      || value.includes("table")
      || value.includes("server_error");
  }

  function unwrap(result, fallback) {
    if (result && result.ok) return result.data;
    return fallback;
  }

  function emitDbChanged(source) {
    setTimeout(() => {
      dbChangedHandlers.forEach((handler) => {
        try { handler({ source: source || "mysql" }); } catch (_err) {}
      });
    }, 0);
  }

  async function mutating(action, data) {
    const out = await apiCall(action, data);
    if (out.ok) emitDbChanged(action);
    return out;
  }

  function normalizeLimit(payloadOrLimit, fallback) {
    if (typeof payloadOrLimit === "number") return { limit: payloadOrLimit };
    if (payloadOrLimit && typeof payloadOrLimit === "object") return payloadOrLimit;
    return { limit: fallback || 500 };
  }

  function simplePdf(title, payload) {
    return pdfCall("pdf.simple", { title, ...(payload || {}) }, title);
  }

  function requestedTemplateKey() {
    const params = new URLSearchParams(window.location.search || "");
    const raw = params.get("database") || params.get("db") || params.get("template") || params.get("workspace") || params.get("demo") || sessionStorage.getItem("wili_requested_template") || "";
    const key = normalizeTemplateKey(raw);
    if (key) sessionStorage.setItem("wili_requested_template", key);
    return key;
  }

  function normalizeTemplateKey(value) {
    const key = String(value || "").trim().toLowerCase().replace(/[\s_-]+/g, "");
    const aliases = {
      alimentation: "alimentation",
      allimatation: "alimentation",
      aliment: "alimentation",
      grocery: "alimentation",
      electric: "electric",
      electrique: "electric",
      electricity: "electric",
      pizzeria: "pizzeria",
      pizza: "pizzeria"
    };
    return aliases[key] || "";
  }

  function directEntryParams() {
    const params = new URLSearchParams(window.location.search || "");
    const user = String(params.get("user") || params.get("username") || params.get("email") || "").trim();
    const database = normalizeTemplateKey(params.get("database") || params.get("db") || params.get("template") || "");
    const pin = String(params.get("pin") || params.get("password") || "test").trim() || "test";
    if (!user || !database) return null;
    return { user, database, pin };
  }

  async function enterFromUrl(params) {
    const payload = params || directEntryParams();
    if (!payload) return { ok: false, error: "direct_demo_params_required" };
    const out = await apiCall("auth.demoEnter", payload);
    if (out.ok && payload.database) {
      sessionStorage.setItem("wili_requested_template", payload.database);
    }
    return out;
  }

  async function ensureRequestedTemplateWorkspace() {
    const templateKey = requestedTemplateKey();
    if (!templateKey) return { ok: true, data: { changed: false, reason: "no_template_requested" } };
    const [mine, current] = await Promise.all([
      apiCall("workspaces.mine", {}),
      apiCall("workspaces.current", {})
    ]);
    if (!mine.ok) return mine;
    if (current.ok && String(current.data?.template_key || "") === templateKey) {
      return { ok: true, data: { changed: false, workspace: current.data } };
    }
    const rows = Array.isArray(mine.data) ? mine.data : [];
    const existing = rows.find((row) => String(row.template_key || "") === templateKey);
    if (existing) {
      const switched = await dbApi.switchWorkspace(existing.id);
      return switched.ok ? { ok: true, data: { changed: true, workspace: switched.data } } : switched;
    }
    const label = templateKey.charAt(0).toUpperCase() + templateKey.slice(1);
    const created = await dbApi.createWorkspaceFromTemplate(templateKey, `Demo ${label}`);
    return created.ok ? { ok: true, data: { changed: true, workspace: created.data } } : created;
  }

  function htmlDocumentUrl(html) {
    const full = String(html || "");
    return URL.createObjectURL(new Blob([full], { type: "text/html;charset=utf-8" }));
  }

  async function pdfCall(action, data, fallbackTitle) {
    const out = await apiCall(action, data || {});
    return normalizePdfOut(out, fallbackTitle);
  }

  function normalizePdfOut(out, fallbackTitle) {
    if (!out || !out.ok || !out.data) return out;
    const data = { ...(out.data || {}) };
    const html = String(data.editable_html || "");
    if (html) {
      data.file_path = htmlDocumentUrl(html);
      data.web_html = html;
    } else if (String(data.file_path || "").startsWith("data:text/html")) {
      const decoded = decodeDataHtml(String(data.file_path || ""));
      if (decoded) {
        data.editable_html = decoded;
        data.web_html = decoded;
        data.file_path = htmlDocumentUrl(decoded);
      }
    }
    if (!data.suggested_name) {
      const safeTitle = String(fallbackTitle || "document").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "") || "document";
      data.suggested_name = `${safeTitle}.html`;
    }
    return { ...out, data };
  }

  function decodeDataHtml(value) {
    const raw = String(value || "");
    const comma = raw.indexOf(",");
    if (comma < 0) return "";
    try {
      return decodeURIComponent(raw.slice(comma + 1));
    } catch (_err) {
      return "";
    }
  }

  function downloadText(name, text, type) {
    const blob = new Blob([text], { type: type || "text/plain;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = name;
    a.click();
    setTimeout(() => URL.revokeObjectURL(url), 1500);
    return url;
  }

  const api = {
    async getState() {
      const out = await apiCall("app.getState", {});
      if (!out.ok) return { activated: true, mode: "mysql", error: out.error };
      return { ...(out.data || {}), activated: true };
    },
    activate: () => ok({ activated: true }),
    async hasUsers() {
      let out = await apiCall("auth.hasUsers", {});
      if (!out.ok) {
        try {
          await installDatabase();
          out = await apiCall("auth.hasUsers", {}, { retry: false });
        } catch (err) {
          return { ok: false, error: err.message || String(err) };
        }
      }
      return out;
    },
    bootstrapFirstAdmin: (payload) => mutating("auth.bootstrapFirstAdmin", payload),
    login: (payload) => apiCall("auth.login", payload),
    logout: () => apiCall("auth.logout", {}),
    me: () => apiCall("auth.me", {}),
    listUsers: () => ok([{ id: "current", username: "test", display_name: "test", role: "admin", active: 1 }]),
    createUser: () => fail("users_admin_not_available_in_web_demo_yet"),
    updateUserRole: () => fail("users_admin_not_available_in_web_demo_yet"),
    setUserPin: () => fail("users_admin_not_available_in_web_demo_yet"),
    deleteUser: () => fail("users_admin_not_available_in_web_demo_yet"),
    applyDisplayProfile: () => ok({}),
    onNavigate: () => {},
    onDbChanged: (handler) => {
      if (typeof handler === "function") dbChangedHandlers.add(handler);
      return () => dbChangedHandlers.delete(handler);
    }
  };

  const files = {
    listPrinters: () => ok([]),
    async printPdf(filePath, options) {
      const silent = Boolean(options?.silent);
      if (silent) {
        return fail("silent_print_not_available_in_browser");
      }
      return printHtmlPath(filePath);
    },
    openPath: (filePath) => {
      window.open(String(filePath || ""), "_blank");
      return ok({});
    },
    saveCopyAs: (filePath, suggestedName) => {
      const a = document.createElement("a");
      a.href = String(filePath || "");
      a.download = suggestedName || "document.html";
      a.click();
      return ok({ file_path: filePath });
    },
    saveEditedPdf: (html, suggestedName) => ok({ file_path: htmlDocumentUrl(html), editable_html: String(html || ""), suggested_name: suggestedName || "document.html" }),
    pickProductImage: () => fail("browser_file_picker_not_available"),
    pickProductImagesDir: () => fail("browser_directory_picker_not_available"),
    getProductImagesDefaultDir: () => ok(""),
    pickXlsxFile: () => fail("xlsx_import_uses_browser_upload_in_web_demo"),
    readXlsxFile: () => fail("xlsx_import_uses_browser_upload_in_web_demo")
  };

  function printHtmlPath(filePath) {
    const src = String(filePath || "").trim();
    if (!src) return fail("pdf_file_not_found");
    const iframe = document.createElement("iframe");
    iframe.style.position = "fixed";
    iframe.style.right = "0";
    iframe.style.bottom = "0";
    iframe.style.width = "1px";
    iframe.style.height = "1px";
    iframe.style.opacity = "0";
    iframe.style.border = "0";
    iframe.setAttribute("aria-hidden", "true");
    document.body.appendChild(iframe);
    let cleaned = false;
    const cleanup = () => {
      if (cleaned) return;
      cleaned = true;
      setTimeout(() => {
        try { iframe.remove(); } catch (_err) {}
      }, 15000);
    };
    iframe.onload = () => {
      setTimeout(() => {
        try {
          iframe.contentWindow?.focus?.();
          iframe.contentWindow?.print?.();
        } catch (_err) {
          const win = window.open(src, "_blank");
          if (win) setTimeout(() => { try { win.print(); } catch (__err) {} }, 500);
        } finally {
          cleanup();
        }
      }, 120);
    };
    iframe.src = src;
    return ok({ printed: true, browser_print: true, silent_supported: false });
  }

  const dbApi = {
    getState: () => apiCall("app.getState", {}),
    getSettings: () => apiCall("settings.get", {}),
    updateSettings: (patch) => mutating("settings.update", patch || {}),

    listWorkspaces: () => apiCall("workspaces.mine", {}),
    listWorkspaceTemplates: () => apiCall("workspaces.templates", {}),
    getCurrentWorkspace: () => apiCall("workspaces.current", {}),
    switchWorkspace: (workspaceId) => mutating("workspaces.switch", { workspace_id: workspaceId }),
    createWorkspaceFromTemplate: (templateKey, name) => mutating("workspaces.createFromTemplate", { template_key: templateKey, name }),
    resetWorkspace: () => mutating("workspaces.reset", {}),

    createBackup: async () => {
      const dump = await apiCall("workspaces.current", {});
      const name = `wili-web-workspace-${new Date().toISOString().slice(0, 10)}.json`;
      const url = downloadText(name, JSON.stringify(dump.data || {}, null, 2), "application/json;charset=utf-8");
      return { ok: true, data: { file_path: url, suggested_name: name } };
    },
    restoreBackup: () => fail("restore_not_available_in_web_demo_yet"),
    pickAndRestoreBackup: () => fail("restore_not_available_in_web_demo_yet"),
    getArchiveStatus: () => ok({ available: false, mode: "mysql-web-demo" }),
    listArchives: () => ok([]),
    runArchive: () => ok({ archived: 0 }),

    listProducts: (limit) => apiCall("products.list", normalizeLimit(limit, 500)),
    listProductsPage: (payload) => apiCall("products.page", payload || {}),
    getProductsSummary: () => apiCall("products.summary", {}),
    searchProducts: (query, limit) => apiCall("products.search", { query, limit: limit || 100 }),
    findProductByLookup: (query) => apiCall("products.lookup", { query }),
    createProduct: (payload) => mutating("products.create", payload || {}),
    updateProduct: (id, payload) => mutating("products.update", { id, payload: payload || {} }),
    deleteProduct: (id) => mutating("products.delete", { id }),
    adjustStock: (payload) => mutating("products.update", payload || {}),
    exportProductBarcodePdf: (id) => simplePdf("Code barre", { id }),
    recordStockLoss: (payload) => mutating("stock.loss.add", payload || {}),
    listStockLosses: (limit) => apiCall("stock.loss.list", normalizeLimit(limit, 500)),
    getProductTrace: (productId) => apiCall("products.trace", { product_id: productId }),
    getProductsTraceAll: () => apiCall("products.traceAll", {}),

    listCustomers: (limit) => apiCall("customers.list", normalizeLimit(limit, 1000)),
    listCustomersPage: (payload) => apiCall("customers.page", payload || {}),
    createCustomer: (payload) => mutating("customers.create", payload || {}),
    updateCustomer: (id, payload) => mutating("customers.update", { id, payload: payload || {} }),
    deleteCustomer: (id) => mutating("customers.delete", { id }),
    exportCustomersPdf: () => simplePdf("Clients", {}),

    listSuppliers: (limit) => apiCall("suppliers.list", normalizeLimit(limit, 1000)),
    listSuppliersPage: (payload) => apiCall("suppliers.page", payload || {}),
    createSupplier: (payload) => mutating("suppliers.create", payload || {}),
    updateSupplier: (id, payload) => mutating("suppliers.update", { id, payload: payload || {} }),
    deleteSupplier: (id) => mutating("suppliers.delete", { id }),
    exportSuppliersPdf: () => simplePdf("Fournisseurs", {}),

    listSales: (payloadOrLimit) => apiCall("sales.list", normalizeLimit(payloadOrLimit, 1000)),
    createSale: (payload) => mutating("sales.create", payload || {}),
    updateSale: (payload) => mutating("sales.update", payload || {}),
    getSaleDetails: (id) => apiCall("sales.details", { id }),
    deleteSale: (id) => mutating("sales.delete", { id }),
    listSalesDues: (customerId) => apiCall("sales.dues", { customer_id: customerId || "" }),
    addSalePayment: (payload) => mutating("sales.payment.add", payload || {}),
    getSalesTraceBulk: () => ok([]),
    listSalesQuotes: () => ok([]),
    createSaleQuote: () => fail("quotes_not_available_in_web_demo_yet"),
    getSaleQuoteDetails: () => ok(null),
    deleteSaleQuote: () => ok({ deleted: true }),
    convertSaleQuote: () => fail("quotes_not_available_in_web_demo_yet"),
    addSaleReturn: () => fail("returns_not_available_in_web_demo_yet"),
    addSaleReturnAll: () => fail("returns_not_available_in_web_demo_yet"),
    addSaleReturnGeneral: () => fail("returns_not_available_in_web_demo_yet"),
    settleSaleReturn: () => fail("returns_not_available_in_web_demo_yet"),
    exportSaleCartPdf: (payload) => pdfCall("pdf.saleCart", payload || {}, "Ticket de caisse"),
    exportSaleQuotePdf: (id) => simplePdf("Facture Proforma", { id }),
    exportSaleInvoicePdf: (id, options) => pdfCall("pdf.saleInvoice", { id, ...(options || {}) }, "Bon de Livraison"),
    exportSaleReturnPdf: (id) => simplePdf("Bon de Retour client", { id }),
    exportSalesHistoryPdf: () => simplePdf("Historique ventes", {}),
    exportSalePaymentReceiptPdf: (id) => simplePdf("Recu paiement client", { id }),

    listPurchases: (payloadOrLimit) => apiCall("purchases.list", normalizeLimit(payloadOrLimit, 1000)),
    createPurchase: (payload) => mutating("purchases.create", payload || {}),
    updatePurchase: (payload) => mutating("purchases.update", payload || {}),
    getPurchaseDetails: (id) => apiCall("purchases.details", { id }),
    deletePurchase: (id) => mutating("purchases.delete", { id }),
    listPurchaseDues: (supplierId) => apiCall("purchases.dues", { supplier_id: supplierId || "" }),
    addPurchasePayment: (payload) => mutating("purchases.payment.add", payload || {}),
    getPurchaseTraceBulk: () => ok([]),
    addPurchaseReturn: () => fail("returns_not_available_in_web_demo_yet"),
    addPurchaseReturnAll: () => fail("returns_not_available_in_web_demo_yet"),
    addPurchaseReturnGeneral: () => fail("returns_not_available_in_web_demo_yet"),
    settlePurchaseReturn: () => fail("returns_not_available_in_web_demo_yet"),
    exportPurchasePdf: (payload) => pdfCall("pdf.purchase", payload || {}, "Bon de Reception"),
    exportPurchaseReturnPdf: (id) => simplePdf("Bon de Retour fournisseur", { id }),
    exportPurchasesHistoryPdf: () => simplePdf("Historique achats", {}),
    exportPurchasePaymentReceiptPdf: (id) => simplePdf("Recu paiement fournisseur", { id }),

    listFinanceEntries: (limit) => apiCall("finance.entries.list", normalizeLimit(limit, 1000)),
    getFinanceReport: () => apiCall("finance.report", {}),
    getFinanceSummary: () => apiCall("finance.summary", {}),
    addFinanceEntry: (payload) => mutating("finance.entry.add", payload || {}),
    exportFinanceEntryPdf: (entry) => simplePdf("Reglement", { entry }),
    getDebtsSummary: () => apiCall("debts.summary", {}),
    listAuditEvents: (payload) => apiCall("audit.list", payload || {}),
    exportAuditPdf: () => simplePdf("Audit", {}),
    getDailyReport: (date) => apiCall("reports.daily", { date }),
    exportDailyReportPdf: (date) => simplePdf("Rapport journalier", { date }),
    exportCustomersDebtPdf: () => simplePdf("Dettes clients", {}),
    exportSuppliersDebtPdf: () => simplePdf("Dettes fournisseurs", {}),

    getCaisseState: () => ok({ open: false, box_id: "main" }),
    openCaisse: () => ok({ open: true }),
    closeCaisse: () => ok({ open: false }),
    listCaisseSessions: () => ok([])
  };

  function initWorkspaceSwitcher() {
    let pending = false;
    async function refresh() {
      const appRoot = document.getElementById("appRoot");
      const host = document.querySelector(".topbar-right");
      if (!appRoot || appRoot.classList.contains("hidden") || !host) return;
      let root = document.getElementById("workspaceDemoSwitcher");
      if (!root) {
        root = document.createElement("div");
        root.id = "workspaceDemoSwitcher";
        root.className = "workspace-demo-switcher";
        root.innerHTML = [
          '<select data-role="workspace" title="Workspace demo"></select>',
          '<select data-role="template" title="Template demo"></select>',
          '<button type="button" data-action="create">Creer</button>',
          '<button type="button" class="danger" data-action="reset">Reset</button>'
        ].join("");
        host.insertBefore(root, document.getElementById("userBadge"));
        bindWorkspaceSwitcher(root);
      }
      if (pending) return;
      pending = true;
      try {
        const [mine, current, templates] = await Promise.all([
          apiCall("workspaces.mine", {}),
          apiCall("workspaces.current", {}),
          apiCall("workspaces.templates", {})
        ]);
        if (!mine.ok || !templates.ok) return;
        const workspaceSelect = root.querySelector('[data-role="workspace"]');
        const templateSelect = root.querySelector('[data-role="template"]');
        const currentId = String(current?.data?.id || "");
        workspaceSelect.innerHTML = (mine.data || []).map((row) => `<option value="${escapeAttr(row.id)}">${escapeHtml(row.name || row.template_key || row.id)}</option>`).join("");
        workspaceSelect.value = currentId;
        templateSelect.innerHTML = (templates.data || []).map((row) => `<option value="${escapeAttr(row.template_key)}">${escapeHtml(row.template_key || row.name)}</option>`).join("");
      } finally {
        pending = false;
      }
    }
    window.addEventListener("load", () => {
      setInterval(refresh, 1500);
      setTimeout(refresh, 300);
    });
  }

  function bindWorkspaceSwitcher(root) {
    root.addEventListener("change", async (event) => {
      const target = event.target;
      if (!target || target.getAttribute("data-role") !== "workspace") return;
      const value = target.value;
      if (!value) return;
      const out = await dbApi.switchWorkspace(value);
      if (out.ok) location.reload();
      else alert(out.error || "switch_workspace_failed");
    });
    root.addEventListener("click", async (event) => {
      const button = event.target.closest("button[data-action]");
      if (!button) return;
      const action = button.getAttribute("data-action");
      if (action === "create") {
        const templateKey = root.querySelector('[data-role="template"]')?.value || "electric";
        const name = prompt("Nom de la nouvelle demo", `Demo ${templateKey}`);
        if (!name) return;
        const out = await dbApi.createWorkspaceFromTemplate(templateKey, name);
        if (out.ok) location.reload();
        else alert(out.error || "create_workspace_failed");
      }
      if (action === "reset") {
        if (!confirm("Reinitialiser cette demo depuis son template ?")) return;
        const out = await dbApi.resetWorkspace();
        if (out.ok) location.reload();
        else alert(out.error || "reset_workspace_failed");
      }
    });
  }

  function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (ch) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;"
    })[ch]);
  }

  function escapeAttr(value) {
    return escapeHtml(value);
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
  window.WILI_MYSQL_DEMO = {
    install: installDatabase,
    apiCall,
    switchWorkspace: dbApi.switchWorkspace,
    createWorkspaceFromTemplate: dbApi.createWorkspaceFromTemplate,
    resetWorkspace: dbApi.resetWorkspace,
    requestedTemplateKey,
    directEntryParams,
    enterFromUrl,
    ensureRequestedTemplateWorkspace
  };
  initWorkspaceSwitcher();
}());
