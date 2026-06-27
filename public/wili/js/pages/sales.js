let posSearchRequestSeq = 0;

function saleBonLabel(row = {}) {
  const docType = String(row.doc_type || "").toUpperCase();
  return docType === "VCC" || !String(row.customer_id || "").trim()
    ? langText("Bon VCC", "\u0648\u0635\u0644 \u0628\u064a\u0639 \u0645\u0628\u0627\u0634\u0631", "\u0628\u0648\u0646 VCC")
    : langText("Bon de Livraison", "\u0648\u0635\u0644 \u062a\u0633\u0644\u064a\u0645", "\u0628\u0648\u0646 \u0644\u064a\u0641\u0631\u064a\u0632\u0648\u0646");
}

function salePdfResultLabel(row = {}, mode = "bon") {
  return mode === "facture" ? langText("Facture De Vente", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0628\u064a\u0639", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0628\u064a\u0639") : saleBonLabel(row);
}

function upsertCachedProduct(product) {
  const p = product && typeof product === "object" ? product : null;
  const pid = String(p && p.id || "").trim();
  if (!pid) return null;
  const idx = cache.products.findIndex((x) => String(x.id || "") === pid);
  if (idx >= 0) {
    cache.products[idx] = { ...cache.products[idx], ...p };
    return cache.products[idx];
  }
  cache.products.push(p);
  return p;
}

function upsertCachedProducts(rows) {
  (Array.isArray(rows) ? rows : []).forEach((row) => upsertCachedProduct(row));
}

function addProductToCart(productOrId, qtyToAdd = 1) {
  const inputProduct = productOrId && typeof productOrId === "object" ? productOrId : null;
  const productId = String(inputProduct && inputProduct.id ? inputProduct.id : productOrId || "");
  const p = upsertCachedProduct(inputProduct) || cache.products.find((x) => String(x.id) === productId);
  if (!p) return;
  const mode = selectedSalePriceMode();
  const unit = modePriceCents(p, mode);
  const result = window.WiliPosCartModel.addProduct(posCart, p, {
    qtyToAdd,
    mode,
    unitPriceCents: unit,
    stockQty: stockForProduct(p.id),
    blockNegativeStock: isNegativeStockBlockedUi()
  });
  if (!result || !result.ok) {
    if (result && result.error === "insufficient_stock") {
      notifyInsufficientStock(String(p.name || "-"), result.stockQty, result.requestedQty);
    }
    return;
  }
  renderCart();
  updatePosTotals();
  updatePosLiveBoard(p);
}
function changeCartQty(productId, delta) {
  const line = posCart.find((x) => String(x.product_id) === String(productId || ""));
  if (!line) return;
  const result = window.WiliPosCartModel.changeQty(posCart, productId, {
    delta,
    stockQty: stockForProduct(line.product_id),
    blockNegativeStock: isNegativeStockBlockedUi()
  });
  if (!result || !result.ok) {
    if (result && result.error === "insufficient_stock") {
      notifyInsufficientStock(String(line.name || "-"), result.stockQty, result.requestedQty);
    }
    return;
  }
  renderCart();
  updatePosTotals();
}
function removeCartLine(productId) {
  window.WiliPosCartModel.removeLine(posCart, productId);
  renderCart();
  updatePosTotals();
}
function setSaleEditMode(saleId = "", saleRef = "") {
  editingSaleId = String(saleId || "");
  editingSaleRef = String(saleRef || "");
  if (ui.posValidateBtn) {
    ui.posValidateBtn.textContent = editingSaleId
      ? langText("Enregistrer modification", "حفظ التعديل", "سجل التبديل")
      : langText("Valider vente", "تأكيد البيع", "أكد البيع");
  }
}

function resetSaleEditMode() {
  setSaleEditMode("", "");
}

function clearCart() {
  posCart.splice(0, posCart.length);
  if (ui.saleDiscountValue) ui.saleDiscountValue.value = "0";
  if (ui.saleDiscountType) ui.saleDiscountType.value = "amount";
  ui.salePaidTouched = false;
  renderCart();
  updatePosTotals();
  updatePosLiveBoard(null);
}

async function confirmAndClearCart() {
  if (!posCart.length) return;
  const ok = await appConfirm(langText("Confirmer vidage du panier ?", "تأكيد تفريغ السلة؟", "تأكد بلي تفرّغ السلة؟"));
  if (!ok) return;
  clearCart();
  resetSaleEditMode();
  appNotify(langText("Panier vide", "تم تفريغ السلة", "السلة تفرغت"), false, 2200);
}

async function startSaleEdit(saleId) {
  const id = String(saleId || "");
  if (!id) return;
  const out = await window.wiliDb.getSaleDetails(id);
  if (!out || !out.ok) {
    appNotify(out && out.error ? out.error : "sale_details_failed", true, 5000);
    return;
  }
  const details = out.data || {};
  const returns = Array.isArray(details.returns) ? details.returns : [];
  if (returns.length) {
    appNotify(langText(
      "Cette vente contient deja des retours. Utilisez le retour client au lieu de modifier.",
      "هذه العملية فيها إرجاعات. استعمل إرجاع الزبون بدل التعديل.",
      "هاد البيع فيه رتور. استعمل رتور كليان بلا ما تبدل."
    ), true, 6000);
    return;
  }
  const inv = details.invoice || {};
  const items = Array.isArray(details.items) ? details.items : [];
  posCart.splice(0, posCart.length);
  for (const it of items) {
    const pid = String(it.product_id || "");
    if (!pid) continue;
    const existing = cache.products.find((p) => String(p.id || "") === pid) || {};
    const restoredStock = Number(existing.stock_qty || 0) + qtyNum(it.qty || 0);
    upsertCachedProduct({
      ...existing,
      id: pid,
      name: it.product_name || existing.name || pid,
      barcode: it.barcode || existing.barcode || "",
      reference: it.reference || existing.reference || "",
      stock_qty: restoredStock,
      retail_price_cents: Number(it.unit_price_cents || existing.retail_price_cents || 0),
      wholesale_price_cents: Number(existing.wholesale_price_cents || it.unit_price_cents || 0),
      assisted_price_cents: Number(existing.assisted_price_cents || it.unit_price_cents || 0)
    });
    posCart.push({
      product_id: pid,
      name: it.product_name || existing.name || pid,
      barcode: it.barcode || existing.barcode || "",
      reference: it.reference || existing.reference || "",
      price_mode: "retail",
      qty: qtyNum(it.qty || 0, 1),
      unit_price_cents: Number(it.unit_price_cents || 0),
      line_total_cents: Number(it.line_total_cents || 0)
    });
  }
  if (ui.saleCustomer) ui.saleCustomer.value = String(inv.customer_id || "");
  if (typeof window.syncSaleCustomerSearchInput === "function") window.syncSaleCustomerSearchInput();
  if (ui.salePaid) {
    ui.salePaid.value = money(inv.paid_cents || 0);
    ui.salePaidTouched = true;
  }
  setSaleEditMode(id, salePublicRef(inv.id || id, inv.created_at));
  renderCart();
  updatePosTotals();
  switchView("sales");
  appNotify(langText(
    `Modification vente ${editingSaleRef}`,
    `تعديل البيع ${editingSaleRef}`,
    `تبديل البيع ${editingSaleRef}`
  ), false, 3500);
  setTimeout(() => ui.posSearchInput?.focus(), 0);
}

