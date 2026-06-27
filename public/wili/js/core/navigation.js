function viewTitle(view) {
  if (view === "losses") return langText("Pertes stock", "خسائر المخزون", "خساير الستوك");
  if (view === "caisse") return langText("Caisse", "الصندوق", "لاكاص");
  return t(view);
}

function viewSub(view) {
  if (view === "losses") return langText("Produits casses, perdus ou perimes", "المنتجات المكسورة أو المفقودة أو المنتهية", "المنتوجات المكسرة ولا الضايعة ولا الفاسدة");
  if (view === "caisse") return langText("Ouverture, controle et fin de jour", "فتح الصندوق ومراقبة نهاية اليوم", "حل لاكاص وكونترول فين دو جور");
  return t(`sub_${view}`);
}

function setHeader(view) {
  headerTitle.textContent = viewTitle(view);
  headerSub.textContent = viewSub(view);
}

function renderWorkspaceTabs(activeView) {
  if (!ui.workspaceTabs) return;
  const uniq = Array.from(new Set(openTabs)).filter((v) => canAccessView(v));
  if (!uniq.length) {
    ui.workspaceTabs.innerHTML = "";
    return;
  }
  ui.workspaceTabs.innerHTML = uniq.map((v) => `<button class="ws-tab ${v === activeView ? "active" : ""}" data-ws-view="${esc(v)}">${esc(viewTitle(v))}</button>`).join("");
  ui.workspaceTabs.querySelectorAll("[data-ws-view]").forEach((btn) => {
    btn.addEventListener("click", () => switchView(String(btn.getAttribute("data-ws-view") || "sales")));
  });
}

function resolveDomainByView(view) {
  for (const key of Object.keys(DOMAIN_CONFIG)) {
    if (DOMAIN_CONFIG[key].views.includes(view)) return key;
  }
  return "settings";
}

function renderDomainState() {
  document.querySelectorAll(".domain-btn").forEach((btn) => {
    const domain = String(btn.getAttribute("data-domain") || "");
    const allowed = canAccessDomain(domain);
    btn.classList.toggle("hidden", !allowed);
    btn.classList.toggle("active", allowed && domain === activeDomain);
  });
}

function renderSubnav() {
  const cfg = DOMAIN_CONFIG[activeDomain] || DOMAIN_CONFIG.comptoir;
  const domainViews = (cfg.views || []).filter((v) => canAccessView(v));
  const fallbackView = domainViews[0] || firstAccessibleViewForDomain(activeDomain);
  if (!ui.subnav) return;
  if (!domainViews.length) {
    ui.subnav.innerHTML = "";
    return;
  }
  ui.subnav.innerHTML = domainViews.map((view) => {
    const hot = VIEW_SHORTCUTS[view] ? VIEW_SHORTCUTS[view].label : "";
    const label = view === "settings" ? t("nav_settings") : viewTitle(view);
    return `<button class="nav-btn subnav-btn ${view === activeView ? "active" : ""}" data-view="${esc(view)}">${iconSvg(iconForView(view))}<span>${esc(label)}</span>${hot ? `<span class="hot">${hot}</span>` : ""}</button>`;
  }).join("");
  ui.subnav.querySelectorAll(".subnav-btn").forEach((btn) => {
    btn.addEventListener("click", () => switchView(String(btn.getAttribute("data-view") || fallbackView)));
  });
}

function updateStatusBar(view) {
  const isArabicUi = session.language === "ar" || session.language === "dz";
  ui.sbView.textContent = isArabicUi
    ? (session.language === "dz" ? `الفيو: ${viewTitle(view)}` : `الواجهة: ${viewTitle(view)}`)
    : `Vue: ${view}`;
  if (session.user) {
    const role = getUserRoleLabel(session.user.role);
    ui.sbUser.textContent = isArabicUi
      ? (session.language === "dz"
        ? `ليوزر: ${session.user.display_name || session.user.username} (${role})`
        : `المستخدم: ${session.user.display_name || session.user.username} (${role})`)
      : `Utilisateur: ${session.user.display_name || session.user.username} (${role})`;
  } else {
    ui.sbUser.textContent = isArabicUi
      ? (session.language === "dz" ? "ليوزر: -" : "المستخدم: -")
      : "Utilisateur: -";
  }
}

