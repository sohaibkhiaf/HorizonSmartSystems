const customerTraceSortState = { sortKey: "created_at", sortDir: "desc" };
const customerTraceViewState = { showAllCustomers: false, dueSingle: 0, dueMap: new Map() };
const supplierTraceSortState = { sortKey: "created_at", sortDir: "desc" };
const supplierTraceViewState = { showAllSuppliers: false, dueSingle: 0, dueMap: new Map() };
let customerTraceReqSeq = 0;
let supplierTraceReqSeq = 0;
const TRACE_DETAILS_BATCH = 24;
const customerReturnCart = [];
const supplierReturnCart = [];

async function preloadDetailsInCache(ids, cacheMap, loaderFn, isStale) {
  const pending = Array.isArray(ids) ? ids.filter((id) => id && !cacheMap.has(id)) : [];
  if (!pending.length) return;
  for (let i = 0; i < pending.length; i += TRACE_DETAILS_BATCH) {
    if (isStale && isStale()) return;
    const chunk = pending.slice(i, i + TRACE_DETAILS_BATCH);
    const results = await Promise.all(chunk.map(async (id) => {
      try {
        const out = await loaderFn(id);
        if (out && out.ok) return { id, data: out.data };
      } catch (_e) {}
      return null;
    }));
    if (isStale && isStale()) return;
    results.forEach((r) => {
      if (r && r.id && r.data) cacheMap.set(r.id, r.data);
    });
    // Yield to UI thread for responsiveness on large traces.
    await new Promise((resolve) => setTimeout(resolve, 0));
  }
}

function sortCustomerTraceRows(rows) {
  const list = Array.isArray(rows) ? rows.slice() : [];
  const key = String(customerTraceSortState.sortKey || "created_at");
  const dir = customerTraceSortState.sortDir === "asc" ? 1 : -1;
  list.sort((a, b) => {
    if (key === "created_at") return String(a.created_at || "").localeCompare(String(b.created_at || "")) * dir;
    if (key === "total_cents") return (Number(a.total_cents || 0) - Number(b.total_cents || 0)) * dir;
    if (key === "paid_cents") return (Number(a.paid_cents || 0) - Number(b.paid_cents || 0)) * dir;
    if (key === "debt_after_cents") return (Number(a.debt_after_cents || 0) - Number(b.debt_after_cents || 0)) * dir;
    if (key === "public_ref") {
      const ar = String(a.public_ref || salePublicRef(a.ref, a.created_at) || "");
      const br = String(b.public_ref || salePublicRef(b.ref, b.created_at) || "");
      return ar.localeCompare(br, undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "type_label") {
      const at = journalTypeLabel(a.type, "customer");
      const bt = journalTypeLabel(b.type, "customer");
      return String(at || "").localeCompare(String(bt || ""), undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "customer_name") {
      return String(a.customer_name || "").localeCompare(String(b.customer_name || ""), undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "note") {
      return String(a.note || "").localeCompare(String(b.note || ""), undefined, { sensitivity: "base" }) * dir;
    }
    return 0;
  });
  return list;
}

function customerTraceDeltaCents(entry) {
  const amount = Math.max(0, Number(entry && entry.amount_cents || 0));
  const paidAtCreate = Math.max(0, Number(entry && entry.paid_at_create_cents || 0));
  const type = String(entry && entry.type || "").toLowerCase();
  if (type === "sale") return Math.max(0, amount - paidAtCreate);
  if (type === "payment" && String(entry && entry.source_kind || "") === "finance_entry") {
    return String(entry && entry.finance_entry_type || "") === "expense" ? amount : -amount;
  }
  if (type === "payment" || type === "sale_return") return -amount;
  return 0;
}

function supplierTraceDeltaCents(entry) {
  const amount = Math.max(0, Number(entry && entry.amount_cents || 0));
  const paidAtCreate = Math.max(0, Number(entry && entry.paid_at_create_cents || 0));
  const type = String(entry && entry.type || "").toLowerCase();
  if (type === "purchase") return Math.max(0, amount - paidAtCreate);
  if (type === "payment" && String(entry && entry.source_kind || "") === "finance_entry") {
    return String(entry && entry.finance_entry_type || "") === "income" ? amount : -amount;
  }
  if (type === "payment" || type === "purchase_return") return -amount;
  return 0;
}

function partyBalanceValue(row) {
  return Number(row && row.balance_cents || 0);
}

function customerBalanceCents(customerId) {
  const cid = String(customerId || "");
  const row = (cache.customers || []).find((x) => String(x.id || "") === cid);
  return partyBalanceValue(row);
}

function supplierBalanceCents(supplierId) {
  const sid = String(supplierId || "");
  const row = (cache.suppliers || []).find((x) => String(x.id || "") === sid);
  return partyBalanceValue(row);
}

function balanceChipHtml(balance) {
  const value = Number(balance || 0);
  const cls = value > 0 ? "ok" : (value < 0 ? "bad" : "neutral");
  const sign = value > 0 ? "+" : "";
  return `<span class="debt-amt ${cls}">${sign}${money(value)}</span>`;
}

function financeTracePartyInfo(scope, partyKey) {
  const key = String(partyKey || "").trim();
  const [kind, id] = key.split(":");
  const isCustomer = scope === "customer" && kind === "customer";
  const isSupplier = scope === "supplier" && kind === "supplier";
  if (!isCustomer && !isSupplier) return null;
  const source = isCustomer ? (cache.customers || []) : (cache.suppliers || []);
  const row = source.find((x) => String(x.id || "") === String(id || ""));
  return {
    id: String(id || ""),
    name: String((row && row.name) || "")
  };
}

function financeEntryToTraceEvent(row, scope) {
  const partyKey = String(row && row.party_key || "").trim();
  const party = financeTracePartyInfo(scope, partyKey);
  if (!party || !party.id) return null;
  const isCustomer = scope === "customer";
  return {
    created_at: String(row.created_at || ""),
    type: "payment",
    ref: "",
    public_ref: String(row.public_ref || row.ref || row.id || ""),
    movement_id: String(row.id || ""),
    source_kind: "finance_entry",
    finance_entry_type: String(row.entry_type || ""),
    amount_cents: Number(row.amount_cents || 0),
    invoice_total_cents: 0,
    paid_at_create_cents: 0,
    note: String(row.note || row.source_label || ""),
    trace_owner_id: party.id,
    customer_id: isCustomer ? party.id : "",
    customer_name: isCustomer ? String(row.party_name || party.name || "-") : "",
    supplier_id: isCustomer ? "" : party.id,
    supplier_name: isCustomer ? "" : String(row.party_name || party.name || "-")
  };
}

async function loadFinanceTraceEvents(scope, selectedId = "") {
  if (!window.wiliDb || typeof window.wiliDb.getFinanceReport !== "function") return [];
  const out = await window.wiliDb.getFinanceReport();
  const rows = out && out.ok && out.data && Array.isArray(out.data.rows) ? out.data.rows : [];
  const prefix = scope === "customer" ? "customer:" : "supplier:";
  const selectedKey = selectedId ? `${prefix}${selectedId}` : "";
  return rows
    .filter((row) => {
      const sourceType = String(row && row.source_type || row && row.ref_type || "manual");
      return sourceType === "manual";
    })
    .filter((row) => String(row && row.party_key || "").startsWith(prefix))
    .filter((row) => !selectedKey || String(row && row.party_key || "") === selectedKey)
    .map((row) => financeEntryToTraceEvent(row, scope))
    .filter(Boolean);
}

function attachTraceDebtRunning(rows, idKey, deltaFn) {
  const ordered = (Array.isArray(rows) ? rows.slice() : [])
    .sort((a, b) => String(a.created_at || "").localeCompare(String(b.created_at || "")));
  const dueByInvoice = new Map();
  const dueByOwner = new Map();
  for (const e of ordered) {
    const type = String(e && e.type || "").toLowerCase();
    const amount = Math.max(0, Number(e && e.amount_cents || 0));
    const invoiceTotal = Math.max(0, Number(e && e.invoice_total_cents || 0));
    const paidAtCreate = Math.max(0, Number(e && e.paid_at_create_cents || 0));
    const explicitDueAfter = Number(e && e.due_after_cents);
    const explicitDueBefore = Number(e && e.due_before_cents);
    const refId = String(e && e.ref || "").trim();
    const ownerId = String(e && e[idKey] || "");
    e.total_cents = (type === "sale" || type === "purchase") ? amount : invoiceTotal;
    // For invoice rows: show what was paid at creation.
    // For movement rows (payment/return): show current movement amount.
    if (type === "sale" || type === "purchase") {
      e.paid_cents = paidAtCreate;
      const initialDue = Math.max(0, amount - paidAtCreate);
      const dueNow = Number.isFinite(explicitDueAfter) ? Math.max(0, explicitDueAfter) : initialDue;
      e.debt_after_cents = dueNow;
      if (refId) dueByInvoice.set(refId, dueNow);
      if (ownerId) dueByOwner.set(ownerId, Math.max(0, Number(dueByOwner.get(ownerId) || 0) + initialDue));
    } else {
      e.paid_cents = amount;
      if (Number.isFinite(explicitDueAfter)) {
        e.debt_after_cents = Math.max(0, explicitDueAfter);
        if (refId) dueByInvoice.set(refId, e.debt_after_cents);
      } else if (refId) {
        const prevDue = dueByInvoice.has(refId)
          ? Math.max(0, Number(dueByInvoice.get(refId) || 0))
          : (Number.isFinite(explicitDueBefore) ? Math.max(0, explicitDueBefore) : Math.max(0, invoiceTotal));
        const nextDue = Math.max(0, prevDue + Number(deltaFn(e) || 0));
        e.debt_after_cents = nextDue;
        dueByInvoice.set(refId, nextDue);
      } else if (ownerId) {
        const prev = Math.max(0, Number(dueByOwner.get(ownerId) || 0));
        const next = Math.max(0, prev + Number(deltaFn(e) || 0));
        dueByOwner.set(ownerId, next);
        e.debt_after_cents = next;
      } else {
        e.debt_after_cents = Math.max(0, Number(e && e.debt_after_cents || 0));
      }
      if (ownerId && Number.isFinite(e.debt_after_cents)) {
        // Keep owner-level map coherent for legacy rows without ref on next iterations.
        dueByOwner.set(ownerId, Math.max(0, Number(e.debt_after_cents || 0)));
      }
      continue;
    }
  }
  return ordered;
}


function tracePdfInfo(entry, scope) {
  const type = String(entry && entry.type || "").toLowerCase();
  const invoiceId = String(entry && entry.ref || "").trim();
  const movementId = String(entry && (entry.movement_id || entry.return_id || entry.source_id) || "").trim();
  const rowId = String(entry && entry.id || "").replace(/^(sale_return|purchase_return):/i, "").trim();
  const returnId = movementId || rowId || String(entry && entry.public_ref || "").trim();
  if (scope === "customer") {
    if (type === "payment" && String(entry && entry.source_kind || "") === "finance_entry" && movementId) {
      return { kind: "finance_entry", id: movementId, label: langText("Reglement", "تسديد", "خلاص") };
    }
    if ((type === "sale_return" || type === "customer_return") && returnId) {
      return { kind: "sale_return", id: returnId, label: langText("Bon de Retour client", "وصل إرجاع زبون", "بون روتور كليان") };
    }
    if (type === "sale" && invoiceId) {
      return { kind: "sale_invoice", id: invoiceId, label: langText("Telecharger vente", "تحميل البيع", "تحميل لبيع") };
    }
    if (type === "payment" && movementId) {
      return { kind: "sale_payment", id: movementId, label: langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص") };
    }
  }
  if (scope === "supplier") {
    if (type === "payment" && String(entry && entry.source_kind || "") === "finance_entry" && movementId) {
      return { kind: "finance_entry", id: movementId, label: langText("Reglement", "تسديد", "خلاص") };
    }
    if ((type === "purchase_return" || type === "supplier_return") && returnId) {
      return { kind: "purchase_return", id: returnId, label: langText("Bon de Retour fournisseur", "وصل إرجاع مورد", "بون روتور فرنسور") };
    }
    if (type === "purchase" && invoiceId) {
      return { kind: "purchase_invoice", id: invoiceId, label: langText("Telecharger achat", "تحميل الشراء", "تحميل لشرا") };
    }
    if (type === "payment" && movementId) {
      return { kind: "purchase_payment", id: movementId, label: langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص") };
    }
  }
  return null;
}

function tracePdfButton(entry, scope, index) {
  const info = tracePdfInfo(entry, scope);
  if (!info) return "-";
  const attr = scope === "supplier" ? "data-supplier-trace-pdf" : "data-customer-trace-pdf";
  const editAllowed = isAdminUser();
  const editLabel = langText("Modifier", "تعديل", "بدل");
  const entryType = String(entry && entry.type || "").toLowerCase();
  if (entryType === "sale_return" || entryType === "customer_return") {
    const bonRetourLabel = langText("Bon de Retour client", "وصل إرجاع زبون", "بون روتور كليان");
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonRetourLabel)}</button></div>`;
  }
  if (entryType === "purchase_return" || entryType === "supplier_return") {
    const bonRetourLabel = langText("Bon de Retour fournisseur", "وصل إرجاع مورد", "بون روتور فرنسور");
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonRetourLabel)}</button></div>`;
  }
  if (info.kind === "sale_return") {
    const bonRetourLabel = langText("Bon de Retour client", "وصل إرجاع زبون", "بون روتور كليان");
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonRetourLabel)}</button></div>`;
  }
  if (info.kind === "purchase_return") {
    const bonRetourLabel = langText("Bon de Retour fournisseur", "وصل إرجاع مورد", "بون روتور فرنسور");
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonRetourLabel)}</button></div>`;
  }
  if (info.kind === "sale_invoice") {
    const bonLabel = String(entry && entry.type || "").toLowerCase() === "sale_return" ? langText("Retour Client", "\u0625\u0631\u062c\u0627\u0639 \u0632\u0628\u0648\u0646", "\u0631\u0648\u062a\u0648\u0631 \u0643\u0644\u064a\u0627\u0646") : langText("Bon de Livraison", "\u0648\u0635\u0644 \u062a\u0633\u0644\u064a\u0645", "\u0628\u0648\u0646 \u0644\u064a\u0641\u0631\u064a\u0632\u0648\u0646");
    const editBtn = editAllowed && String(entry && entry.type || "").toLowerCase() === "sale"
      ? `<button type="button" class="btn-mini" data-customer-trace-edit-sale="${esc(info.id)}">${esc(editLabel)}</button>`
      : "";
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonLabel)}</button><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="facture">${esc(langText("Facture De Vente", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0628\u064a\u0639", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0628\u064a\u0639"))}</button>${editBtn}</div>`;
  }
  if (info.kind === "purchase_invoice") {
    const bonLabel = langText("Bon de Reception", "\u0648\u0635\u0644 \u0627\u0633\u062a\u0644\u0627\u0645", "\u0628\u0648\u0646 \u0631\u064a\u0633\u0628\u0633\u064a\u0648\u0646");
    const editBtn = editAllowed && entryType === "purchase"
      ? `<button type="button" class="btn-mini" data-supplier-trace-edit-purchase="${esc(info.id)}">${esc(editLabel)}</button>`
      : "";
    return `<div class="qty-actions"><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="bon">${esc(bonLabel)}</button><button type="button" class="btn-mini btn-pdf" ${attr}="${index}" data-trace-pdf-mode="facture">${esc(langText("Facture D'Achat", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621"))}</button>${editBtn}</div>`;
  }
  return `<button type="button" class="btn-mini btn-pdf" ${attr}="${index}">${langText("Reglement", "تسديد", "خلاص")}</button>`;
}

function traceSettlementButton(entry, scope, index) {
  const type = String(entry && entry.type || "").toLowerCase();
  if (type === "sale_return" || type === "customer_return" || type === "purchase_return" || type === "supplier_return") return "";
  const due = Math.max(0, Number(entry && entry.debt_after_cents || 0));
  const amount = Math.max(0, Number(entry && entry.amount_cents || 0));
  const refunded = Math.max(0, Number(entry && entry.refund_cents || 0));
  const refundDue = Math.max(0, amount - refunded);
  if (scope === "customer") {
    if (type === "sale" && due > 0) return `<button type="button" class="btn-mini" data-customer-trace-settle="${index}" data-settle-kind="sale">${esc(langText("Regler", "تسديد", "ريغلي"))}</button>`;
  }
  if (scope === "supplier") {
    if (type === "purchase" && due > 0) return `<button type="button" class="btn-mini" data-supplier-trace-settle="${index}" data-settle-kind="purchase">${esc(langText("Regler", "تسديد", "ريغلي"))}</button>`;
  }
  return "";
}

function promptSettlementAmount(defaultCents, title) {
  const raw = window.prompt(String(title || langText("Montant", "المبلغ", "المبلغ")), money(defaultCents || 0));
  if (raw == null) return null;
  return Math.max(0, toCents(raw));
}

async function refreshAfterTraceSettlement(scope) {
  await refreshAll();
  if (scope === "supplier") await refreshSupplierTrace();
  else await refreshCustomerTrace();
}

async function settleCustomerTraceRow(row) {
  const type = String(row && row.type || "").toLowerCase();
  if (type === "sale") {
    const due = Math.max(0, Number(row && row.debt_after_cents || 0));
    const amount = promptSettlementAmount(due, langText("Montant du reglement", "مبلغ التسديد", "مونطو ريغلو"));
    if (amount == null) return;
    if (!amount || amount > due) return appNotify(langText("Montant invalide", "مبلغ غير صالح", "مونطو ماشي صحيح"), true, 3500);
    const out = await window.wiliDb.addSalePayment({ sale_id: String(row.ref || ""), amount_cents: amount, note: "reglement depuis journal client" });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "sale_payment_failed", true, 5000);
    appNotify(langText("Reglement ajoute", "تمت إضافة التسديد", "تزادت الريغلة"), false, 2500);
    await refreshAfterTraceSettlement("customer");
    return;
  }
  if (type === "sale_return") {
    const refundDue = Math.max(0, Number(row && row.amount_cents || 0) - Number(row && row.refund_cents || 0));
    const amount = promptSettlementAmount(refundDue, langText("Montant du remboursement", "مبلغ التعويض", "مونطو رمبورسمو"));
    if (amount == null) return;
    if (!amount || amount > refundDue) return appNotify(langText("Montant invalide", "مبلغ غير صالح", "مونطو ماشي صحيح"), true, 3500);
    const out = await window.wiliDb.settleSaleReturn({ return_id: String(row.movement_id || ""), amount_cents: amount, note: "remboursement depuis journal client" });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "sale_return_settle_failed", true, 5000);
    appNotify(langText("Remboursement ajoute", "تمت إضافة التعويض", "تزاد الرمبورسمو"), false, 2500);
    await refreshAfterTraceSettlement("customer");
  }
}

