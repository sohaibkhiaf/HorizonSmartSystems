function applyPurchasesJournalFilters(resetPage = true) {
  const allowDelete = isAdminUser();
  const q = String(ui.purchasesHistSearch?.value || "").trim().toLowerCase();
  const from = String(ui.purchasesHistFrom?.value || "").trim();
  const to = String(ui.purchasesHistTo?.value || "").trim();
  const filtered = purchaseRows.filter((p) => {
    const day = String(p.created_at || "").slice(0, 10);
    const inFrom = !from || day >= from;
    const inTo = !to || day <= to;
    const byQ = !q
      || String(p.id || "").toLowerCase().includes(q)
      || purchasePublicRef(p.id, p.created_at).toLowerCase().includes(q)
      || String(p.supplier_name || "").toLowerCase().includes(q);
    return inFrom && inTo && byQ;
  });
  purchaseRowsFiltered = sortPurchasesRows(filtered);
  if (resetPage) purchasesHistPage = 1;
  const pageSize = Math.max(1, intQty(ui.purchasesHistPageSize?.value || 20));
  const totalPages = Math.max(1, Math.ceil(purchaseRowsFiltered.length / pageSize));
  purchasesHistPage = Math.min(totalPages, Math.max(1, purchasesHistPage));
  const start = (purchasesHistPage - 1) * pageSize;
  const pageRows = purchaseRowsFiltered.slice(start, start + pageSize);
  const total = purchaseRowsFiltered.reduce((sum, x) => sum + Number(x.total_cents || 0), 0);
  const due = purchaseRowsFiltered.reduce((sum, x) => sum + Number(x.due_cents || 0), 0);
  ui.purchaseKpiCount.textContent = String(purchaseRowsFiltered.length);
  ui.purchaseKpiTotal.textContent = money(total);
  ui.purchaseKpiDue.textContent = money(due);
  if (ui.purchasesHistPageInfo) {
    ui.purchasesHistPageInfo.textContent = langText(`Page ${purchasesHistPage}/${totalPages}`, `صفحة ${purchasesHistPage}/${totalPages}`);
  }
  ui.purchasesJournalTableBody.innerHTML = pageRows.length ? pageRows.map((r) => `
    <tr data-purchase-row="${esc(r.id)}" class="${String(r.id || "") === String(purchaseSelectedId || "") ? "row-selected" : ""}">
      <td><strong>${esc(purchasePublicRef(r.id, r.created_at))}</strong></td>
      <td>${esc(fmtDateTime(r.created_at))}</td>
      <td>${esc(r.supplier_name || "-")}</td>
      <td>${money(r.total_cents)}</td>
      <td>${money(r.paid_cents)}</td>
      <td>${money(r.due_cents)}</td>
      <td>${Number(r.due_cents || 0) > 0 ? trText("Credit") : trText("Solde")}</td>
      <td>
        <div class="qty-actions">
          <button class="btn-mini" data-purchase-details="${esc(r.id)}">${langText("Ouvrir", "فتح", "حل")}</button>
          <button class="btn-mini" data-purchase-return-all="${esc(r.id)}">${esc(langText("Retour Fournisseur", "\u0625\u0631\u062c\u0627\u0639 \u0645\u0648\u0631\u062f", "\u0631\u0648\u062a\u0648\u0631 \u0641\u0631\u0646\u0633\u0648\u0631"))}</button>
          <button class="btn-mini" data-purchase-pdf="${esc(r.id)}">${esc(langText("Bon de Reception", "\u0648\u0635\u0644 \u0627\u0633\u062a\u0644\u0627\u0645", "\u0628\u0648\u0646 \u0631\u064a\u0633\u0628\u0633\u064a\u0648\u0646"))}</button>
          <button class="btn-mini" data-purchase-facture-pdf="${esc(r.id)}">${esc(langText("Facture D'Achat", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621"))}</button>
          ${allowDelete ? `<button class="btn-mini" data-purchase-edit="${esc(r.id)}">${langText("Modifier", "تعديل", "بدل")}</button>` : ""}
          ${allowDelete ? `<button class="btn-mini" data-purchase-del="${esc(r.id)}">${langText("Supprimer", "حذف", "حذف")}</button>` : ""}
        </div>
      </td>
    </tr>
  `).join("") : `<tr><td colspan="8" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.purchasesJournalTable, purchasesJournalState.sortKey || "created_at", purchasesJournalState.sortDir || "desc");
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-row]").forEach((el) => {
    el.addEventListener("click", () => {
      purchaseSelectedId = String(el.getAttribute("data-purchase-row") || "");
      applyPurchasesJournalFilters(false);
      void renderPurchaseSelectedDetail();
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-details]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const pid = String(btn.getAttribute("data-purchase-details") || "");
      purchaseSelectedId = pid;
      applyPurchasesJournalFilters(false);
      await openPurchaseDetailModal(pid);
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const sid = String(btn.getAttribute("data-purchase-pdf") || "");
      if (!sid) return;
      await exportPurchaseInvoicePdfById(sid, ui.purchasesJournalTableBody, "bon");
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-facture-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const sid = String(btn.getAttribute("data-purchase-facture-pdf") || "");
      if (!sid) return;
      await exportPurchaseInvoicePdfById(sid, ui.purchasesJournalTableBody, "facture");
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-return-all]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const purchaseId = String(btn.getAttribute("data-purchase-return-all") || "");
      if (!purchaseId) return;
      const ok = await appConfirm(
        langText(
          "Confirmer retour complet de cette facture ?",
          "تأكيد إرجاع الفاتورة كاملة؟",
          "تأكد بلي ترجع كامل هاد الفاتورة؟"
        )
      );
      if (!ok) return;
      const out = await window.wiliDb.addPurchaseReturnAll({
        purchase_id: purchaseId,
        note: langText("retour facture complete", "ارجاع فاتورة كاملة", "رتور فاكتور كاملة")
      });
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "purchase_return_failed", true, 5000);
        return;
      }
      const count = Array.isArray(out.data && out.data.returns) ? out.data.returns.length : 0;
      appNotify(
        langText(
          `Retour complet effectue (${count} ligne)`,
          `تم إرجاع كامل الفاتورة (${count} سطر)`,
          `تم رتور كامل للفاكتور (${count} سطر)`
        ),
        false,
        3500
      );
      if (String(purchaseSelectedId || "") === purchaseId) {
        purchaseDetailsCache.delete(purchaseId);
      }
      await refreshAll();
      applyPurchasesJournalFilters(false);
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-edit]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const purchaseId = String(btn.getAttribute("data-purchase-edit") || "");
      if (!purchaseId) return;
      await startPurchaseEdit(purchaseId);
    });
  });
  ui.purchasesJournalTableBody.querySelectorAll("[data-purchase-del]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const purchaseId = String(btn.getAttribute("data-purchase-del") || "");
      if (!purchaseId) return;
      const ok = await appConfirm(langText("Confirmer suppression de cet achat ?", "تأكيد حذف هذا الشراء؟", "تأكد بلي تحذف هاذ الشرا؟"));
      if (!ok) return;
      const out = await window.wiliDb.deletePurchase(purchaseId);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true);
        return;
      }
      if (String(purchaseSelectedId || "") === purchaseId) purchaseSelectedId = "";
      await refreshAll();
      applyPurchasesJournalFilters(false);
      appNotify(langText("Suppression terminee", "تم الحذف", "تم الحذف"), false);
    });
  });
}

async function renderPurchaseSelectedDetail() {
  if (!purchaseSelectedId) {
    ui.purchaseDetailBox.innerHTML = row("Selection", "Choisir un achat");
    return;
  }
  let details = purchaseDetailsCache.get(purchaseSelectedId);
  if (!details) {
    const out = await window.wiliDb.getPurchaseDetails(purchaseSelectedId);
    if (!out || !out.ok) {
      ui.purchaseDetailBox.innerHTML = row("Erreur", out && out.error ? out.error : "purchase_details_failed");
      return;
    }
    details = out.data;
    purchaseDetailsCache.set(purchaseSelectedId, details);
  }
  const inv = details.invoice || {};
  const lines = Array.isArray(details.items) ? details.items : [];
  const payments = Array.isArray(details.payments) ? details.payments : [];
  const returns = Array.isArray(details.returns) ? details.returns : [];
  const returnedByProduct = new Map();
  for (const r of returns) {
    const pid = String(r && r.product_id ? r.product_id : "");
    if (!pid) continue;
    returnedByProduct.set(pid, qtyNum((returnedByProduct.get(pid) || 0) + Math.max(0, qtyNum(r && r.qty))));
  }
  const rowsHtml = lines.map((it) => `
    <tr>
      <td>${esc(it.product_name || it.product_id || "-")}</td>
      <td>${qtyText(it.qty)}</td>
      <td>${qtyText(returnedByProduct.get(String(it.product_id || "")) || 0)}</td>
      <td>${qtyText(Math.max(0, qtyNum(it.qty) - qtyNum(returnedByProduct.get(String(it.product_id || "")) || 0)))}</td>
      <td>${money(it.unit_cost_cents)}</td>
      <td>${money(it.line_total_cents)}</td>
      <td><button class="btn-mini" data-open-product-p="${esc(it.product_id)}">H</button> <button class="btn-mini" data-purchase-return="${esc(it.product_id)}">Retour</button></td>
    </tr>
  `).join("");
  const payRows = payments.map((p) => `
    <tr>
      <td>${esc(fmtDateTime(p.created_at))}</td>
      <td>${money(p.amount_cents)}</td>
      <td>${esc(p.note || "-")}</td>
      <td><button class="btn-mini" data-purchase-pay-pdf="${esc(p.id)}">Telecharger</button></td>
    </tr>
  `).join("");
  const returnRows = returns.map((r) => `
    <tr>
      <td>${esc(r.public_ref || "-")}</td>
      <td>${esc(fmtDateTime(r.created_at))}</td>
      <td>${esc(r.product_name || r.product_id || "-")}</td>
      <td>${qtyText(r.qty)}</td>
      <td>${money(r.amount_cents)}</td>
      <td>${money(r.refund_cents)}</td>
      <td>${esc(r.note || "-")}</td>
    </tr>
  `).join("");
  ui.purchaseDetailBox.innerHTML = [
    detailSummaryGrid([
      { label: "Achat", value: purchasePublicRef(inv.id, inv.created_at), accent: true },
      { label: "Type", value: String(inv.doc_type || "BR") },
      { label: "Date", value: fmtDateTime(inv.created_at || "") },
      { label: "Fournisseur", value: (details.supplier && details.supplier.name) ? details.supplier.name : "-" },
      { label: "Total", value: money(inv.total_cents || 0), accent: true },
      { label: "Paye", value: money(inv.paid_cents || 0) },
      { label: "Reste", value: money(inv.due_cents || 0) }
    ]),
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Lignes achat")}</div><table class="table-lite"><thead><tr><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${esc(langText("Qte retournee", "الكمية المرجعة", "لكمية لمرجعة"))}</th><th>${esc(langText("Qte restante", "الكمية المتبقية", "لكمية لي باقية"))}</th><th>${trText("Cout")}</th><th>${trText("Total")}</th><th>${trText("Hist")}</th></tr></thead><tbody>${rowsHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Paiements")}</div><table class="table-lite"><thead><tr><th>${trText("Date")}</th><th>${trText("Montant")}</th><th>${trText("Note")}</th><th>${trText("Recu")}</th></tr></thead><tbody>${payRows || `<tr><td colspan="4" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Retours fournisseur")}</div><table class="table-lite"><thead><tr><th>${trText("Ref")}</th><th>${trText("Date")}</th><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${trText("Montant")}</th><th>${trText("Rembourse")}</th><th>${trText("Note")}</th></tr></thead><tbody>${returnRows || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`
  ].join("");
  ui.purchaseDetailBox.querySelectorAll("[data-open-product-p]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const pid = String(btn.getAttribute("data-open-product-p") || "");
      if (!pid) return;
      switchView("history");
      refreshHistory(pid);
    });
  });
  ui.purchaseDetailBox.querySelectorAll("[data-purchase-pay-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const pid = String(btn.getAttribute("data-purchase-pay-pdf") || "");
      if (!pid) return;
      const out = await window.wiliDb.exportPurchasePaymentReceiptPdf(pid);
      handlePdfResult(out, langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص"), ui.purchaseDetailBox);
    });
  });
  ui.purchaseDetailBox.querySelectorAll("[data-purchase-return]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const productId = String(btn.getAttribute("data-purchase-return") || "");
      if (!productId || !purchaseSelectedId) return;
      const payload = await appAskReturnPayload({
        title: langText("Retour fournisseur", "ارجاع مورد", "رتور لفرنسور"),
        defaultQty: 1,
        defaultNote: ""
      });
      if (!payload) return;
      const returnQty = Math.max(0.01, qtyNum(payload.qty || 1, 1));
      const note = String(payload.note || "").trim();
      const out = await window.wiliDb.addPurchaseReturn({
        purchase_id: purchaseSelectedId,
        product_id: productId,
        qty: returnQty,
        note
      });
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "purchase_return_failed", true, 5000);
        return;
      }
      purchaseDetailsCache.delete(String(purchaseSelectedId || ""));
      await refreshAll();
      await renderPurchaseSelectedDetail();
    });
  });
}

