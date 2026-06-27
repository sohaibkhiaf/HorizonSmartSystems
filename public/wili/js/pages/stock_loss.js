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
  Array.from(ui.lossReason?.options || []).forEach((option) => { option.textContent = labels[String(option.value || "")] || option.textContent; });
  Array.from(ui.lossFilterReason?.options || []).forEach((option) => {
    option.textContent = option.value ? (labels[String(option.value || "")] || option.textContent) : langText("Tous motifs", "كل الأسباب", "كل الاسباب");
  });
  const headLabels = [
    langText("Date", "التاريخ", "التاريخ"), langText("Produit", "المنتج", "المنتوج"),
    langText("Qte", "الكمية", "الكمية"), langText("Cout unit", "تكلفة الوحدة", "سومة الوحدة"),
    langText("Perte", "الخسارة", "الخسارة"), langText("Motif / note", "السبب / الملاحظة", "السبب / الملاحظة")
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
  const units = rows.reduce((sum, row) => sum + Math.abs(Number(row.qty || row.qty_delta || 0)), 0);
  const total = rows.reduce((sum, row) => sum + Number(row.total_cost_cents || row.amount_cents || 0), 0);
  ui.lossCount.textContent = String(rows.length);
  ui.lossUnits.textContent = qtyText(units);
  ui.lossTotalCost.textContent = money(total);
  ui.lossTableBody.innerHTML = rows.length ? rows.map((loss) => `<tr>
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
  const [lossRes, productRes] = await Promise.all([window.wiliDb.listStockLosses(500), window.wiliDb.listProducts(10000)]);
  stockLossRows = lossRes && lossRes.ok && Array.isArray(lossRes.data) ? lossRes.data : [];
  const products = productRes && productRes.ok && Array.isArray(productRes.data) ? productRes.data : [];
  const selected = String(ui.lossProduct?.value || "");
  ui.lossProduct.innerHTML = `<option value="">${esc(langText("Selectionner produit", "اختر المنتج", "اختار المنتوج"))}</option>` +
    products.map((product) => `<option value="${esc(product.id)}">${esc(product.name || "-")} | ${esc(product.barcode || product.reference || "-")} | ${langText("Stock", "المخزون", "الستوك")}: ${qtyText(product.stock_qty || 0)}</option>`).join("");
  if (products.some((product) => String(product.id || "") === selected)) ui.lossProduct.value = selected;
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
      const out = await window.wiliDb.recordStockLoss({ product_id: productId, qty, reason: String(ui.lossReason?.value || "damaged"), note: String(ui.lossNote?.value || "").trim() });
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