async function settleSupplierTraceRow(row) {
  const type = String(row && row.type || "").toLowerCase();
  if (type === "purchase") {
    const due = Math.max(0, Number(row && row.debt_after_cents || 0));
    const amount = promptSettlementAmount(due, langText("Montant du reglement", "مبلغ التسديد", "مونطو ريغلو"));
    if (amount == null) return;
    if (!amount || amount > due) return appNotify(langText("Montant invalide", "مبلغ غير صالح", "مونطو ماشي صحيح"), true, 3500);
    const out = await window.wiliDb.addPurchasePayment({ purchase_id: String(row.ref || ""), amount_cents: amount, note: "reglement depuis journal fournisseur" });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "purchase_payment_failed", true, 5000);
    appNotify(langText("Reglement ajoute", "تمت إضافة التسديد", "تزادت الريغلة"), false, 2500);
    await refreshAfterTraceSettlement("supplier");
    return;
  }
  if (type === "purchase_return") {
    const refundDue = Math.max(0, Number(row && row.amount_cents || 0) - Number(row && row.refund_cents || 0));
    const amount = promptSettlementAmount(refundDue, langText("Montant du remboursement", "مبلغ الاسترجاع", "مونطو رمبورسمو"));
    if (amount == null) return;
    if (!amount || amount > refundDue) return appNotify(langText("Montant invalide", "مبلغ غير صالح", "مونطو ماشي صحيح"), true, 3500);
    const out = await window.wiliDb.settlePurchaseReturn({ return_id: String(row.movement_id || ""), amount_cents: amount, note: "remboursement depuis journal fournisseur" });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "purchase_return_settle_failed", true, 5000);
    appNotify(langText("Remboursement ajoute", "تمت إضافة الاسترجاع", "تزاد الرمبورسمو"), false, 2500);
    await refreshAfterTraceSettlement("supplier");
  }
}

async function exportTraceRowPdf(entry, scope, targetEl, documentMode = "bon") {
  const info = tracePdfInfo(entry, scope);
  if (!info) {
    appNotify(langText("Telechargement indisponible pour cette ligne", "لا يوجد تحميل لهذا السطر", "ماكانش تحميل لهاد السطر"), true, 2500);
    return;
  }
  const entryType = String(entry && entry.type || "").toLowerCase();
  if (entryType === "sale_return" || entryType === "customer_return" || entryType === "purchase_return" || entryType === "supplier_return") {
    const isSupplierReturn = entryType === "purchase_return" || entryType === "supplier_return";
    const returnId = String(entry && (entry.movement_id || entry.return_id || entry.source_id || entry.id || entry.public_ref) || "")
      .replace(/^(sale_return|purchase_return):/i, "")
      .trim();
    if (!returnId) {
      appNotify(langText("Telechargement indisponible pour cette ligne", "لا يوجد تحميل لهذا السطر", "ماكانش تحميل لهاد السطر"), true, 2500);
      return;
    }
    const label = isSupplierReturn
      ? langText("Bon de Retour fournisseur", "\u0648\u0635\u0644 \u0625\u0631\u062c\u0627\u0639 \u0645\u0648\u0631\u062f", "\u0628\u0648\u0646 \u0631\u0648\u062a\u0648\u0631 \u0641\u0631\u0646\u0633\u0648\u0631")
      : langText("Bon de Retour client", "\u0648\u0635\u0644 \u0625\u0631\u062c\u0627\u0639 \u0632\u0628\u0648\u0646", "\u0628\u0648\u0646 \u0631\u0648\u062a\u0648\u0631 \u0643\u0644\u064a\u0627\u0646");
    const out = isSupplierReturn
      ? await window.wiliDb.exportPurchaseReturnPdf(returnId)
      : await window.wiliDb.exportSaleReturnPdf(returnId);
    handlePdfResult(out, label, targetEl || null);
    return;
  }
  let out = null;
  if (info.kind === "sale_return") out = await window.wiliDb.exportSaleReturnPdf(info.id);
  else if (info.kind === "purchase_return") out = await window.wiliDb.exportPurchaseReturnPdf(info.id);
  else if (info.kind === "sale_invoice") out = await window.wiliDb.exportSaleInvoicePdf(info.id, { document_mode: documentMode });
  else if (info.kind === "sale_payment") out = await window.wiliDb.exportSalePaymentReceiptPdf(info.id);
  else if (info.kind === "purchase_payment") out = await window.wiliDb.exportPurchasePaymentReceiptPdf(info.id);
  else if (info.kind === "finance_entry") out = await window.wiliDb.exportFinanceEntryPdf(entry);
  else if (info.kind === "purchase_invoice") {
    await exportPurchaseInvoicePdfById(info.id, targetEl || null, documentMode);
    return;
  }
  handlePdfResult(out, info.label || langText("Reglement", "تسديد", "خلاص"), targetEl || null);
}

function renderCustomerTraceTable() {
  const filtered = sortCustomerTraceRows(customerTraceRows);
  const showAllCustomers = Boolean(customerTraceViewState.showAllCustomers);
  const actionHead = `<th>${langText("Actions", "الإجراءات", "الإجراءات")}</th>`;
  const headCols = showAllCustomers
    ? `<tr><th data-sort="created_at">${langText("Date", "التاريخ", "التاريخ")}</th><th data-sort="customer_name">${langText("Client", "الزبون", "الكليان")}</th><th data-sort="type_label">${langText("Type", "النوع", "النوع")}</th><th data-sort="public_ref">${langText("Ref", "المرجع", "ريف")}</th><th data-sort="total_cents">${langText("Total", "الإجمالي", "الإجمالي")}</th><th data-sort="paid_cents">${langText("Paye", "المدفوع", "المدفوع")}</th><th data-sort="debt_after_cents">${langText("Reste dette", "باقي الدين", "باقي الدين")}</th><th data-sort="note">${langText("Note", "ملاحظة", "نوتة")}</th>${actionHead}</tr>`
    : `<tr><th data-sort="created_at">${langText("Date", "التاريخ", "التاريخ")}</th><th data-sort="type_label">${langText("Type", "النوع", "النوع")}</th><th data-sort="public_ref">${langText("Ref", "المرجع", "ريف")}</th><th data-sort="total_cents">${langText("Total", "الإجمالي", "الإجمالي")}</th><th data-sort="paid_cents">${langText("Paye", "المدفوع", "المدفوع")}</th><th data-sort="debt_after_cents">${langText("Reste dette", "باقي الدين", "باقي الدين")}</th><th data-sort="note">${langText("Note", "ملاحظة", "نوتة")}</th>${actionHead}</tr>`;
  const rowsHtml = filtered.length
    ? filtered.map((e, idx) => {
      const dueNow = Math.max(0, Number(e.debt_after_cents || 0));
      const actionCell = `<td>${tracePdfButton(e, "customer", idx)}${traceSettlementButton(e, "customer", idx)}</td>`;
      if (showAllCustomers) {
        return `<tr><td>${esc(fmtDateTime(e.created_at))}</td><td>${esc(e.customer_name || "-")}</td><td>${esc(journalTypeLabel(e.type, "customer"))}</td><td>${esc(e.public_ref || salePublicRef(e.ref, e.created_at))}</td><td>${money(e.total_cents || 0)}</td><td>${money(e.paid_cents || 0)}</td><td>${money(dueNow)}</td><td>${esc(e.note || "-")}</td>${actionCell}</tr>`;
      }
      return `<tr><td>${esc(fmtDateTime(e.created_at))}</td><td>${esc(journalTypeLabel(e.type, "customer"))}</td><td>${esc(e.public_ref || salePublicRef(e.ref, e.created_at))}</td><td>${money(e.total_cents || 0)}</td><td>${money(e.paid_cents || 0)}</td><td>${money(dueNow)}</td><td>${esc(e.note || "-")}</td>${actionCell}</tr>`;
    }).join("")
    : `<tr><td colspan="${showAllCustomers ? 9 : 8}" class="empty">${t("no_data")}</td></tr>`;
  ui.customerTraceBox.innerHTML = `<table class="table-lite data-grid"><thead>${headCols}</thead><tbody>${rowsHtml}</tbody></table>`;
  const table = ui.customerTraceBox.querySelector("table");
  if (!table) return;
  updateSortHeaderIndicators(table, customerTraceSortState.sortKey || "created_at", customerTraceSortState.sortDir || "desc");
  table.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "created_at");
      if (customerTraceSortState.sortKey === key) {
        customerTraceSortState.sortDir = customerTraceSortState.sortDir === "asc" ? "desc" : "asc";
      } else {
        customerTraceSortState.sortKey = key;
        customerTraceSortState.sortDir = key === "created_at" ? "desc" : "asc";
      }
      renderCustomerTraceTable();
    });
  });
  table.querySelectorAll("[data-customer-trace-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const idx = Number(btn.getAttribute("data-customer-trace-pdf") || -1);
      const row = filtered[idx];
      if (!row) return;
      await exportTraceRowPdf(row, "customer", ui.customerTraceBox, btn.getAttribute("data-trace-pdf-mode") || "bon");
    });
  });
  table.querySelectorAll("[data-customer-trace-edit-sale]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const saleId = String(btn.getAttribute("data-customer-trace-edit-sale") || "").trim();
      if (!saleId) return appNotify("sale_id_missing", true, 4000);
      if (typeof window.startSaleEdit !== "function") return appNotify("sale_edit_unavailable", true, 4000);
      try {
        await window.startSaleEdit(saleId);
      } catch (err) {
        appNotify(err && err.message ? err.message : "sale_edit_failed", true, 5000);
      }
    });
  });
  table.querySelectorAll("[data-customer-trace-settle]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const idx = Number(btn.getAttribute("data-customer-trace-settle") || -1);
      const row = filtered[idx];
      if (!row) return;
      try {
        await settleCustomerTraceRow(row);
      } catch (err) {
        appNotify(err && err.message ? err.message : "trace_settlement_failed", true, 5000);
      }
    });
  });
}

function sortSupplierTraceRows(rows) {
  const list = Array.isArray(rows) ? rows.slice() : [];
  const key = String(supplierTraceSortState.sortKey || "created_at");
  const dir = supplierTraceSortState.sortDir === "asc" ? 1 : -1;
  list.sort((a, b) => {
    if (key === "created_at") return String(a.created_at || "").localeCompare(String(b.created_at || "")) * dir;
    if (key === "total_cents") return (Number(a.total_cents || 0) - Number(b.total_cents || 0)) * dir;
    if (key === "paid_cents") return (Number(a.paid_cents || 0) - Number(b.paid_cents || 0)) * dir;
    if (key === "debt_after_cents") return (Number(a.debt_after_cents || 0) - Number(b.debt_after_cents || 0)) * dir;
    if (key === "public_ref") {
      const ar = String(a.public_ref || purchasePublicRef(a.ref, a.created_at) || "");
      const br = String(b.public_ref || purchasePublicRef(b.ref, b.created_at) || "");
      return ar.localeCompare(br, undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "type_label") {
      const at = journalTypeLabel(a.type, "supplier");
      const bt = journalTypeLabel(b.type, "supplier");
      return String(at || "").localeCompare(String(bt || ""), undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "supplier_name") {
      return String(a.supplier_name || "").localeCompare(String(b.supplier_name || ""), undefined, { sensitivity: "base" }) * dir;
    }
    if (key === "note") {
      return String(a.note || "").localeCompare(String(b.note || ""), undefined, { sensitivity: "base" }) * dir;
    }
    return 0;
  });
  return list;
}

function renderSupplierTraceTable() {
  const filtered = sortSupplierTraceRows(supplierTraceRows);
  const showAllSuppliers = Boolean(supplierTraceViewState.showAllSuppliers);
  const actionHead = `<th>${langText("Actions", "الإجراءات", "الإجراءات")}</th>`;
  const headCols = showAllSuppliers
    ? `<tr><th data-sort="created_at">${langText("Date", "التاريخ", "التاريخ")}</th><th data-sort="supplier_name">${langText("Fournisseur", "المورد", "الفرنسور")}</th><th data-sort="type_label">${langText("Type", "النوع", "النوع")}</th><th data-sort="public_ref">${langText("Ref", "المرجع", "ريف")}</th><th data-sort="total_cents">${langText("Total", "الإجمالي", "الإجمالي")}</th><th data-sort="paid_cents">${langText("Paye", "المدفوع", "المدفوع")}</th><th data-sort="debt_after_cents">${langText("Reste dette", "باقي الدين", "باقي الدين")}</th><th data-sort="note">${langText("Note", "ملاحظة", "نوتة")}</th>${actionHead}</tr>`
    : `<tr><th data-sort="created_at">${langText("Date", "التاريخ", "التاريخ")}</th><th data-sort="type_label">${langText("Type", "النوع", "النوع")}</th><th data-sort="public_ref">${langText("Ref", "المرجع", "ريف")}</th><th data-sort="total_cents">${langText("Total", "الإجمالي", "الإجمالي")}</th><th data-sort="paid_cents">${langText("Paye", "المدفوع", "المدفوع")}</th><th data-sort="debt_after_cents">${langText("Reste dette", "باقي الدين", "باقي الدين")}</th><th data-sort="note">${langText("Note", "ملاحظة", "نوتة")}</th>${actionHead}</tr>`;
  const rowsHtml = filtered.length
    ? filtered.map((e, idx) => {
      const dueNow = Math.max(0, Number(e.debt_after_cents || 0));
      const actionCell = `<td>${tracePdfButton(e, "supplier", idx)}${traceSettlementButton(e, "supplier", idx)}</td>`;
      if (showAllSuppliers) {
        return `<tr><td>${esc(fmtDateTime(e.created_at))}</td><td>${esc(e.supplier_name || "-")}</td><td>${esc(journalTypeLabel(e.type, "supplier"))}</td><td>${esc(e.public_ref || purchasePublicRef(e.ref, e.created_at))}</td><td>${money(e.total_cents || 0)}</td><td>${money(e.paid_cents || 0)}</td><td>${money(dueNow)}</td><td>${esc(e.note || "-")}</td>${actionCell}</tr>`;
      }
      return `<tr><td>${esc(fmtDateTime(e.created_at))}</td><td>${esc(journalTypeLabel(e.type, "supplier"))}</td><td>${esc(e.public_ref || purchasePublicRef(e.ref, e.created_at))}</td><td>${money(e.total_cents || 0)}</td><td>${money(e.paid_cents || 0)}</td><td>${money(dueNow)}</td><td>${esc(e.note || "-")}</td>${actionCell}</tr>`;
    }).join("")
    : `<tr><td colspan="${showAllSuppliers ? 9 : 8}" class="empty">${t("no_data")}</td></tr>`;
  ui.supplierTraceBox.innerHTML = `<table class="table-lite data-grid"><thead>${headCols}</thead><tbody>${rowsHtml}</tbody></table>`;
  const table = ui.supplierTraceBox.querySelector("table");
  if (!table) return;
  updateSortHeaderIndicators(table, supplierTraceSortState.sortKey || "created_at", supplierTraceSortState.sortDir || "desc");
  table.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "created_at");
      if (supplierTraceSortState.sortKey === key) {
        supplierTraceSortState.sortDir = supplierTraceSortState.sortDir === "asc" ? "desc" : "asc";
      } else {
        supplierTraceSortState.sortKey = key;
        supplierTraceSortState.sortDir = key === "created_at" ? "desc" : "asc";
      }
      renderSupplierTraceTable();
    });
  });
  table.querySelectorAll("[data-supplier-trace-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const idx = Number(btn.getAttribute("data-supplier-trace-pdf") || -1);
      const row = filtered[idx];
      if (!row) return;
      await exportTraceRowPdf(row, "supplier", ui.supplierTraceBox, btn.getAttribute("data-trace-pdf-mode") || "bon");
    });
  });
  table.querySelectorAll("[data-supplier-trace-edit-purchase]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const purchaseId = String(btn.getAttribute("data-supplier-trace-edit-purchase") || "").trim();
      if (!purchaseId) return appNotify("purchase_id_missing", true, 4000);
      if (typeof window.startPurchaseEdit !== "function") return appNotify("purchase_edit_unavailable", true, 4000);
      try {
        await window.startPurchaseEdit(purchaseId);
      } catch (err) {
        appNotify(err && err.message ? err.message : "purchase_edit_failed", true, 5000);
      }
    });
  });
  table.querySelectorAll("[data-supplier-trace-settle]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const idx = Number(btn.getAttribute("data-supplier-trace-settle") || -1);
      const row = filtered[idx];
      if (!row) return;
      try {
        await settleSupplierTraceRow(row);
      } catch (err) {
        appNotify(err && err.message ? err.message : "trace_settlement_failed", true, 5000);
      }
    });
  });
}

let stockLossRows = [];
let stockLossPageBound = false;
let stockLossSortKey = "created_at";
let stockLossSortDir = "desc";

function stockLossReasonLabels() {
  return {
    damaged: langText("Casse", "مكسور", "تكسر"),
    expired: langText("Perime", "منتهي الصلاحية", "فاسد"),
    lost: langText("Perdu", "مفقود", "ضاع"),
    other: langText("Autre", "آخر", "آخر")
  };
}