async function refreshPurchaseDebtInvoiceOptions(supplierId = "") {
  const sid = String(supplierId || ui.purchaseDebtSupplier.value || "").trim();
  const out = await window.wiliDb.listPurchaseDues(sid);
  const rows = out && out.ok ? (out.data || []) : [];
  const options = [`<option value="">${trText("Choisir facture credit")}</option>`].concat(
    rows.map((x) => `<option value="${esc(x.id)}">${esc(purchasePublicRef(x.id, x.created_at, rows))} | ${esc(fmtDateTime(x.created_at))} | ${money(x.due_cents)} | ${esc(x.supplier_name || "-")}</option>`)
  );
  const selected = String(ui.purchaseDebtInvoice.value || "");
  ui.purchaseDebtInvoice.innerHTML = options.join("");
  if (selected && rows.some((x) => String(x.id || "") === selected)) ui.purchaseDebtInvoice.value = selected;
  const chosen = rows.find((x) => String(x.id || "") === String(ui.purchaseDebtInvoice.value || ""));
  if (chosen) ui.purchaseDebtAmount.value = money(chosen.due_cents);

  if (ui.purchaseDebtTableBody) {
    ui.purchaseDebtTableBody.innerHTML = rows.length ? rows.map((x) => {
      const due = Number(x.due_cents || 0);
      const status = due <= 0 ? langText("Regle", "مسدد", "مسدد") : langText("Partiel", "جزئي", "جزئي");
      return `
        <tr>
          <td>${esc(purchasePublicRef(x.id, x.created_at, rows))}</td>
          <td>${esc(fmtDateTime(x.created_at))}</td>
          <td>${esc(x.supplier_name || "-")}</td>
          <td>${money(x.total_cents || 0)}</td>
          <td>${money(x.paid_cents || 0)}</td>
          <td class="${due > 0 ? "neg" : "pos"}">${money(due)}</td>
          <td>${esc(status)}</td>
          <td><button class="btn-mini" data-pd-select="${esc(x.id)}">${langText("Choisir", "اختيار", "اختار")}</button></td>
        </tr>
      `;
    }).join("") : `<tr><td colspan="8" class="empty">${t("no_data")}</td></tr>`;

    ui.purchaseDebtTableBody.querySelectorAll("[data-pd-select]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const pid = String(btn.getAttribute("data-pd-select") || "");
        if (!pid) return;
        ui.purchaseDebtInvoice.value = pid;
        const inv = rows.find((x) => String(x.id || "") === pid);
        if (inv) {
          ui.purchaseDebtAmount.value = money(inv.due_cents || 0);
          purchaseSelectedId = pid;
        }
      });
    });
  }
}

function updatePurchaseComputed() {
  const totalCents = (purchaseCart || []).reduce((sum, line) => sum + Math.max(0, Number(line.line_total_cents || 0)), 0);
  const isMiscPurchase = !String(ui.purchaseSupplier?.value || "").trim();
  if (ui.purchasePaid) {
    if (isMiscPurchase) {
      ui.purchasePaid.value = money(totalCents);
      purchasePaidManual = false;
    } else if (purchasePaidAutoFull) {
      ui.purchasePaid.value = money(totalCents);
      purchasePaidManual = false;
    } else if (!purchasePaidManual) {
      ui.purchasePaid.value = "0.00";
    }
    ui.purchasePaid.disabled = isMiscPurchase;
  }
  if (ui.purchasePaidAutoFull) ui.purchasePaidAutoFull.disabled = isMiscPurchase;
  const paidCents = Math.max(0, toCents(ui.purchasePaid?.value || 0));
  const dueCents = Math.max(0, totalCents - paidCents);
  if (ui.purchaseGrandTotal) ui.purchaseGrandTotal.textContent = money(totalCents);
  if (ui.purchaseDueTotal) ui.purchaseDueTotal.textContent = money(dueCents);
  if (ui.purchaseDueTotal) ui.purchaseDueTotal.classList.toggle("neg", dueCents > 0);
  if (ui.purchaseDueTotal) ui.purchaseDueTotal.classList.toggle("pos", dueCents <= 0);
  if (ui.purchaseDueLabel) ui.purchaseDueLabel.classList.toggle("neg", dueCents > 0);
  if (ui.purchaseDueLabel) ui.purchaseDueLabel.classList.toggle("pos", dueCents <= 0);
  if (ui.purchaseSaveBtn) ui.purchaseSaveBtn.disabled = !(purchaseCart && purchaseCart.length);
}