window.startSaleEdit = startSaleEdit;

function repriceCartToMode() {
  // Global mode is used for adding/search context; each cart line has its own mode.
  renderCart();
  updatePosTotals();
}

function sortPosSearchRows(rows) {
  return window.WiliPosSearchModel.sortRows(rows, posSearchSortState);
}
function revealSelectedPosSearchRow() {
  const selectedRow = ui.posResults?.querySelector(`[data-row-index="${searchSelectedIndex}"]`);
  if (selectedRow && typeof selectedRow.scrollIntoView === "function") {
    selectedRow.scrollIntoView({ block: "nearest", inline: "nearest" });
  }
}

let posKeyboardNavigationActive = false;

function focusPosSearchTableSoon() {
  const focusTable = () => ui.posResults?.querySelector(".pos-search-focus")?.focus();
  focusTable();
  setTimeout(focusTable, 0);
  if (typeof requestAnimationFrame === "function") requestAnimationFrame(focusTable);
}

function stockQtyText(value, fallback = 0) {
  return window.WiliPosRenderModel.stockQtyText(value, fallback);
}

function currentComptoirLayout() {
  const fromSelect = String(ui.setComptoirLayout?.value || "").trim();
  const fromBody = String(document.body?.dataset?.comptoirLayout || "").trim();
  const layout = fromSelect || fromBody || "table";
  return (layout === "compact_images" || layout === "large_images" || layout === "table") ? layout : "table";
}

function productImageSrc(pathValue) {
  const p = String(pathValue || "").trim();
  if (!p) return "";
  if (/^(file|https?):\/\//i.test(p)) return p;
  return `file:///${p.replace(/\\/g, "/").replace(/^\/+/, "")}`;
}

function renderProductImage(pathValue, className = "pos-product-thumb") {
  const src = productImageSrc(pathValue);
  return `<div class="${className}${src ? "" : " empty"}">${src ? `<img src="${esc(src)}" alt="">` : `<span>W</span>`}</div>`;
}

function posSearchRowsFromResult(out) {
  const data = out && out.ok ? out.data : null;
  if (Array.isArray(data)) return data;
  if (data && Array.isArray(data.rows)) return data.rows;
  return [];
}

async function loadPosSearchRows(q, posLimit) {
  if (q && window.wiliDb?.searchProducts) {
    return posSearchRowsFromResult(await window.wiliDb.searchProducts(q, posLimit));
  }
  if (window.wiliDb?.listProductsPage) {
    return posSearchRowsFromResult(await window.wiliDb.listProductsPage({
      q,
      limit: posLimit,
      offset: 0,
      sortKey: posSearchSortState.key || "name",
      sortDir: posSearchSortState.dir || "asc"
    }));
  }
  if (q && window.wiliDb?.searchProducts) {
    return posSearchRowsFromResult(await window.wiliDb.searchProducts(q, posLimit));
  }
  return posSearchRowsFromResult(await window.wiliDb.listProducts(posLimit));
}

function selectPosSearchRow(nextIndex, { reveal = false } = {}) {
  if (!searchRows.length) return;
  searchSelectedIndex = window.WiliPosSearchModel.clampIndex(nextIndex, searchRows);
  ui.posResults?.querySelectorAll("[data-row-index]").forEach((row) => {
    row.classList.toggle("row-selected", intQty(row.getAttribute("data-row-index")) === searchSelectedIndex);
  });
  const selected = searchRows[searchSelectedIndex];
  if (selected) updatePosLiveBoard(selected);
  if (reveal) revealSelectedPosSearchRow();
}

async function renderPosSearch(term, opts = {}) {
  const reqSeq = ++posSearchRequestSeq;
  const keepScroll = Boolean(opts && opts.keepScroll);
  const revealSelection = Boolean(opts && opts.revealSelection);
  const prevWrap = ui.posResults ? ui.posResults.querySelector(".pos-search-wrap") : null;
  const prevScrollTop = keepScroll && prevWrap ? Number(prevWrap.scrollTop || 0) : 0;
  const q = String(term || "").trim().toLowerCase();
  const posLimit = q ? 200 : 80;
  const fetchedRows = await loadPosSearchRows(q, posLimit);
  if (reqSeq !== posSearchRequestSeq) return;
  upsertCachedProducts(fetchedRows);
  const rows = q ? fetchedRows.slice() : sortPosSearchRows(fetchedRows);
  searchRows = rows;
  if (searchSelectedIndex >= rows.length) searchSelectedIndex = 0;

  if (!rows.length) {
    ui.posResults.innerHTML = `<div class="empty">${t("no_data")}</div>`;
    updatePosLiveBoard(null);
    forceEnableSalesControls();
    return;
  }

  const layout = currentComptoirLayout();
  document.body.dataset.comptoirLayout = layout;
  if (layout !== "table") {
    ui.posResults.innerHTML = `
      <div class="pos-card-search-wrap pos-search-wrap ${layout} pos-search-focus" tabindex="0">
        ${rows.map((p, idx) => {
          const view = window.WiliPosRenderModel.searchProductView(p);
          return `
            <article class="pos-product-card ${idx === searchSelectedIndex ? "row-selected" : ""}" data-row-index="${idx}">
              ${renderProductImage(view.image_path, "pos-product-card-image")}
              <div class="pos-product-card-body">
                <strong>${esc(view.name)}</strong>
                <span>${esc(view.barcode || view.reference || "-")}</span>
                <em>${money(view.retail_price_cents)}</em>
                <small>${String(view.product_type || "product") === "service" ? esc(langText("Service", "خدمة", "سرفيس")) : `${esc(view.stock_text)} ${esc(langText("en stock", "في المخزون", "في الستوك"))}`}</small>
              </div>
              <button class="btn-mini pos-card-add" data-add-product="${esc(view.id)}" data-row-index="${idx}">+</button>
            </article>
          `;
        }).join("")}
      </div>
    `;
  } else {
    ui.posResults.innerHTML = `
    <div class="table-wrap fixed-list-height pos-search-wrap">
      <table class="table-lite data-grid pos-search-table pos-search-focus" tabindex="0">
        <thead>
          <tr>
            <th data-sort="name">${trText("Produit")}</th>
            <th data-sort="barcode">${t("ph_barcode")}</th>
            <th data-sort="reference">${langText("Reference", "\u0627\u0644\u0645\u0631\u062C\u0639", "\u0627\u0644\u0631\u064A\u0641\u0631\u0648\u0646\u0633")}</th>
            <th data-sort="stock_qty">${trText("Stock")}</th>
            <th data-sort="retail_price_cents">${trText("Detail")}</th>
            <th data-sort="wholesale_price_cents">${trText("Gros")}</th>
            <th data-sort="assisted_price_cents">${trText("Remise")}</th>
            <th>${trText("Actions")}</th>
          </tr>
        </thead>
        <tbody>
          ${rows.map((p, idx) => {
            const view = window.WiliPosRenderModel.searchProductView(p);
            return `
            <tr data-row-index="${idx}" class="${idx === searchSelectedIndex ? "row-selected" : ""}">
              <td><strong>${esc(view.name)}</strong></td>
              <td>${esc(view.barcode || "-")}</td>
              <td>${esc(view.reference || "-")}</td>
              <td class="${esc(view.stock_class)}">${esc(view.stock_text)}</td>
              <td>${money(view.retail_price_cents)}</td>
              <td>${money(view.wholesale_price_cents)}</td>
              <td>${money(view.assisted_price_cents)}</td>
              <td><button class="btn-mini" data-add-product="${esc(view.id)}" data-row-index="${idx}">${langText("Ajouter", "اضافة", "زيد")}</button></td>
            </tr>
          `;
          }).join("")}
        </tbody>
      </table>
    </div>
  `;
  }
  if (keepScroll) {
    const nextWrap = ui.posResults.querySelector(".pos-search-wrap");
    if (nextWrap) nextWrap.scrollTop = prevScrollTop;
  }
  if (revealSelection) revealSelectedPosSearchRow();
  const posTable = ui.posResults.querySelector("table.data-grid");
  if (posTable) updateSortHeaderIndicators(posTable, posSearchSortState.key || "name", posSearchSortState.dir || "asc");

  ui.posResults.querySelectorAll("th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "name");
      window.WiliPosSearchModel.toggleSortState(posSearchSortState, key);
      searchSelectedIndex = 0;
      renderPosSearch(ui.posSearchInput?.value || "");
    });
  });

  ui.posResults.querySelectorAll("[data-add-product]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const qte = Math.max(0.01, qtyNum(ui.posQuickQty?.value || 1, 1));
      const idx = Math.max(0, intQty(btn.getAttribute("data-row-index")));
      searchSelectedIndex = Math.min(idx, Math.max(0, searchRows.length - 1));
      posKeyboardNavigationActive = true;
      addProductToCart(searchRows[idx] || String(btn.getAttribute("data-add-product") || ""), qte);
      selectPosSearchRow(searchSelectedIndex, { reveal: true });
      focusPosSearchTableSoon();
    });
  });
  ui.posResults.querySelectorAll("[data-row-index]").forEach((rowEl) => {
    rowEl.addEventListener("click", (e) => {
      if (e.target && e.target.closest && e.target.closest("[data-add-product]")) return;
      posKeyboardNavigationActive = true;
      const idx = Math.max(0, intQty(rowEl.getAttribute("data-row-index")));
      selectPosSearchRow(idx, { reveal: true });
      focusPosSearchTableSoon();
    });
    rowEl.addEventListener("dblclick", (e) => {
      if (e.target && e.target.closest && e.target.closest("[data-add-product]")) return;
      posKeyboardNavigationActive = true;
      const idx = Math.max(0, intQty(rowEl.getAttribute("data-row-index")));
      const prod = searchRows[idx];
      if (!prod) return;
      searchSelectedIndex = idx;
      const qte = Math.max(0.01, qtyNum(ui.posQuickQty?.value || 1, 1));
      addProductToCart(prod, qte);
      selectPosSearchRow(searchSelectedIndex, { reveal: true });
      focusPosSearchTableSoon();
    });
    rowEl.addEventListener("mouseenter", () => {
      if (posKeyboardNavigationActive) return;
      searchSelectedIndex = Math.max(0, intQty(rowEl.getAttribute("data-row-index")));
      const prod = searchRows[searchSelectedIndex];
      if (prod) updatePosLiveBoard(prod);
    });
    rowEl.addEventListener("mousemove", () => {
      if (posKeyboardNavigationActive) return;
      selectPosSearchRow(intQty(rowEl.getAttribute("data-row-index")));
    });
  });
  ui.posResults.querySelector(".pos-search-focus")?.addEventListener("keydown", (e) => {
    window.WiliPosSearchModel.handleKeyEvent(e, {
      rows: searchRows,
      selectedIndex: searchSelectedIndex,
      quickQty: qtyNum(ui.posQuickQty?.value || 1, 1),
      setKeyboardActive: (active) => { posKeyboardNavigationActive = Boolean(active); },
      selectRow: selectPosSearchRow,
      focusSearch: focusPosSearchTableSoon,
      addSelected: (product, qty) => addProductToCart(product, qty),
      decrementSelected: (product) => changeCartQty(product.id, -1)
    });
  });
  if (rows.length) updatePosLiveBoard(rows[searchSelectedIndex]); else updatePosLiveBoard(null);
  forceEnableSalesControls();
}