function stockLossReasonKey(loss) {
  const note = String(loss && loss.note ? loss.note : "").trim().toLowerCase();
  if (["expired", "perime", "périmé"].some((x) => note.startsWith(x))) return "expired";
  if (["lost", "perdu"].some((x) => note.startsWith(x))) return "lost";
  if (["other", "autre"].some((x) => note.startsWith(x))) return "other";
  if (["damaged", "casse", "cassé"].some((x) => note.startsWith(x))) return "damaged";
  return "damaged";
}

function stockLossReasonText(loss) {
  const labels = stockLossReasonLabels();
  const key = stockLossReasonKey(loss);
  const note = String(loss && loss.note ? loss.note : "").trim();
  const cleaned = note.replace(/^(expired|perime|périmé|lost|perdu|other|autre|damaged|casse|cassé)\s*-\s*/i, "").trim();
  return cleaned ? `${labels[key]} - ${cleaned}` : labels[key];
}

function localizeStockLossView() {
  if (!ui.stockLossForm) return;
  const set = (id, text) => { const el = byId(id); if (el) el.textContent = text; };
  set("lossTitle", langText("Pertes stock", "خسائر المخزون", "خساير الستوك"));
  set("lossSubtitle", langText("Declarer les pertes, filtrer l'historique et suivre le cout reel", "سجل الخسائر، صف السجل، وتابع التكلفة الحقيقية", "سجل الخساير، فلتر ليستوريك، وشوف الخسارة الحقيقية"));
  document.querySelector('[data-loss-tab="entry"]') && (document.querySelector('[data-loss-tab="entry"]').textContent = langText("Pertes", "تسجيل خسارة", "خسارة"));
  document.querySelector('[data-loss-tab="archive"]') && (document.querySelector('[data-loss-tab="archive"]').textContent = langText("Archive pertes", "أرشيف الخسائر", "أرشيف الخساير"));
  set("lossEntryTitle", langText("Declarer une perte", "تسجيل خسارة", "سجل خسارة"));
  set("lossEntrySubtitle", langText("Produit casse, perdu ou perime avec mise a jour automatique du stock.", "منتج مكسور أو مفقود أو منتهي مع تحديث المخزون تلقائيا.", "منتوج تكسر ولا ضاع ولا فسد والستوك يتبدل وحدو."));
  set("lossArchiveTitle", langText("Archive pertes", "أرشيف الخسائر", "أرشيف الخساير"));
  set("lossArchiveSubtitle", langText("Filtrer l'historique et suivre la quantite et le cout reel perdu.", "تصفية السجل ومتابعة الكمية والتكلفة الحقيقية المفقودة.", "فلتر ليستوريك وشوف الكمية والتكلفة لي ضاعت."));
  set("lossProductLabel", langText("Produit", "المنتج", "المنتوج"));
  set("lossQtyLabel", langText("Quantite", "الكمية", "الكمية"));
  set("lossReasonLabel", langText("Motif", "السبب", "السبب"));
  set("lossNoteLabel", langText("Note", "ملاحظة", "ملاحظة"));
  set("lossSubmitBtn", langText("Enregistrer la perte", "تسجيل الخسارة", "سجل الخسارة"));
  set("lossCountLabel", langText("Operations filtrees", "العمليات المصفاة", "العمليات المفلترين"));
  set("lossUnitsLabel", langText("Quantite perdue", "الكمية المفقودة", "الكمية الضايعة"));
  set("lossCostLabel", langText("Cout total perdu", "إجمالي التكلفة المفقودة", "مجموع الخسارة"));
  set("lossFilterSearchLabel", langText("Recherche", "بحث", "بحث"));
  set("lossFilterReasonLabel", langText("Motif", "السبب", "السبب"));
  set("lossFilterFromLabel", langText("Du", "من", "من"));
  set("lossFilterToLabel", langText("Au", "إلى", "حتى"));
  set("lossFilterClear", langText("Reinitialiser", "إعادة ضبط", "ريسيتي"));
  if (ui.lossQty) ui.lossQty.placeholder = langText("Qte", "الكمية", "الكمية");
  if (ui.lossNote) ui.lossNote.placeholder = langText("Note optionnelle", "ملاحظة اختيارية", "ملاحظة اختيارية");
  if (ui.lossFilterSearch) ui.lossFilterSearch.placeholder = langText("Produit / code barre / reference / note", "منتج / باركود / مرجع / ملاحظة", "منتوج / باركود / ريفيرانس / ملاحظة");
  const labels = stockLossReasonLabels();
  Array.from(ui.lossReason?.options || []).forEach((option) => {
    option.textContent = labels[String(option.value || "")] || option.textContent;
  });
  Array.from(ui.lossFilterReason?.options || []).forEach((option) => {
    option.textContent = option.value ? (labels[String(option.value || "")] || option.textContent) : langText("Tous motifs", "كل الأسباب", "كل الاسباب");
  });
  const headLabels = [
    langText("Date", "التاريخ", "التاريخ"),
    langText("Produit", "المنتج", "المنتوج"),
    langText("Qte", "الكمية", "الكمية"),
    langText("Cout unit", "تكلفة الوحدة", "سومة الوحدة"),
    langText("Perte", "الخسارة", "الخسارة"),
    langText("Motif / note", "السبب / الملاحظة", "السبب / الملاحظة")
  ];
  Array.from(ui.lossTable?.querySelectorAll("thead th") || []).forEach((head, index) => { head.textContent = headLabels[index] || head.textContent; });
}

function stockLossSortValue(loss, key) {
  if (["qty", "unit_cost_cents", "total_cost_cents", "amount_cents"].includes(key)) return Number(loss[key] || 0);
  if (key === "note") return stockLossReasonText(loss).toLowerCase();
  if (key === "created_at") return String(loss.created_at || "");
  return String(loss[key] || "").toLowerCase();
}

function filteredStockLossRows() {
  const q = String(ui.lossFilterSearch?.value || "").trim().toLowerCase();
  const reason = String(ui.lossFilterReason?.value || "").trim();
  const from = String(ui.lossFilterFrom?.value || "").trim();
  const to = String(ui.lossFilterTo?.value || "").trim();
  const rows = stockLossRows.filter((loss) => {
    const haystack = [
      loss.product_name, loss.product_barcode, loss.product_reference, loss.note, stockLossReasonText(loss)
    ].map((x) => String(x || "").toLowerCase()).join(" ");
    if (q && !haystack.includes(q)) return false;
    if (reason && stockLossReasonKey(loss) !== reason) return false;
    const day = String(loss.created_at || "").slice(0, 10);
    if (from && day && day < from) return false;
    if (to && day && day > to) return false;
    return true;
  });
  const dir = stockLossSortDir === "desc" ? -1 : 1;
  rows.sort((a, b) => {
    const av = stockLossSortValue(a, stockLossSortKey);
    const bv = stockLossSortValue(b, stockLossSortKey);
    if (typeof av === "number" || typeof bv === "number") return (Number(av || 0) - Number(bv || 0)) * dir;
    return String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" }) * dir;
  });
  return rows;
}

function renderStockLossTable() {
  if (!ui.lossTableBody) return;
  const rows = filteredStockLossRows();
  const totalUnits = rows.reduce((sum, row) => sum + Math.abs(Number(row.qty || row.qty_delta || 0)), 0);
  const totalCost = rows.reduce((sum, row) => sum + Number(row.total_cost_cents || row.amount_cents || 0), 0);
  ui.lossCount.textContent = String(rows.length);
  ui.lossUnits.textContent = qtyText(totalUnits);
  ui.lossTotalCost.textContent = money(totalCost);
  ui.lossTableBody.innerHTML = rows.length ? rows.map((loss) => `
    <tr>
      <td>${esc(fmtDateTime(loss.created_at || ""))}</td>
      <td><strong>${esc(loss.product_name || "-")}</strong><small>${esc([loss.product_barcode, loss.product_reference].filter(Boolean).join(" / "))}</small></td>
      <td class="neg">-${qtyText(Math.abs(Number(loss.qty || loss.qty_delta || 0)))}</td>
      <td>${money(loss.unit_cost_cents || 0)}</td>
      <td class="neg">${money(loss.total_cost_cents || loss.amount_cents || 0)}</td>
      <td>${esc(stockLossReasonText(loss))}</td>
    </tr>`).join("") : `<tr><td colspan="6" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.lossTable, stockLossSortKey, stockLossSortDir);
}

async function refreshStockLosses() {
  if (!ui.stockLossForm || !window.wiliDb?.listStockLosses) return;
  localizeStockLossView();
  const [lossRes, productRes] = await Promise.all([
    window.wiliDb.listStockLosses(500),
    window.wiliDb.listProducts(10000)
  ]);
  const losses = lossRes && lossRes.ok ? (lossRes.data || []) : [];
  const products = productRes && productRes.ok ? (productRes.data || []) : [];
  stockLossRows = Array.isArray(losses) ? losses : [];
  const currentProduct = String(ui.lossProduct?.value || "");
  if (ui.lossProduct) {
    ui.lossProduct.innerHTML = `<option value="">${esc(langText("Selectionner produit", "اختر المنتج", "اختار المنتوج"))}</option>` +
      (Array.isArray(products) ? products : []).map((product) => `<option value="${esc(product.id)}">${esc(product.name || "-")} | ${esc(product.barcode || product.reference || "-")} | ${langText("Stock", "المخزون", "الستوك")}: ${qtyText(product.stock_qty || 0)}</option>`).join("");
    if (products.some((product) => String(product.id || "") === currentProduct)) ui.lossProduct.value = currentProduct;
  }
  if (typeof window.syncLossProductSearchInput === "function") window.syncLossProductSearchInput();
  renderStockLossTable();
}

function bindStockLossPage() {
  if (stockLossPageBound || !ui.stockLossForm) return;
  stockLossPageBound = true;
  ui.stockLossForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const productId = String(ui.lossProduct?.value || "").trim();
    const qty = Math.abs(Number(ui.lossQty?.value || 0));
    if (!productId || !qty) {
      appNotify(langText("Selectionnez un produit et une quantite", "اختر منتجا وكمية", "اختار منتوج وكمية"), true, 3000);
      return;
    }
    const product = cache.products.find((p) => String(p.id || "") === productId) || {};
    const productName = String(product.name || ui.lossProduct?.selectedOptions?.[0]?.textContent || productId);
    const confirmed = await appConfirm(langText(
      `Confirmer l'enregistrement de ${qtyText(qty)} unite(s) comme perte stock pour ${productName} ?`,
      `تأكيد تسجيل ${qtyText(qty)} وحدة كخسارة مخزون للمنتج ${productName}؟`,
      `تأكد تسجيل ${qtyText(qty)} وحدة خسارة ستوك للمنتوج ${productName}؟`
    ));
    if (!confirmed) return;
    ui.lossSubmitBtn.disabled = true;
    try {
      const out = await window.wiliDb.recordStockLoss({
        product_id: productId,
        qty,
        reason: String(ui.lossReason?.value || "damaged"),
        note: String(ui.lossNote?.value || "").trim()
      });
      if (!out || !out.ok) throw new Error(out && out.error ? out.error : "stock_loss_failed");
      ui.lossQty.value = "1";
      ui.lossNote.value = "";
      appNotify(langText("Perte enregistree et stock mis a jour", "تم تسجيل الخسارة وتحديث المخزون", "الخسارة تسجلت والستوك تبدل"), false, 3200);
      await Promise.all([refreshProducts({ reloadCache: true }), refreshStockLosses(), refreshFinance(), refreshDashboard()]);
      renderAlerts();
    } catch (error) {
      const msg = String(error && error.message ? error.message : error || "stock_loss_failed");
      appNotify(msg, true, 4200);
    } finally {
      ui.lossSubmitBtn.disabled = false;
    }
  });
  [ui.lossFilterSearch, ui.lossFilterReason, ui.lossFilterFrom, ui.lossFilterTo].forEach((el) => {
    if (el) el.addEventListener("input", renderStockLossTable);
    if (el) el.addEventListener("change", renderStockLossTable);
  });
  if (ui.lossFilterClear) {
    ui.lossFilterClear.addEventListener("click", () => {
      if (ui.lossFilterSearch) ui.lossFilterSearch.value = "";
      if (ui.lossFilterReason) ui.lossFilterReason.value = "";
      if (ui.lossFilterFrom) ui.lossFilterFrom.value = "";
      if (ui.lossFilterTo) ui.lossFilterTo.value = "";
      renderStockLossTable();
    });
  }
  ui.lossTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "created_at");
      if (stockLossSortKey === key) stockLossSortDir = stockLossSortDir === "asc" ? "desc" : "asc";
      else {
        stockLossSortKey = key;
        stockLossSortDir = key === "created_at" ? "desc" : "asc";
      }
      renderStockLossTable();
    });
  });
}

bindStockLossPage();