async function setPurchasePaidAutoFull(nextValue, save = true) {
  purchasePaidAutoFull = Boolean(nextValue);
  if (ui.purchasePaidAutoFull) ui.purchasePaidAutoFull.checked = purchasePaidAutoFull;
  purchasePaidManual = false;
  updatePurchaseComputed();
  if (save && window.wiliDb && typeof window.wiliDb.updateSettings === "function") {
    try {
      await window.wiliDb.updateSettings({ purchase_paid_auto_full: purchasePaidAutoFull });
    } catch (_err) {}
  }
}

async function resolvePurchaseProductFromInput() {
  const isPurchasableProduct = (product) => product && String(product.product_type || "product").toLowerCase() !== "service";
  const rememberProduct = (product) => {
    if (!product || !product.id) return;
    const idx = cache.products.findIndex((p) => String(p.id || "") === String(product.id || ""));
    if (idx >= 0) cache.products[idx] = { ...cache.products[idx], ...product };
    else cache.products.push(product);
  };
  const selectedId = String(ui.purchaseProduct.value || "").trim();
  if (selectedId) {
    const selected = cache.products.find((x) => String(x.id) === selectedId);
    if (isPurchasableProduct(selected)) return selected;
  }
  const raw = String(ui.purchaseProductSearch.value || "").trim();
  if (raw) {
    const parts = raw.split("|").map((part) => String(part || "").trim()).filter(Boolean);
    const candidates = [raw].concat(parts).filter((value, idx, arr) => arr.indexOf(value) === idx);
    for (const candidate of candidates) {
      const exactOut = await window.wiliDb.findProductByLookup(candidate);
      if (exactOut && exactOut.ok && exactOut.data) {
        const exact = exactOut.data;
        rememberProduct(exact);
        if (isPurchasableProduct(exact)) return exact;
      }
    }
    for (const candidate of candidates) {
      const searchOut = await window.wiliDb.searchProducts(candidate, 12);
      const rows = searchOut && searchOut.ok ? (searchOut.data || []) : [];
      rows.forEach(rememberProduct);
      const firstPurchasable = rows.find(isPurchasableProduct);
      if (firstPurchasable) return firstPurchasable;
    }
    return null;
  }
  return null;
}

function showPurchaseInlineCreate(seedName = "", seedBarcode = "") {
  ui.purchaseInlineCreate.classList.remove("hidden");
  ui.purchaseNewName.value = seedName || ui.purchaseProductSearch.value.split("|")[0].trim();
  ui.purchaseNewBarcode.value = seedBarcode || "";
  if (!ui.purchaseNewCost.value) ui.purchaseNewCost.value = "0";
  if (!ui.purchaseNewWholesale.value) ui.purchaseNewWholesale.value = "0";
  if (!ui.purchaseNewRetail.value) ui.purchaseNewRetail.value = "0";
  if (!ui.purchaseNewAssist.value) ui.purchaseNewAssist.value = "0";
  ui.purchaseNewName.focus();
}

function addPurchaseLine(productId, qtyToAdd = 1) {
  const p = cache.products.find((x) => String(x.id) === String(productId || ""));
  if (!p) return;
  if (String(p.product_type || "product").toLowerCase() === "service") {
    appNotify(langText("Un service ne peut pas etre achete", "الخدمة لا يمكن شراؤها", "السرفيس ما يتشراش"), true, 3000);
    return;
  }
  const qtyAdd = Math.max(0.01, qtyNum(qtyToAdd, 1));
  const unit = Math.max(0, Number(p.cost_cents || 0));
  const existing = purchaseCart.find((x) => String(x.product_id || "") === String(p.id));
  if (existing) {
    existing.qty += qtyAdd;
    existing.unit_cost_cents = unit;
    existing.line_total_cents = Math.round(existing.qty * existing.unit_cost_cents);
  } else {
    purchaseCart.push({
      product_id: p.id,
      name: p.name,
      qty: qtyAdd,
      unit_cost_cents: unit,
      line_total_cents: Math.round(qtyAdd * unit)
    });
  }
  renderPurchaseCart();
  updatePurchaseComputed();
  ui.purchaseProductSearch.value = "";
  ui.purchaseProduct.value = "";
  setTimeout(() => ui.purchaseProductSearch?.focus(), 0);
}

function changePurchaseLineQty(productId, delta) {
  const line = purchaseCart.find((x) => String(x.product_id || "") === String(productId || ""));
  if (!line) return;
  line.qty = qtyNum(qtyNum(line.qty) + delta);
  if (line.qty <= 0) {
    const idx = purchaseCart.findIndex((x) => String(x.product_id || "") === String(productId || ""));
    if (idx >= 0) purchaseCart.splice(idx, 1);
  } else {
    line.line_total_cents = Math.round(qtyNum(line.qty) * Number(line.unit_cost_cents || 0));
  }
  renderPurchaseCart();
  updatePurchaseComputed();
}

function removePurchaseLine(productId) {
  const idx = purchaseCart.findIndex((x) => String(x.product_id || "") === String(productId || ""));
  if (idx >= 0) purchaseCart.splice(idx, 1);
  renderPurchaseCart();
  updatePurchaseComputed();
}

function clearPurchaseCart() {
  purchaseCart.splice(0, purchaseCart.length);
  purchasePaidManual = false;
  resetPurchaseEditMode();
  renderPurchaseCart();
  updatePurchaseComputed();
}

function setPurchaseEditMode(purchaseId = "", purchaseRef = "") {
  editingPurchaseId = String(purchaseId || "");
  editingPurchaseRef = String(purchaseRef || "");
  if (ui.purchaseSaveBtn) {
    ui.purchaseSaveBtn.textContent = editingPurchaseId
      ? langText("Enregistrer modification", "حفظ التعديل", "سجل التبديل")
      : langText("Enregistrer achat", "تسجيل الشراء", "سجل الشرا");
  }
}

function resetPurchaseEditMode() {
  setPurchaseEditMode("", "");
}

async function startPurchaseEdit(purchaseId) {
  const id = String(purchaseId || "");
  if (!id) return;
  const out = await window.wiliDb.getPurchaseDetails(id);
  if (!out || !out.ok) {
    appNotify(out && out.error ? out.error : "purchase_details_failed", true, 5000);
    return;
  }
  const details = out.data || {};
  const returns = Array.isArray(details.returns) ? details.returns : [];
  if (returns.length) {
    appNotify(langText(
      "Cet achat contient deja des retours. Utilisez le retour fournisseur au lieu de modifier.",
      "هذا الشراء فيه إرجاعات. استعمل إرجاع المورد بدل التعديل.",
      "هاد الشرا فيه رتور. استعمل رتور فورنيسور بلا ما تبدل."
    ), true, 6000);
    return;
  }
  const inv = details.invoice || {};
  const items = Array.isArray(details.items) ? details.items : [];
  purchaseCart.splice(0, purchaseCart.length);
  for (const it of items) {
    const pid = String(it.product_id || "");
    if (!pid) continue;
    const existing = cache.products.find((p) => String(p.id || "") === pid) || {};
    const restoredStock = Number(existing.stock_qty || 0) - qtyNum(it.qty || 0);
    const product = {
      ...existing,
      id: pid,
      name: it.product_name || existing.name || pid,
      stock_qty: restoredStock,
      cost_cents: Number(it.unit_cost_cents || existing.cost_cents || 0)
    };
    const idx = cache.products.findIndex((p) => String(p.id || "") === pid);
    if (idx >= 0) cache.products[idx] = product;
    else cache.products.push(product);
    purchaseCart.push({
      product_id: pid,
      name: product.name,
      qty: qtyNum(it.qty || 0, 1),
      unit_cost_cents: Number(it.unit_cost_cents || 0),
      line_total_cents: Number(it.line_total_cents || 0)
    });
  }
  if (ui.purchaseSupplier) ui.purchaseSupplier.value = String(inv.supplier_id || "");
  if (typeof window.syncPurchaseSupplierSearchInput === "function") window.syncPurchaseSupplierSearchInput();
  if (ui.purchaseDocType) ui.purchaseDocType.value = String(inv.doc_type || "BR").toUpperCase() === "FC" ? "FC" : "BR";
  if (ui.purchasePaid) {
    ui.purchasePaid.value = money(inv.paid_cents || 0);
    purchasePaidManual = true;
  }
  setPurchaseEditMode(id, purchasePublicRef(inv.id || id, inv.created_at));
  renderPurchaseCart();
  updatePurchaseComputed();
  switchView("purchases");
  appNotify(langText(
    `Modification achat ${editingPurchaseRef}`,
    `تعديل الشراء ${editingPurchaseRef}`,
    `تبديل الشرا ${editingPurchaseRef}`
  ), false, 3500);
  setTimeout(() => ui.purchaseProductSearch?.focus(), 0);
}