function renderCart() {
  if (!posCart.length) {
    ui.posCartList.innerHTML = `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`;
    forceEnableSalesControls();
    return;
  }

  ui.posCartList.innerHTML = posCart.map((line) => {
    const view = window.WiliPosRenderModel.cartLineView(line, cache.products);
    const mode = view.mode;
    const image = currentComptoirLayout() === "table" ? "" : renderProductImage(view.image_path, "pos-cart-line-image");
    return `<tr data-cart-row="${esc(view.product_id)}"><td><div class="pos-cart-product-cell">${image}<div><strong>${esc(view.name)}</strong><div class="small">${esc(view.barcode || "-")} | ${esc(view.reference || "-")}</div></div></div></td><td><input class="cart-cell-input cart-qty-input" type="number" min="0.01" step="0.01" value="${qtyText(view.qty)}" data-cart-qty="${esc(view.product_id)}"></td><td><input class="cart-cell-input" type="number" min="0" step="0.01" value="${money(view.unit_price_cents)}" data-cart-price="${esc(view.product_id)}"></td><td><select class="cart-cell-input" data-cart-mode="${esc(view.product_id)}"><option value="retail" ${mode === "retail" ? "selected" : ""}>${esc(langText("Detail", "تفصيل", "تفصيل"))}</option><option value="wholesale" ${mode === "wholesale" ? "selected" : ""}>${esc(langText("Gros", "جملة", "جملة"))}</option><option value="assist" ${mode === "assist" ? "selected" : ""}>${esc(langText("Remise", "تخفيض", "تخفيض"))}</option></select></td><td><input class="cart-cell-input cart-total-input" type="number" min="0.01" step="0.01" value="${money(view.line_total_cents)}" data-cart-total="${esc(view.product_id)}"></td><td>${String(view.product_type || "product") === "service" ? esc(langText("Service", "خدمة", "سرفيس")) : esc(view.stock_text)}</td><td><div class="qty-actions cart-actions"><button class="btn-mini" data-plus="${esc(view.product_id)}">+</button><button class="btn-mini" data-minus="${esc(view.product_id)}">-</button><button class="btn-mini" data-del="${esc(view.product_id)}">X</button></div></td></tr>`;
  }).join("");

  ui.posCartList.querySelectorAll("[data-plus]").forEach((btn) => btn.addEventListener("click", () => changeCartQty(String(btn.getAttribute("data-plus") || ""), 1)));
  ui.posCartList.querySelectorAll("[data-minus]").forEach((btn) => btn.addEventListener("click", () => changeCartQty(String(btn.getAttribute("data-minus") || ""), -1)));
  ui.posCartList.querySelectorAll("[data-del]").forEach((btn) => btn.addEventListener("click", () => removeCartLine(String(btn.getAttribute("data-del") || ""))));
  ui.posCartList.querySelectorAll("[data-cart-qty]").forEach((el) => {
    el.addEventListener("change", () => {
      const pid = String(el.getAttribute("data-cart-qty") || "");
      const nextQty = Math.max(0.01, qtyNum(el.value || 1, 1));
      setCartQtyManual(pid, nextQty);
    });
  });
  ui.posCartList.querySelectorAll("[data-cart-price]").forEach((el) => {
    el.addEventListener("change", () => {
      const pid = String(el.getAttribute("data-cart-price") || "");
      const nextPrice = Math.max(0, toCents(el.value || 0));
      setCartPriceManual(pid, nextPrice);
    });
  });
  ui.posCartList.querySelectorAll("[data-cart-total]").forEach((el) => {
    const applyTotal = () => {
      const pid = String(el.getAttribute("data-cart-total") || "");
      const nextTotal = Math.max(1, toCents(el.value || 0));
      setCartTotalManual(pid, nextTotal);
    };
    el.addEventListener("change", applyTotal);
    el.addEventListener("blur", applyTotal);
    el.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        applyTotal();
      }
    });
  });
  ui.posCartList.querySelectorAll("[data-cart-mode]").forEach((el) => {
    el.addEventListener("change", () => {
      const pid = String(el.getAttribute("data-cart-mode") || "");
      const mode = String(el.value || "retail");
      setCartMode(pid, mode);
    });
  });
  forceEnableSalesControls();
}