function updateProductGrid() {
  const rows = productGridState.rows || [];
  const allowDelete = isAdminUser();
  const q = String(ui.productGridSearch?.value || "").trim().toLowerCase();
  const filtered = productGridState.searchMode ? rows.slice() : rows.filter((p) => {
    if (!q) return true;
    return String(p.name || "").toLowerCase().includes(q)
      || String(p.barcode || "").toLowerCase().includes(q)
      || String(p.reference || "").toLowerCase().includes(q);
  });
  const dir = productGridState.sortDir === "desc" ? -1 : 1;
  if (!productGridState.searchMode) {
    filtered.sort((a, b) => {
      const key = productGridState.sortKey || "name";
      const av = a[key];
      const bv = b[key];
      if (key.includes("cents") || key.includes("stock")) return (Number(av || 0) - Number(bv || 0)) * dir;
      return String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" }) * dir;
    });
  }
  productGridState.filtered = filtered;
  if (!filtered.length) productGridState.selectedIndex = -1;
  else productGridState.selectedIndex = Math.min(Math.max(productGridState.selectedIndex, 0), filtered.length - 1);

  ui.productsTableBody.innerHTML = filtered.length ? filtered.map((p, idx) => {
    const stock = Number(p.stock_qty || 0);
    return `<tr data-product-row="${esc(p.id)}" data-row-index="${idx}" class="${idx === productGridState.selectedIndex ? "row-selected" : ""}">
      <td><strong>${esc(p.name || "-")}</strong></td>
      <td>${esc(p.barcode || "-")}</td>
      <td>${esc(p.reference || "-")}</td>
      <td class="${stock <= 0 ? "stock-zero" : ""}">${stock}</td>
      <td>${money(p.cost_cents)}</td>
      <td>${money(p.wholesale_price_cents)}</td>
      <td>${money(p.retail_price_cents || p.price_cents)}</td>
      <td>${money(p.assisted_price_cents || p.retail_price_cents)}</td>
      <td>${esc(p.expiry_date || "-")}</td>
      <td><div class="qty-actions"><button class="btn btn-soft with-icon" data-p-edit="${esc(p.id)}">${iconSvg("edit")}<span>${esc(langText("Modifier", "\u062a\u0639\u062f\u064a\u0644", "\u0628\u062f\u0651\u0644"))}</span></button><button class="btn btn-soft with-icon" data-p-copy="${esc(p.id)}">${iconSvg("add")}<span>${esc(langText("Copier", "نسخ", "نسخ"))}</span></button><button class="btn btn-soft with-icon" data-p-barcode="${esc(p.id)}">${iconSvg("backup")}<span>${esc(langText("Code barre", "باركود", "باركود"))}</span></button>${allowDelete ? `<button class="btn btn-soft with-icon" data-p-del="${esc(p.id)}">${iconSvg("delete")}<span>${esc(langText("Supprimer", "\u062d\u0630\u0641", "\u062d\u0630\u0641"))}</span></button>` : ""}</div></td>
    </tr>`;
  }).join("") : `<tr><td colspan="10" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.productsTable, productGridState.sortKey || "name", productGridState.sortDir || "asc");
  if (ui.productGridMeta) {
    const totalProducts = Math.max(0, intQty(productGridState.totalRows || rows.length));
    const totalStock = Number.isFinite(Number(productGridState.totalStock))
      ? Number(productGridState.totalStock || 0)
      : rows.reduce((sum, p) => sum + Math.max(0, Number(p.stock_qty || 0)), 0);
    const shownSuffix = q
      ? langText(` | Affiches : ${filtered.length}`, ` | المعروض : ${filtered.length}`, ` | لي باينين : ${filtered.length}`)
      : "";
    ui.productGridMeta.textContent = langText(
      `produit : ${totalProducts} | Stock : ${qtyText(totalStock)}${shownSuffix}`,
      `المنتجات : ${totalProducts} | المخزون : ${qtyText(totalStock)}${shownSuffix}`,
      `المنتوجات : ${totalProducts} | الستوك : ${qtyText(totalStock)}${shownSuffix}`
    );
  }
  const pageSize = Math.max(1, intQty(productGridState.pageSize || 100));
  const totalProducts = Math.max(0, intQty(productGridState.totalRows || rows.length));
  const totalPages = productGridState.searchMode ? 1 : Math.max(1, Math.ceil(totalProducts / pageSize));
  productGridState.totalPages = totalPages;
  if (!productGridState.searchMode) {
    productGridState.page = Math.min(Math.max(1, intQty(productGridState.page || 1)), totalPages);
  } else {
    productGridState.page = 1;
  }
  if (ui.productsPageInfo) {
    ui.productsPageInfo.textContent = productGridState.searchMode
      ? langText(`Recherche : ${filtered.length}`, `نتائج البحث : ${filtered.length}`, `نتائج البحث : ${filtered.length}`)
      : `Page ${productGridState.page}/${totalPages}`;
  }
  if (ui.productsPagePrevBtn) ui.productsPagePrevBtn.disabled = productGridState.searchMode || productGridState.page <= 1;
  if (ui.productsPageNextBtn) ui.productsPageNextBtn.disabled = productGridState.searchMode || productGridState.page >= totalPages;

  ui.productsTableBody.querySelectorAll("[data-product-row]").forEach((tr) => {
    tr.addEventListener("click", () => {
      productGridState.selectedIndex = intQty(tr.getAttribute("data-row-index"));
      updateProductGrid();
    });
    tr.addEventListener("dblclick", () => {
      const pid = String(tr.getAttribute("data-product-row") || "");
      void openProductTrace(pid);
    });
  });
  ui.productsTableBody.querySelectorAll("[data-p-edit]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const p = rows.find((x) => String(x.id) === String(btn.getAttribute("data-p-edit") || ""));
      if (p) fillProductFormForEdit(p);
    });
  });
  ui.productsTableBody.querySelectorAll("[data-p-copy]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const p = rows.find((x) => String(x.id) === String(btn.getAttribute("data-p-copy") || ""));
      if (p) fillProductFormForCopy(p);
    });
  });
  ui.productsTableBody.querySelectorAll("[data-p-del]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-p-del") || "");
      if (!id) return;
      const ok = await appConfirm(t("delete_confirm"));
      if (!ok) return;
      const out = await window.wiliDb.deleteProduct(id);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true);
        return;
      }
      await refreshProducts();
      await refreshDashboard();
      appNotify(langText("Suppression terminee", "تم الحذف", "تم الحذف"), false);
    });
  });
  ui.productsTableBody.querySelectorAll("[data-p-barcode]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-p-barcode") || "");
      if (!id) return;
      const size = await appPickBarcodeSize();
      if (!size) return;
      const out = await window.wiliDb.exportProductBarcodePdf(id, { size });
      handlePdfResult(out, langText("Telecharger code barre", "تحميل بار كود", "تحميل بار كود"));
    });
  });
}

async function refreshProducts(options = {}) {
  const full = options && options.full === false ? false : true;
  const searchTerm = String(options && options.search != null ? options.search : (ui.productGridSearch?.value || "")).trim();
  const pageSize = Math.max(1, intQty(options && options.pageSize ? options.pageSize : productGridState.pageSize || 100));
  productGridState.pageSize = pageSize;
  const shouldRefreshAux = Boolean(options && options.reloadCache === true) || !Array.isArray(cache.products) || !cache.products.length;
  if (shouldRefreshAux) {
    const res = await window.wiliDb.listProducts(500);
    cache.products = res && res.ok ? (res.data || []) : [];
  }
  const summaryRes = await window.wiliDb.getProductsSummary();
  const fallbackStock = (Array.isArray(cache.products) ? cache.products : []).reduce((sum, p) => sum + Math.max(0, Number(p && p.stock_qty || 0)), 0);
  productGridState.totalRows = summaryRes && summaryRes.ok ? intQty(summaryRes.data && summaryRes.data.total_products) : intQty((cache.products || []).length);
  productGridState.totalStock = summaryRes && summaryRes.ok ? Number(summaryRes.data && summaryRes.data.total_stock_qty || 0) : fallbackStock;
  let gridRows = [];
  if (searchTerm) {
    productGridState.searchMode = true;
    productGridState.page = 1;
    const searchRes = await window.wiliDb.searchProducts(searchTerm, 200);
    gridRows = searchRes && searchRes.ok ? (searchRes.data || []) : [];
    gridRows.forEach((row) => {
      const idx = cache.products.findIndex((p) => String(p.id || "") === String(row.id || ""));
      if (idx >= 0) cache.products[idx] = { ...cache.products[idx], ...row };
      else cache.products.push(row);
    });
  } else {
    productGridState.searchMode = false;
    const totalPages = Math.max(1, Math.ceil(Math.max(0, Number(productGridState.totalRows || 0)) / pageSize));
    const requestedPage = Math.min(Math.max(1, intQty(options && options.page ? options.page : productGridState.page || 1)), totalPages);
    productGridState.page = requestedPage;
    const pageRes = await window.wiliDb.listProductsPage({
      limit: pageSize,
      offset: (requestedPage - 1) * pageSize,
      sortKey: productGridState.sortKey || "name",
      sortDir: productGridState.sortDir || "asc"
    });
    gridRows = pageRes && pageRes.ok ? (pageRes.data || []) : [];
  }
  productGridState.rows = gridRows.slice();
  const selectedHistoryProduct = String(ui.historyProduct?.value || ui.historyProduct?.dataset?.selectedProductId || "");
  if (shouldRefreshAux) {
    const historyOpts = [`<option value="">${langText("Tout", "الكل", "ڨاع")}</option>`]
      .concat(cache.products.map((p) => `<option value="${esc(p.id)}">${esc(p.name)}</option>`))
      .join("");
    const productOpts = cache.products.map((p) => `<option value="${esc(p.id)}">${esc(p.name)}</option>`).join("");
    if (ui.historyProduct) {
      ui.historyProduct.innerHTML = historyOpts || "<option value=''>-</option>";
      setHistoryProductValue(selectedHistoryProduct);
    }
    if (ui.purchaseProduct) {
      ui.purchaseProduct.innerHTML = productOpts || "<option value=''>-</option>";
    }
    if (ui.purchaseProductOptions) {
      ui.purchaseProductOptions.innerHTML = cache.products.map((p) => `<option value="${esc(`${p.name} | ${p.barcode || "-"} | ${p.reference || "-"}`)}"></option>`).join("");
    }
  }

  if (full) updateProductGrid();
  renderAlerts();
}
function historyMovementType(x) {
  const reason = String((x && (x.reason || x.ref_type)) || "").toLowerCase();
  if (reason.includes("stock_loss")) return "stock_loss";
  if (reason.includes("sale_return")) return "sale_return";
  if (reason.includes("purchase_return")) return "purchase_return";
  if (reason.includes("sale")) return "sale";
  if (reason.includes("purchase")) return "purchase";
  return "manual";
}

function journalTypeLabel(typeKey, scope = "customer") {
  const key = String(typeKey || "").trim().toLowerCase();
  if (scope === "supplier") {
    if (key === "purchase") return langText("Achat", "شراء", "شرا");
    if (key === "payment") return langText("Paiement", "دفع", "خلاص");
    if (key === "purchase_return") return langText("Retour fournisseur", "ارجاع مورد", "ريطور فورنيسور");
    return langText("Operation", "عملية", "عملية");
  }
  if (key === "sale") return langText("Vente", "بيع", "بيع");
  if (key === "payment") return langText("Paiement", "دفع", "خلاص");
  if (key === "sale_return") return langText("Retour client", "ارجاع زبون", "ريطور كليون");
  return langText("Operation", "عملية", "عملية");
}

function historyTableHeaders() {
  return [
    langText("Date", "\u0627\u0644\u062a\u0627\u0631\u064a\u062e", "\u0627\u0644\u062a\u0627\u0631\u064a\u062e"),
    langText("Type", "\u0627\u0644\u0646\u0648\u0639", "\u0627\u0644\u0646\u0648\u0639"),
    langText("Produit", "\u0627\u0644\u0645\u0646\u062a\u062c", "\u0627\u0644\u0645\u0646\u062a\u0648\u062c"),
    langText("Qte", "\u0627\u0644\u0643\u0645\u064a\u0629", "\u0627\u0644\u0643\u0645\u064a\u0629"),
    langText("Prix unit", "\u0633\u0639\u0631 \u0627\u0644\u0648\u062d\u062f\u0629", "\u0633\u0639\u0631 \u0627\u0644\u0648\u062d\u062f\u0629"),
    langText("Total", "\u0627\u0644\u0625\u062c\u0645\u0627\u0644\u064a", "\u0627\u0644\u0645\u062c\u0645\u0648\u0639"),
    langText("Ref", "\u0627\u0644\u0645\u0631\u062c\u0639", "\u0627\u0644\u0645\u0631\u062c\u0639"),
    langText("Utilisateur", "\u0627\u0644\u0645\u0633\u062a\u062e\u062f\u0645", "\u0644\u064a\u0648\u0632\u0631"),
    langText("Note", "\u0645\u0644\u0627\u062d\u0638\u0629", "\u0645\u0644\u0627\u062d\u0638\u0629")
  ];
}

function renderHistoryTableHeader() {
  const table = ui.historyTableBody ? ui.historyTableBody.closest("table") : null;
  const head = table ? table.querySelector("thead tr") : null;
  if (!head) return;
  head.innerHTML = historyTableHeaders().map((label) => "<th>" + esc(label) + "</th>").join("");
}

function setHistoryProductValue(productId, productName = "") {
  if (!ui.historyProduct) return "";
  const pid = String(productId || "").trim();
  if (!pid) {
    delete ui.historyProduct.dataset.selectedProductId;
    ui.historyProduct.value = "";
    if (typeof window.syncHistoryProductSearchInput === "function") window.syncHistoryProductSearchInput();
    return "";
  }
  ui.historyProduct.dataset.selectedProductId = pid;
  const exists = Array.from(ui.historyProduct.options || []).some((opt) => String(opt.value || "") === pid);
  if (!exists) {
    const opt = document.createElement("option");
    opt.value = pid;
    opt.textContent = productName || `${langText("Produit", "المنتج", "المنتوج")} #${pid}`;
    opt.dataset.synthetic = "1";
    ui.historyProduct.appendChild(opt);
  }
  ui.historyProduct.value = pid;
  if (typeof window.syncHistoryProductSearchInput === "function") window.syncHistoryProductSearchInput();
  return pid;
}

function parsePartyPageResponse(res, fallbackRows = []) {
  const data = res && res.ok ? res.data : res;
  if (Array.isArray(data)) return { rows: data, total: data.length };
  if (data && Array.isArray(data.rows)) {
    return {
      rows: data.rows,
      total: Math.max(0, intQty(data.total != null ? data.total : data.rows.length))
    };
  }
  return { rows: Array.isArray(fallbackRows) ? fallbackRows : [], total: Array.isArray(fallbackRows) ? fallbackRows.length : 0 };
}

function partySearchMatch(row, query) {
  if (!query) return true;
  return ["name", "phone", "nif", "nis", "rc", "ai", "activity", "commerce_name", "email", "address", "notes"]
    .some((field) => String(row && row[field] || "").toLowerCase().includes(query));
}

function sortPartyRows(rows, sortKey, sortDir) {
  const key = String(sortKey || "name");
  const dir = sortDir === "desc" ? -1 : 1;
  return (Array.isArray(rows) ? rows.slice() : []).sort((a, b) => {
    if (key === "balance_cents") return (partyBalanceValue(a) - partyBalanceValue(b)) * dir;
    const av = a && Object.prototype.hasOwnProperty.call(a, key) ? a[key] : "";
    const bv = b && Object.prototype.hasOwnProperty.call(b, key) ? b[key] : "";
    const cmp = String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" });
    if (cmp !== 0) return cmp * dir;
    return String(b && b.updated_at || "").localeCompare(String(a && a.updated_at || ""));
  });
}

function updatePartyPager(state, pageInfoEl, prevBtn, nextBtn, resultCount) {
  const pageSize = Math.max(1, intQty(state.pageSize || 100));
  const totalRows = Math.max(0, intQty(state.totalRows || resultCount || 0));
  const totalPages = state.searchMode ? 1 : Math.max(1, Math.ceil(totalRows / pageSize));
  state.totalPages = totalPages;
  state.page = state.searchMode ? 1 : Math.min(Math.max(1, intQty(state.page || 1)), totalPages);
  if (pageInfoEl) {
    pageInfoEl.textContent = state.searchMode
      ? langText(`Recherche : ${totalRows}`, `نتائج البحث : ${totalRows}`, `نتائج البحث : ${totalRows}`)
      : `Page ${state.page}/${totalPages}`;
  }
  if (prevBtn) prevBtn.disabled = state.searchMode || state.page <= 1;
  if (nextBtn) nextBtn.disabled = state.searchMode || state.page >= totalPages;
}

async function loadCustomerGridRows(options = {}) {
  const searchTerm = String(ui.customerGridSearch?.value || "").trim();
  const query = searchTerm.toLowerCase();
  const pageSize = Math.max(1, intQty(options.pageSize || customerGridState.pageSize || 100));
  customerGridState.pageSize = pageSize;
  customerGridState.searchMode = Boolean(searchTerm);
  let requestedPage = customerGridState.searchMode ? 1 : Math.max(1, intQty(options.page || customerGridState.page || 1));
  const loadPage = async (page) => {
    const out = await window.wiliDb.listCustomersPage({
      limit: pageSize,
      offset: (page - 1) * pageSize,
      sortKey: customerGridState.sortKey || "name",
      sortDir: customerGridState.sortDir || "asc",
      query: searchTerm
    });
    return parsePartyPageResponse(out);
  };
  if (window.wiliDb && typeof window.wiliDb.listCustomersPage === "function") {
    let pageData = await loadPage(requestedPage);
    customerGridState.totalRows = Math.max(0, intQty(pageData.total));
    const totalPages = Math.max(1, Math.ceil(customerGridState.totalRows / pageSize));
    if (!customerGridState.searchMode && requestedPage > totalPages) {
      requestedPage = totalPages;
      pageData = await loadPage(requestedPage);
      customerGridState.totalRows = Math.max(0, intQty(pageData.total));
    }
    customerGridState.page = customerGridState.searchMode ? 1 : requestedPage;
    return pageData.rows;
  }
  const filtered = sortPartyRows((cache.customers || []).filter((row) => partySearchMatch(row, query)), customerGridState.sortKey, customerGridState.sortDir);
  customerGridState.totalRows = filtered.length;
  customerGridState.page = customerGridState.searchMode ? 1 : requestedPage;
  return filtered.slice((customerGridState.page - 1) * pageSize, customerGridState.page * pageSize);
}

async function loadSupplierGridRows(options = {}) {
  const searchTerm = String(ui.supplierGridSearch?.value || "").trim();
  const query = searchTerm.toLowerCase();
  const pageSize = Math.max(1, intQty(options.pageSize || supplierGridState.pageSize || 100));
  supplierGridState.pageSize = pageSize;
  supplierGridState.searchMode = Boolean(searchTerm);
  let requestedPage = supplierGridState.searchMode ? 1 : Math.max(1, intQty(options.page || supplierGridState.page || 1));
  const loadPage = async (page) => {
    const out = await window.wiliDb.listSuppliersPage({
      limit: pageSize,
      offset: (page - 1) * pageSize,
      sortKey: supplierGridState.sortKey || "name",
      sortDir: supplierGridState.sortDir || "asc",
      query: searchTerm
    });
    return parsePartyPageResponse(out);
  };
  if (window.wiliDb && typeof window.wiliDb.listSuppliersPage === "function") {
    let pageData = await loadPage(requestedPage);
    supplierGridState.totalRows = Math.max(0, intQty(pageData.total));
    const totalPages = Math.max(1, Math.ceil(supplierGridState.totalRows / pageSize));
    if (!supplierGridState.searchMode && requestedPage > totalPages) {
      requestedPage = totalPages;
      pageData = await loadPage(requestedPage);
      supplierGridState.totalRows = Math.max(0, intQty(pageData.total));
    }
    supplierGridState.page = supplierGridState.searchMode ? 1 : requestedPage;
    return pageData.rows;
  }
  const filtered = sortPartyRows((cache.suppliers || []).filter((row) => partySearchMatch(row, query)), supplierGridState.sortKey, supplierGridState.sortDir);
  supplierGridState.totalRows = filtered.length;
  supplierGridState.page = supplierGridState.searchMode ? 1 : requestedPage;
  return filtered.slice((supplierGridState.page - 1) * pageSize, supplierGridState.page * pageSize);
}

function buildHistoryRows(trace) {
  const productName = String(trace && trace.product && trace.product.name ? trace.product.name : "-");
  const stockMoves = Array.isArray(trace && trace.stock_moves) ? trace.stock_moves : [];
  const salesItems = Array.isArray(trace && trace.sales_items) ? trace.sales_items : [];
  const purchaseItems = Array.isArray(trace && trace.purchase_items) ? trace.purchase_items : [];
  const salesReturns = Array.isArray(trace && trace.sales_returns) ? trace.sales_returns : [];
  const purchaseReturns = Array.isArray(trace && trace.purchase_returns) ? trace.purchase_returns : [];
  const rows = [];
  const detailedKeys = new Set();
  const rowKey = (row) => [
    String(row.type || "").toLowerCase(),
    String(row.product_id || ""),
    String(row.ref_id || ""),
    String(Math.abs(Number(row.qty_delta || 0)))
  ].join("|");
  const pushRow = (row, detailed = false) => {
    const normalized = {
      created_at: String(row.created_at || ""),
      type: String(row.type || "manual").toLowerCase(),
      product_id: String(row.product_id || ""),
      product_name: String(row.product_name || productName || "-"),
      qty_delta: Number(row.qty_delta || 0),
      unit_cents: Number(row.unit_cents || 0),
      total_cents: Number(row.total_cents || 0),
      ref_id: String(row.ref_id || ""),
      ref_type: String(row.ref_type || row.type || ""),
      user_name: String(row.user_name || "-"),
      note: String(row.note || "")
    };
    if (detailed) detailedKeys.add(rowKey(normalized));
    rows.push(normalized);
  };

  for (const s of salesItems) {
    pushRow({
      created_at: s.created_at,
      type: "sale",
      product_id: s.product_id,
      product_name: s.product_name,
      qty_delta: -Math.abs(Number(s.qty || 0)),
      unit_cents: Number(s.unit_price_cents || 0),
      total_cents: Number(s.line_total_cents || 0),
      ref_id: s.sale_id,
      ref_type: "sale",
      user_name: s.user_name,
      note: s.note
    }, true);
  }
  for (const p of purchaseItems) {
    pushRow({
      created_at: p.created_at,
      type: "purchase",
      product_id: p.product_id,
      product_name: p.product_name,
      qty_delta: Math.abs(Number(p.qty || 0)),
      unit_cents: Number(p.unit_cost_cents || 0),
      total_cents: Number(p.line_total_cents || 0),
      ref_id: p.purchase_id,
      ref_type: "purchase",
      user_name: p.user_name,
      note: p.note
    }, true);
  }
  for (const r of salesReturns) {
    pushRow({
      created_at: r.created_at,
      type: "sale_return",
      product_id: r.product_id,
      product_name: r.product_name,
      qty_delta: Math.abs(Number(r.qty || 0)),
      unit_cents: Number(r.amount_cents || 0),
      total_cents: Number(r.refund_cents || r.amount_cents || 0),
      ref_id: r.sale_id,
      ref_type: "sale_return",
      user_name: r.user_name,
      note: r.note
    }, true);
  }
  for (const r of purchaseReturns) {
    pushRow({
      created_at: r.created_at,
      type: "purchase_return",
      product_id: r.product_id,
      product_name: r.product_name,
      qty_delta: -Math.abs(Number(r.qty || 0)),
      unit_cents: Number(r.amount_cents || 0),
      total_cents: Number(r.refund_cents || r.amount_cents || 0),
      ref_id: r.purchase_id,
      ref_type: "purchase_return",
      user_name: r.user_name,
      note: r.note
    }, true);
  }
  for (const m of stockMoves) {
    const type = historyMovementType(m);
    const candidate = {
      created_at: m.created_at,
      type,
      product_id: m.product_id,
      product_name: m.product_name,
      qty_delta: Number(m.qty_delta || 0),
      unit_cents: Number(m.unit_cents || m.unit_price_cents || m.unit_cost_cents || 0),
      total_cents: Number(m.total_cents || m.line_total_cents || m.amount_cents || 0),
      ref_id: m.ref_id,
      ref_type: m.ref_type || type,
      user_name: m.user_name,
      note: m.note
    };
    const isDocumentMove = ["sale", "purchase", "sale_return", "purchase_return"].includes(type);
    if (isDocumentMove && detailedKeys.has(rowKey(candidate))) continue;
    pushRow(candidate);
  }
  return rows;
}