window.startPurchaseEdit = startPurchaseEdit;

function setPurchaseLineQty(productId, value) {
  const line = purchaseCart.find((x) => String(x.product_id || "") === String(productId || ""));
  if (!line) return;
  const nextQty = Math.max(0, qtyNum(value, 0));
  if (nextQty <= 0) {
    removePurchaseLine(productId);
    return;
  }
  line.qty = nextQty;
  line.line_total_cents = Math.round(qtyNum(line.qty) * Number(line.unit_cost_cents || 0));
  renderPurchaseCart();
  updatePurchaseComputed();
}

function openPurchaseLineProductEditor(productId) {
  const pid = String(productId || "");
  const product = (cache.products || []).find((p) => String(p.id || "") === pid);
  if (!product) {
    notifyError(langText("Produit introuvable", "المنتج غير موجود", "المنتوج ما لڨيناهش"));
    return;
  }
  ui.productId.value = product.id;
  ui.productName.value = product.name || "";
  ui.productBarcode.value = product.barcode || "";
  if (ui.productReference) ui.productReference.value = product.reference || "";
  ui.productCost.value = money(product.cost_cents || 0);
  ui.productWholesale.value = money(product.wholesale_price_cents || 0);
  ui.productRetail.value = money(product.retail_price_cents || product.price_cents || 0);
  ui.productAssist.value = money(product.assisted_price_cents || product.retail_price_cents || 0);
  ui.productStock.value = qtyText(product.stock_qty);
  ui.productStock.disabled = true;
  ui.productExpiry.value = product.expiry_date ? String(product.expiry_date).slice(0, 10) : "";
  if (ui.productModalTitle) ui.productModalTitle.textContent = langText("Modifier produit", "تعديل منتج", "بدّل منتوج");
  ui.productSubmitBtn.textContent = langText("Mettre a jour", "تحديث المنتج", "بدّل المنتوج");
  ui.productCancelBtn.classList.remove("hidden");
  openProductModal();
  setTimeout(() => ui.productCost?.focus(), 0);
}

function refreshPurchaseCartProductData(productId = "") {
  const productMap = new Map((cache.products || []).map((p) => [String(p.id || ""), p]));
  let changed = false;
  purchaseCart.forEach((line) => {
    const pid = String(line.product_id || "");
    if (productId && pid !== String(productId)) return;
    const product = productMap.get(pid);
    if (!product) return;
    line.name = product.name || line.name;
    line.unit_cost_cents = Math.max(0, Number(product.cost_cents || 0));
    line.line_total_cents = Math.round(qtyNum(line.qty) * Number(line.unit_cost_cents || 0));
    changed = true;
  });
  if (changed) {
    renderPurchaseCart();
    updatePurchaseComputed();
  }
}

function renderPurchaseCart() {
  if (!purchaseCart.length) {
    ui.purchaseCartList.innerHTML = `<tr><td colspan="5" class="empty">${t("no_data")}</td></tr>`;
    return;
  }
  ui.purchaseCartList.innerHTML = purchaseCart.map((line) => `
    <tr>
      <td><strong>${esc(line.name || "-")}</strong></td>
      <td>
        <input
          type="number"
          min="0.01"
          step="0.01"
          class="cart-cell-input table-qty-input"
          value="${esc(qtyText(line.qty))}"
          data-pc-qty="${esc(line.product_id)}"
        >
      </td>
      <td data-pc-unit="${esc(line.product_id)}">
        <div class="qty-actions">
          <span>${money(line.unit_cost_cents)}</span>
          <button type="button" class="btn-mini" data-pc-edit-product="${esc(line.product_id)}">...</button>
        </div>
      </td>
      <td>${money(line.line_total_cents)}</td>
      <td>
        <div class="qty-actions">
          <button class="btn-mini" data-pc-del="${esc(line.product_id)}">X</button>
        </div>
      </td>
    </tr>
  `).join("");
  ui.purchaseCartList.querySelectorAll("[data-pc-qty]").forEach((input) => {
    const commit = () => setPurchaseLineQty(String(input.getAttribute("data-pc-qty") || ""), input.value);
    input.addEventListener("change", commit);
    input.addEventListener("blur", commit);
    input.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        commit();
        input.select();
      }
    });
  });
  ui.purchaseCartList.querySelectorAll("[data-pc-edit-product]").forEach((btn) => btn.addEventListener("click", () => openPurchaseLineProductEditor(String(btn.getAttribute("data-pc-edit-product") || ""))));
  ui.purchaseCartList.querySelectorAll("[data-pc-unit]").forEach((cell) => cell.addEventListener("dblclick", () => openPurchaseLineProductEditor(String(cell.getAttribute("data-pc-unit") || ""))));
  ui.purchaseCartList.querySelectorAll("[data-pc-del]").forEach((btn) => btn.addEventListener("click", () => removePurchaseLine(String(btn.getAttribute("data-pc-del") || ""))));
}

function sortFinanceRows(rows = []) {
  const key = String(financeJournalState.sortKey || "created_at");
  const dir = String(financeJournalState.sortDir || "desc") === "asc" ? 1 : -1;
  return [...rows].sort((a, b) => {
    const av = a && a[key] != null ? a[key] : "";
    const bv = b && b[key] != null ? b[key] : "";
    if (["amount_cents", "qty_total"].includes(key)) return (Number(av || 0) - Number(bv || 0)) * dir;
    return String(av || "").localeCompare(String(bv || ""), undefined, { numeric: true, sensitivity: "base" }) * dir;
  });
}

function fillFinancePartyOptions(rows = []) {
  if (!ui.financeParty) return;
  const selected = String(ui.financeParty.value || "");
  const unique = [...new Map(
    (rows || [])
      .filter((x) => x.party_key && x.party_name && x.party_name !== "-")
      .map((x) => [x.party_key, x.party_name])
  ).entries()];
  ui.financeParty.innerHTML = [`<option value="">${t("finance_all_parties")}</option>`]
    .concat(unique.map(([key, name]) => `<option value="${esc(key)}">${esc(name)}</option>`))
    .join("");
  if (selected && unique.some(([key]) => key === selected)) ui.financeParty.value = selected;
}

function financeEntryPartyLabel(mode) {
  if (mode === "customer") return langText("Choisir client", "اختر زبونا", "اختار كليان");
  if (mode === "supplier") return langText("Choisir fournisseur", "اختر موردا", "اختار فرنسور");
  return t("finance_party_anonymous");
}

function fillFinanceEntryPartyOptions() {
  if (!ui.financeEntryPartyMode || !ui.financeEntryParty) return;
  const mode = String(ui.financeEntryPartyMode.value || "anonymous");
  const previous = String(ui.financeEntryParty.value || "");
  const rows = mode === "customer"
    ? (cache.customers || []).map((x) => ({ key: `customer:${String(x.id || "")}`, name: String(x.name || "-") }))
    : (mode === "supplier" ? (cache.suppliers || []).map((x) => ({ key: `supplier:${String(x.id || "")}`, name: String(x.name || "-") })) : []);
  const active = mode === "customer" || mode === "supplier";
  ui.financeEntryParty.classList.toggle("hidden", !active);
  ui.financeEntryParty.disabled = !active;
  ui.financeEntryParty.required = active;
  if (!active) {
    ui.financeEntryParty.innerHTML = `<option value="">${esc(financeEntryPartyLabel("anonymous"))}</option>`;
    ui.financeEntryParty.value = "";
    return;
  }
  ui.financeEntryParty.innerHTML = [`<option value="">${esc(financeEntryPartyLabel(mode))}</option>`]
    .concat(rows.map((x) => `<option value="${esc(x.key)}">${esc(x.name)}</option>`))
    .join("");
  if (previous && rows.some((x) => x.key === previous)) ui.financeEntryParty.value = previous;
}