function setCartQtyManual(productId, nextQty) {
  const line = posCart.find((x) => String(x.product_id) === String(productId || ""));
  if (!line) return;
  const result = window.WiliPosCartModel.setQty(posCart, productId, Math.max(0.01, qtyNum(nextQty, 1)), {
    stockQty: stockForProduct(line.product_id),
    blockNegativeStock: isNegativeStockBlockedUi()
  });
  if (!result || !result.ok) {
    if (result && result.error === "insufficient_stock") {
      notifyInsufficientStock(String(line.name || "-"), result.stockQty, result.requestedQty);
    }
  }
  renderCart();
  updatePosTotals();
}
function setCartTotalManual(productId, nextTotalCents) {
  if (typeof bindActivePosCartLines === "function") bindActivePosCartLines();
  if (typeof clearActiveSaleQuoteSelection === "function") clearActiveSaleQuoteSelection();
  const line = posCart.find((x) => String(x.product_id) === String(productId || ""));
  if (!line) return;
  const result = window.WiliPosCartModel.setLineTotal(posCart, productId, Math.max(0, Number(nextTotalCents || 0)), {
    adjustQtyFromUnitPrice: true,
    stockQty: stockForProduct(line.product_id),
    blockNegativeStock: isNegativeStockBlockedUi()
  });
  if (!result || !result.ok) {
    if (result && result.error === "insufficient_stock") {
      notifyInsufficientStock(String(line.name || "-"), result.stockQty, result.requestedQty);
    }
  }
  renderCart();
  updatePosTotals();
}
function setCartPriceManual(productId, nextPriceCents) {
  window.WiliPosCartModel.setUnitPrice(posCart, productId, nextPriceCents);
  renderCart();
  updatePosTotals();
}
function setCartMode(productId, mode) {
  const line = posCart.find((x) => String(x.product_id) === String(productId || ""));
  if (!line) return;
  const p = cache.products.find((x) => String(x.id) === String(line.product_id || ""));
  if (!p) return;
  line.price_mode = String(mode || "retail");
  line.unit_price_cents = modePriceCents(p, line.price_mode);
  line.line_total_cents = Math.round(qtyNum(line.qty) * Number(line.unit_price_cents || 0));
  renderCart();
  updatePosTotals();
}

function getPosCheckoutSummary() {
  if (typeof bindActivePosCartLines === "function") bindActivePosCartLines();
  const isCounterCustomer = !String(ui.saleCustomer?.value || "").trim();
  const totalBeforePaid = window.WiliPosCheckoutModel.computeSummary(posCart, {
    discountType: ui.saleDiscountType?.value || "amount",
    discountRaw: ui.saleDiscountValue?.value || 0,
    paidValue: "0"
  }).total_cents || 0;
  const paidRaw = String(ui.salePaid?.value || "");
  const paidValue = isCounterCustomer
    ? money(totalBeforePaid)
    : (ui.salePaidTouched && paidRaw.trim() === "" ? "0" : paidRaw);
  return window.WiliPosCheckoutModel.computeSummary(posCart, {
    discountType: ui.saleDiscountType?.value || "amount",
    discountRaw: ui.saleDiscountValue?.value || 0,
    paidValue
  });
}
function buildPosSaleItemsPayload(summary = getPosCheckoutSummary()) {
  return window.WiliPosCheckoutModel.buildSaleItemsPayload(posCart, summary);
}
function updatePosTotals() {
  const summary = getPosCheckoutSummary();
  const total = summary.total_cents;
  const isCounterCustomer = !String(ui.saleCustomer?.value || "").trim();
  if (isCounterCustomer) {
    ui.salePaid.value = total > 0 ? money(total) : "0.00";
    ui.salePaid.disabled = true;
    ui.salePaid.classList.add("input-locked");
  } else {
    ui.salePaid.disabled = false;
    ui.salePaid.classList.remove("input-locked");
  }
  if (!isCounterCustomer && !ui.salePaidTouched) {
    ui.salePaid.value = total > 0 ? money(total) : "";
  }
  if (!isCounterCustomer && toCents(ui.salePaid.value || 0) > total) {
    ui.salePaid.value = money(total);
  }

  const paid = isCounterCustomer ? total : Math.max(0, Math.min(total, toCents(ui.salePaid.value || 0)));
  const due = Math.max(0, total - paid);
  const qty = summary.qty;

  ui.posGrandTotal.textContent = money(total);
  if (ui.saleDiscountHint) {
    const discountText = summary.discount_type === "percent"
      ? `${summary.discount_raw_value.toFixed(2)}%`
      : money(summary.discount_cents);
    ui.saleDiscountHint.textContent = langText(
      `Remise: ${discountText}`,
      `الخصم: ${discountText}`,
      `الروميز: ${discountText}`
    );
  }
  if (ui.posCartTotals) {
    ui.posCartTotals.innerHTML = [
      row("Qte", qtyText(qty)),
      row("Sous-total", money(summary.subtotal_cents)),
      row("Remise", money(summary.discount_cents)),
      row("Total", money(total)),
      row("Paye", money(paid)),
      row("Reste", money(due))
    ].join("");
  }
  if (typeof updateSaleCustomerDebtHint === "function") updateSaleCustomerDebtHint();
}

async function refreshSales() {
  const res = await window.wiliDb.listSales(2000);
  const rows = res && res.ok ? (res.data || []) : [];
  salesDetailsCache.clear();
  salesRows = rows.map((s) => {
    const customer = cache.customers.find((c) => String(c.id || "") === String(s.customer_id || ""));
    return {
      ...s,
      customer_name: customer ? String(customer.name || "-") : (s.customer_name || "-")
    };
  });
  salesPublicRefMap = buildPublicRefMap(salesRows);
  updateSaleCustomerDebtHint();
  applySalesHistoryFilters(false);
}