function switchView(view) {
  const nextView = normalizeTargetView(view);
  const prevView = activeView;
  activeView = nextView;
  views.forEach((v) => byId(`view-${v}`)?.classList.toggle("active", v === nextView));
  let nextDomain = resolveDomainByView(nextView);
  if (!canAccessDomain(nextDomain)) nextDomain = "comptoir";
  activeDomain = nextDomain;
  renderDomainState();
  renderSubnav();
  if (!openTabs.includes(nextView)) openTabs.push(nextView);
  renderWorkspaceTabs(nextView);
  setHeader(nextView);
  updateStatusBar(nextView);
  if (nextView === "sales") {
    if (saleSubmitInFlight) {
      saleSubmitInFlight = false;
      stopSaleSubmitWatchdog();
    }
    forceEnableSalesControls();
    setTimeout(() => ui.posSearchInput?.focus(), 0);
    renderPosSearch(ui.posSearchInput?.value || "");
  }
  if (nextView === "losses" && typeof refreshStockLosses === "function") {
    Promise.resolve().then(() => refreshStockLosses());
  }
  if (nextView === "caisse" && typeof refreshCaisseState === "function") {
    Promise.resolve().then(() => refreshCaisseState());
  }
  if (nextView === "customerJournal") {
    Promise.resolve().then(async () => {
      try {
        if (typeof refreshCustomers === "function") await refreshCustomers();
        if (typeof refreshSales === "function") await refreshSales();
        if (typeof refreshCustomerTrace === "function") await refreshCustomerTrace();
      } catch (_e) {}
    });
  }
  if (nextView === "supplierJournal") {
    Promise.resolve().then(async () => {
      try {
        if (typeof refreshSuppliers === "function") await refreshSuppliers();
        if (typeof refreshPurchases === "function") await refreshPurchases();
        if (typeof refreshSupplierTrace === "function") await refreshSupplierTrace();
      } catch (_e) {}
    });
  }
}

function showGate(step) {
  gateRoot.classList.remove("hidden");
  appRoot.classList.add("hidden");
  stepActivate.classList.toggle("hidden", step !== "activate");
  stepBootstrap.classList.toggle("hidden", step !== "bootstrap");
  stepLogin.classList.toggle("hidden", step !== "login");
}

function showApp() {
  gateRoot.classList.add("hidden");
  appRoot.classList.remove("hidden");
}

function openProductModal() {
  ui.productModal?.classList.remove("hidden");
}

function closeProductModal() {
  ui.productModal?.classList.add("hidden");
}

function openCustomerModal() {
  ui.customerModal?.classList.remove("hidden");
}

function closeCustomerModal() {
  ui.customerModal?.classList.add("hidden");
}

function openSupplierModal() {
  ui.supplierModal?.classList.remove("hidden");
}

function closeSupplierModal() {
  ui.supplierModal?.classList.add("hidden");
}

function openJournalDetailModal(title) {
  if (ui.journalDetailTitle) ui.journalDetailTitle.textContent = normalizeUiText(title || langText("Details", "تفاصيل", "تفاصيل"));
  ui.journalDetailModal?.classList.remove("hidden");
}

function closeJournalDetailModal() {
  journalDetailContext = { type: "", id: "" };
  if (ui.journalDetailBody) ui.journalDetailBody.innerHTML = "";
  ui.journalDetailModal?.classList.add("hidden");
}