function selectedFinanceEntryPartyPayload() {
  const mode = String(ui.financeEntryPartyMode?.value || "anonymous");
  const key = mode === "customer" || mode === "supplier" ? String(ui.financeEntryParty?.value || "").trim() : "";
  if (!key) return { party_key: "", party_name: "" };
  const [kind, id] = key.split(":");
  const source = kind === "customer" ? (cache.customers || []) : (kind === "supplier" ? (cache.suppliers || []) : []);
  const row = source.find((x) => String(x.id || "") === String(id || ""));
  return { party_key: key, party_name: String(row && row.name ? row.name : "") };
}

function fillFinanceDayOptions(days = []) {
  if (!ui.financeDay) return;
  const selected = String(ui.financeDay.value || "");
  ui.financeDay.innerHTML = [`<option value="">${t("finance_all_days")}</option>`]
    .concat((days || []).map((day) => `<option value="${esc(day)}">${esc(day)}</option>`))
    .join("");
  if (selected && (days || []).includes(selected)) ui.financeDay.value = selected;
}

function financeAvailableDaysFromRows() {
  return [...new Set((financeRows || [])
    .map((row) => String(row.day || String(row.created_at || "").slice(0, 10)).trim())
    .filter(Boolean))]
    .sort()
    .reverse();
}

function rerenderFinanceTranslations() {
  fillFinancePartyOptions(financeRows || []);
  fillFinanceEntryPartyOptions();
  fillFinanceDayOptions(financeAvailableDaysFromRows());
  renderFinanceAnalysis();
  renderFinanceList(false);
}

function financePageTokens(totalPages, currentPage) {
  if (totalPages <= 7) return Array.from({ length: totalPages }, (_, i) => i + 1);
  const keep = new Set([1, totalPages, currentPage - 2, currentPage - 1, currentPage, currentPage + 1, currentPage + 2]);
  const pages = Array.from(keep)
    .filter((p) => Number(p) >= 1 && Number(p) <= totalPages)
    .sort((a, b) => a - b);
  const out = [];
  for (let i = 0; i < pages.length; i += 1) {
    const p = pages[i];
    const prev = pages[i - 1];
    if (i > 0 && p - prev > 1) out.push("...");
    out.push(p);
  }
  return out;
}

function renderFinancePager(totalPages) {
  if (ui.financePageInfo) ui.financePageInfo.textContent = langText(`Page ${financePage}/${totalPages}`, `صفحة ${financePage}/${totalPages}`, `صفحة ${financePage}/${totalPages}`);
  if (ui.financePrevBtn) ui.financePrevBtn.disabled = financePage <= 1;
  if (ui.financeNextBtn) ui.financeNextBtn.disabled = financePage >= totalPages;
  if (!ui.financePageNums) return;
  const tokens = financePageTokens(totalPages, financePage);
  ui.financePageNums.innerHTML = tokens.map((x) => {
    if (x === "...") return `<span class="pager-dot">...</span>`;
    const p = Number(x);
    return `<button type="button" class="btn-mini ${p === financePage ? "active" : ""}" data-fin-page="${p}">${p}</button>`;
  }).join("");
  ui.financePageNums.querySelectorAll("[data-fin-page]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const p = Math.max(1, intQty(btn.getAttribute("data-fin-page")));
      if (p === financePage) return;
      financePage = p;
      renderFinanceList(false);
    });
  });
}

function applyFinanceFilters(resetPage = true) {
  const q = String(ui.financeSearch?.value || "").trim().toLowerCase();
  const party = String(ui.financeParty?.value || "").trim();
  const day = String(ui.financeDay?.value || "").trim();
  const from = String(ui.financeFrom?.value || "").trim();
  const to = String(ui.financeTo?.value || "").trim();
  const filtered = (financeRows || []).filter((r) => {
    const rowDay = String(r.day || String(r.created_at || "").slice(0, 10));
    const inDay = !day || rowDay === day;
    const inFrom = !from || rowDay >= from;
    const inTo = !to || rowDay <= to;
    const byParty = !party || String(r.party_key || "") === party;
    const blob = [r.public_ref, r.party_name, r.products_text, r.note, r.source_label, r.status_label].join(" ").toLowerCase();
    const byQ = !q || blob.includes(q);
    return inDay && inFrom && inTo && byParty && byQ;
  });
  financeRowsFiltered = sortFinanceRows(filtered);
  renderFinanceList(resetPage);
}

async function exportFinanceRowPdf(row, targetEl, documentMode = "bon") {
  const kind = String(row && row.pdf_kind || "");
  const id = String(row && row.pdf_id || "");
  const returnScope = financeReturnScope(row);
  if (returnScope) {
    const returnId = financeReturnId(row);
    if (!returnId) {
      appNotify(langText("PDF indisponible pour cette ligne", "لا يوجد PDF لهذا السطر", "ماكانش PDF لهاد السطر"), true, 2500);
      return;
    }
    const out = returnScope === "customer"
      ? await window.wiliDb.exportSaleReturnPdf(returnId)
      : await window.wiliDb.exportPurchaseReturnPdf(returnId);
    handlePdfResult(out, financeReturnBonLabel(returnScope), targetEl || ui.financeTableBody);
    return;
  }
  if (!kind || !id) {
    appNotify(langText("PDF indisponible pour cette ligne", "لا يوجد PDF لهذا السطر", "ماكانش PDF لهاد السطر"), true, 2500);
    return;
  }
  let out = null;
  if (kind === "sale_invoice") out = await window.wiliDb.exportSaleInvoicePdf(id, { document_mode: documentMode });
  else if (kind === "sale_payment") out = await window.wiliDb.exportSalePaymentReceiptPdf(id);
  else if (kind === "purchase_payment") out = await window.wiliDb.exportPurchasePaymentReceiptPdf(id);
  else if (kind === "finance_entry") out = await window.wiliDb.exportFinanceEntryPdf(row);
  else if (kind === "purchase_invoice") {
    await exportPurchaseInvoicePdfById(id, targetEl || ui.financeTableBody, documentMode);
    return;
  }
  const doneLabel = documentMode === "facture"
    ? langText("Facture", "فاتورة", "فاكتورة")
    : (kind === "sale_invoice" ? saleFinanceBonLabel(row) : langText("Bon", "وصل", "بون"));
  handlePdfResult(out, doneLabel, targetEl || ui.financeTableBody);
}

function saleFinanceBonLabel(row = {}) {
  const ref = String(row.public_ref || "").trim().toUpperCase();
  const hasCustomer = String(row.party_key || "").startsWith("customer:");
  return ref.startsWith("VCC/") || !hasCustomer ? langText("Bon VCC", "\u0648\u0635\u0644 \u0628\u064a\u0639 \u0645\u0628\u0627\u0634\u0631", "\u0628\u0648\u0646 VCC") : langText("Bon de Livraison", "\u0648\u0635\u0644 \u062a\u0633\u0644\u064a\u0645", "\u0628\u0648\u0646 \u0644\u064a\u0641\u0631\u064a\u0632\u0648\u0646");
}

function financeReturnScope(row = {}) {
  const sourceType = String(row.source_type || "").toLowerCase();
  const sourceLabel = String(row.source_label || "").toLowerCase();
  const kind = String(row.pdf_kind || "").toLowerCase();
  if (kind === "sale_return" || sourceType === "sale_return" || sourceType === "customer_return" || sourceLabel.includes("remboursement rc") || sourceLabel.includes("retour client")) return "customer";
  if (kind === "purchase_return" || sourceType === "purchase_return" || sourceType === "supplier_return" || sourceLabel.includes("remboursement rf") || sourceLabel.includes("retour fournisseur")) return "supplier";
  return "";
}

function financeReturnId(row = {}) {
  const sourceId = String(row.source_id || "").trim();
  if (sourceId) return sourceId;
  const id = String(row.id || "").trim();
  const match = id.match(/^(sale_return|purchase_return):(.+)$/i);
  if (match && match[2]) return match[2];
  return String(row.pdf_id || "").trim();
}

function financeReturnBonLabel(scope) {
  return scope === "supplier"
    ? langText("Bon de Retour fournisseur", "\u0648\u0635\u0644 \u0625\u0631\u062c\u0627\u0639 \u0645\u0648\u0631\u062f", "\u0628\u0648\u0646 \u0631\u0648\u062a\u0648\u0631 \u0641\u0631\u0646\u0633\u0648\u0631")
    : langText("Bon de Retour client", "\u0648\u0635\u0644 \u0625\u0631\u062c\u0627\u0639 \u0632\u0628\u0648\u0646", "\u0628\u0648\u0646 \u0631\u0648\u062a\u0648\u0631 \u0643\u0644\u064a\u0627\u0646");
}