function sortSalesRows(rows) {
  const copy = rows.slice();
  const dir = salesJournalState.sortDir === "asc" ? 1 : -1;
  const key = String(salesJournalState.sortKey || "created_at");
  copy.sort((a, b) => {
    if (key === "created_at") return String(a.created_at || "").localeCompare(String(b.created_at || "")) * dir;
    if (key === "total_cents") return (Number(a.total_cents || 0) - Number(b.total_cents || 0)) * dir;
    if (key === "paid_cents") return (Number(a.paid_cents || 0) - Number(b.paid_cents || 0)) * dir;
    if (key === "due_cents") return (Number(a.due_cents || 0) - Number(b.due_cents || 0)) * dir;
    if (key === "status") {
      const rank = (x) => (Number(x && x.due_cents || 0) > 0 ? 1 : 0);
      return (rank(a) - rank(b)) * dir;
    }
    if (key === "public_ref") return String(salePublicRef(a.id, a.created_at) || "").localeCompare(String(salePublicRef(b.id, b.created_at) || "")) * dir;
    return String(a.customer_name || "").localeCompare(String(b.customer_name || ""), undefined, { sensitivity: "base" }) * dir;
  });
  return copy;
}

function sortPurchasesRows(rows) {
  const copy = rows.slice();
  const dir = purchasesJournalState.sortDir === "asc" ? 1 : -1;
  const key = String(purchasesJournalState.sortKey || "created_at");
  copy.sort((a, b) => {
    if (key === "created_at") return String(a.created_at || "").localeCompare(String(b.created_at || "")) * dir;
    if (key === "total_cents") return (Number(a.total_cents || 0) - Number(b.total_cents || 0)) * dir;
    if (key === "paid_cents") return (Number(a.paid_cents || 0) - Number(b.paid_cents || 0)) * dir;
    if (key === "due_cents") return (Number(a.due_cents || 0) - Number(b.due_cents || 0)) * dir;
    if (key === "status") {
      const rank = (x) => (Number(x && x.due_cents || 0) > 0 ? 1 : 0);
      return (rank(a) - rank(b)) * dir;
    }
    if (key === "public_ref") return String(purchasePublicRef(a.id, a.created_at) || "").localeCompare(String(purchasePublicRef(b.id, b.created_at) || "")) * dir;
    return String(a.supplier_name || "").localeCompare(String(b.supplier_name || ""), undefined, { sensitivity: "base" }) * dir;
  });
  return copy;
}

function applySalesSortModeFromSelect(modeRaw) {
  const mode = String(modeRaw || "date_desc");
  if (mode === "date_desc") {
    salesJournalState.sortKey = "created_at";
    salesJournalState.sortDir = "desc";
  } else if (mode === "date_asc") {
    salesJournalState.sortKey = "created_at";
    salesJournalState.sortDir = "asc";
  } else if (mode === "total_desc") {
    salesJournalState.sortKey = "total_cents";
    salesJournalState.sortDir = "desc";
  } else if (mode === "due_desc") {
    salesJournalState.sortKey = "due_cents";
    salesJournalState.sortDir = "desc";
  } else if (mode === "customer_asc") {
    salesJournalState.sortKey = "customer_name";
    salesJournalState.sortDir = "asc";
  }
}

function applyPurchasesSortModeFromSelect(modeRaw) {
  const mode = String(modeRaw || "date_desc");
  if (mode === "date_desc") {
    purchasesJournalState.sortKey = "created_at";
    purchasesJournalState.sortDir = "desc";
  } else if (mode === "date_asc") {
    purchasesJournalState.sortKey = "created_at";
    purchasesJournalState.sortDir = "asc";
  } else if (mode === "total_desc") {
    purchasesJournalState.sortKey = "total_cents";
    purchasesJournalState.sortDir = "desc";
  } else if (mode === "due_desc") {
    purchasesJournalState.sortKey = "due_cents";
    purchasesJournalState.sortDir = "desc";
  } else if (mode === "supplier_asc") {
    purchasesJournalState.sortKey = "supplier_name";
    purchasesJournalState.sortDir = "asc";
  }
}

async function openSaleDetailModal(saleId) {
  const sid = String(saleId || "").trim();
  if (!sid) return;
  const s = salesRows.find((x) => String(x.id || "") === sid);
  const out = await window.wiliDb.getSaleDetails(sid);
  if (!out || !out.ok) {
    if (ui.journalDetailBody) ui.journalDetailBody.innerHTML = row("Erreur", out && out.error ? out.error : "sale_details_failed");
    openJournalDetailModal(langText("Details vente", "تفاصيل البيع", "تفاصيل البيع"));
    return;
  }
  const d = out.data || {};
  const inv = d.invoice || {};
  const lines = Array.isArray(d.items) ? d.items : [];
  const pays = Array.isArray(d.payments) ? d.payments : [];
  const rets = Array.isArray(d.returns) ? d.returns : [];
  const returnedByProduct = new Map();
  for (const r of rets) {
    const pid = String(r && r.product_id ? r.product_id : "");
    if (!pid) continue;
    returnedByProduct.set(pid, qtyNum((returnedByProduct.get(pid) || 0) + Math.max(0, qtyNum(r && r.qty))));
  }
  const linesHtml = lines.map((it) => `
    <tr>
      <td>${esc(it.product_name || it.product_id || "-")}</td>
      <td>${qtyText(it.qty)}</td>
      <td>${qtyText(returnedByProduct.get(String(it.product_id || "")) || 0)}</td>
      <td>${qtyText(Math.max(0, qtyNum(it.qty) - qtyNum(returnedByProduct.get(String(it.product_id || "")) || 0)))}</td>
      <td>${money(it.unit_price_cents)}</td>
      <td>${money(it.line_total_cents)}</td>
      <td><button class="btn-mini" data-sale-return-modal="${esc(it.product_id)}">Retour</button></td>
    </tr>
  `).join("");
  const paysHtml = pays.map((p) => `
    <tr>
      <td>${esc(fmtDateTime(p.created_at))}</td>
      <td>${money(p.amount_cents)}</td>
      <td>${esc(p.note || "-")}</td>
    </tr>
  `).join("");
  const retsHtml = rets.map((r) => `
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
  if (ui.journalDetailBody) {
    ui.journalDetailBody.innerHTML = [
      detailSummaryGrid([
        { label: "Vente", value: salePublicRef(inv.id || sid, inv.created_at || (s && s.created_at)), accent: true },
        { label: "Type", value: String(inv.doc_type || (String(inv.customer_id || "").trim() ? "BL" : "VCC")) },
        { label: "Date", value: fmtDateTime(inv.created_at || (s && s.created_at) || "") },
        { label: "Client", value: (d.customer && d.customer.name) ? d.customer.name : ((s && s.customer_name) || "-") },
        { label: "Total", value: money(inv.total_cents || 0), accent: true },
        { label: "Paye", value: money(inv.paid_cents || 0) },
        { label: "Reste", value: money(inv.due_cents || 0) }
      ]),
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Lignes facture")}</div><table class="table-lite"><thead><tr><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${esc(langText("Qte retournee", "الكمية المرجعة", "لكمية لمرجعة"))}</th><th>${esc(langText("Qte restante", "الكمية المتبقية", "لكمية لي باقية"))}</th><th>${trText("Prix")}</th><th>${trText("Total")}</th><th>${trText("Hist")}</th></tr></thead><tbody>${linesHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Paiements")}</div><table class="table-lite"><thead><tr><th>${trText("Date")}</th><th>${trText("Montant")}</th><th>${trText("Note")}</th></tr></thead><tbody>${paysHtml || `<tr><td colspan="3" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Retours client")}</div><table class="table-lite"><thead><tr><th>${trText("Ref")}</th><th>${trText("Date")}</th><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${trText("Montant")}</th><th>${trText("Rembourse")}</th><th>${trText("Note")}</th></tr></thead><tbody>${retsHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`
    ].join("");
    ui.journalDetailBody.querySelectorAll("[data-sale-return-modal]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const productId = String(btn.getAttribute("data-sale-return-modal") || "");
        if (!productId || !sid) return;
        const payload = await appAskReturnPayload({
          title: langText("Retour client", "ارجاع زبون", "رتور لكليان"),
          defaultQty: 1,
          defaultNote: ""
        });
        if (!payload) return;
        const returnQty = Math.max(0.01, qtyNum(payload.qty || 1, 1));
        const note = String(payload.note || "").trim();
        const retOut = await window.wiliDb.addSaleReturn({
          sale_id: sid,
          product_id: productId,
          qty: returnQty,
          note
        });
        if (!retOut || !retOut.ok) {
          appNotify(retOut && retOut.error ? retOut.error : "sale_return_failed", true, 5000);
          return;
        }
        salesDetailsCache.delete(String(sid || ""));
        await refreshAll();
        await openSaleDetailModal(sid);
      });
    });
  }
  journalDetailContext = { type: "sale", id: sid };
  openJournalDetailModal(langText("Details vente", "تفاصيل البيع", "تفاصيل البيع"));
}