function historyStatsRows() {
  const rows = buildHistoryRows(historyTraceState);
  const from = String(ui.historyStatsFrom?.value || "").trim();
  const to = String(ui.historyStatsTo?.value || "").trim();
  return rows.filter((r) => {
    const day = toDay(r.created_at);
    if (from && (!day || day < from)) return false;
    if (to && (!day || day > to)) return false;
    return true;
  });
}

function historyStatsPeriodKey(value, mode) {
  const day = toDay(value);
  if (!day) return langText("Sans date", "بدون تاريخ", "بلا تاريخ");
  return String(mode || "month") === "year" ? day.slice(0, 4) : day.slice(0, 7);
}

function renderHistoryStats(sourceRows = null) {
  if (!ui.historyBox && !ui.historyCharts) return;
  const rows = Array.isArray(sourceRows) ? sourceRows : historyStatsRows();
  const productName = String((historyTraceState && historyTraceState.product && historyTraceState.product.name) || "");
  const compareMode = String(ui.historyStatsCompare?.value || "month");
  const periodMap = new Map();
  const totals = {
    soldQty: 0,
    boughtQty: 0,
    clientReturnQty: 0,
    supplierReturnQty: 0,
    lossQty: 0,
    salesCents: 0,
    purchasesCents: 0,
    clientReturnsCents: 0,
    supplierReturnsCents: 0,
    lossesCents: 0
  };

  const periodBucket = (createdAt) => {
    const key = historyStatsPeriodKey(createdAt, compareMode);
    if (!periodMap.has(key)) periodMap.set(key, { sales: 0, purchases: 0, losses: 0, soldQty: 0 });
    return periodMap.get(key);
  };

  for (const r of rows) {
    const qty = Math.abs(Number(r.qty_delta || 0));
    const total = Math.abs(Number(r.total_cents || 0));
    const bucket = periodBucket(r.created_at);
    if (r.type === "sale") {
      totals.soldQty += qty;
      totals.salesCents += total;
      bucket.sales += total;
      bucket.soldQty += qty;
    } else if (r.type === "purchase") {
      totals.boughtQty += qty;
      totals.purchasesCents += total;
      bucket.purchases += total;
    } else if (r.type === "sale_return") {
      totals.clientReturnQty += qty;
      totals.clientReturnsCents += total;
      bucket.sales -= total;
    } else if (r.type === "purchase_return") {
      totals.supplierReturnQty += qty;
      totals.supplierReturnsCents += total;
      bucket.purchases -= total;
    } else if (r.type === "stock_loss") {
      totals.lossQty += qty;
      totals.lossesCents += total;
      bucket.losses += total;
    }
  }

  const netSales = totals.salesCents - totals.clientReturnsCents;
  const netPurchases = totals.purchasesCents - totals.supplierReturnsCents;
  const estimatedProfit = netSales - netPurchases - totals.lossesCents;
  const from = String(ui.historyStatsFrom?.value || "").trim();
  const to = String(ui.historyStatsTo?.value || "").trim();
  const periodLabel = from || to
    ? `${from || "..."} -> ${to || "..."}`
    : langText("Toutes les dates", "كل التواريخ", "ڨاع التواريخ");
  const scopeLabel = productName || langText("Tous les produits", "كل المنتجات", "ڨاع المنتوجات");
  const kpis = [
    { label: langText("Quantite vendue", "الكمية المباعة", "الكمية المباعة"), value: String(intQty(totals.soldQty)), meta: langText("Ventes nettes", "مبيعات صافية", "بيع صافي") },
    { label: langText("Valeur vendue", "قيمة البيع", "قيمة البيع"), value: money(netSales), meta: productName || langText("Tous produits", "كل المنتجات", "ڨاع المنتوجات") },
    { label: langText("Valeur achetee", "قيمة الشراء", "قيمة الشرا"), value: money(netPurchases), meta: langText("Achats nets", "شراء صافي", "شرا صافي") },
    { label: langText("Profit estime", "ربح تقديري", "ربح تقريبي"), value: money(estimatedProfit), meta: langText("Ventes - achats - pertes", "بيع - شراء - خسائر", "بيع - شرا - خسائر"), tone: estimatedProfit >= 0 ? "pos" : "neg" },
    { label: langText("Retours client", "مرتجعات الزبائن", "روتور كليان"), value: String(intQty(totals.clientReturnQty)), meta: money(totals.clientReturnsCents) },
    { label: langText("Retours fournisseur", "مرتجعات المورد", "روتور فرنسور"), value: String(intQty(totals.supplierReturnQty)), meta: money(totals.supplierReturnsCents) },
    { label: langText("Pertes stock", "خسائر المخزون", "بيرت ستوك"), value: String(intQty(totals.lossQty)), meta: money(totals.lossesCents), tone: "neg" },
    { label: langText("Operations", "العمليات", "العمليات"), value: String(rows.length), meta: compareMode === "year" ? langText("Par annee", "حسب السنة", "بالعام") : langText("Par mois", "حسب الشهر", "بالشهر") }
  ];

  if (ui.historyBox) {
    ui.historyBox.innerHTML = `<div class="history-summary-strip">
      <div><span>${esc(langText("Produit analyse", "المنتج المحلل", "المنتوج المحلل"))}</span><strong>${esc(scopeLabel)}</strong></div>
      <div><span>${esc(langText("Periode", "الفترة", "الفترة"))}</span><small>${esc(periodLabel)}</small></div>
      <div><span>${esc(langText("Operations", "العمليات", "العمليات"))}</span><strong>${esc(String(rows.length))}</strong></div>
    </div><div class="history-kpi-grid">${kpis.map((x) => `
      <div class="history-kpi-card ${x.tone || ""}">
        <span>${esc(x.label)}</span>
        <strong>${esc(x.value)}</strong>
        <small>${esc(x.meta || "")}</small>
      </div>
    `).join("")}</div>`;
  }

  if (ui.historyCharts) {
    const periodRows = Array.from(periodMap.entries()).sort((a, b) => a[0].localeCompare(b[0]));
    const soldByPeriod = periodRows.map(([label, v]) => ({ label, value: v.soldQty }));
    const salesByPeriod = periodRows.map(([label, v]) => ({ label, value: Math.max(0, v.sales) }));
    const profitByPeriod = periodRows.map(([label, v]) => {
      const net = Number(v.sales || 0) - Number(v.purchases || 0) - Number(v.losses || 0);
      return { label: `${label} ${money(net)}`, value: Math.abs(net) };
    });
    ui.historyCharts.innerHTML = [
      chartBars(langText("Quantite vendue par periode", "الكمية المباعة حسب الفترة", "الكمية المباعة بالفترة"), soldByPeriod, (v) => String(intQty(v))),
      chartBars(langText("Valeur vendue par periode", "قيمة البيع حسب الفترة", "قيمة البيع بالفترة"), salesByPeriod, (v) => money(v)),
      chartBars(langText("Profit estime par periode", "الربح التقديري حسب الفترة", "الربح التقريبي بالفترة"), profitByPeriod, (v) => money(v)),
      chartDonut(langText("Flux produit", "حركة المنتج", "حركة البرودوي"), [
        { label: langText("Vendu", "مباع", "مباع"), value: totals.soldQty },
        { label: langText("Achete", "مشتَرى", "مشترى"), value: totals.boughtQty },
        { label: langText("Retour client", "مرتجع زبون", "روتور كليان"), value: totals.clientReturnQty },
        { label: langText("Perte", "خسارة", "بيرت"), value: totals.lossQty }
      ])
    ].join("");
  }
}

function applyHistoryFilters() {
  renderHistoryTableHeader();
  const d = historyTraceState;
  const rows = buildHistoryRows(d);
  const type = String(ui.historyType?.value || "").trim().toLowerCase();
  const from = String(ui.historyFrom?.value || "").trim();
  const to = String(ui.historyTo?.value || "").trim();
  const refQ = String(ui.historyRef?.value || "").trim().toLowerCase();
  const userQ = String(ui.historyUser?.value || "").trim().toLowerCase();

  const filtered = rows.filter((r) => {
    const day = toDay(r.created_at);
    if (type && String(r.type || "").toLowerCase() !== type) return false;
    if (from && (!day || day < from)) return false;
    if (to && (!day || day > to)) return false;
    if (refQ && !(`${r.ref_id} ${r.ref_type} ${formatTransactionRef(r.ref_id, r.ref_type, r.created_at)}`).toLowerCase().includes(refQ)) return false;
    if (userQ && !String(r.user_name || "").toLowerCase().includes(userQ)) return false;
    return true;
  });

  filtered.sort((a, b) => String(b.created_at || "").localeCompare(String(a.created_at || "")));
  ui.historyTableBody.innerHTML = filtered.length
    ? filtered.map((r) => `<tr>
      <td>${esc(fmtDateTime(r.created_at))}</td>
      <td>${esc(r.type)}</td>
      <td>${esc(r.product_name)}</td>
      <td class="${Number(r.qty_delta || 0) < 0 ? "neg" : "pos"}">${Number(r.qty_delta || 0)}</td>
      <td>${money(r.unit_cents || 0)}</td>
      <td>${money(r.total_cents || 0)}</td>
      <td>${esc(formatTransactionRef(r.ref_id, r.ref_type, r.created_at))}</td>
      <td>${esc(r.user_name || "-")}</td>
      <td>${esc(r.note || "-")}</td>
    </tr>`).join("")
    : `<tr><td colspan="9" class="empty">${t("no_data")}</td></tr>`;
}

async function refreshHistory(productId = "") {
  if (!ui.historyProduct || !ui.historyTableBody) return;
  const explicitProductId = arguments.length > 0
    ? String(productId || "")
    : String(ui.historyProduct?.value || "");
  const pid = setHistoryProductValue(explicitProductId);
  let res = null;
  try {
    res = pid
      ? await window.wiliDb.getProductTrace(pid)
      : await window.wiliDb.getProductsTraceAll();
  } catch (err) {
    res = { ok: false, error: String(err && err.message ? err.message : err || "trace_failed") };
  }
  if (!res || !res.ok) {
    historyTraceState = null;
    ui.historyBox.innerHTML = row("Erreur", res && res.error ? res.error : "trace_failed");
    ui.historyTableBody.innerHTML = `<tr><td colspan="9" class="empty">${t("no_data")}</td></tr>`;
    renderHistoryStats([]);
    return;
  }
  const d = res.data;
  historyTraceState = d;
  const productName = pid
    ? String((d.product && d.product.name) || "-")
    : langText("Tout", "الكل", "ڨاع");
  if (pid) setHistoryProductValue(pid, productName);
  applyHistoryFilters();
  renderHistoryStats();
}