function financePdfActions(row) {
  const kind = String(row && row.pdf_kind || "");
  const returnScope = financeReturnScope(row);
  if (returnScope) {
    return `<button class="btn-mini" data-fin-pdf="${esc(row.id)}">${esc(financeReturnBonLabel(returnScope))}</button>`;
  }
  if (kind === "sale_invoice") {
    return `<button class="btn-mini" data-fin-pdf="${esc(row.id)}">${esc(saleFinanceBonLabel(row))}</button><button class="btn-mini" data-fin-facture-pdf="${esc(row.id)}">${esc(langText("Facture De Vente", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0628\u064a\u0639", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0628\u064a\u0639"))}</button><button class="btn-mini" data-fin-edit-sale="${esc(row.id)}">${esc(langText("Modifier", "تعديل", "بدل"))}</button>`;
  }
  if (kind === "purchase_invoice") {
    return `<button class="btn-mini" data-fin-pdf="${esc(row.id)}">${esc(langText("Bon de Reception", "\u0648\u0635\u0644 \u0627\u0633\u062a\u0644\u0627\u0645", "\u0628\u0648\u0646 \u0631\u064a\u0633\u0628\u0633\u064a\u0648\u0646"))}</button><button class="btn-mini" data-fin-facture-pdf="${esc(row.id)}">${esc(langText("Facture D'Achat", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621"))}</button><button class="btn-mini" data-fin-edit-purchase="${esc(row.id)}">${esc(langText("Modifier", "تعديل", "بدل"))}</button>`;
  }
  return `<button class="btn-mini" data-fin-pdf="${esc(row.id)}">${langText("Reglement", "تسديد", "خلاص")}</button>`;
}

function renderFinanceList(resetPage = false) {
  const rows = financeRowsFiltered || [];
  const pageSize = Math.max(1, intQty(ui.financePageSize?.value || 20));
  const totalPages = Math.max(1, Math.ceil(rows.length / pageSize));
  if (resetPage) financePage = 1;
  financePage = Math.min(totalPages, Math.max(1, financePage));
  const start = (financePage - 1) * pageSize;
  const pageRows = rows.slice(start, start + pageSize);
  if (ui.financeTableBody) {
    ui.financeTableBody.innerHTML = pageRows.length ? pageRows.map((r) => {
      const isIn = String(r.entry_type || "") === "income";
      return `
        <tr>
          <td>${esc(fmtDateTime(r.created_at || ""))}</td>
          <td>${esc(r.source_label || "-")}</td>
          <td><strong>${esc(r.public_ref || "-")}</strong></td>
          <td>${esc(r.party_name || "-")}</td>
          <td title="${esc(r.products_text || "-")}">${esc(r.products_text || "-")}</td>
          <td>${qtyText(r.qty_total || 0)}</td>
          <td>${amountChip(r.amount_cents || 0, isIn ? "in" : "out")}</td>
          <td>${esc(r.status_label || "-")}</td>
          <td><div class="qty-actions">${financePdfActions(r)}</div></td>
        </tr>
      `;
    }).join("") : `<tr><td colspan="9" class="empty">${t("no_data")}</td></tr>`;
    updateSortHeaderIndicators(ui.financeTable, financeJournalState.sortKey || "created_at", financeJournalState.sortDir || "desc");
    ui.financeTableBody.querySelectorAll("[data-fin-pdf]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const row = (financeRowsFiltered || []).find((x) => String(x.id || "") === String(btn.getAttribute("data-fin-pdf") || ""));
        if (!row) return;
        await exportFinanceRowPdf(row, ui.financeTableBody, "bon");
      });
    });
    ui.financeTableBody.querySelectorAll("[data-fin-facture-pdf]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const row = (financeRowsFiltered || []).find((x) => String(x.id || "") === String(btn.getAttribute("data-fin-facture-pdf") || ""));
        if (!row) return;
        await exportFinanceRowPdf(row, ui.financeTableBody, "facture");
      });
    });
    ui.financeTableBody.querySelectorAll("[data-fin-edit-sale]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const row = (financeRowsFiltered || []).find((x) => String(x.id || "") === String(btn.getAttribute("data-fin-edit-sale") || ""));
        const id = String(row && (row.pdf_id || row.ref_id) || "");
        if (id && typeof startSaleEdit === "function") await startSaleEdit(id);
      });
    });
    ui.financeTableBody.querySelectorAll("[data-fin-edit-purchase]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const row = (financeRowsFiltered || []).find((x) => String(x.id || "") === String(btn.getAttribute("data-fin-edit-purchase") || ""));
        const id = String(row && (row.pdf_id || row.ref_id) || "");
        if (id && typeof startPurchaseEdit === "function") await startPurchaseEdit(id);
      });
    });
  }
  renderFinancePager(totalPages);
}

function financeAnalysisRows() {
  const from = String(ui.financeAnalysisFrom?.value || "").trim();
  const to = String(ui.financeAnalysisTo?.value || "").trim();
  return (financeRows || []).filter((row) => {
    const day = String(row.day || String(row.created_at || "").slice(0, 10));
    return (!from || day >= from) && (!to || day <= to);
  });
}

function renderFinanceAnalysis(rows = financeAnalysisRows()) {
  const income = rows.filter((x) => String(x.entry_type || "") === "income").reduce((sum, row) => sum + Number(row.amount_cents || 0), 0);
  const expense = rows.filter((x) => String(x.entry_type || "") === "expense").reduce((sum, row) => sum + Number(row.amount_cents || 0), 0);
  const profit = rows.reduce((sum, row) => sum + Number(row.estimated_profit_cents || 0), 0);
  if (ui.financeSummary) {
    ui.financeSummary.innerHTML = [
      `<div class="row"><div><strong>${esc(trText("Entrees"))}</strong></div><div>${amountChip(income, "in")}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Sorties"))}</strong></div><div>${amountChip(expense, "out")}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Balance"))}</strong></div><div>${amountChip(income - expense, "net")}</div></div>`,
      `<div class="row"><div><strong>${esc(langText("Profit estime", "الربح التقديري", "الربح التقريبي"))}</strong></div><div>${amountChip(profit, "net")}</div></div>`,
      `<div class="row"><div><strong>${esc(langText("Operations", "العمليات", "ليزوبيراسيون"))}</strong></div><div>${esc(String(rows.length))}</div></div>`
    ].join("");
  }
  renderFinanceCharts(rows);
}

async function refreshFinance() {
  const [reportRes, sumRes] = await Promise.all([window.wiliDb.getFinanceReport(), window.wiliDb.getFinanceSummary()]);
  const report = reportRes && reportRes.ok ? (reportRes.data || {}) : {};
  const entries = Array.isArray(report.rows) ? report.rows : [];
  financeRows = entries;
  fillFinancePartyOptions(entries);
  fillFinanceEntryPartyOptions();
  fillFinanceDayOptions(Array.isArray(report.available_days) ? report.available_days : []);
  renderFinanceAnalysis();
  applyFinanceFilters(true);
  drawList(ui.dashFinance, entries.slice(0, 5), (r) => {
    const isIn = String(r.entry_type || "") === "income";
    return `<div class="row fin-row ${isIn ? "income" : "expense"}">
      <div>
        <strong>${esc(r.source_label || "-")}</strong>
        <div class="small">${esc(r.party_name || "-")} | ${esc(r.public_ref || "-")}</div>
      </div>
      <div>${amountChip(r.amount_cents || 0, isIn ? "in" : "out")}</div>
    </div>`;
  });
}

async function refreshDebtSummary() {
  const out = await window.wiliDb.getDebtsSummary();
  const d = out && out.ok ? out.data : null;
  debtSummaryState = d;
  if (!d) {
    ui.debtKpiCustomers.textContent = money(0);
    ui.debtKpiSuppliers.textContent = money(0);
    ui.debtKpiNet.textContent = money(0);
    ui.debtSummaryBox.innerHTML = row("Credit", "Aucune donnee");
    renderDebtCharts();
    return;
  }
  const cust = Number((d.totals && d.totals.customers_due_cents) || 0);
  const supp = Number((d.totals && d.totals.suppliers_due_cents) || 0);
  ui.debtKpiCustomers.textContent = money(cust);
  ui.debtKpiSuppliers.textContent = money(supp);
  ui.debtKpiNet.textContent = money(cust - supp);

  const topCustomers = (d.customers || []).slice(0, 5).map((x) => row(`Client: ${x.name}`, `${x.invoices} facture(s) | ${money(x.due_cents)}`));
  const topSuppliers = (d.suppliers || []).slice(0, 5).map((x) => row(`Fournisseur: ${x.name}`, `${x.invoices} facture(s) | ${money(x.due_cents)}`));
  ui.debtSummaryBox.innerHTML = topCustomers.concat(topSuppliers).join("") || row("Credit", "Aucun credit actif");
  renderDebtCharts();
}