async function openPurchaseDetailModal(purchaseId) {
  const pid = String(purchaseId || "").trim();
  if (!pid) return;
  const p = purchaseRows.find((x) => String(x.id || "") === pid);
  const out = await window.wiliDb.getPurchaseDetails(pid);
  if (!out || !out.ok) {
    if (ui.journalDetailBody) ui.journalDetailBody.innerHTML = row("Erreur", out && out.error ? out.error : "purchase_details_failed");
    openJournalDetailModal(langText("Details achat", "تفاصيل الشراء", "تفاصيل الشرا"));
    return;
  }
  const d = out.data || {};
  const inv = d.invoice || {};
  const lines = Array.isArray(d.items) ? d.items : [];
  const pays = Array.isArray(d.payments) ? d.payments : [];
  const rets = Array.isArray(d.returns) ? d.returns : [];
  const returnedByProduct = new Map();
  for (const r of rets) {
    const pid = String(r && r.product_id ? r.product_id : "");
    if (!pid) continue;
    returnedByProduct.set(pid, qtyNum((returnedByProduct.get(pid) || 0) + Math.max(0, qtyNum(r && r.qty))));
  }
  const linesHtml = lines.map((it) => `
    <tr>
      <td>${esc(it.product_name || it.product_id || "-")}</td>
      <td>${qtyText(it.qty)}</td>
      <td>${qtyText(returnedByProduct.get(String(it.product_id || "")) || 0)}</td>
      <td>${qtyText(Math.max(0, qtyNum(it.qty) - qtyNum(returnedByProduct.get(String(it.product_id || "")) || 0)))}</td>
      <td>${money(it.unit_cost_cents)}</td>
      <td>${money(it.line_total_cents)}</td>
      <td><button class="btn-mini" data-purchase-return-modal="${esc(it.product_id)}">Retour</button></td>
    </tr>
  `).join("");
  const paysHtml = pays.map((x) => `
    <tr>
      <td>${esc(fmtDateTime(x.created_at))}</td>
      <td>${money(x.amount_cents)}</td>
      <td>${esc(x.note || "-")}</td>
    </tr>
  `).join("");
  const retsHtml = rets.map((x) => `
    <tr>
      <td>${esc(x.public_ref || "-")}</td>
      <td>${esc(fmtDateTime(x.created_at))}</td>
      <td>${esc(x.product_name || x.product_id || "-")}</td>
      <td>${qtyText(x.qty)}</td>
      <td>${money(x.amount_cents)}</td>
      <td>${money(x.refund_cents)}</td>
      <td>${esc(x.note || "-")}</td>
    </tr>
  `).join("");
  if (ui.journalDetailBody) {
    ui.journalDetailBody.innerHTML = [
      detailSummaryGrid([
        { label: "Achat", value: purchasePublicRef(inv.id || pid, inv.created_at || (p && p.created_at)), accent: true },
        { label: "Type", value: String(inv.doc_type || "BR") },
        { label: "Date", value: fmtDateTime(inv.created_at || (p && p.created_at) || "") },
        { label: "Fournisseur", value: (d.supplier && d.supplier.name) ? d.supplier.name : ((p && p.supplier_name) || "-") },
        { label: "Total", value: money(inv.total_cents || 0), accent: true },
        { label: "Paye", value: money(inv.paid_cents || 0) },
        { label: "Reste", value: money(inv.due_cents || 0) }
      ]),
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Lignes achat")}</div><table class="table-lite"><thead><tr><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${esc(langText("Qte retournee", "الكمية المرجعة", "لكمية لمرجعة"))}</th><th>${esc(langText("Qte restante", "الكمية المتبقية", "لكمية لي باقية"))}</th><th>${trText("Cout")}</th><th>${trText("Total")}</th><th>${trText("Hist")}</th></tr></thead><tbody>${linesHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Paiements")}</div><table class="table-lite"><thead><tr><th>${trText("Date")}</th><th>${trText("Montant")}</th><th>${trText("Note")}</th></tr></thead><tbody>${paysHtml || `<tr><td colspan="3" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
      `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Retours fournisseur")}</div><table class="table-lite"><thead><tr><th>${trText("Ref")}</th><th>${trText("Date")}</th><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${trText("Montant")}</th><th>${trText("Rembourse")}</th><th>${trText("Note")}</th></tr></thead><tbody>${retsHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`
    ].join("");
    ui.journalDetailBody.querySelectorAll("[data-purchase-return-modal]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const productId = String(btn.getAttribute("data-purchase-return-modal") || "");
        if (!productId || !pid) return;
        const payload = await appAskReturnPayload({
          title: langText("Retour fournisseur", "ارجاع مورد", "رتور لفرنسور"),
          defaultQty: 1,
          defaultNote: ""
        });
        if (!payload) return;
        const returnQty = Math.max(0.01, qtyNum(payload.qty || 1, 1));
        const note = String(payload.note || "").trim();
        const retOut = await window.wiliDb.addPurchaseReturn({
          purchase_id: pid,
          product_id: productId,
          qty: returnQty,
          note
        });
        if (!retOut || !retOut.ok) {
          appNotify(retOut && retOut.error ? retOut.error : "purchase_return_failed", true, 5000);
          return;
        }
        purchaseDetailsCache.delete(String(pid || ""));
        await refreshAll();
        await openPurchaseDetailModal(pid);
      });
    });
  }
  journalDetailContext = { type: "purchase", id: pid };
  openJournalDetailModal(langText("Details achat", "تفاصيل الشراء", "تفاصيل الشرا"));
}