function updateCustomerGrid() {
  const rows = customerGridState.rows || [];
  const allowDelete = isAdminUser();
  const q = String(ui.customerGridSearch?.value || "").trim().toLowerCase();
  const filtered = rows.filter((c) => {
    return partySearchMatch(c, q);
  });

  const key = String(customerGridState.sortKey || "name");
  const dir = customerGridState.sortDir === "desc" ? -1 : 1;
  filtered.sort((a, b) => {
    if (key === "balance_cents") {
      return (partyBalanceValue(a) - partyBalanceValue(b)) * dir;
    }
    const av = a[key];
    const bv = b[key];
    return String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" }) * dir;
  });

  customerGridState.filtered = filtered;
  if (!filtered.length) customerGridState.selectedIndex = -1;
  else customerGridState.selectedIndex = Math.min(Math.max(customerGridState.selectedIndex, 0), filtered.length - 1);

  ui.customersTableBody.innerHTML = filtered.length ? filtered.map((c, idx) => `
    <tr data-c-row="${esc(c.id)}" data-row-index="${idx}" class="${idx === customerGridState.selectedIndex ? "row-selected" : ""}">
      <td><strong>${esc(c.name || "-")}</strong></td>
      <td>${esc(c.phone || "-")}</td>
      <td>${balanceChipHtml(c.balance_cents || 0)}</td>
      <td><div class="qty-actions"><button class="btn-mini" data-c-edit="${esc(c.id)}">${esc(langText("Modifier", "\u062a\u0639\u062f\u064a\u0644", "\u0628\u062f\u0651\u0644"))}</button>${allowDelete ? `<button class="btn-mini" data-c-del="${esc(c.id)}">${esc(langText("Supprimer", "\u062d\u0630\u0641", "\u062d\u0630\u0641"))}</button>` : ""}</div></td>
    </tr>
  `).join("") : `<tr><td colspan="4" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.customersTable, customerGridState.sortKey || "name", customerGridState.sortDir || "asc");
  if (ui.customerGridMeta) ui.customerGridMeta.textContent = trText(`${filtered.length} / ${Math.max(0, intQty(customerGridState.totalRows || rows.length))}`);
  updatePartyPager(customerGridState, ui.customersPageInfo, ui.customersPagePrevBtn, ui.customersPageNextBtn, filtered.length);

  ui.customersTableBody.querySelectorAll("[data-c-row]").forEach((tr) => {
    tr.addEventListener("click", () => {
      customerGridState.selectedIndex = intQty(tr.getAttribute("data-row-index"));
      updateCustomerGrid();
    });
    tr.addEventListener("dblclick", () => {
      const cid = String(tr.getAttribute("data-c-row") || "");
      if (!cid) return;
      const row = rows.find((x) => String(x.id || "") === cid);
      const due = Math.max(0, row ? partyBalanceValue(row) : customerBalanceCents(cid));
      if (due > 0) {
        switchView("customerDebt");
        ui.saleDebtCustomer.value = cid;
        void refreshDebtInvoiceOptions(cid);
        return;
      }
      void openCustomerTrace(cid);
    });
  });
  ui.customersTableBody.querySelectorAll("[data-c-edit]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const c = rows.find((x) => String(x.id) === String(btn.getAttribute("data-c-edit") || ""));
      if (!c) return;
      ui.customerId.value = c.id;
      ui.customerName.value = c.name || "";
      ui.customerPhone.value = c.phone || "";
      if (ui.customerNif) ui.customerNif.value = c.nif || "";
      if (ui.customerNis) ui.customerNis.value = c.nis || "";
      if (ui.customerRc) ui.customerRc.value = c.rc || "";
      if (ui.customerAi) ui.customerAi.value = c.ai || "";
      if (ui.customerAddress) ui.customerAddress.value = c.address || "";
      if (ui.customerActivity) ui.customerActivity.value = c.activity || "";
      if (ui.customerCommerceName) ui.customerCommerceName.value = c.commerce_name || "";
      ui.customerSubmitBtn.textContent = langText("Mettre a jour", "تحديث الزبون", "حدّث لكليان");
      if (ui.customerModalTitle) ui.customerModalTitle.textContent = langText("Modifier client", "تعديل زبون", "بدّل لكليان");
      ui.customerCancelBtn.classList.remove("hidden");
      openCustomerModal();
      setTimeout(() => ui.customerName?.focus(), 0);
    });
  });
  ui.customersTableBody.querySelectorAll("[data-c-del]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-c-del") || "");
      if (!id) return;
      const ok = await appConfirm(t("delete_confirm"));
      if (!ok) return;
      const out = await window.wiliDb.deleteCustomer(id);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true);
        return;
      }
      await refreshCustomers();
      appNotify(langText("Suppression terminee", "تم الحذف", "تم الحذف"), false);
    });
  });
}

async function refreshCustomers(options = {}) {
  const opts = options && typeof options === "object" ? options : {};
  const shouldRefreshCache = opts.reloadCache !== false || !Array.isArray(cache.customers) || !cache.customers.length;
  let rows = Array.isArray(cache.customers) ? cache.customers : [];
  const currentCustomer = String(ui.saleCustomer.value || "");
  const currentDebtCustomer = String(ui.saleDebtCustomer.value || "");
  const currentTraceCustomer = String(ui.customerTraceCustomer.value || "");
  const currentReturnCustomer = String(ui.customerReturnCustomer?.value || "");

  if (shouldRefreshCache) {
    const res = await window.wiliDb.listCustomers(1000);
    rows = res && res.ok ? (res.data || []) : [];
    cache.customers = rows;
  }
  customerGridState.rows = (await loadCustomerGridRows(opts)).slice();
  updateCustomerGrid();
  if (!shouldRefreshCache) return;
  ui.saleCustomer.innerHTML = [`<option value="">${trText("Client comptoir")}</option>`].concat(rows.map((c) => `<option value="${esc(c.id)}">${esc(c.name)}</option>`)).join("");
  ui.saleDebtCustomer.innerHTML = [`<option value="">${trText("Tous clients (credit)")}</option>`].concat(rows.map((c) => `<option value="${esc(c.id)}">${esc(c.name)}</option>`)).join("");
  ui.customerTraceCustomer.innerHTML = [`<option value="">${langText("Tous clients", "كل الزبائن", "قاع لكليان")}</option>`].concat(rows.map((c) => `<option value="${esc(c.id)}">${esc(c.name)}</option>`)).join("");
  if (ui.customerReturnCustomer) {
    ui.customerReturnCustomer.innerHTML = [`<option value="">${langText("Choisir client", "اختر الزبون", "اختار لكليان")}</option>`].concat(rows.map((c) => `<option value="${esc(c.id)}">${esc(c.name)}</option>`)).join("");
  }
  if (currentCustomer && rows.some((c) => String(c.id || "") === currentCustomer)) {
    ui.saleCustomer.value = currentCustomer;
  }
  if (currentDebtCustomer && rows.some((c) => String(c.id || "") === currentDebtCustomer)) {
    ui.saleDebtCustomer.value = currentDebtCustomer;
  }
  if (currentTraceCustomer && rows.some((c) => String(c.id || "") === currentTraceCustomer)) {
    ui.customerTraceCustomer.value = currentTraceCustomer;
  }
  if (ui.customerReturnCustomer && currentReturnCustomer && rows.some((c) => String(c.id || "") === currentReturnCustomer)) {
    ui.customerReturnCustomer.value = currentReturnCustomer;
  }

  if (typeof window.syncSaleCustomerSearchInput === "function") window.syncSaleCustomerSearchInput();
  if (typeof window.syncSaleDebtCustomerSearchInput === "function") window.syncSaleDebtCustomerSearchInput();
  if (typeof window.syncCustomerTraceCustomerSearchInput === "function") window.syncCustomerTraceCustomerSearchInput();
  if (typeof window.syncCustomerReturnCustomerSearchInput === "function") window.syncCustomerReturnCustomerSearchInput();
  updateSaleCustomerDebtHint();
  if (!opts.skipDependentRefresh) {
    await refreshDebtInvoiceOptions(ui.saleDebtCustomer.value || "");
    await refreshCustomerReturnOptions();
  }
}

async function refreshCustomerReturnOptions() {
  if (!ui.customerReturnCustomer || !ui.customerReturnSale || !ui.customerReturnProduct) return;
  const customerId = String(ui.customerReturnCustomer.value || "").trim();
  const currentSale = String(ui.customerReturnSale.value || "").trim();
  const currentProduct = String(ui.customerReturnProduct.value || "").trim();
  const salesRes = customerId && window.wiliDb && typeof window.wiliDb.listSales === "function"
    ? await window.wiliDb.listSales(2000)
    : { ok: true, data: [] };
  const saleRows = salesRes && salesRes.ok ? (salesRes.data || []) : [];
  const customerSales = saleRows.filter((s) => String(s.customer_id || "") === customerId);
  ui.customerReturnSale.innerHTML = [`<option value="">${langText("Retour general", "إرجاع عام", "ريطور عام")}</option>`]
    .concat(customerSales.map((s) => `<option value="${esc(s.id)}">${esc(s.public_ref || salePublicRef(s.id, s.created_at) || s.id)} - ${money(s.total_cents || 0)}</option>`))
    .join("");
  if (currentSale && customerSales.some((s) => String(s.id || "") === currentSale)) ui.customerReturnSale.value = currentSale;
  await refreshCustomerReturnProductOptions(currentProduct);
}

async function refreshCustomerReturnProductOptions(preferredProductId = "") {
  if (!ui.customerReturnSale || !ui.customerReturnProduct) return;
  const saleId = String(ui.customerReturnSale.value || "").trim();
  let rows = [];
  if (saleId && window.wiliDb && typeof window.wiliDb.getSaleDetails === "function") {
    const out = await window.wiliDb.getSaleDetails(saleId);
    const details = out && out.ok ? out.data : {};
    const returns = Array.isArray(details && details.returns) ? details.returns : [];
    rows = (Array.isArray(details && details.items) ? details.items : []).map((line) => {
      const returnedQty = returns
        .filter((r) => String(r.product_id || "") === String(line.product_id || ""))
        .reduce((sum, r) => sum + Math.max(0, Number(r.qty || 0)), 0);
      const qtyLeft = Math.max(0, Number(line.qty || 0) - returnedQty);
      const unit = Number(line.unit_price_cents || (Number(line.line_total_cents || 0) / Math.max(1, Number(line.qty || 1))) || 0);
      return { id: String(line.product_id || ""), name: String(line.product_name || line.product_id || "-"), qty_left: qtyLeft, unit_cents: unit };
    }).filter((x) => x.id && x.qty_left > 0);
  } else {
    rows = (cache.products || []).map((p) => ({
      id: String(p.id || ""),
      name: String(p.name || "-"),
      qty_left: Number(p.stock_qty || 0),
      unit_cents: Number(p.retail_cents || p.price_cents || p.sale_price_cents || 0)
    })).filter((x) => x.id);
  }
  ui.customerReturnProduct.innerHTML = rows.length
    ? rows.map((p) => `<option value="${esc(p.id)}" data-unit="${Number(p.unit_cents || 0)}" data-left="${Number(p.qty_left || 0)}">${esc(p.name)}${saleId ? ` | ${Number(p.qty_left || 0)} max` : ""}</option>`).join("")
    : `<option value="">${langText("Aucun produit", "لا توجد منتجات", "ماكانش منتوج")}</option>`;
  if (preferredProductId && rows.some((p) => String(p.id) === String(preferredProductId))) ui.customerReturnProduct.value = preferredProductId;
  const opt = ui.customerReturnProduct.selectedOptions && ui.customerReturnProduct.selectedOptions[0];
  if (typeof window.syncCustomerReturnProductSearchInput === "function") window.syncCustomerReturnProductSearchInput();
  if (ui.customerReturnPrice && opt && !String(ui.customerReturnPrice.value || "").trim()) ui.customerReturnPrice.value = (Number(opt.dataset.unit || 0) / 100).toFixed(2);
  if (ui.customerReturnHint) {
    ui.customerReturnHint.textContent = saleId
      ? langText("Retour lie au BL selectionne: quantite limitee aux lignes restantes.", "إرجاع مربوط بوصل التسليم المختار: الكمية محدودة بالمتبقي.", "ريطور مربوط بالـ BL: الكمية محدودة بالباقي.")
      : langText("Retour general: le stock augmente et une sortie caisse est creee.", "إرجاع عام: المخزون يزيد وتُسجل خرجة صندوق.", "ريطور عام: الستوك يزيد وتخرج خرجة caisse.");
  }
}

function returnLineKey(sourceId, productId) {
  return `${String(sourceId || "general")}::${String(productId || "")}`;
}

function selectedReturnProductMeta(selectEl, fallbackUnitCents = 0) {
  const opt = selectEl && selectEl.selectedOptions ? selectEl.selectedOptions[0] : null;
  return {
    product_id: String(selectEl && selectEl.value || "").trim(),
    product_name: opt ? String(opt.textContent || "").replace(/\s+\|\s+(?:stock\s+)?[-0-9.,]+\s*(?:max)?$/i, "").trim() : "",
    max_qty: opt ? Number(opt.dataset.left || 0) : 0,
    unit_cents: opt ? Number(opt.dataset.unit || fallbackUnitCents || 0) : Number(fallbackUnitCents || 0)
  };
}

function renderReturnCart(kind) {
  const isCustomer = kind === "customer";
  const cart = isCustomer ? customerReturnCart : supplierReturnCart;
  const body = isCustomer ? ui.customerReturnLinesBody : ui.supplierReturnLinesBody;
  const totalEl = isCustomer ? ui.customerReturnTotal : ui.supplierReturnTotal;
  if (!body) return;
  const total = cart.reduce((sum, line) => sum + Math.max(0, Number(line.qty || 0)) * Math.max(0, Number(line.unit_cents || 0)), 0);
  if (totalEl) totalEl.textContent = money(total);
  body.innerHTML = cart.length ? cart.map((line, idx) => {
    const lineTotal = Math.round(Math.max(0, Number(line.qty || 0)) * Math.max(0, Number(line.unit_cents || 0)));
    return `
      <tr>
        <td><strong>${esc(line.product_name || line.product_id || "-")}</strong>${line.source_ref ? `<div class="small">${esc(line.source_ref)}</div>` : ""}</td>
        <td><input class="table-input" type="number" min="0.01" step="0.01" value="${Number(line.qty || 0)}" data-return-qty="${idx}" data-return-kind="${kind}"></td>
        <td><input class="table-input" type="number" min="0" step="0.01" value="${(Number(line.unit_cents || 0) / 100).toFixed(2)}" data-return-unit="${idx}" data-return-kind="${kind}"></td>
        <td>${money(lineTotal)}</td>
        <td><button type="button" class="btn-mini" data-return-remove="${idx}" data-return-kind="${kind}">${langText("Retirer", "حذف", "نحي")}</button></td>
      </tr>
    `;
  }).join("") : `<tr><td colspan="5" class="empty">${t("no_data")}</td></tr>`;
  body.querySelectorAll("[data-return-qty]").forEach((input) => {
    input.addEventListener("change", () => {
      const idx = intQty(input.getAttribute("data-return-qty"));
      const line = cart[idx];
      if (!line) return;
      const next = qtyNum(input.value || 0);
      const max = Number(line.max_qty || 0);
      line.qty = max > 0 ? Math.min(Math.max(0.01, next), max) : Math.max(0.01, next);
      renderReturnCart(kind);
    });
  });
  body.querySelectorAll("[data-return-unit]").forEach((input) => {
    input.addEventListener("change", () => {
      const idx = intQty(input.getAttribute("data-return-unit"));
      const line = cart[idx];
      if (!line) return;
      line.unit_cents = Math.max(0, toCents(input.value || 0));
      renderReturnCart(kind);
    });
  });
  body.querySelectorAll("[data-return-remove]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const idx = intQty(btn.getAttribute("data-return-remove"));
      if (idx >= 0) cart.splice(idx, 1);
      renderReturnCart(kind);
    });
  });
}

function clearReturnCart(kind) {
  const cart = kind === "customer" ? customerReturnCart : supplierReturnCart;
  cart.splice(0, cart.length);
  renderReturnCart(kind);
}

function addCustomerReturnLine() {
  const customerId = String(ui.customerReturnCustomer?.value || "").trim();
  const saleId = String(ui.customerReturnSale?.value || "").trim();
  if (!customerId) return appNotify(langText("Choisissez un client", "اختر الزبون", "اختار لكليان"), true);
  const meta = selectedReturnProductMeta(ui.customerReturnProduct, toCents(ui.customerReturnPrice?.value || 0));
  const qtyValue = qtyNum(ui.customerReturnQty?.value || 0);
  if (!meta.product_id) return appNotify(langText("Choisissez un produit", "اختر المنتج", "اختار المنتوج"), true);
  if (qtyValue <= 0) return appNotify("return_qty_required", true);
  const sourceRef = ui.customerReturnSale?.selectedOptions?.[0]?.textContent || "";
  const key = returnLineKey(saleId, meta.product_id);
  const max = saleId ? Number(meta.max_qty || 0) : 0;
  const existing = customerReturnCart.find((line) => line.key === key);
  const nextQty = (existing ? Number(existing.qty || 0) : 0) + qtyValue;
  if (max > 0 && nextQty > max) return appNotify(langText("Quantite retour superieure au reste", "كمية الإرجاع أكبر من المتبقي", "الكمية كثر من الباقي"), true);
  if (existing) {
    existing.qty = nextQty;
    existing.unit_cents = toCents(ui.customerReturnPrice?.value || 0) || meta.unit_cents;
  } else {
    customerReturnCart.push({
      key,
      sale_id: saleId,
      customer_id: customerId,
      product_id: meta.product_id,
      product_name: meta.product_name,
      qty: qtyValue,
      max_qty: max,
      unit_cents: toCents(ui.customerReturnPrice?.value || 0) || meta.unit_cents,
      source_ref: saleId ? sourceRef : langText("Retour general", "إرجاع عام", "ريطور عام")
    });
  }
  renderReturnCart("customer");
}

async function submitCustomerReturn() {
  const customerId = String(ui.customerReturnCustomer?.value || "").trim();
  const note = String(ui.customerReturnNote?.value || "").trim();
  if (!customerId) return appNotify(langText("Choisissez un client", "اختر الزبون", "اختار لكليان"), true);
  if (!customerReturnCart.length) return appNotify(langText("Ajoutez au moins une ligne", "أضف سطرا واحدا على الأقل", "زيد سطر واحد على الأقل"), true);
  const ok = await appConfirm(langText("Confirmer le retour client ?", "تأكيد إرجاع الزبون؟", "تأكد ريطور لكليان؟"));
  if (!ok) return;
  for (const line of customerReturnCart.slice()) {
    const payload = { product_id: line.product_id, qty: Number(line.qty || 0), note };
    const out = line.sale_id
      ? await window.wiliDb.addSaleReturn({ ...payload, sale_id: line.sale_id })
      : await window.wiliDb.addSaleReturnGeneral({ ...payload, customer_id: customerId, unit_price_cents: Math.max(0, Number(line.unit_cents || 0)) });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "sale_return_failed", true, 5000);
  }
  clearReturnCart("customer");
  ui.customerReturnQty.value = "1";
  ui.customerReturnNote.value = "";
  ui.customerReturnPrice.value = "";
  await refreshAll();
  await refreshCustomerReturnOptions();
  appNotify(langText("Retour client enregistre", "تم تسجيل إرجاع الزبون", "تسجل ريطور لكليان"), false);
}

async function refreshCustomerTraceSalesRows() {
  if (!window.wiliDb || typeof window.wiliDb.listSales !== "function") return;
  const salesRes = await window.wiliDb.listSales(2000);
  const rows = salesRes && salesRes.ok ? (salesRes.data || []) : [];
  salesDetailsCache.clear();
  salesRows = rows.map((s) => {
    const customer = (cache.customers || []).find((c) => String(c.id || "") === String(s.customer_id || ""));
    return {
      ...s,
      customer_name: customer ? String(customer.name || "-") : (s.customer_name || "-")
    };
  });
  if (typeof buildPublicRefMap === "function") {
    salesPublicRefMap = buildPublicRefMap(salesRows);
  }
}

async function refreshCustomerTrace() {
  const reqSeq = ++customerTraceReqSeq;
  await refreshCustomers({ skipDependentRefresh: true });
  if (reqSeq !== customerTraceReqSeq) return;
  await refreshCustomerTraceSalesRows();
  if (reqSeq !== customerTraceReqSeq) return;
  const cid = String(ui.customerTraceCustomer.value || "").trim();
  const balance = cid
    ? customerBalanceCents(cid)
    : (cache.customers || []).reduce((sum, c) => sum + partyBalanceValue(c), 0);
  const dueSingle = cid ? Math.max(0, customerBalanceCents(cid)) : Math.max(0, (cache.customers || []).reduce((sum, c) => sum + partyBalanceValue(c), 0));
  if (ui.customerTraceDebt) {
    const sign = balance > 0 ? "+" : "";
    ui.customerTraceDebt.textContent = cid
      ? langText(`Solde/Avoir client: ${sign}${money(balance)}`, `رصيد/أفوار الزبون: ${sign}${money(balance)}`, `رصيد/أفوار لكليان: ${sign}${money(balance)}`)
      : langText(`Solde/Avoir clients: ${sign}${money(balance)}`, `رصيد/أفوار الزبائن: ${sign}${money(balance)}`, `رصيد/أفوار لكليان: ${sign}${money(balance)}`);
    ui.customerTraceDebt.classList.toggle("ok", balance >= 0);
    ui.customerTraceDebt.classList.toggle("bad", balance < 0);
  }
  const from = String(ui.customerTraceFrom.value || "").trim();
  const to = String(ui.customerTraceTo.value || "").trim();
  const type = String(ui.customerTraceType.value || "").trim();
  const includeSale = !type || type === "sale";
  const includePayment = !type || type === "payment";
  const includeReturn = !type || type === "sale_return";
  const needDetails = includePayment || includeReturn;
  const sales = cid
    ? salesRows.filter((s) => String(s.customer_id || "") === cid)
    : salesRows.slice();
  const saleIds = sales.map((s) => String(s.id || "")).filter(Boolean);
  const paymentsBySale = new Map();
  const returnsBySale = new Map();
  const generalReturns = [];
  if (needDetails) {
    try {
      const customerIds = cid
        ? [cid]
        : (cache.customers || []).map((c) => String(c.id || "")).filter(Boolean);
      const bulkOut = await window.wiliDb.getSalesTraceBulk({
        sale_ids: saleIds,
        customer_ids: customerIds,
        include_payments: includePayment,
        include_returns: includeReturn
      });
      if (!bulkOut || !bulkOut.ok) throw new Error("trace_bulk_failed");
      const bulk = bulkOut.data || {};
      const pays = Array.isArray(bulk.payments) ? bulk.payments : [];
      const rets = Array.isArray(bulk.returns) ? bulk.returns : [];
      for (const p of pays) {
        const sid = String(p && p.sale_id ? p.sale_id : "");
        if (!sid) continue;
        if (!paymentsBySale.has(sid)) paymentsBySale.set(sid, []);
        paymentsBySale.get(sid).push(p);
      }
      for (const r of rets) {
        const sid = String(r && r.sale_id ? r.sale_id : "");
        if (!sid) {
          generalReturns.push(r);
          continue;
        }
        if (!returnsBySale.has(sid)) returnsBySale.set(sid, []);
        returnsBySale.get(sid).push(r);
      }
    } catch (_e) {
      // Fallback for compatibility: old per-sale cache path.
      await preloadDetailsInCache(
        saleIds,
        salesDetailsCache,
        (id) => window.wiliDb.getSaleDetails(id),
        () => reqSeq !== customerTraceReqSeq
      );
    }
    if (reqSeq !== customerTraceReqSeq) return;
  }
  const events = [];
  for (const s of sales) {
    const customerId = String(s.customer_id || "");
    const customerName = String(s.customer_name || "-");
    const saleId = String(s.id || "");
    const traceOwnerId = customerId || `sale:${saleId}`;
    const saleTotalCents = Math.max(0, Number(s.total_cents || 0));
    const salePaidCents = Math.max(0, Number(s.paid_cents || 0));
    if (includeSale) {
      events.push({
        created_at: String(s.created_at || ""),
        type: "sale",
        ref: saleId,
        public_ref: String(s.public_ref || salePublicRef(saleId, s.created_at) || ""),
        amount_cents: saleTotalCents,
        invoice_total_cents: saleTotalCents,
        paid_at_create_cents: salePaidCents,
        note: "",
        trace_owner_id: traceOwnerId,
        customer_id: customerId,
        customer_name: customerName
      });
    }
    if (needDetails) {
      const sid = saleId;
      const details = salesDetailsCache.get(sid);
      const payments = includePayment
        ? (paymentsBySale.get(sid) || (Array.isArray(details && details.payments) ? details.payments : []))
        : [];
      const returns = includeReturn
        ? (returnsBySale.get(sid) || (Array.isArray(details && details.returns) ? details.returns : []))
        : [];
      for (const p of payments) {
        events.push({
          created_at: String(p.created_at || ""),
          type: "payment",
          ref: sid,
          public_ref: String(p.public_ref || ""),
          movement_id: String(p.id || ""),
          amount_cents: Number(p.amount_cents || 0),
          invoice_total_cents: Number(p.invoice_total_cents || saleTotalCents),
          due_before_cents: Number(p.due_before_cents),
          due_after_cents: Number(p.due_after_cents),
          paid_at_create_cents: 0,
          note: String(p.note || ""),
          trace_owner_id: traceOwnerId,
          customer_id: customerId,
          customer_name: customerName
        });
      }
      for (const r of returns) {
        events.push({
          created_at: String(r.created_at || ""),
          type: "sale_return",
          ref: sid,
          public_ref: String(r.public_ref || ""),
          movement_id: String(r.id || ""),
          amount_cents: Number(r.amount_cents || 0),
          refund_cents: Number(r.refund_cents || 0),
          invoice_total_cents: Number(r.invoice_total_cents || saleTotalCents),
          due_before_cents: Number(r.due_before_cents),
          due_after_cents: Number(r.due_after_cents),
          paid_at_create_cents: 0,
          note: String(r.note || ""),
          trace_owner_id: traceOwnerId,
          customer_id: customerId,
          customer_name: customerName
        });
      }
    }
  }
  for (const r of generalReturns) {
    const customerId = String(r && r.customer_id || "");
    if (!customerId) continue;
    const customer = (cache.customers || []).find((c) => String(c.id || "") === customerId) || {};
    events.push({
      created_at: String(r.created_at || ""),
      type: "sale_return",
      ref: "",
      public_ref: String(r.public_ref || ""),
      movement_id: String(r.id || ""),
      amount_cents: Number(r.amount_cents || r.total_cents || 0),
      refund_cents: Number(r.refund_cents || 0),
      invoice_total_cents: Number(r.amount_cents || r.total_cents || 0),
      paid_at_create_cents: 0,
      note: String(r.note || ""),
      trace_owner_id: customerId,
      customer_id: customerId,
      customer_name: String(customer.name || r.customer_name || "-")
    });
  }
  if (includePayment) {
    try {
      events.push(...await loadFinanceTraceEvents("customer", cid));
    } catch (_e) {}
  }
  if (reqSeq !== customerTraceReqSeq) return;
  const withRunning = attachTraceDebtRunning(events, "trace_owner_id", customerTraceDeltaCents);
  const filtered = withRunning.filter((e) => {
    const day = toDay(e.created_at);
    if (type && e.type !== type) return false;
    if (from && (!day || day < from)) return false;
    if (to && (!day || day > to)) return false;
    return true;
  });
  customerTraceRows = filtered;
  customerTraceViewState.showAllCustomers = !cid;
  customerTraceViewState.dueSingle = dueSingle;
  const dueMap = new Map();
  if (customerTraceViewState.showAllCustomers) {
    for (const c of cache.customers) {
      const id = String(c.id || "");
      if (!id) continue;
      dueMap.set(id, customerDueCents(id));
    }
  }
  customerTraceViewState.dueMap = dueMap;
  renderCustomerTraceTable();
}

function updateSaleCustomerDebtHint() {
  if (!ui.saleCustomerDebt) return;
  const cid = String(ui.saleCustomer.value || "");
  const customerName = (() => {
    if (!cid) return "";
    const c = (cache.customers || []).find((x) => String(x.id || "") === cid);
    return String((c && c.name) || "").trim();
  })();
  const displayName = customerName || "-";
  const currentDue = (() => {
    try {
      if (typeof getPosCheckoutSummary === "function") {
        return Math.max(0, Number(getPosCheckoutSummary().due_cents || 0));
      }
    } catch (_e) {}
    return 0;
  })();
  const setState = (value) => {
    ui.saleCustomerDebt.classList.toggle("ok", value > 0);
    ui.saleCustomerDebt.classList.toggle("bad", value < 0);
    ui.saleCustomerDebt.classList.toggle("neutral", value === 0);
    ui.saleCustomerDebt.setAttribute("data-balance-state", value > 0 ? "ok" : (value < 0 ? "bad" : "neutral"));
  };
  if (!cid) {
    const liveBalance = currentDue;
    const sign = liveBalance > 0 ? "+" : "";
    ui.saleCustomerDebt.textContent = langText(`Solde/Avoir client ${displayName}: ${sign}${money(liveBalance)}`, `رصيد/أفوار الزبون ${displayName}: ${sign}${money(liveBalance)}`, `صولد/أفوار لكليان ${displayName}: ${sign}${money(liveBalance)}`);
    setState(liveBalance);
    return;
  }
  const liveBalance = customerBalanceCents(cid) + currentDue;
  const sign = liveBalance > 0 ? "+" : "";
  ui.saleCustomerDebt.textContent = langText(`Solde/Avoir client ${displayName}: ${sign}${money(liveBalance)}`, `رصيد/أفوار الزبون ${displayName}: ${sign}${money(liveBalance)}`, `صولد/أفوار لكليان ${displayName}: ${sign}${money(liveBalance)}`);
  setState(liveBalance);
}

async function refreshDebtInvoiceOptions(customerId = "") {
  const cid = String(customerId || ui.saleDebtCustomer.value || "").trim();
  const out = await window.wiliDb.listSalesDues(cid);
  const rows = out && out.ok ? (out.data || []) : [];
  const options = [`<option value="">${trText("Choisir facture credit")}</option>`].concat(
    rows.map((s) => `<option value="${esc(s.id)}">${esc(salePublicRef(s.id, s.created_at, rows))} | ${esc(fmtDateTime(s.created_at))} | ${money(s.due_cents)} | ${esc(s.customer_name || "-")}</option>`)
  );
  const selected = String(ui.saleDebtInvoice.value || "");
  ui.saleDebtInvoice.innerHTML = options.join("");
  if (selected && rows.some((x) => String(x.id || "") === selected)) {
    ui.saleDebtInvoice.value = selected;
  }
  const chosen = rows.find((x) => String(x.id || "") === String(ui.saleDebtInvoice.value || ""));
  if (chosen) ui.saleDebtAmount.value = money(chosen.due_cents);

  if (ui.saleDebtTableBody) {
    ui.saleDebtTableBody.innerHTML = rows.length ? rows.map((x) => {
      const due = Number(x.due_cents || 0);
      const status = due <= 0 ? langText("Regle", "مسدد", "مسدد") : langText("Partiel", "جزئي", "جزئي");
      return `
        <tr>
          <td>${esc(salePublicRef(x.id, x.created_at, rows))}</td>
          <td>${esc(fmtDateTime(x.created_at))}</td>
          <td>${esc(x.customer_name || "-")}</td>
          <td>${money(x.total_cents || 0)}</td>
          <td>${money(x.paid_cents || 0)}</td>
          <td class="${due > 0 ? "neg" : "pos"}">${money(due)}</td>
          <td>${esc(status)}</td>
          <td><button class="btn-mini" data-sd-select="${esc(x.id)}">${langText("Choisir", "اختيار", "اختار")}</button></td>
        </tr>
      `;
    }).join("") : `<tr><td colspan="9" class="empty">${t("no_data")}</td></tr>`;

    ui.saleDebtTableBody.querySelectorAll("[data-sd-select]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const sid = String(btn.getAttribute("data-sd-select") || "");
        if (!sid) return;
        ui.saleDebtInvoice.value = sid;
        const inv = rows.find((x) => String(x.id || "") === sid);
        if (inv) {
          ui.saleDebtAmount.value = money(inv.due_cents || 0);
          salesSelectedId = sid;
        }
      });
    });
  }
}

function updateSupplierGrid() {
  const rows = supplierGridState.rows || [];
  const allowDelete = isAdminUser();
  const q = String(ui.supplierGridSearch?.value || "").trim().toLowerCase();
  const filtered = rows.filter((s) => {
    return partySearchMatch(s, q);
  });

  const key = String(supplierGridState.sortKey || "name");
  const dir = supplierGridState.sortDir === "desc" ? -1 : 1;
  filtered.sort((a, b) => {
    if (key === "balance_cents") {
      return (partyBalanceValue(a) - partyBalanceValue(b)) * dir;
    }
    const av = a[key];
    const bv = b[key];
    return String(av || "").localeCompare(String(bv || ""), undefined, { sensitivity: "base" }) * dir;
  });

  supplierGridState.filtered = filtered;
  if (!filtered.length) supplierGridState.selectedIndex = -1;
  else supplierGridState.selectedIndex = Math.min(Math.max(supplierGridState.selectedIndex, 0), filtered.length - 1);

  ui.suppliersTableBody.innerHTML = filtered.length ? filtered.map((s, idx) => `
    <tr data-s-row="${esc(s.id)}" data-row-index="${idx}" class="${idx === supplierGridState.selectedIndex ? "row-selected" : ""}">
      <td><strong>${esc(s.name || "-")}</strong></td>
      <td>${esc(s.phone || "-")}</td>
      <td>${balanceChipHtml(s.balance_cents || 0)}</td>
      <td>
        <div class="qty-actions">
          <button class="btn-mini" data-s-edit="${esc(s.id)}">${langText("Modifier", "\u062a\u0639\u062f\u064a\u0644", "\u0628\u062f\u0651\u0644")}</button>
          ${allowDelete ? `<button class="btn-mini" data-s-del="${esc(s.id)}">${langText("Supprimer", "\u062d\u0630\u0641", "\u062d\u0630\u0641")}</button>` : ""}
        </div>
      </td>
    </tr>
  `).join("") : `<tr><td colspan="4" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.suppliersTable, supplierGridState.sortKey || "name", supplierGridState.sortDir || "asc");
  if (ui.supplierGridMeta) ui.supplierGridMeta.textContent = trText(`${filtered.length} / ${Math.max(0, intQty(supplierGridState.totalRows || rows.length))}`);
  updatePartyPager(supplierGridState, ui.suppliersPageInfo, ui.suppliersPagePrevBtn, ui.suppliersPageNextBtn, filtered.length);

  ui.suppliersTableBody.querySelectorAll("[data-s-row]").forEach((tr) => {
    tr.addEventListener("click", () => {
      supplierGridState.selectedIndex = intQty(tr.getAttribute("data-row-index"));
      updateSupplierGrid();
    });
    tr.addEventListener("dblclick", () => {
      const sid = String(tr.getAttribute("data-s-row") || "");
      if (!sid) return;
      const row = rows.find((x) => String(x.id || "") === sid);
      const due = Math.max(0, -(row ? partyBalanceValue(row) : supplierBalanceCents(sid)));
      if (due > 0) {
        switchView("supplierDebt");
        ui.purchaseDebtSupplier.value = sid;
        void refreshPurchaseDebtInvoiceOptions(sid);
        return;
      }
      void openSupplierTrace(sid);
    });
  });

  ui.suppliersTableBody.querySelectorAll("[data-s-edit]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const s = rows.find((x) => String(x.id) === String(btn.getAttribute("data-s-edit") || ""));
      if (!s) return;
      ui.supplierId.value = s.id;
      ui.supplierName.value = s.name || "";
      ui.supplierPhone.value = s.phone || "";
      if (ui.supplierNif) ui.supplierNif.value = s.nif || "";
      if (ui.supplierNis) ui.supplierNis.value = s.nis || "";
      if (ui.supplierRc) ui.supplierRc.value = s.rc || "";
      if (ui.supplierAi) ui.supplierAi.value = s.ai || "";
      if (ui.supplierAddress) ui.supplierAddress.value = s.address || "";
      if (ui.supplierActivity) ui.supplierActivity.value = s.activity || "";
      if (ui.supplierCommerceName) ui.supplierCommerceName.value = s.commerce_name || "";
      ui.supplierSubmitBtn.textContent = langText("Mettre a jour", "تحديث المورد");
      if (ui.supplierModalTitle) ui.supplierModalTitle.textContent = langText("Modifier fournisseur", "تعديل المورد", "بدّل لفرنسور");
      ui.supplierCancelBtn.classList.remove("hidden");
      openSupplierModal();
      setTimeout(() => ui.supplierName?.focus(), 0);
    });
  });

  ui.suppliersTableBody.querySelectorAll("[data-s-del]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-s-del") || "");
      if (!id) return;
      const ok = await appConfirm(t("delete_confirm"));
      if (!ok) return;
      const out = await window.wiliDb.deleteSupplier(id);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true);
        return;
      }
      await refreshSuppliers();
      appNotify(langText("Suppression terminee", "تم الحذف", "تم الحذف"), false);
    });
  });
}