async function refreshAudit() {
  if (!ui.auditList) {
    auditRows = [];
    return;
  }
  const payload = {
    q: String(ui.auditSearch?.value || "").trim(),
    action: String(ui.auditAction?.value || "").trim(),
    from: String(ui.auditFrom?.value || "").trim(),
    to: String(ui.auditTo?.value || "").trim(),
    limit: 500
  };
  const out = await window.wiliDb.listAuditEvents(payload);
  const rows = out && out.ok ? (out.data || []) : [];
  auditRows = rows.slice();
  renderAuditList(true);
}

async function refreshDashboard() {
  const rep = await window.wiliDb.getDailyReport(toLocalDayIso(new Date()));
  const r = rep && rep.ok ? rep.data : null;
  if (!r) return;
  ui.kpiSales.textContent = money(r.sales_total_cents);
  ui.kpiPurchases.textContent = money(r.purchases_total_cents);
  ui.kpiProfit.textContent = money(r.estimated_profit_cents);
  ui.kpiCash.textContent = money(r.cash_balance_delta_cents);
  ui.dashSummary.innerHTML = [
    row("Nb ventes", String(r.sales_count)),
    row("Nb achats", String(r.purchases_count)),
    `<div class="row"><div><strong>${esc(trText("Cash In"))}</strong></div><div>${amountChip(r.cash_in_cents || 0, "in")}</div></div>`,
    `<div class="row"><div><strong>${esc(trText("Cash Out"))}</strong></div><div>${amountChip(r.cash_out_cents || 0, "out")}</div></div>`
  ].join("");
  renderDashboardCharts();
}

function renderUsersManagement(rows = []) {
  if (!ui.usersList) return;
  if (!hasPermission("users.manage")) {
    ui.usersList.innerHTML = row(langText("Utilisateurs", "المستخدمون", "ليزيتليزاتور"), langText("Gestion utilisateurs requise", "تحتاج صلاحية إدارة المستخدمين", "لازم صلاحية تسيير ليوزرات"));
    return;
  }
  const users = Array.isArray(rows) ? rows.slice() : [];
  if (!users.length) {
    ui.usersList.innerHTML = row(langText("Utilisateurs", "المستخدمون", "ليزيتليزاتور"), langText("Aucun utilisateur", "لا يوجد مستخدمون", "ماكان حتى يوزر"));
    return;
  }
  ui.usersList.innerHTML = users.map((u) => {
    const userId = String(u && u.id ? u.id : "");
    const role = normalizeUserRole(u && u.role ? u.role : "seller");
    const isPrimary = Boolean(u && u.is_primary_admin);
    const isCurrent = String(session.user && session.user.id ? session.user.id : "") === userId;
    const roleLabel = getUserRoleLabel(role);
    const primaryBadge = isPrimary ? `<span class="user-primary-chip">${esc(langText("Admin principal", "المدير الرئيسي", "الادمين الرئيسي"))}</span>` : "";
    const currentBadge = isCurrent ? `<span class="user-role-chip">${esc(langText("Session actuelle", "الجلسة الحالية", "السيشن الحالية"))}</span>` : "";
    return `
      <div class="user-admin-row">
        <div class="user-admin-main">
          <div class="user-admin-top">
            <strong>${esc(u.display_name || u.username || "-")}</strong>
            <div class="user-admin-badges">
              <span class="user-role-chip">${esc(roleLabel)}</span>
              ${primaryBadge}
              ${currentBadge}
            </div>
          </div>
          <div class="small">${esc(u.username || "-")} | ${esc(fmtDateTime(u.created_at || "") || "-")}</div>
        </div>
        <div class="user-admin-actions">
          <button type="button" class="btn-mini" data-user-set-pin="${esc(userId)}">${esc(langText("Mot de passe", "كلمة المرور", "الموطو باس"))}</button>
          <select class="input-inline" data-user-role-select="${esc(userId)}" ${isPrimary ? "disabled" : ""}>${userRoleOptionsHtml(role, { disableAdmin: isPrimary })}</select>
          <button type="button" class="btn-mini" data-user-apply-role="${esc(userId)}" ${isPrimary ? "disabled" : ""}>${esc(langText("Appliquer role", "تطبيق الدور", "طبّق الدور"))}</button>
          <button type="button" class="btn-mini danger" data-user-delete="${esc(userId)}" ${isPrimary || isCurrent ? "disabled" : ""}>${esc(langText("Supprimer", "حذف", "حذف"))}</button>
        </div>
      </div>
    `;
  }).join("");

  ui.usersList.querySelectorAll("[data-user-set-pin]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const userId = String(btn.getAttribute("data-user-set-pin") || "");
      const user = users.find((x) => String(x.id || "") === userId);
      if (!userId || !user) return;
      const pin = await appAskUserPin({
        title: langText(`Modifier mot de passe: ${user.display_name || user.username}`, `تغيير كلمة المرور: ${user.display_name || user.username}`, `بدل الموطو باس: ${user.display_name || user.username}`)
      });
      if (!pin) return;
      const out = await window.wiliApi.setUserPin(userId, pin);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "pin_update_failed", true, 4200);
        return;
      }
      appNotify(langText("Mot de passe modifie", "تم تغيير كلمة المرور", "الموطو باس تبدل"), false, 2600);
      await refreshSettings();
    });
  });

  ui.usersList.querySelectorAll("[data-user-apply-role]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const userId = String(btn.getAttribute("data-user-apply-role") || "");
      const user = users.find((x) => String(x.id || "") === userId);
      if (!userId || !user) return;
      const select = ui.usersList.querySelector(`[data-user-role-select="${CSS.escape(userId)}"]`);
      const nextRole = normalizeUserRole(select && select.value ? select.value : user.role || "seller");
      if (normalizeUserRole(user.role || "seller") === nextRole) return;
      const ok = await appConfirm(
        langText(
          `Confirmer changement du role de ${user.display_name || user.username} vers ${getUserRoleLabel(nextRole)} ?`,
          `تأكيد تغيير دور ${user.display_name || user.username} إلى ${getUserRoleLabel(nextRole)}؟`,
          `تأكد بلي تبدل دور ${user.display_name || user.username} لـ ${getUserRoleLabel(nextRole)}؟`
        )
      );
      if (!ok) return;
      const out = await window.wiliApi.updateUserRole(userId, nextRole);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "user_role_failed", true, 4200);
        return;
      }
      appNotify(langText("Role utilisateur mis a jour", "تم تحديث دور المستخدم", "دور ليوزر تبدل"), false, 2600);
      await refreshSettings();
    });
  });

  ui.usersList.querySelectorAll("[data-user-delete]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const userId = String(btn.getAttribute("data-user-delete") || "");
      const user = users.find((x) => String(x.id || "") === userId);
      if (!userId || !user) return;
      const ok = await appConfirm(
        langText(
          `Confirmer suppression de ${user.display_name || user.username} ?`,
          `تأكيد حذف ${user.display_name || user.username}?`,
          `تأكد بلي تحذف ${user.display_name || user.username}?`
        )
      );
      if (!ok) return;
      const out = await window.wiliApi.deleteUser(userId);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true, 4200);
        return;
      }
      appNotify(langText("Utilisateur supprime", "تم حذف المستخدم", "ليوزر تمسح"), false, 2600);
      await refreshSettings();
    });
  });
}

function normalizeComptoirLayout(value) {
  const layout = String(value || "table").trim();
  return (layout === "compact_images" || layout === "large_images" || layout === "table") ? layout : "table";
}