function productImageUrl(pathValue) {
  const p = String(pathValue || "").trim();
  if (!p) return "";
  if (/^(file|https?):\/\//i.test(p)) return p;
  return `file:///${p.replace(/\\/g, "/").replace(/^\/+/, "")}`;
}

function setProductImagePreview(pathValue) {
  const path = String(pathValue || "").trim();
  if (ui.productImagePath) ui.productImagePath.value = path;
  if (!ui.productImagePreview) return;
  const url = productImageUrl(path);
  ui.productImagePreview.style.backgroundImage = url ? `url("${url.replace(/"/g, "%22")}")` : "";
  ui.productImagePreview.classList.toggle("empty", !url);
}

function isProductServiceForm() {
  return String(ui.productType?.value || "product") === "service";
}

function applyProductTypeUi() {
  const isService = isProductServiceForm();
  const detail = ui.productRetail?.value || "0";
  [
    ui.productCost,
    ui.productWholesale,
    ui.productAssist,
    ui.productStock,
    ui.productExpiry
  ].forEach((input) => {
    if (!input) return;
    input.disabled = isService || (input === ui.productStock && Boolean(ui.productId?.value));
    input.closest?.(".form-field")?.classList.toggle("product-service-hidden", isService);
  });
  if (isService) {
    if (ui.productCost) ui.productCost.value = "0";
    if (ui.productWholesale) ui.productWholesale.value = detail;
    if (ui.productAssist) ui.productAssist.value = detail;
    if (ui.productStock) ui.productStock.value = "0";
    if (ui.productExpiry) ui.productExpiry.value = "";
  }
  if (typeof setProductCostMeta === "function") setProductCostMeta();
}

function resetProductForm() {
  ui.productId.value = "";
  ui.productName.value = "";
  if (ui.productType) ui.productType.value = "product";
  setProductImagePreview("");
  ui.productBarcode.value = "";
  if (ui.productReference) ui.productReference.value = "";
  ui.productCost.value = "";
  ui.productWholesale.value = "";
  ui.productRetail.value = "";
  ui.productAssist.value = "";
  ui.productStock.value = "";
  ui.productStock.disabled = false;
  ui.productExpiry.value = "";
  if (ui.productModalTitle) ui.productModalTitle.textContent = langText("Ajouter produit", "اضافة منتج", "زيد منتوج");
  ui.productSubmitBtn.textContent = langText("Sauvegarder produit", "حفظ المنتج");
  ui.productCancelBtn.classList.add("hidden");
  if (ui.productCostMeta) {
    ui.productCostMeta.textContent = "";
    ui.productCostMeta.dataset.avgCostCents = "0";
    ui.productCostMeta.dataset.lastCostCents = "0";
    ui.productCostMeta.classList.add("hidden");
  }
  if (ui.productMarginHint) {
    ui.productMarginHint.textContent = "";
    ui.productMarginHint.classList.add("hidden");
  }
  [ui.productWholesale, ui.productRetail, ui.productAssist].forEach((input) => input?.classList.remove("price-warning-input"));
  applyProductTypeUi();
}

function resetCustomerForm() {
  ui.customerId.value = "";
  ui.customerName.value = "";
  ui.customerPhone.value = "";
  if (ui.customerNif) ui.customerNif.value = "";
  if (ui.customerNis) ui.customerNis.value = "";
  if (ui.customerRc) ui.customerRc.value = "";
  if (ui.customerAi) ui.customerAi.value = "";
  if (ui.customerAddress) ui.customerAddress.value = "";
  if (ui.customerActivity) ui.customerActivity.value = "";
  if (ui.customerCommerceName) ui.customerCommerceName.value = "";
  ui.customerSubmitBtn.textContent = langText("Ajouter client", "اضافة زبون");
  if (ui.customerModalTitle) ui.customerModalTitle.textContent = langText("Ajouter client", "اضافة زبون", "زيد لكليان");
  ui.customerCancelBtn.classList.add("hidden");
}

function resetSupplierForm() {
  ui.supplierId.value = "";
  ui.supplierName.value = "";
  ui.supplierPhone.value = "";
  if (ui.supplierNif) ui.supplierNif.value = "";
  if (ui.supplierNis) ui.supplierNis.value = "";
  if (ui.supplierRc) ui.supplierRc.value = "";
  if (ui.supplierAi) ui.supplierAi.value = "";
  if (ui.supplierAddress) ui.supplierAddress.value = "";
  if (ui.supplierActivity) ui.supplierActivity.value = "";
  if (ui.supplierCommerceName) ui.supplierCommerceName.value = "";
  ui.supplierSubmitBtn.textContent = langText("Ajouter fournisseur", "اضافة مورد");
  if (ui.supplierModalTitle) ui.supplierModalTitle.textContent = langText("Ajouter fournisseur", "اضافة مورد", "زيد لفرنسور");
  ui.supplierCancelBtn.classList.add("hidden");
}

function alertsFromProducts(products) {
  const out = [];
  for (const p of products) {
    const st = Number(p.stock_qty || 0);
    if (st < 3) out.push({
      title: langText("Stock", "المخزون", "الستوك"),
      text: langText(`Stock faible: ${p.name} (${st})`, `مخزون منخفض: ${p.name} (${st})`, `الستوك ناقص: ${p.name} (${st})`),
      view: "products",
      openable: false,
      level: st <= 0 ? "high" : "medium"
    });
    const d = dayLeft(p.expiry_date);
    if (d != null && d >= 0 && d <= 14) out.push({
      title: langText("Expiration", "الصلاحية", "الصلاحية"),
      text: langText(`Expiration proche: ${p.name} (${d} jour)`, `قرب انتهاء الصلاحية: ${p.name} (${d} يوم)`, `الاكسبيراسيون قريبة: ${p.name} (${d} يوم)`),
      view: "products",
      action: "product",
      product_id: String(p.id || ""),
      product_name: String(p.name || ""),
      level: d <= 3 ? "high" : "medium"
    });
  }
  return out;
}

async function openAlertTarget(alertItem) {
  const a = alertItem || {};
  const action = String(a.action || "");
  if (action === "product") {
    switchView("products");
    const q = String(a.product_name || a.product_id || "").trim();
    if (ui.productGridSearch && q) ui.productGridSearch.value = q;
    if (typeof updateProductGrid === "function") updateProductGrid();
    setTimeout(() => ui.productGridSearch?.focus(), 0);
    return;
  }
  if (action === "customer_debt") {
    switchView("customerDebt");
    const cid = String(a.customer_id || "").trim();
    if (typeof refreshSales === "function") await refreshSales();
    if (ui.saleDebtCustomer && cid) ui.saleDebtCustomer.value = cid;
    if (typeof window.syncSaleDebtCustomerSearchInput === "function") window.syncSaleDebtCustomerSearchInput();
    if (typeof refreshDebtInvoiceOptions === "function") await refreshDebtInvoiceOptions(cid);
    const sid = String(a.sale_id || "").trim();
    if (ui.saleDebtInvoice && sid) {
      const hasInvoice = Array.from(ui.saleDebtInvoice.options || []).some((opt) => String(opt.value || "") === sid);
      if (hasInvoice) {
        ui.saleDebtInvoice.value = sid;
        ui.saleDebtInvoice.dispatchEvent(new Event("change"));
      }
    }
    setTimeout(() => (ui.saleDebtAmount || ui.saleDebtInvoice || ui.saleDebtCustomerSearch || ui.saleDebtCustomer)?.focus(), 0);
    return;
  }
  if (action === "supplier_debt") {
    switchView("supplierDebt");
    const sid = String(a.supplier_id || "").trim();
    if (typeof refreshPurchases === "function") await refreshPurchases();
    if (ui.purchaseDebtSupplier && sid) ui.purchaseDebtSupplier.value = sid;
    if (typeof window.syncPurchaseDebtSupplierSearchInput === "function") window.syncPurchaseDebtSupplierSearchInput();
    if (typeof refreshPurchaseDebtInvoiceOptions === "function") await refreshPurchaseDebtInvoiceOptions(sid);
    const pid = String(a.purchase_id || "").trim();
    if (ui.purchaseDebtInvoice && pid) {
      const hasInvoice = Array.from(ui.purchaseDebtInvoice.options || []).some((opt) => String(opt.value || "") === pid);
      if (hasInvoice) {
        ui.purchaseDebtInvoice.value = pid;
        ui.purchaseDebtInvoice.dispatchEvent(new Event("change"));
      }
    }
    setTimeout(() => (ui.purchaseDebtAmount || ui.purchaseDebtInvoice || ui.purchaseDebtSupplierSearch || ui.purchaseDebtSupplier)?.focus(), 0);
    return;
  }
  const target = String(a.view || "").trim();
  if (target && views.includes(target)) switchView(target);
}

let alertsOpenDelegationBound = false;

function bindAlertsOpenDelegation() {
  if (alertsOpenDelegationBound) return;
  alertsOpenDelegationBound = true;
  const handler = async (event) => {
    const btn = event.target?.closest?.("[data-alert-idx]");
    if (!btn) return;
    const inAlerts = btn.closest("#alertsPanel, #alertsList");
    if (!inAlerts) return;
    event.preventDefault();
    event.stopPropagation();
    const idx = Number.parseInt(String(btn.getAttribute("data-alert-idx") || "-1"), 10);
    const item = Number.isFinite(idx) && idx >= 0 ? (currentAlerts[idx] || null) : null;
    if (!item) {
      appNotify(langText("Alerte introuvable", "التنبيه غير موجود", "لالارت ما لقايناهش"), true, 2600);
      return;
    }
    btn.disabled = true;
    try {
      await openAlertTarget(item);
      ui.alertsPanel?.classList.add("hidden");
    } catch (err) {
      const msg = String(err && err.message ? err.message : err || "alert_open_failed");
      appNotify(msg, true, 4200);
    } finally {
      btn.disabled = false;
    }
  };
  ui.alertsPanel?.addEventListener("click", handler);
  ui.alertsList?.addEventListener("click", handler);
}

function renderAlerts() {
  if (!ui.alertsList || !ui.alertsPanel || !ui.notifBadge) {
    currentAlerts = [];
    if (ui.notifBtn) ui.notifBtn.classList.add("hidden");
    return;
  }
  if (ui.notifBtn) ui.notifBtn.classList.remove("hidden");
  const alerts = alertsFromProducts(cache.products);
  const customersDue = salesRows.reduce((sum, s) => sum + Math.max(0, Number(s.due_cents || 0)), 0);
  const suppliersDue = purchaseRows.reduce((sum, p) => sum + Math.max(0, Number(p.due_cents || 0)), 0);
  const customerDueRows = salesRows
    .filter((s) => Number(s.due_cents || 0) > 0)
    .sort((a, b) => {
      const byDue = Number(b.due_cents || 0) - Number(a.due_cents || 0);
      if (byDue !== 0) return byDue;
      return String(b.created_at || "").localeCompare(String(a.created_at || ""));
    });
  const supplierDueRows = purchaseRows
    .filter((p) => Number(p.due_cents || 0) > 0)
    .sort((a, b) => {
      const byDue = Number(b.due_cents || 0) - Number(a.due_cents || 0);
      if (byDue !== 0) return byDue;
      return String(b.created_at || "").localeCompare(String(a.created_at || ""));
    });
  if (customersDue > 0) {
    alerts.push({
      title: langText("Solde/Avoir clients", "رصيد/أفوار الزبائن", "صولد/أفوار لكليان"),
      text: langText(
        `Total credit clients: ${money(customersDue)} | Factures dues: ${customerDueRows.length}`,
        `إجمالي كريدي الزبائن: ${money(customersDue)} | الفواتير المدينة: ${customerDueRows.length}`,
        `مجموع كريدي لكليان: ${money(customersDue)} | لفاكتور المدينين: ${customerDueRows.length}`
      ),
      action: "customer_debt",
      openable: true,
      level: "low"
    });
    customerDueRows.forEach((r) => {
      const ref = salePublicRef(r.id, r.created_at, salesRows);
      alerts.push({
        title: langText("Solde/Avoir clients", "رصيد/أفوار الزبائن", "صولد/أفوار لكليان"),
        text: langText(
          `Client: ${String(r.customer_name || "-")} | Facture: ${ref} | Reste: ${money(r.due_cents || 0)}`,
          `الزبون: ${String(r.customer_name || "-")} | الفاتورة: ${ref} | المتبقي: ${money(r.due_cents || 0)}`,
          `لكليان: ${String(r.customer_name || "-")} | لفاكتور: ${ref} | الباقي: ${money(r.due_cents || 0)}`
        ),
        action: "customer_debt",
        customer_id: String(r.customer_id || ""),
        sale_id: String(r.id || ""),
        openable: true,
        level: "low"
      });
    });
  }
  if (suppliersDue > 0) {
    alerts.push({
      title: langText("Solde/Avoir fournisseurs", "رصيد/أفوار الموردين", "صولد/أفوار لفرنسور"),
      text: langText(
        `Total credit fournisseurs: ${money(suppliersDue)} | Factures dues: ${supplierDueRows.length}`,
        `إجمالي كريدي الموردين: ${money(suppliersDue)} | الفواتير المدينة: ${supplierDueRows.length}`,
        `مجموع كريدي لفرنسور: ${money(suppliersDue)} | لفاكتور المدينين: ${supplierDueRows.length}`
      ),
      action: "supplier_debt",
      openable: true,
      level: "low"
    });
    supplierDueRows.forEach((r) => {
      const ref = purchasePublicRef(r.id, r.created_at, purchaseRows);
      alerts.push({
        title: langText("Solde/Avoir fournisseurs", "رصيد/أفوار الموردين", "صولد/أفوار لفرنسور"),
        text: langText(
          `Fournisseur: ${String(r.supplier_name || "-")} | Facture: ${ref} | Reste: ${money(r.due_cents || 0)}`,
          `المورد: ${String(r.supplier_name || "-")} | الفاتورة: ${ref} | المتبقي: ${money(r.due_cents || 0)}`,
          `لفرنسور: ${String(r.supplier_name || "-")} | لفاكتور: ${ref} | الباقي: ${money(r.due_cents || 0)}`
        ),
        action: "supplier_debt",
        supplier_id: String(r.supplier_id || ""),
        purchase_id: String(r.id || ""),
        openable: true,
        level: "low"
      });
    });
  }
  currentAlerts = alerts.slice();
  ui.notifBadge.textContent = String(alerts.length);
  ui.notifBadge.classList.toggle("hidden", alerts.length === 0);
  const levelLabel = (lv) => {
    const key = String(lv || "low");
    if (key === "high") return langText("Eleve", "مرتفع", "عالي");
    if (key === "medium") return langText("Moyen", "متوسط", "متوسط");
    return langText("Faible", "منخفض", "خفيف");
  };
  const alertsPanelHtml = alerts.length
    ? alerts.map((a, i) => `<div class="row"><div><strong>${esc(a.title || langText("ALERTE", "تنبيه", "تنبيه"))}</strong><div class="small">${esc(a.text || "")}</div></div>${a.openable === false ? `<span class="small">-</span>` : `<button type="button" class="btn btn-soft" data-alert-idx="${i}">${langText("Ouvrir", "فتح", "حل")}</button>`}</div>`).join("")
    : `<div class="small">${t("no_data")}</div>`;
  const alertsTableHtml = alerts.length
    ? alerts.map((a, i) => `
      <tr class="alert-row ${esc(String(a.level || "low"))} ${a.openable === false ? "" : "clickable-row"}" ${a.openable === false ? "" : `data-alert-idx="${i}"`} title="${a.openable === false ? "" : esc(langText("Ouvrir", "فتح", "حل"))}">
        <td><strong>${esc(a.title || langText("ALERTE", "تنبيه", "تنبيه"))}</strong></td>
        <td><span class="alert-level ${esc(String(a.level || "low"))}">${esc(levelLabel(a.level))}</span></td>
        <td>${esc(a.text || "-")}</td>
      </tr>
    `).join("")
    : `<tr><td colspan="3" class="empty">${t("no_data")}</td></tr>`;
  ui.alertsPanel.innerHTML = alertsPanelHtml;
  ui.alertsList.innerHTML = alertsTableHtml;
  bindAlertsOpenDelegation();
}

function selectedSalePriceMode() {
  const checked = ui.salePriceModes.find((x) => x.checked);
  return String((checked && checked.value) || "retail");
}

function modePriceCents(product, mode) {
  const m = String(mode || "retail");
  if (m === "wholesale") return Number(product.wholesale_price_cents || product.retail_price_cents || product.price_cents || 0);
  if (m === "assist") return Number(product.assisted_price_cents || product.retail_price_cents || product.price_cents || 0);
  return Number(product.retail_price_cents || product.price_cents || 0);
}

function selectedPriceCents(product) {
  return modePriceCents(product, selectedSalePriceMode());
}

function updatePosLiveBoard(product = null) {
  if (!product) {
    ui.posLiveName.textContent = "-";
    ui.posLiveMeta.textContent = langText("Qte: 0 | Stock: 0 | Exp: -", "الكمية: 0 | المخزون: 0 | الصلاحية: -", "الكمية: 0 | الستوك: 0 | الصلاحية: -");
    ui.posLiveTotal.textContent = money(0);
    return;
  }
  const qty = Math.max(0.01, qtyNum(ui.posQuickQty?.value || 1, 1));
  const unit = selectedPriceCents(product);
  ui.posLiveName.textContent = String(product.name || "-");
  ui.posLiveMeta.textContent = langText(
    `Qte: ${qty} | Stock: ${Number(product.stock_qty || 0)} | Exp: ${product.expiry_date || "-"}`,
    `الكمية: ${qty} | المخزون: ${Number(product.stock_qty || 0)} | الصلاحية: ${product.expiry_date || "-"}`,
    `الكمية: ${qty} | الستوك: ${Number(product.stock_qty || 0)} | الصلاحية: ${product.expiry_date || "-"}`
  );
  ui.posLiveTotal.textContent = money(qty * unit);
}

function readProductPriceInputCents(input) {
  const raw = String(input && input.value != null ? input.value : "").trim().replace(",", ".");
  const n = Number(raw);
  return Number.isFinite(n) ? Math.max(0, Math.round(n * 100)) : 0;
}

function setProductCostMeta(product = null) {
  if (!ui.productCostMeta || !ui.productMarginHint) return;
  const avgCost = Math.max(
    0,
    Number(
      product && product.avg_cost_cents != null
        ? product.avg_cost_cents
        : (ui.productCostMeta.dataset.avgCostCents || 0)
    ) || 0
  );
  const lastCost = Math.max(
    0,
    Number(
      product && product.last_cost_cents != null
        ? product.last_cost_cents
        : (ui.productCostMeta.dataset.lastCostCents || 0)
    ) || 0
  );
  const fallbackCost = readProductPriceInputCents(ui.productCost);
  const baselineCost = avgCost > 0 ? avgCost : Math.max(lastCost, fallbackCost);

  ui.productCostMeta.dataset.avgCostCents = String(avgCost || 0);
  ui.productCostMeta.dataset.lastCostCents = String(lastCost || 0);
  if (avgCost > 0 || lastCost > 0) {
    ui.productCostMeta.textContent = langText(
      `Cout moyen actuel: ${money(avgCost || baselineCost)} | Dernier achat: ${money(lastCost || baselineCost)}`,
      `متوسط التكلفة الحالي: ${money(avgCost || baselineCost)} | آخر سعر شراء: ${money(lastCost || baselineCost)}`,
      `متوسط الكوست الحالي: ${money(avgCost || baselineCost)} | لخر شرا: ${money(lastCost || baselineCost)}`
    );
    ui.productCostMeta.classList.remove("hidden");
  } else {
    ui.productCostMeta.textContent = "";
    ui.productCostMeta.classList.add("hidden");
  }

  const priceEntries = [
    { input: ui.productWholesale, label: langText("Prix gros", "سعر الجملة", "سعر الجملة") },
    { input: ui.productRetail, label: langText("Prix detail", "سعر التفصيل", "سعر التفصيل") },
    { input: ui.productAssist, label: langText("Prix remise", "سعر التخفيض", "سعر التخفيض") }
  ];
  const warnings = [];
  for (const entry of priceEntries) {
    const input = entry.input;
    if (!input) continue;
    const priceCents = readProductPriceInputCents(input);
    const isWarning = baselineCost > 0 && priceCents > 0 && priceCents < baselineCost;
    input.classList.toggle("price-warning-input", isWarning);
    if (isWarning) warnings.push(`${entry.label}: ${money(priceCents)}`);
  }
  if (warnings.length && baselineCost > 0) {
    ui.productMarginHint.textContent = langText(
      `Attention: prix inferieur au cout moyen (${money(baselineCost)}). ${warnings.join(" | ")}`,
      `تنبيه: السعر أقل من متوسط التكلفة (${money(baselineCost)}). ${warnings.join(" | ")}`,
      `تنبيه: السعر راه أقل من متوسط الكوست (${money(baselineCost)}). ${warnings.join(" | ")}`
    );
    ui.productMarginHint.classList.remove("hidden");
  } else {
    ui.productMarginHint.textContent = "";
    ui.productMarginHint.classList.add("hidden");
  }
}

function fillProductFormForEdit(p) {
  ui.productId.value = p.id;
  ui.productName.value = p.name || "";
  if (ui.productType) ui.productType.value = String(p.product_type || "product") === "service" ? "service" : "product";
  setProductImagePreview(p.image_path || "");
  ui.productBarcode.value = p.barcode || "";
  if (ui.productReference) ui.productReference.value = p.reference || "";
  ui.productCost.value = money(p.cost_cents || 0);
  ui.productWholesale.value = money(p.wholesale_price_cents || 0);
  ui.productRetail.value = money(p.retail_price_cents || p.price_cents || 0);
  ui.productAssist.value = money(p.assisted_price_cents || p.retail_price_cents || 0);
  ui.productStock.value = qtyText(p.stock_qty);
  ui.productStock.disabled = true;
  ui.productExpiry.value = p.expiry_date ? String(p.expiry_date).slice(0, 10) : "";
  if (ui.productModalTitle) ui.productModalTitle.textContent = langText("Modifier produit", "تعديل منتج", "بدّل منتوج");
  ui.productSubmitBtn.textContent = langText("Mettre a jour", "تحديث المنتج", "بدّل المنتوج");
  ui.productCancelBtn.classList.remove("hidden");
  setProductCostMeta(p);
  applyProductTypeUi();
  switchView("products");
  openProductModal();
  setTimeout(() => ui.productName?.focus(), 0);
}

function fillProductFormForCopy(p) {
  ui.productId.value = "";
  ui.productName.value = p.name || "";
  if (ui.productType) ui.productType.value = String(p.product_type || "product") === "service" ? "service" : "product";
  setProductImagePreview(p.image_path || "");
  ui.productBarcode.value = p.barcode || "";
  if (ui.productReference) ui.productReference.value = p.reference || "";
  ui.productCost.value = money(p.cost_cents || 0);
  ui.productWholesale.value = money(p.wholesale_price_cents || 0);
  ui.productRetail.value = money(p.retail_price_cents || p.price_cents || 0);
  ui.productAssist.value = money(p.assisted_price_cents || p.retail_price_cents || 0);
  ui.productStock.value = qtyText(p.stock_qty);
  ui.productStock.disabled = false;
  ui.productExpiry.value = p.expiry_date ? String(p.expiry_date).slice(0, 10) : "";
  if (ui.productModalTitle) ui.productModalTitle.textContent = langText("Copier produit", "نسخ منتج", "نسخ منتوج");
  ui.productSubmitBtn.textContent = langText("Sauvegarder produit", "حفظ المنتج", "حفظ المنتوج");
  ui.productCancelBtn.classList.add("hidden");
  setProductCostMeta(p);
  applyProductTypeUi();
  switchView("products");
  openProductModal();
  setTimeout(() => ui.productName?.focus(), 0);
}

async function openProductTrace(productId) {
  const pid = String(productId || "").trim();
  if (!pid) return;
  switchView("products");
}

async function openCustomerTrace(customerId) {
  const cid = String(customerId || "").trim();
  if (!cid) return;
  switchView("customerJournal");
  ui.customerTraceCustomer.value = cid;
  if (typeof window.syncCustomerTraceCustomerSearchInput === "function") window.syncCustomerTraceCustomerSearchInput();
  await refreshCustomerTrace();
  ui.customerTraceForm?.scrollIntoView({ behavior: "smooth", block: "start" });
}

function customerDueCents(customerId) {
  const cid = String(customerId || "").trim();
  if (!cid) return 0;
  return salesRows
    .filter((s) => String(s.customer_id || "") === cid)
    .reduce((sum, s) => sum + Math.max(0, Number(s.due_cents || 0)), 0);
}

async function openSupplierTrace(supplierId) {
  const sid = String(supplierId || "").trim();
  if (!sid) return;
  switchView("supplierJournal");
  ui.supplierTraceSupplier.value = sid;
  if (typeof window.syncSupplierTraceSupplierSearchInput === "function") window.syncSupplierTraceSupplierSearchInput();
  await refreshSupplierTrace();
  ui.supplierTraceForm?.scrollIntoView({ behavior: "smooth", block: "start" });
}