async function refreshSuppliers(options = {}) {
  const opts = options && typeof options === "object" ? options : {};
  const shouldRefreshCache = opts.reloadCache !== false || !Array.isArray(cache.suppliers) || !cache.suppliers.length;
  let rows = Array.isArray(cache.suppliers) ? cache.suppliers : [];
  const currentPurchaseSupplier = String(ui.purchaseSupplier?.value || "");
  const currentDebtSupplier = String(ui.purchaseDebtSupplier.value || "");
  const currentTraceSupplier = String(ui.supplierTraceSupplier.value || "");
  const currentReturnSupplier = String(ui.supplierReturnSupplier?.value || "");

  if (shouldRefreshCache) {
    const res = await window.wiliDb.listSuppliers(1000);
    rows = res && res.ok ? (res.data || []) : [];
    cache.suppliers = rows;
  }
  supplierGridState.rows = (await loadSupplierGridRows(opts)).slice();
  updateSupplierGrid();
  if (!shouldRefreshCache) return;
  ui.purchaseSupplier.innerHTML = [`<option value="">${langText("Achat divers", "شراء متنوع", "شرا متنوع")}</option>`].concat(rows.map((s) => `<option value="${esc(s.id)}">${esc(s.name)}</option>`)).join("");
  ui.purchaseDebtSupplier.innerHTML = [`<option value="">${trText("Tous fournisseurs (credit)")}</option>`].concat(rows.map((s) => `<option value="${esc(s.id)}">${esc(s.name)}</option>`)).join("");
  ui.supplierTraceSupplier.innerHTML = [`<option value="">${langText("Tous fournisseurs", "كل الموردين", "قاع لفرنسور")}</option>`].concat(rows.map((s) => `<option value="${esc(s.id)}">${esc(s.name)}</option>`)).join("");
  if (ui.supplierReturnSupplier) {
    ui.supplierReturnSupplier.innerHTML = [`<option value="">${langText("Choisir fournisseur", "اختر المورد", "اختار لفرنسور")}</option>`].concat(rows.map((s) => `<option value="${esc(s.id)}">${esc(s.name)}</option>`)).join("");
  }
  if (currentPurchaseSupplier && rows.some((s) => String(s.id || "") === currentPurchaseSupplier)) {
    ui.purchaseSupplier.value = currentPurchaseSupplier;
  }
  if (typeof window.syncPurchaseSupplierSearchInput === "function") {
    window.syncPurchaseSupplierSearchInput();
  } else if (ui.purchaseSupplierSearch) {
    const selected = rows.find((s) => String(s.id || "") === String(ui.purchaseSupplier?.value || ""));
    ui.purchaseSupplierSearch.value = selected ? String(selected.name || "") : "";
  }
  if (currentDebtSupplier && rows.some((s) => String(s.id || "") === currentDebtSupplier)) {
    ui.purchaseDebtSupplier.value = currentDebtSupplier;
  }
  if (typeof window.syncPurchaseDebtSupplierSearchInput === "function") window.syncPurchaseDebtSupplierSearchInput();
  if (currentTraceSupplier && rows.some((s) => String(s.id || "") === currentTraceSupplier)) {
    ui.supplierTraceSupplier.value = currentTraceSupplier;
  }
  if (typeof window.syncSupplierTraceSupplierSearchInput === "function") window.syncSupplierTraceSupplierSearchInput();
  if (ui.supplierReturnSupplier && currentReturnSupplier && rows.some((s) => String(s.id || "") === currentReturnSupplier)) {
    ui.supplierReturnSupplier.value = currentReturnSupplier;
  }
  if (typeof window.syncSupplierReturnSupplierSearchInput === "function") window.syncSupplierReturnSupplierSearchInput();

  if (!opts.skipDependentRefresh) {
    await refreshPurchaseDebtInvoiceOptions(ui.purchaseDebtSupplier.value || "");
    await refreshSupplierTrace();
    await refreshSupplierReturnOptions();
  }
}