function renderComptoirLayoutPreview(layoutValue) {
  const layout = normalizeComptoirLayout(layoutValue);
  document.body.dataset.comptoirLayout = layout;
  if (!ui.comptoirLayoutPreview) return;
  const meta = {
    table: {
      title: langText("Classique sans images", "الكلاسيكي بدون صور", "كلاسيك بلا تصاور"),
      img: "./assets/comptoir-layouts/1.png"
    },
    compact_images: {
      title: langText("Images petites", "صور صغيرة", "تصاور صغار"),
      img: "./assets/comptoir-layouts/2.png"
    },
    large_images: {
      title: langText("Images grandes", "صور كبيرة", "تصاور كبار"),
      img: "./assets/comptoir-layouts/3.png"
    }
  }[layout];
  ui.comptoirLayoutPreview.innerHTML = `
    <div class="layout-preview-card ${layout}">
      <img src="${esc(meta.img)}" alt="">
      <div class="layout-preview-copy">
        <strong>${esc(meta.title)}</strong>
        <small>${esc(langText("Apercu Comptoir", "معاينة الكونتوار", "بريفيو الكونتوار"))}</small>
      </div>
    </div>
  `;
}

async function refreshSettings() {
  const [stRes, usersRes] = await Promise.all([
    window.wiliDb.getSettings(),
    hasPermission("users.manage") ? window.wiliApi.listUsers(250) : Promise.resolve({ ok: true, data: [] })
  ]);
  const st = stRes && stRes.ok ? (stRes.data || {}) : {};
  cache.settings = { ...(cache.settings || {}), ...st };
  await setPurchasePaidAutoFull(Boolean(st.purchase_paid_auto_full), false);
  ui.setAllowNegativeStock.value = String(Boolean(st.allow_negative_stock));
  if (ui.setAutoSettleDocuments) ui.setAutoSettleDocuments.value = String(st.auto_settle_documents !== false);
  if (ui.setAutoPrintSaleCart) ui.setAutoPrintSaleCart.value = String(st.auto_print_sale_cart !== false);
  if (typeof refreshPrintProfilesSettingsUi === "function") await refreshPrintProfilesSettingsUi(st);
  ui.setCurrency.value = String(st.currency || "DZD");
  ui.setLanguage.value = String(st.language || session.language || "fr");
  populateUserRoleSelect(ui.userRole, String(ui.userRole?.value || "seller"));
  const dbTypographyPrefs = {
    ui_font_size: normalizeUiFontScale(st.ui_font_size || "medium"),
    ui_font_bold: Boolean(st.ui_font_bold)
  };
  const effectiveTypographyPrefs = uiTypographyState.pendingTimer
    ? currentUiTypographyPrefs()
    : dbTypographyPrefs;
  ui.setUiFontSize.value = normalizeUiFontScale(effectiveTypographyPrefs.ui_font_size);
  if (ui.setUiDisplayProfile) ui.setUiDisplayProfile.value = normalizeUiDisplayProfile(st.ui_display_profile || "1080");
  ui.setUiFontBold.checked = Boolean(effectiveTypographyPrefs.ui_font_bold);
  applyUiTypography(effectiveTypographyPrefs);
  if (!uiTypographyState.pendingTimer) {
    uiTypographyState.lastSavedKey = uiTypographyKey(dbTypographyPrefs);
  }
  if (ui.setInvoiceTaxRate) ui.setInvoiceTaxRate.value = String(Number(st.invoice_tax_rate_percent ?? 19));
  if (ui.setProductImagesDir) {
    const def = await window.wiliFiles?.getProductImagesDefaultDir?.();
    const defaultDir = def && def.ok ? String((def.data || {}).dir_path || "") : "";
    const configuredDir = String(st.product_images_dir || "").trim();
    ui.setProductImagesDir.value = configuredDir || defaultDir;
    if (ui.productImagesDirHint) {
      ui.productImagesDirHint.textContent = configuredDir
        ? langText("Dossier personnalise", "مجلد مخصص", "دوسي مخصص")
        : langText(`Dossier par defaut: ${defaultDir}`, `المجلد الافتراضي: ${defaultDir}`, `الدوسي ديفو: ${defaultDir}`);
    }
  }
  if (ui.setComptoirLayout) ui.setComptoirLayout.value = normalizeComptoirLayout(st.comptoir_layout || "table");
  renderComptoirLayoutPreview(ui.setComptoirLayout?.value || "table");
  if (ui.setBarcodeShowName) ui.setBarcodeShowName.checked = st.barcode_label_show_name !== false;
  if (ui.setBarcodeShowRef) ui.setBarcodeShowRef.checked = st.barcode_label_show_ref !== false;
  if (ui.setBarcodeShowPrice) ui.setBarcodeShowPrice.checked = st.barcode_label_show_price === true;
  ui.setStoreName.value = String(st.store_name || "");
  ui.setStoreActivity.value = String(st.store_activity || "");
  ui.setStoreAddress.value = String(st.store_address || "");
  ui.setStorePhone.value = String(st.store_phone || "");
  ui.setStoreEmail.value = String(st.store_email || "");
  ui.setStoreNif.value = String(st.store_nif || "");
  ui.setStoreNis.value = String(st.store_nis || "");
  ui.setStoreRc.value = String(st.store_rc || "");
  ui.setStoreAi.value = String(st.store_ai || "");
  renderUsersManagement(usersRes && usersRes.ok ? (usersRes.data || []) : []);
  if (typeof refreshPriceDisplayPanel === "function") await refreshPriceDisplayPanel();
}

function renderAuditPager(totalPages) {
  if (ui.auditPageInfo) ui.auditPageInfo.textContent = langText(`Page ${auditPage}/${totalPages}`, `صفحة ${auditPage}/${totalPages}`, `صفحة ${auditPage}/${totalPages}`);
  if (ui.auditPrevBtn) ui.auditPrevBtn.disabled = auditPage <= 1;
  if (ui.auditNextBtn) ui.auditNextBtn.disabled = auditPage >= totalPages;
  if (!ui.auditPageNums) return;
  const tokens = financePageTokens(totalPages, auditPage);
  ui.auditPageNums.innerHTML = tokens.map((x) => {
    if (x === "...") return `<span class="pager-dot">...</span>`;
    const p = Number(x);
    return `<button type="button" class="btn-mini ${p === auditPage ? "active" : ""}" data-audit-page="${p}">${p}</button>`;
  }).join("");
  ui.auditPageNums.querySelectorAll("[data-audit-page]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const p = Math.max(1, intQty(btn.getAttribute("data-audit-page")));
      if (p === auditPage) return;
      auditPage = p;
      renderAuditList(false);
    });
  });
}

function renderAuditList(resetPage = false) {
  const rows = auditRows || [];
  const totalPages = Math.max(1, Math.ceil(rows.length / AUDIT_PAGE_SIZE));
  if (resetPage) auditPage = 1;
  auditPage = Math.min(totalPages, Math.max(1, auditPage));
  const start = (auditPage - 1) * AUDIT_PAGE_SIZE;
  const pageRows = rows.slice(start, start + AUDIT_PAGE_SIZE);
  if (!ui.auditList) return;
  ui.auditList.innerHTML = pageRows.length
    ? pageRows.map((e) => {
      const dt = fmtDateTime(e.created_at);
      const action = String(e.action || "-");
      const payloadJson = String(e.payload_json || "{}");
      return `<tr><td>${esc(dt)}</td><td><strong>${esc(action)}</strong></td><td><div class="small">${esc(payloadJson)}</div></td></tr>`;
    }).join("")
    : `<tr><td colspan="3" class="empty">${t("no_data")}</td></tr>`;
  renderAuditPager(totalPages);
}

async function refreshAll() {
  const safe = async (label, fn) => {
    try {
      if (typeof fn === "function") await fn();
    } catch (err) {
      console.error(`refresh_failed:${label}`, err);
    }
  };
  await Promise.all([
    safe("products", () => refreshProducts({ reloadCache: true })),
    safe("customers", refreshCustomers),
    safe("suppliers", refreshSuppliers)
  ]);
  await Promise.all([
    safe("dashboard", refreshDashboard),
    safe("sales", refreshSales),
    safe("purchases", refreshPurchases),
    safe("finance", refreshFinance),
    safe("caisse", refreshCaisseState),
    safe("debt", refreshDebtSummary),
    safe("audit", refreshAudit),
    safe("settings", refreshSettings)
  ]);
  await safe("history", () => refreshHistory(String(ui.historyProduct?.value || "")));
  await safe("customer_trace", refreshCustomerTrace);
  await safe("supplier_trace", refreshSupplierTrace);
  renderPurchaseCart();
  updatePurchaseComputed();
  renderAlerts();
  renderDashboardCharts();
  renderSalesHistoryCharts();
  renderPurchasesCharts();
  renderFinanceAnalysis();
  renderDebtCharts();
  if (typeof renderHistoryStats === "function") renderHistoryStats();
  else renderHistoryCharts();
}