async function exportPurchaseInvoicePdfById(purchaseId, targetEl, documentMode = "bon") {
  const pid = String(purchaseId || "").trim();
  if (!pid) return;
  const out = await window.wiliDb.getPurchaseDetails(pid);
  if (!out || !out.ok) {
    appNotify(out && out.error ? out.error : "purchase_details_failed", true, 5200);
    return;
  }
  const d = out.data || {};
  const items = Array.isArray(d.items) ? d.items.map((it) => ({
    product_id: String(it.product_id || ""),
    qty: qtyNum(it.qty),
    unit_cost_cents: Number(it.unit_cost_cents || 0),
    product_name: String(it.product_name || it.product_id || "-")
  })) : [];
  const exportOut = await window.wiliDb.exportPurchasePdf({
    invoice: d.invoice || {},
    items,
    paid_cents: Number((d.invoice || {}).paid_cents || 0),
    document_mode: documentMode
  });
  handlePdfResult(exportOut, documentMode === "facture" ? langText("Facture D'Achat", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0634\u0631\u0627\u0621") : langText("Bon de Reception", "\u0648\u0635\u0644 \u0627\u0633\u062a\u0644\u0627\u0645", "\u0628\u0648\u0646 \u0631\u064a\u0633\u0628\u0633\u064a\u0648\u0646"), targetEl || ui.purchasesJournalTableBody);
}

async function renderSalesSelectedDetail() {
  if (!salesSelectedId) {
    ui.salesHistDetail.innerHTML = row("Selection", "Choisir une vente depuis la liste");
    return;
  }
  const s = salesRowsFiltered.find((x) => String(x.id || "") === String(salesSelectedId || ""));
  if (!s) {
    ui.salesHistDetail.innerHTML = row("Selection", "Aucune vente selectionnee");
    return;
  }
  ui.salesHistDetail.innerHTML = [
    row("Vente", salePublicRef(s.id, s.created_at)),
    row("Date", fmtDateTime(s.created_at)),
    row("Client", s.customer_name || "-"),
    row("Total", money(s.total_cents)),
    row("Paye", money(s.paid_cents)),
    row("Reste", money(s.due_cents)),
    row("Lignes", "Chargement...")
  ].join("");
  let details = salesDetailsCache.get(String(s.id || ""));
  if (!details) {
    const seq = ++salesDetailLoadSeq;
    const out = await window.wiliDb.getSaleDetails(String(s.id || ""));
    if (seq !== salesDetailLoadSeq) return;
    if (!out || !out.ok) {
      ui.salesHistDetail.innerHTML = row("Erreur", out && out.error ? out.error : "sale_details_failed");
      return;
    }
    details = out.data;
    salesDetailsCache.set(String(s.id || ""), details);
  }
  const lines = Array.isArray(details && details.items) ? details.items : [];
  const payments = Array.isArray(details && details.payments) ? details.payments : [];
  const returns = Array.isArray(details && details.returns) ? details.returns : [];
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
      <td>${money(it.unit_price_cents)}</td>
      <td>${money(it.line_total_cents)}</td>
      <td><button class="btn-mini" data-open-product="${esc(it.product_id)}">H</button> <button class="btn-mini" data-sale-return="${esc(it.product_id)}">Retour</button></td>
    </tr>
  `).join("");
  const payRows = payments.map((p) => `
    <tr>
      <td>${esc(fmtDateTime(p.created_at))}</td>
      <td>${money(p.amount_cents)}</td>
      <td>${esc(p.note || "-")}</td>
      <td><button class="btn-mini" data-pay-pdf="${esc(p.id)}">Telecharger</button></td>
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
  ui.salesHistDetail.innerHTML = [
    row("Vente", salePublicRef(s.id, s.created_at)),
    row("Type", String(s.doc_type || (String(s.customer_id || "").trim() ? "BL" : "VCC"))),
    row("Date", fmtDateTime(s.created_at)),
    row("Client", s.customer_name || "-"),
    row("Total", money(s.total_cents)),
    row("Paye", money(s.paid_cents)),
    row("Reste", money(s.due_cents)),
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Lignes facture")}</div><table class="table-lite"><thead><tr><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${esc(langText("Qte retournee", "الكمية المرجعة", "لكمية لمرجعة"))}</th><th>${esc(langText("Qte restante", "الكمية المتبقية", "لكمية لي باقية"))}</th><th>${trText("Prix")}</th><th>${trText("Total")}</th><th>${trText("Hist")}</th></tr></thead><tbody>${rowsHtml || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Paiements")}</div><table class="table-lite"><thead><tr><th>${trText("Date")}</th><th>${trText("Montant")}</th><th>${trText("Note")}</th><th>${trText("Recu")}</th></tr></thead><tbody>${payRows || `<tr><td colspan="4" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`,
    `<div class="row"><div class="detail-section"><div class="detail-section-title">${trText("Retours client")}</div><table class="table-lite"><thead><tr><th>${trText("Ref")}</th><th>${trText("Date")}</th><th>${trText("Produit")}</th><th>${trText("Qte")}</th><th>${trText("Montant")}</th><th>${trText("Rembourse")}</th><th>${trText("Note")}</th></tr></thead><tbody>${returnRows || `<tr><td colspan="7" class="empty">${t("no_data")}</td></tr>`}</tbody></table></div></div>`
  ].join("");
  ui.salesHistDetail.querySelectorAll("[data-open-product]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const pid = String(btn.getAttribute("data-open-product") || "");
      if (!pid) return;
      switchView("history");
      refreshHistory(pid);
    });
  });
  ui.salesHistDetail.querySelectorAll("[data-pay-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const pid = String(btn.getAttribute("data-pay-pdf") || "");
      if (!pid) return;
      const out = await window.wiliDb.exportSalePaymentReceiptPdf(pid);
      handlePdfResult(out, langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص"), ui.salesHistDetail);
    });
  });
  ui.salesHistDetail.querySelectorAll("[data-sale-return]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const productId = String(btn.getAttribute("data-sale-return") || "");
      if (!productId || !salesSelectedId) return;
      const payload = await appAskReturnPayload({
        title: langText("Retour client", "ارجاع زبون", "رتور لكليان"),
        defaultQty: 1,
        defaultNote: ""
      });
      if (!payload) return;
      const returnQty = Math.max(0.01, qtyNum(payload.qty || 1, 1));
      const note = String(payload.note || "").trim();
      const out = await window.wiliDb.addSaleReturn({
        sale_id: salesSelectedId,
        product_id: productId,
        qty: returnQty,
        note
      });
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "sale_return_failed", true, 5000);
        return;
      }
      salesDetailsCache.delete(String(salesSelectedId || ""));
      await refreshAll();
      await renderSalesSelectedDetail();
    });
  });
}