function normalizeRowsResponse(res) {
  const data = res && res.ok ? res.data : res;
  if (Array.isArray(data)) return data;
  if (data && Array.isArray(data.rows)) return data.rows;
  return [];
}

async function loadSupplierReturnPurchases(supplierId) {
  const sid = String(supplierId || "").trim();
  if (!sid || !window.wiliDb || typeof window.wiliDb.listPurchases !== "function") return [];
  const load = async (payload) => {
    try {
      return normalizeRowsResponse(await window.wiliDb.listPurchases(payload));
    } catch (_err) {
      return [];
    }
  };
  const rows = await load({ limit: 100000, supplier_id: sid });
  const fallbackRows = rows.length ? rows : await load(100000);
  const byId = new Map();
  fallbackRows.forEach((p) => {
    if (String(p && p.supplier_id || "") !== sid) return;
    const id = String(p && p.id || "");
    if (id) byId.set(id, p);
  });
  return Array.from(byId.values());
}

async function refreshSupplierReturnOptions() {
  if (!ui.supplierReturnSupplier || !ui.supplierReturnPurchase || !ui.supplierReturnProduct) return;
  const supplierId = String(ui.supplierReturnSupplier.value || "").trim();
  const currentPurchase = String(ui.supplierReturnPurchase.value || "").trim();
  const currentProduct = String(ui.supplierReturnProduct.value || "").trim();
  const supplierPurchases = await loadSupplierReturnPurchases(supplierId);
  ui.supplierReturnPurchase.innerHTML = [`<option value="">${langText("Retour general", "إرجاع عام", "ريطور عام")}</option>`]
    .concat(supplierPurchases.map((p) => `<option value="${esc(p.id)}">${esc(p.public_ref || purchasePublicRef(p.id, p.created_at) || p.id)} - ${money(p.total_cents || 0)}</option>`))
    .join("");
  if (currentPurchase && supplierPurchases.some((p) => String(p.id || "") === currentPurchase)) ui.supplierReturnPurchase.value = currentPurchase;
  await refreshSupplierReturnProductOptions(currentProduct);
}

async function refreshSupplierReturnProductOptions(preferredProductId = "") {
  if (!ui.supplierReturnPurchase || !ui.supplierReturnProduct) return;
  const purchaseId = String(ui.supplierReturnPurchase.value || "").trim();
  let rows = [];
  if (purchaseId && window.wiliDb && typeof window.wiliDb.getPurchaseDetails === "function") {
    const out = await window.wiliDb.getPurchaseDetails(purchaseId);
    const details = out && out.ok ? out.data : {};
    const returns = Array.isArray(details && details.returns) ? details.returns : [];
    rows = (Array.isArray(details && details.items) ? details.items : []).map((line) => {
      const returnedQty = returns
        .filter((r) => String(r.product_id || "") === String(line.product_id || ""))
        .reduce((sum, r) => sum + Math.max(0, Number(r.qty || 0)), 0);
      const qtyLeft = Math.max(0, Number(line.qty || 0) - returnedQty);
      const unit = Number(line.unit_cost_cents || (Number(line.line_total_cents || 0) / Math.max(1, Number(line.qty || 1))) || 0);
      return { id: String(line.product_id || ""), name: String(line.product_name || line.product_id || "-"), qty_left: qtyLeft, unit_cents: unit };
    }).filter((x) => x.id && x.qty_left > 0);
  } else {
    rows = (cache.products || []).map((p) => ({
      id: String(p.id || ""),
      name: String(p.name || "-"),
      qty_left: Number(p.stock_qty || 0),
      unit_cents: Number(p.last_cost_cents || p.cost_cents || 0)
    })).filter((x) => x.id);
  }
  ui.supplierReturnProduct.innerHTML = rows.length
    ? rows.map((p) => `<option value="${esc(p.id)}" data-unit="${Number(p.unit_cents || 0)}" data-left="${Number(p.qty_left || 0)}">${esc(p.name)}${purchaseId ? ` | ${Number(p.qty_left || 0)} max` : ` | stock ${Number(p.qty_left || 0)}`}</option>`).join("")
    : `<option value="">${langText("Aucun produit", "لا توجد منتجات", "ماكانش منتوج")}</option>`;
  if (preferredProductId && rows.some((p) => String(p.id) === String(preferredProductId))) ui.supplierReturnProduct.value = preferredProductId;
  const opt = ui.supplierReturnProduct.selectedOptions && ui.supplierReturnProduct.selectedOptions[0];
  if (ui.supplierReturnCost && opt && !String(ui.supplierReturnCost.value || "").trim()) ui.supplierReturnCost.value = (Number(opt.dataset.unit || 0) / 100).toFixed(2);
  if (typeof window.syncSupplierReturnProductSearchInput === "function") window.syncSupplierReturnProductSearchInput();
  if (ui.supplierReturnHint) {
    ui.supplierReturnHint.textContent = purchaseId
      ? langText("Retour lie au BR selectionne: quantite limitee aux lignes restantes.", "إرجاع مربوط بوصل الاستلام المختار: الكمية محدودة بالمتبقي.", "ريطور مربوط بالـ BR: الكمية محدودة بالباقي.")
      : langText("Retour general: le stock diminue et une entree caisse est creee.", "إرجاع عام: المخزون ينقص وتُسجل دخلة صندوق.", "ريطور عام: الستوك ينقص وتدخل caisse.");
  }
}

function addSupplierReturnLine() {
  const supplierId = String(ui.supplierReturnSupplier?.value || "").trim();
  const purchaseId = String(ui.supplierReturnPurchase?.value || "").trim();
  if (!supplierId) return appNotify(langText("Choisissez un fournisseur", "اختر المورد", "اختار لفرنسور"), true);
  const meta = selectedReturnProductMeta(ui.supplierReturnProduct, toCents(ui.supplierReturnCost?.value || 0));
  const qtyValue = qtyNum(ui.supplierReturnQty?.value || 0);
  if (!meta.product_id) return appNotify(langText("Choisissez un produit", "اختر المنتج", "اختار المنتوج"), true);
  if (qtyValue <= 0) return appNotify("return_qty_required", true);
  const sourceRef = ui.supplierReturnPurchase?.selectedOptions?.[0]?.textContent || "";
  const key = returnLineKey(purchaseId, meta.product_id);
  const max = Number(meta.max_qty || 0);
  const existing = supplierReturnCart.find((line) => line.key === key);
  const nextQty = (existing ? Number(existing.qty || 0) : 0) + qtyValue;
  if (max > 0 && nextQty > max) return appNotify(langText("Quantite retour superieure au reste/stock", "كمية الإرجاع أكبر من المتبقي/المخزون", "الكمية كثر من الباقي/الستوك"), true);
  if (existing) {
    existing.qty = nextQty;
    existing.unit_cents = toCents(ui.supplierReturnCost?.value || 0) || meta.unit_cents;
  } else {
    supplierReturnCart.push({
      key,
      purchase_id: purchaseId,
      supplier_id: supplierId,
      product_id: meta.product_id,
      product_name: meta.product_name,
      qty: qtyValue,
      max_qty: max,
      unit_cents: toCents(ui.supplierReturnCost?.value || 0) || meta.unit_cents,
      source_ref: purchaseId ? sourceRef : langText("Retour general", "إرجاع عام", "ريطور عام")
    });
  }
  renderReturnCart("supplier");
}

async function submitSupplierReturn() {
  const supplierId = String(ui.supplierReturnSupplier?.value || "").trim();
  const note = String(ui.supplierReturnNote?.value || "").trim();
  if (!supplierId) return appNotify(langText("Choisissez un fournisseur", "اختر المورد", "اختار لفرنسور"), true);
  if (!supplierReturnCart.length) return appNotify(langText("Ajoutez au moins une ligne", "أضف سطرا واحدا على الأقل", "زيد سطر واحد على الأقل"), true);
  const ok = await appConfirm(langText("Confirmer le retour fournisseur ?", "تأكيد إرجاع المورد؟", "تأكد ريطور لفرنسور؟"));
  if (!ok) return;
  for (const line of supplierReturnCart.slice()) {
    const payload = { product_id: line.product_id, qty: Number(line.qty || 0), note };
    const out = line.purchase_id
      ? await window.wiliDb.addPurchaseReturn({ ...payload, purchase_id: line.purchase_id })
      : await window.wiliDb.addPurchaseReturnGeneral({ ...payload, supplier_id: supplierId, unit_cost_cents: Math.max(0, Number(line.unit_cents || 0)) });
    if (!out || !out.ok) return appNotify(out && out.error ? out.error : "purchase_return_failed", true, 5000);
  }
  clearReturnCart("supplier");
  ui.supplierReturnQty.value = "1";
  ui.supplierReturnNote.value = "";
  ui.supplierReturnCost.value = "";
  await refreshAll();
  await refreshSupplierReturnOptions();
  appNotify(langText("Retour fournisseur enregistre", "تم تسجيل إرجاع المورد", "تسجل ريطور لفرنسور"), false);
}

async function refreshSupplierTrace() {
  const reqSeq = ++supplierTraceReqSeq;
  await refreshSuppliers({ skipDependentRefresh: true });
  if (reqSeq !== supplierTraceReqSeq) return;
  const sid = String(ui.supplierTraceSupplier.value || "").trim();
  const from = String(ui.supplierTraceFrom.value || "").trim();
  const to = String(ui.supplierTraceTo.value || "").trim();
  const type = String(ui.supplierTraceType.value || "").trim();
  const includePurchase = !type || type === "purchase";
  const includePayment = !type || type === "payment";
  const includeReturn = !type || type === "purchase_return";
  const needDetails = includePayment || includeReturn;
  const purchases = sid
    ? purchaseRows.filter((p) => String(p.supplier_id || "") === sid)
    : purchaseRows.slice();
  const paymentsByPurchase = new Map();
  const returnsByPurchase = new Map();
  const generalReturns = [];
  if (needDetails) {
    const ids = purchases.map((p) => String(p.id || "")).filter(Boolean);
    if (window.wiliDb && typeof window.wiliDb.getPurchaseTraceBulk === "function") {
      try {
        const supplierIds = sid
          ? [sid]
          : (cache.suppliers || []).map((s) => String(s.id || "")).filter(Boolean);
        const bulkOut = await window.wiliDb.getPurchaseTraceBulk({
          purchase_ids: ids,
          supplier_ids: supplierIds,
          include_payments: includePayment,
          include_returns: includeReturn
        });
        if (!bulkOut || !bulkOut.ok) throw new Error("purchase_trace_bulk_failed");
        const bulk = bulkOut.data || {};
        const pays = Array.isArray(bulk.payments) ? bulk.payments : [];
        const rets = Array.isArray(bulk.returns) ? bulk.returns : [];
        for (const p of pays) {
          const pid = String(p && p.purchase_id ? p.purchase_id : "");
          if (!pid) continue;
          if (!paymentsByPurchase.has(pid)) paymentsByPurchase.set(pid, []);
          paymentsByPurchase.get(pid).push(p);
        }
        for (const r of rets) {
          const pid = String(r && r.purchase_id ? r.purchase_id : "");
          if (!pid) {
            generalReturns.push(r);
            continue;
          }
          if (!returnsByPurchase.has(pid)) returnsByPurchase.set(pid, []);
          returnsByPurchase.get(pid).push(r);
        }
      } catch (_e) {
        await preloadDetailsInCache(
          ids,
          purchaseDetailsCache,
          (id) => window.wiliDb.getPurchaseDetails(id),
          () => reqSeq !== supplierTraceReqSeq
        );
      }
    } else {
      await preloadDetailsInCache(
        ids,
        purchaseDetailsCache,
        (id) => window.wiliDb.getPurchaseDetails(id),
        () => reqSeq !== supplierTraceReqSeq
      );
    }
    if (reqSeq !== supplierTraceReqSeq) return;
  }
  const events = [];
  for (const r of generalReturns) {
    const supplierId = String(r && r.supplier_id || "");
    if (!supplierId) continue;
    const supplier = (cache.suppliers || []).find((s) => String(s.id || "") === supplierId) || {};
    events.push({
      created_at: String(r.created_at || ""),
      type: "purchase_return",
      ref: "",
      public_ref: String(r.public_ref || ""),
      movement_id: String(r.id || ""),
      amount_cents: Number(r.amount_cents || r.total_cents || 0),
      refund_cents: Number(r.refund_cents || 0),
      invoice_total_cents: Number(r.amount_cents || r.total_cents || 0),
      paid_at_create_cents: 0,
      note: String(r.note || ""),
      trace_owner_id: supplierId,
      supplier_id: supplierId,
      supplier_name: String(supplier.name || r.supplier_name || "-")
    });
  }
  const dueSingle = sid ? Math.max(0, -supplierBalanceCents(sid)) : Math.max(0, -(cache.suppliers || []).reduce((sum, s) => sum + partyBalanceValue(s), 0));
  if (ui.supplierTraceDebt) {
    const balance = sid ? supplierBalanceCents(sid) : (cache.suppliers || []).reduce((sum, s) => sum + partyBalanceValue(s), 0);
    const sign = balance > 0 ? "+" : "";
    ui.supplierTraceDebt.textContent = sid
      ? langText(`Solde/Avoir fournisseur: ${sign}${money(balance)}`, `رصيد/أفوار المورد: ${sign}${money(balance)}`, `رصيد/أفوار لفرنسور: ${sign}${money(balance)}`)
      : langText(`Solde/Avoir fournisseurs: ${sign}${money(balance)}`, `رصيد/أفوار الموردين: ${sign}${money(balance)}`, `رصيد/أفوار لفرنسور: ${sign}${money(balance)}`);
    ui.supplierTraceDebt.classList.toggle("ok", balance >= 0);
    ui.supplierTraceDebt.classList.toggle("bad", balance < 0);
  }
  for (const p of purchases) {
    const supplierId = String(p.supplier_id || "");
    const supplierName = String(p.supplier_name || "");
    const purchaseId = String(p.id || "");
    const traceOwnerId = supplierId || `purchase:${purchaseId}`;
    const purchaseTotalCents = Math.max(0, Number(p.total_cents || 0));
    const purchasePaidCents = Math.max(0, Number(p.paid_cents || 0));
    if (includePurchase) {
      events.push({
        created_at: String(p.created_at || ""),
        type: "purchase",
        ref: purchaseId,
        public_ref: String(p.public_ref || purchasePublicRef(purchaseId, p.created_at) || ""),
        amount_cents: purchaseTotalCents,
        invoice_total_cents: purchaseTotalCents,
        paid_at_create_cents: purchasePaidCents,
        note: "",
        trace_owner_id: traceOwnerId,
        supplier_id: supplierId,
        supplier_name: supplierName
      });
    }
    if (needDetails) {
      const pid = purchaseId;
      const details = purchaseDetailsCache.get(pid);
      const payments = includePayment
        ? (paymentsByPurchase.get(pid) || (Array.isArray(details && details.payments) ? details.payments : []))
        : [];
      const returns = includeReturn
        ? (returnsByPurchase.get(pid) || (Array.isArray(details && details.returns) ? details.returns : []))
        : [];
      for (const x of payments) {
        events.push({
          created_at: String(x.created_at || ""),
          type: "payment",
          ref: pid,
          public_ref: String(x.public_ref || ""),
          movement_id: String(x.id || ""),
          amount_cents: Number(x.amount_cents || 0),
          refund_cents: Number(x.refund_cents || 0),
          invoice_total_cents: Number(x.invoice_total_cents || purchaseTotalCents),
          due_before_cents: Number(x.due_before_cents),
          due_after_cents: Number(x.due_after_cents),
          paid_at_create_cents: 0,
          note: String(x.note || ""),
          trace_owner_id: traceOwnerId,
          supplier_id: supplierId,
          supplier_name: supplierName
        });
      }
      for (const x of returns) {
        events.push({
          created_at: String(x.created_at || ""),
          type: "purchase_return",
          ref: pid,
          public_ref: String(x.public_ref || ""),
          movement_id: String(x.id || ""),
          amount_cents: Number(x.amount_cents || 0),
          invoice_total_cents: Number(x.invoice_total_cents || purchaseTotalCents),
          due_before_cents: Number(x.due_before_cents),
          due_after_cents: Number(x.due_after_cents),
          paid_at_create_cents: 0,
          note: String(x.note || ""),
          trace_owner_id: traceOwnerId,
          supplier_id: supplierId,
          supplier_name: supplierName
        });
      }
    }
  }
  if (includePayment) {
    try {
      events.push(...await loadFinanceTraceEvents("supplier", sid));
    } catch (_e) {}
  }
  if (reqSeq !== supplierTraceReqSeq) return;
  const withRunning = attachTraceDebtRunning(events, "trace_owner_id", supplierTraceDeltaCents);
  const filtered = withRunning.filter((e) => {
    const day = toDay(e.created_at);
    if (type && e.type !== type) return false;
    if (from && (!day || day < from)) return false;
    if (to && (!day || day > to)) return false;
    return true;
  });
  supplierTraceRows = filtered;
  supplierTraceViewState.showAllSuppliers = !sid;
  supplierTraceViewState.dueSingle = dueSingle;
  const dueMap = new Map();
  if (!sid) {
    for (const s of cache.suppliers || []) {
      const supplierId = String(s.id || "");
      if (!supplierId) continue;
      const dueSupplier = purchaseRows
        .filter((x) => String(x.supplier_id || "") === supplierId)
        .reduce((sum, x) => sum + Math.max(0, Number(x.due_cents || 0)), 0);
      dueMap.set(supplierId, dueSupplier);
    }
  }
  supplierTraceViewState.dueMap = dueMap;
  renderSupplierTraceTable();
}
