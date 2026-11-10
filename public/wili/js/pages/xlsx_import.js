(function () {
  const FIELD_SETS = {
    products: [
      { key: "name", label: "Nom produit", type: "text", required: true, aliases: ["designation", "desig", "nom", "libelle", "article", "produit"] },
      { key: "barcode", label: "Code barre", type: "text", aliases: ["barcode", "code barre", "code", "ean"] },
      { key: "reference", label: "Reference", type: "text", aliases: ["reference", "ref", "code famille"] },
      { key: "stock_qty", label: "Stock", type: "number", aliases: ["stock", "stk init", "qte", "quantite"] },
      { key: "cost_cents", label: "Prix achat", type: "money", aliases: ["pu achat", "prix achat", "achat", "prix achat int"] },
      { key: "wholesale_price_cents", label: "Prix gros", type: "money", aliases: ["prix vente gros", "vente gros", "gros"] },
      { key: "assisted_price_cents", label: "Prix demi gros / special", type: "money", aliases: ["prix vente demi gros", "demi gros", "special", "prix special", "pu special"] },
      { key: "retail_price_cents", label: "Prix detail", type: "money", aliases: ["pu detail", "prix detail", "detail", "prix vente detail"] },
      { key: "price_cents", label: "Prix principal", type: "money", aliases: ["pu detail", "prix vente", "prix", "pu a fact"] },
      { key: "expiry_date", label: "Date expiration", type: "date", aliases: ["expiration", "date exp", "date expiration", "peremption"] },
      { key: "product_type", label: "Type", type: "text", aliases: ["type", "nature"] }
    ],
    customers: [
      { key: "name", label: "Nom client", type: "text", required: true, aliases: ["nom", "client", "raison sociale", "name"] },
      { key: "phone", label: "Telephone", type: "text", aliases: ["telephone", "tel", "phone", "mobile"] },
      { key: "commerce_name", label: "Nom commercial", type: "text", aliases: ["commerce", "nom commercial", "enseigne"] },
      { key: "activity", label: "Activite", type: "text", aliases: ["activite", "activity"] },
      { key: "nif", label: "NIF", type: "text", aliases: ["nif"] },
      { key: "nis", label: "NIS", type: "text", aliases: ["nis"] },
      { key: "rc", label: "RC", type: "text", aliases: ["rc", "registre commerce"] },
      { key: "ai", label: "AI", type: "text", aliases: ["ai", "article imposition"] },
      { key: "email", label: "Email", type: "text", aliases: ["email", "mail"] },
      { key: "address", label: "Adresse", type: "text", aliases: ["adresse", "address"] },
      { key: "notes", label: "Notes", type: "text", aliases: ["note", "notes", "observation"] }
    ],
    suppliers: [
      { key: "name", label: "Nom fournisseur", type: "text", required: true, aliases: ["nom", "fournisseur", "raison sociale", "name"] },
      { key: "phone", label: "Telephone", type: "text", aliases: ["telephone", "tel", "phone", "mobile"] },
      { key: "commerce_name", label: "Nom commercial", type: "text", aliases: ["commerce", "nom commercial", "enseigne"] },
      { key: "activity", label: "Activite", type: "text", aliases: ["activite", "activity"] },
      { key: "nif", label: "NIF", type: "text", aliases: ["nif"] },
      { key: "nis", label: "NIS", type: "text", aliases: ["nis"] },
      { key: "rc", label: "RC", type: "text", aliases: ["rc", "registre commerce"] },
      { key: "ai", label: "AI", type: "text", aliases: ["ai", "article imposition"] },
      { key: "email", label: "Email", type: "text", aliases: ["email", "mail"] },
      { key: "address", label: "Adresse", type: "text", aliases: ["adresse", "address"] },
      { key: "notes", label: "Notes", type: "text", aliases: ["note", "notes", "observation"] }
    ]
  };

  const state = {
    workbook: null,
    sheetIndex: 0
  };

  function $(id) {
    return document.getElementById(id);
  }

  function tr(fr, ar, dz) {
    return typeof langText === "function" ? langText(fr, ar || fr, dz || ar || fr) : fr;
  }

  function i18n(key, fallback = "") {
    return typeof t === "function" ? t(key) : fallback;
  }

  function i18nFormat(key, fallback, values = {}) {
    let text = i18n(key, fallback);
    Object.entries(values || {}).forEach(([name, value]) => {
      text = text.replace(new RegExp(`\\{${name}\\}`, "g"), String(value));
    });
    return text;
  }

  const FIELD_LABEL_KEYS = {
    "Nom produit": "xlsx_field_product_name",
    "Code barre": "xlsx_field_barcode",
    "Reference": "xlsx_field_reference",
    "Stock": "xlsx_field_stock",
    "Prix achat": "xlsx_field_cost",
    "Prix gros": "xlsx_field_wholesale",
    "Prix demi gros / special": "xlsx_field_assisted",
    "Prix detail": "xlsx_field_retail",
    "Prix principal": "xlsx_field_main_price",
    "Date expiration": "xlsx_field_expiry",
    "Type": "xlsx_field_type",
    "Nom client": "xlsx_field_customer_name",
    "Nom fournisseur": "xlsx_field_supplier_name",
    "Telephone": "xlsx_field_phone",
    "Nom commercial": "xlsx_field_commerce",
    "Activite": "xlsx_field_activity",
    "Email": "xlsx_field_email",
    "Adresse": "xlsx_field_address",
    "Notes": "xlsx_field_notes"
  };

  function fieldDisplayLabel(field) {
    const key = FIELD_LABEL_KEYS[String(field && field.label || "")];
    return key ? i18n(key, field.label) : String(field && field.label || "");
  }

  function targetDisplayLabel(target) {
    if (target === "customers") return i18n("xlsx_target_customers", "Clients");
    if (target === "suppliers") return i18n("xlsx_target_suppliers", "Fournisseurs");
    return i18n("xlsx_target_products", "Produits");
  }

  function esc(value) {
    return String(value == null ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function notify(message, danger = false) {
    if (typeof appNotify === "function") appNotify(message, danger, 3000);
  }

  function normalizeLabel(value) {
    return String(value == null ? "" : value)
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, " ")
      .trim();
  }

  function currentSheet() {
    const sheets = state.workbook && Array.isArray(state.workbook.sheets) ? state.workbook.sheets : [];
    return sheets[state.sheetIndex] || null;
  }

  function fieldsForTarget() {
    const target = $("xlsxTargetSelect")?.value || "products";
    return FIELD_SETS[target] || FIELD_SETS.products;
  }

  function headerOptions(headers, selected = "") {
    const list = Array.isArray(headers) ? headers : [];
    return [
      `<option value="">${esc(i18n("xlsx_ignore", "-- Ignorer --"))}</option>`,
      ...list.map((h) => `<option value="${esc(h.key)}"${String(h.key) === String(selected) ? " selected" : ""}>${esc(h.column)} - ${esc(h.label)}</option>`)
    ].join("");
  }

  function suggestHeader(headers, field) {
    const list = Array.isArray(headers) ? headers : [];
    const aliases = [field.key, field.label, ...(field.aliases || [])].map(normalizeLabel).filter(Boolean);
    let best = "";
    for (const h of list) {
      const label = normalizeLabel(h.label);
      if (!label) continue;
      if (aliases.some((alias) => label === alias)) return h.key;
      if (!best && aliases.some((alias) => label.includes(alias) || alias.includes(label))) best = h.key;
    }
    return best;
  }

  function getMappings() {
    const out = {};
    document.querySelectorAll("[data-xlsx-map-field]").forEach((select) => {
      const field = select.getAttribute("data-xlsx-map-field") || "";
      if (field) out[field] = select.value || "";
    });
    return out;
  }

  function parseNumber(value) {
    if (typeof value === "number" && Number.isFinite(value)) return value;
    const cleaned = String(value == null ? "" : value)
      .replace(/\s+/g, "")
      .replace(/[^\d,.-]/g, "");
    if (!cleaned) return 0;
    const normalized = cleaned.includes(",") && !cleaned.includes(".")
      ? cleaned.replace(",", ".")
      : cleaned.replace(/,/g, "");
    const num = Number(normalized);
    return Number.isFinite(num) ? num : 0;
  }

  function moneyToCents(value) {
    return Math.max(0, Math.round(parseNumber(value) * 100));
  }

  function excelDateToIso(value) {
    if (typeof value === "number" && Number.isFinite(value) && value > 20000) {
      const ms = Date.UTC(1899, 11, 30) + Math.round(value) * 86400000;
      return new Date(ms).toISOString().slice(0, 10);
    }
    const raw = String(value == null ? "" : value).trim();
    if (!raw) return "";
    const iso = raw.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})/);
    if (iso) return `${iso[1]}-${String(iso[2]).padStart(2, "0")}-${String(iso[3]).padStart(2, "0")}`;
    const local = raw.match(/^(\d{1,2})[-/](\d{1,2})[-/](\d{4})/);
    if (local) return `${local[3]}-${String(local[2]).padStart(2, "0")}-${String(local[1]).padStart(2, "0")}`;
    return raw;
  }

  function fieldValue(raw, field) {
    if (raw == null || raw === "") return "";
    if (field.type === "money") return moneyToCents(raw);
    if (field.type === "number") return parseNumber(raw);
    if (field.type === "date") return excelDateToIso(raw);
    return String(raw).trim();
  }

  function buildPayload(row, target, mappings) {
    const fields = FIELD_SETS[target] || [];
    const payload = {};
    for (const field of fields) {
      const headerKey = mappings[field.key];
      if (!headerKey) continue;
      const value = fieldValue(row.values ? row.values[headerKey] : "", field);
      if (value === "") continue;
      payload[field.key] = value;
    }

    if (target === "products") {
      payload.product_type = String(payload.product_type || "product").toLowerCase().includes("service") ? "service" : "product";
      if (payload.retail_price_cents == null && payload.price_cents != null) payload.retail_price_cents = payload.price_cents;
      if (payload.price_cents == null && payload.retail_price_cents != null) payload.price_cents = payload.retail_price_cents;
      if (payload.assisted_price_cents == null && payload.retail_price_cents != null) payload.assisted_price_cents = payload.retail_price_cents;
      if (payload.wholesale_price_cents == null && payload.retail_price_cents != null) payload.wholesale_price_cents = payload.retail_price_cents;
    }
    return payload;
  }

  function requiredFieldsMapped() {
    const mappings = getMappings();
    return fieldsForTarget().filter((field) => field.required).every((field) => Boolean(mappings[field.key]));
  }

  function renderHeaders() {
    const box = $("xlsxHeadersBox");
    const sheet = currentSheet();
    if (!box) return;
    if (!sheet) {
      box.innerHTML = `<div class="empty">${esc(i18n("xlsx_no_file_selected", "Aucun fichier XLSX selectionne"))}</div>`;
      return;
    }
    const headers = Array.isArray(sheet.headers) ? sheet.headers : [];
    box.innerHTML = `
      <div class="small">${esc(i18n("xlsx_header_row", "Ligne en-tete"))}: ${esc(sheet.header_row || 1)} | ${esc(i18n("xlsx_rows", "Lignes"))}: ${esc((sheet.rows || []).length)}</div>
      <div class="xlsx-headers-list">
        ${headers.map((h) => `<span class="xlsx-header-chip">${esc(h.column)}: ${esc(h.label)}</span>`).join("")}
      </div>
    `;
  }

  function renderMapping() {
    const box = $("xlsxMappingBox");
    const importBtn = $("xlsxImportBtn");
    const sheet = currentSheet();
    if (!box) return;
    if (!sheet) {
      box.innerHTML = "";
      if (importBtn) importBtn.disabled = true;
      return;
    }
    const headers = Array.isArray(sheet.headers) ? sheet.headers : [];
    box.innerHTML = fieldsForTarget().map((field) => {
      const selected = suggestHeader(headers, field);
      return `
        <label class="xlsx-map-row${field.required ? " required" : ""}">
          <strong>${esc(fieldDisplayLabel(field))}</strong>
          <select data-xlsx-map-field="${esc(field.key)}">${headerOptions(headers, selected)}</select>
        </label>
      `;
    }).join("");
    box.querySelectorAll("select[data-xlsx-map-field]").forEach((select) => {
      select.addEventListener("change", () => {
        renderPreview();
        if (importBtn) importBtn.disabled = !requiredFieldsMapped();
      });
    });
    if (importBtn) importBtn.disabled = !requiredFieldsMapped();
  }

  function renderPreview() {
    const box = $("xlsxPreviewBox");
    const sheet = currentSheet();
    if (!box) return;
    if (!sheet) {
      box.innerHTML = "";
      return;
    }
    const mappings = getMappings();
    const fields = fieldsForTarget().filter((field) => mappings[field.key]);
    if (!fields.length) {
      box.innerHTML = `<div class="empty">${esc(i18n("xlsx_preview_hint", "Relier au moins un champ pour voir l'apercu."))}</div>`;
      return;
    }
    const rows = (sheet.preview_rows || sheet.rows || []).slice(0, 8);
    box.innerHTML = `
      <table class="xlsx-preview-table">
        <thead><tr>${fields.map((field) => `<th>${esc(fieldDisplayLabel(field))}</th>`).join("")}</tr></thead>
        <tbody>
          ${rows.map((row) => {
            const payload = buildPayload(row, $("xlsxTargetSelect")?.value || "products", mappings);
            return `<tr>${fields.map((field) => `<td>${esc(payload[field.key] != null ? payload[field.key] : "")}</td>`).join("")}</tr>`;
          }).join("")}
        </tbody>
      </table>
    `;
  }

  function renderWorkbook() {
    const sheetSelect = $("xlsxSheetSelect");
    const workbook = state.workbook;
    const sheets = workbook && Array.isArray(workbook.sheets) ? workbook.sheets : [];
    if (sheetSelect) {
      sheetSelect.innerHTML = sheets.map((sheet, index) => `<option value="${index}">${esc(sheet.name || i18nFormat("xlsx_sheet_fallback", "Feuille {number}", { number: index + 1 }))}</option>`).join("");
      sheetSelect.value = String(state.sheetIndex || 0);
      sheetSelect.disabled = !sheets.length;
    }
    renderHeaders();
    renderMapping();
    renderPreview();
  }

  async function pickWorkbook() {
    const pickBtn = $("xlsxPickBtn");
    const fileName = $("xlsxFileName");
    if (!window.wiliFiles || typeof window.wiliFiles.pickXlsxFile !== "function" || typeof window.wiliFiles.readXlsxFile !== "function") {
      notify("xlsx_import_unavailable", true);
      return;
    }
    if (pickBtn) pickBtn.disabled = true;
    try {
      const picked = await window.wiliFiles.pickXlsxFile();
      if (!picked || !picked.ok) {
        if (picked && picked.error !== "xlsx_pick_cancelled") notify(picked.error || "xlsx_pick_failed", true);
        return;
      }
      const filePath = picked.data && picked.data.file_path ? picked.data.file_path : "";
      if (fileName) fileName.textContent = picked.data && picked.data.file_name ? picked.data.file_name : filePath;
      const read = await window.wiliFiles.readXlsxFile({ file_path: filePath, max_rows: 20000 });
      if (!read || !read.ok) {
        notify(read && read.error ? read.error : "xlsx_read_failed", true);
        return;
      }
      state.workbook = read.data || null;
      state.sheetIndex = 0;
      renderWorkbook();
    } finally {
      if (pickBtn) pickBtn.disabled = false;
    }
  }

  async function refreshAfterImport(target) {
    if (typeof refreshAfterMutation === "function") {
      await refreshAfterMutation(target || activeView, { reason: "xlsx_import" });
      return;
    }
    if (target === "products" && typeof refreshProducts === "function") await refreshProducts();
    if (target === "customers" && typeof refreshCustomers === "function") await refreshCustomers({ skipDependentRefresh: true });
    if (target === "suppliers" && typeof refreshSuppliers === "function") await refreshSuppliers({ skipDependentRefresh: true });
  }

  async function importRows() {
    const sheet = currentSheet();
    const resultBox = $("xlsxResultBox");
    const importBtn = $("xlsxImportBtn");
    if (!sheet) return;
    if (!requiredFieldsMapped()) {
      notify(i18n("xlsx_required_unmapped", "Les champs obligatoires ne sont pas relies."), true);
      return;
    }
    const target = $("xlsxTargetSelect")?.value || "products";
    const rows = Array.isArray(sheet.rows) ? sheet.rows : [];
    const confirmText = i18nFormat("xlsx_confirm_import", "Importer {count} lignes vers {target} ?", { count: rows.length, target: targetDisplayLabel(target) });
    if (typeof appConfirm === "function") {
      const ok = await appConfirm(confirmText, { okText: i18n("xlsx_import_rows", "Importer lignes"), cancelText: langText("Annuler", "إلغاء", "حبس") });
      if (!ok) return;
    }

    const mappings = getMappings();
    let created = 0;
    let skipped = 0;
    const errors = [];
    if (importBtn) importBtn.disabled = true;
    try {
      for (let i = 0; i < rows.length; i += 1) {
        const payload = buildPayload(rows[i], target, mappings);
        if (!String(payload.name || "").trim()) {
          skipped += 1;
          continue;
        }
        let out = null;
        if (target === "products") out = await window.wiliDb.createProduct(payload);
        else if (target === "customers") out = await window.wiliDb.createCustomer(payload);
        else out = await window.wiliDb.createSupplier(payload);
        if (out && out.ok) created += 1;
        else errors.push({ row: rows[i].row_number, error: out && out.error ? out.error : "create_failed" });
        if (resultBox && (i + 1) % 25 === 0) {
          resultBox.innerHTML = `<div class="small">${esc(i18n("xlsx_progress", "Progression"))}: ${i + 1}/${rows.length} | OK: ${created} | ${esc(i18n("xlsx_errors", "Erreurs"))}: ${errors.length}</div>`;
        }
      }
      await refreshAfterImport(target);
      if (resultBox) {
        resultBox.innerHTML = `
          <div class="small">${esc(i18n("xlsx_done", "Import termine"))} | ${esc(i18n("xlsx_created", "Cree"))}: ${created} | ${esc(i18n("xlsx_skipped", "Ignore"))}: ${skipped} | ${esc(i18n("xlsx_errors", "Erreurs"))}: ${errors.length}</div>
          ${errors.length ? `<ul>${errors.slice(0, 12).map((err) => `<li>${esc(i18n("xlsx_line", "Ligne"))} ${esc(err.row)}: ${esc(err.error)}</li>`).join("")}</ul>` : ""}
        `;
      }
      notify(i18n("xlsx_done", "Import XLSX termine"));
    } finally {
      if (importBtn) importBtn.disabled = !requiredFieldsMapped();
    }
  }

  function activateBackupTab(tabKey = "backup") {
    const root = $("view-backups");
    if (!root) return;
    const wanted = String(tabKey || "backup");
    root.querySelectorAll("[data-backup-tab]").forEach((btn) => {
      btn.classList.toggle("active", String(btn.getAttribute("data-backup-tab") || "") === wanted);
    });
    root.querySelectorAll("[data-backup-pane]").forEach((pane) => {
      pane.classList.toggle("active", String(pane.getAttribute("data-backup-pane") || "") === wanted);
    });
  }

  function initXlsxImport() {
    const root = $("view-backups");
    if (!root || root.dataset.xlsxImportReady === "1") return;
    root.dataset.xlsxImportReady = "1";
    root.querySelectorAll("[data-backup-tab]").forEach((btn) => {
      btn.addEventListener("click", () => activateBackupTab(btn.getAttribute("data-backup-tab") || "backup"));
    });
    $("xlsxPickBtn")?.addEventListener("click", () => void pickWorkbook());
    $("xlsxSheetSelect")?.addEventListener("change", (event) => {
      state.sheetIndex = Number(event.target.value || 0);
      renderWorkbook();
    });
    $("xlsxTargetSelect")?.addEventListener("change", () => {
      renderMapping();
      renderPreview();
    });
    $("xlsxImportBtn")?.addEventListener("click", () => void importRows());
    activateBackupTab(root.querySelector("[data-backup-tab].active")?.getAttribute("data-backup-tab") || "backup");
    renderWorkbook();
  }

  window.rerenderXlsxImportTranslations = function rerenderXlsxImportTranslations() {
    const fileName = $("xlsxFileName");
    if (fileName && !state.workbook) fileName.textContent = i18n("xlsx_no_file", "Aucun fichier");
    renderWorkbook();
  };

  initXlsxImport();
})();