function applySalesHistoryFilters(resetPage = true) {
  const allowDelete = isAdminUser();
  const q = String(ui.salesHistSearch?.value || "").trim().toLowerCase();
  const from = String(ui.salesHistFrom?.value || "").trim();
  const to = String(ui.salesHistTo?.value || "").trim();
  const filtered = salesRows.filter((s) => {
    const day = String(s.created_at || "").slice(0, 10);
    const inFrom = !from || day >= from;
    const inTo = !to || day <= to;
    const byQ = !q
      || String(s.id || "").toLowerCase().includes(q)
      || salePublicRef(s.id, s.created_at).toLowerCase().includes(q)
      || String(s.customer_name || "").toLowerCase().includes(q);
    return inFrom && inTo && byQ;
  });
  salesRowsFiltered = sortSalesRows(filtered);
  if (resetPage) salesHistPage = 1;
  const pageSize = Math.max(1, intQty(ui.salesHistPageSize?.value || 20));
  const totalPages = Math.max(1, Math.ceil(salesRowsFiltered.length / pageSize));
  salesHistPage = Math.min(totalPages, Math.max(1, salesHistPage));
  const start = (salesHistPage - 1) * pageSize;
  const pageRows = salesRowsFiltered.slice(start, start + pageSize);
  const total = salesRowsFiltered.reduce((sum, s) => sum + Number(s.total_cents || 0), 0);
  const paid = salesRowsFiltered.reduce((sum, s) => sum + Number(s.paid_cents || 0), 0);
  const due = salesRowsFiltered.reduce((sum, s) => sum + Number(s.due_cents || 0), 0);
  ui.salesKpiCount.textContent = String(salesRowsFiltered.length);
  ui.salesKpiTotal.textContent = money(total);
  ui.salesKpiDue.textContent = money(due);
  ui.salesHistPageInfo.textContent = langText(`Page ${salesHistPage}/${totalPages}`, `صفحة ${salesHistPage}/${totalPages}`);
  ui.salesHistStats.innerHTML = [
    row("Ventes filtrees", String(salesRowsFiltered.length)),
    row("Total", money(total)),
    row("Paye", money(paid)),
    row("Reste", money(due))
  ].join("");
  ui.salesHistoryTableBody.innerHTML = pageRows.length ? pageRows.map((s) => `
    <tr data-sale-row="${esc(s.id)}" class="${String(s.id || "") === String(salesSelectedId || "") ? "row-selected" : ""}">
      <td><strong>${esc(salePublicRef(s.id, s.created_at))}</strong></td>
      <td>${esc(fmtDateTime(s.created_at))}</td>
      <td>${esc(s.customer_name || "-")}</td>
      <td>${money(s.total_cents)}</td>
      <td>${money(s.paid_cents)}</td>
      <td>${money(s.due_cents)}</td>
      <td>${Number(s.due_cents || 0) > 0 ? trText("Credit") : trText("Solde")}</td>
      <td>
        <div class="qty-actions">
          <button class="btn-mini" data-sale-details="${esc(s.id)}">${langText("Ouvrir", "فتح", "حل")}</button>
          <button class="btn-mini" data-sale-return-all="${esc(s.id)}">${esc(langText("Retour Client", "\u0625\u0631\u062c\u0627\u0639 \u0632\u0628\u0648\u0646", "\u0631\u0648\u062a\u0648\u0631 \u0643\u0644\u064a\u0627\u0646"))}</button>
          <button class="btn-mini" data-sale-pdf="${esc(s.id)}">${esc(saleBonLabel(s))}</button>
          <button class="btn-mini" data-sale-facture-pdf="${esc(s.id)}">${esc(langText("Facture De Vente", "\u0641\u0627\u062a\u0648\u0631\u0629 \u0628\u064a\u0639", "\u0641\u0627\u0643\u062a\u0648\u0631\u0629 \u0628\u064a\u0639"))}</button>
          ${allowDelete ? `<button class="btn-mini" data-sale-edit="${esc(s.id)}">${langText("Modifier", "تعديل", "بدل")}</button>` : ""}
          ${allowDelete ? `<button class="btn-mini" data-sale-del="${esc(s.id)}">${langText("Supprimer", "حذف", "حذف")}</button>` : ""}
        </div>
      </td>
    </tr>
  `).join("") : `<tr><td colspan="8" class="empty">${t("no_data")}</td></tr>`;
  updateSortHeaderIndicators(ui.salesHistoryTable, salesJournalState.sortKey || "created_at", salesJournalState.sortDir || "desc");
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-row]").forEach((el) => {
    el.addEventListener("click", () => {
      salesSelectedId = String(el.getAttribute("data-sale-row") || "");
      applySalesHistoryFilters(false);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-details]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const sid = String(btn.getAttribute("data-sale-details") || "");
      salesSelectedId = sid;
      applySalesHistoryFilters(false);
      await openSaleDetailModal(sid);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-sale-pdf") || "");
      const row = salesRowsFiltered.find((x) => String(x.id || "") === id) || {};
      const out = await window.wiliDb.exportSaleInvoicePdf(id, { document_mode: "bon" });
      handlePdfResult(out, salePdfResultLabel(row, "bon"), ui.salesHistoryTableBody);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-facture-pdf]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = String(btn.getAttribute("data-sale-facture-pdf") || "");
      const row = salesRowsFiltered.find((x) => String(x.id || "") === id) || {};
      const out = await window.wiliDb.exportSaleInvoicePdf(id, { document_mode: "facture" });
      handlePdfResult(out, salePdfResultLabel(row, "facture"), ui.salesHistoryTableBody);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-return-all]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const saleId = String(btn.getAttribute("data-sale-return-all") || "");
      if (!saleId) return;
      const ok = await appConfirm(
        langText(
          "Confirmer retour complet de cette facture ?",
          "تأكيد إرجاع الفاتورة كاملة؟",
          "تأكد بلي ترجع كامل هاد الفاتورة؟"
        )
      );
      if (!ok) return;
      const out = await window.wiliDb.addSaleReturnAll({
        sale_id: saleId,
        note: langText("retour facture complete", "ارجاع فاتورة كاملة", "رتور فاكتور كاملة")
      });
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "sale_return_failed", true, 5000);
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
      if (String(salesSelectedId || "") === saleId) {
        salesDetailsCache.delete(saleId);
      }
      await refreshAll();
      applySalesHistoryFilters(false);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-edit]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const saleId = String(btn.getAttribute("data-sale-edit") || "");
      if (!saleId) return;
      await startSaleEdit(saleId);
    });
  });
  ui.salesHistoryTableBody.querySelectorAll("[data-sale-del]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const saleId = String(btn.getAttribute("data-sale-del") || "");
      if (!saleId) return;
      const ok = await appConfirm(langText("Confirmer suppression de cette vente ?", "تأكيد حذف هذه العملية؟", "تأكد بلي تحذف هاذ العملية؟"));
      if (!ok) return;
      const out = await window.wiliDb.deleteSale(saleId);
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "delete_failed", true);
        return;
      }
      if (String(salesSelectedId || "") === saleId) salesSelectedId = "";
      await refreshAll();
      applySalesHistoryFilters(false);
      appNotify(langText("Suppression terminee", "تم الحذف", "تم الحذف"), false);
    });
  });
  if (salesSelectedId && !salesRowsFiltered.some((x) => String(x.id || "") === String(salesSelectedId || ""))) {
    salesSelectedId = "";
  }
  renderSalesHistoryCharts();
  void renderSalesSelectedDetail();
}

async function refreshPurchases() {
  const res = await window.wiliDb.listPurchases(250);
  const rows = res && res.ok ? (res.data || []) : [];
  purchaseDetailsCache.clear();
  purchaseRows = rows.map((r) => {
    const supplier = cache.suppliers.find((s) => String(s.id || "") === String(r.supplier_id || ""));
    return {
      ...r,
      supplier_name: supplier ? String(supplier.name || "-") : (r.supplier_name || "-")
    };
  });
  purchasesPublicRefMap = buildPublicRefMap(purchaseRows);
  applyPurchasesJournalFilters();
  if (!purchaseSelectedId && purchaseRows.length) {
    purchaseSelectedId = String(purchaseRows[0].id || "");
  }
  if (purchaseSelectedId && !purchaseRows.some((x) => String(x.id || "") === String(purchaseSelectedId || ""))) {
    purchaseSelectedId = "";
  }
  await refreshPurchaseDebtInvoiceOptions(ui.purchaseDebtSupplier.value || "");
  await renderPurchaseSelectedDetail();
  renderPurchasesCharts();
}
