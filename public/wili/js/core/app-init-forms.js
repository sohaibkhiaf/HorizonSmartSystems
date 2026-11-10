function bindForms() {
  let productGridDebounceTimer = null;
  let customerGridDebounceTimer = null;
  let supplierGridDebounceTimer = null;
  let posSearchDebounceTimer = null;
  let posExactLookupSeq = 0;
  const safeCreateSaleQuoteFromCart = typeof createSaleQuoteFromCart === "function"
    ? createSaleQuoteFromCart
    : async () => appNotify(langText("Devis indisponible dans cette edition", "عروض الأسعار غير متاحة في هذه النسخة", "الديفي ماشي متاح في هاد النسخة"), true, 2600);
  const safeExportActiveSaleQuotePdf = typeof exportActiveSaleQuotePdf === "function"
    ? exportActiveSaleQuotePdf
    : async () => appNotify(langText("Devis indisponible dans cette edition", "عروض الأسعار غير متاحة في هذه النسخة", "الديفي ماشي متاح في هاد النسخة"), true, 2600);
  const safeClearActiveSaleQuoteSelection = () => {
    if (typeof clearActiveSaleQuoteSelection === "function") clearActiveSaleQuoteSelection();
  };
  const queueProductGridRefresh = (value, delay = 120) => {
    if (productGridDebounceTimer) clearTimeout(productGridDebounceTimer);
    productGridDebounceTimer = setTimeout(() => {
      productGridDebounceTimer = null;
      productGridState.selectedIndex = 0;
      void refreshProducts({ search: value, reloadCache: false });
    }, delay);
  };
  const queueCustomerGridRefresh = (delay = 120) => {
    if (customerGridDebounceTimer) clearTimeout(customerGridDebounceTimer);
    customerGridDebounceTimer = setTimeout(() => {
      customerGridDebounceTimer = null;
      customerGridState.page = 1;
      customerGridState.selectedIndex = 0;
      void refreshCustomers({ reloadCache: false, skipDependentRefresh: true, page: 1 });
    }, delay);
  };
  const queueSupplierGridRefresh = (delay = 120) => {
    if (supplierGridDebounceTimer) clearTimeout(supplierGridDebounceTimer);
    supplierGridDebounceTimer = setTimeout(() => {
      supplierGridDebounceTimer = null;
      supplierGridState.page = 1;
      supplierGridState.selectedIndex = 0;
      void refreshSuppliers({ reloadCache: false, skipDependentRefresh: true, page: 1 });
    }, delay);
  };
  const queuePosSearch = (value, delay = 90) => {
    if (posSearchDebounceTimer) clearTimeout(posSearchDebounceTimer);
    posSearchDebounceTimer = setTimeout(() => {
      posSearchDebounceTimer = null;
      void renderPosSearch(value);
    }, delay);
  };
  const isExactPosLookupMatch = (product, query) => {
    const q = String(query || "").trim().toLowerCase();
    if (!q || !product) return false;
    return ["barcode", "reference", "sku"].some((key) => String(product[key] || "").trim().toLowerCase() === q);
  };
  const handleExactPosLookup = async (rawQuery, options = {}) => {
    const query = String(rawQuery || "").trim();
    if (!query || !window.wiliDb || typeof window.wiliDb.findProductByLookup !== "function") return false;
    const seq = ++posExactLookupSeq;
    const lookupOut = await window.wiliDb.findProductByLookup(query);
    if (seq !== posExactLookupSeq) return false;
    if (String(ui.posSearchInput?.value || "").trim() !== query) return false;
    const exact = lookupOut && lookupOut.ok ? lookupOut.data : null;
    if (!isExactPosLookupMatch(exact, query)) {
      if (options.renderOnMiss) await renderPosSearch(query);
      return false;
    }
    const qty = Math.max(0.01, qtyNum(ui.posQuickQty?.value || 1, 1));
    addProductToCart(exact, qty);
    if (ui.posSearchInput) ui.posSearchInput.value = "";
    await renderPosSearch("");
    setTimeout(() => ui.posSearchInput?.focus(), 0);
    return true;
  };
  const goToProductPage = (nextPage) => {
    if (productGridState.searchMode) return;
    const targetPage = Math.max(1, intQty(nextPage || 1));
    if (targetPage === productGridState.page) return;
    productGridState.page = targetPage;
    void refreshProducts({ reloadCache: false, page: targetPage });
  };
  const goToCustomerPage = (nextPage) => {
    if (customerGridState.searchMode) return;
    const targetPage = Math.max(1, intQty(nextPage || 1));
    if (targetPage === customerGridState.page) return;
    customerGridState.page = targetPage;
    void refreshCustomers({ reloadCache: false, skipDependentRefresh: true, page: targetPage });
  };
  const goToSupplierPage = (nextPage) => {
    if (supplierGridState.searchMode) return;
    const targetPage = Math.max(1, intQty(nextPage || 1));
    if (targetPage === supplierGridState.page) return;
    supplierGridState.page = targetPage;
    void refreshSuppliers({ reloadCache: false, skipDependentRefresh: true, page: targetPage });
  };
  const generateRandomProductBarcode = () => {
    const used = new Set((Array.isArray(cache.products) ? cache.products : [])
      .map((p) => String(p && p.barcode || "").trim())
      .filter(Boolean));
    const tryGenerate = (length, attempts) => {
      const min = length === 4 ? 1000 : 10000;
      const max = length === 4 ? 9999 : 99999;
      for (let i = 0; i < attempts; i += 1) {
        const candidate = String(Math.floor(Math.random() * (max - min + 1)) + min);
        if (!used.has(candidate)) return candidate;
      }
      return "";
    };
    return tryGenerate(4, 120) || tryGenerate(5, 240);
  };

  const bootShowPin = byId("bootShowPin");
  const loginShowPin = byId("loginShowPin");
  bootShowPin?.addEventListener("change", () => {
    const show = !!bootShowPin.checked;
    const pin = byId("bootPin");
    const pinConfirm = byId("bootPinConfirm");
    if (pin) pin.type = show ? "text" : "password";
    if (pinConfirm) pinConfirm.type = show ? "text" : "password";
  });
  loginShowPin?.addEventListener("change", () => {
    const pin = byId("loginPin");
    if (pin) pin.type = loginShowPin.checked ? "text" : "password";
  });

  activateForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    setMsg(activateMsg, trText("Activation..."));
    const out = await window.wiliApi.activate(String(byId("codeInput").value || "").trim());
    if (!out || !out.ok) return setMsg(activateMsg, trText(out && out.error ? out.error : "activation_failed"), true);
    setMsg(activateMsg, trText("Activation OK"));
    await resolveEntryFlow();
  });

  bootstrapForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const pin = String(byId("bootPin").value || "").trim();
    const pinConfirm = String(byId("bootPinConfirm").value || "").trim();
    if (pin !== pinConfirm) {
      setMsg(
        bootstrapMsg,
        langText("Le PIN et sa confirmation ne correspondent pas", "الرقم السري وتأكيده غير متطابقين", "الپين وتأكيدو ماشي كيف كيف"),
        true
      );
      return;
    }
    const out = await window.wiliApi.bootstrapFirstAdmin({
      username: String(byId("bootUsername").value || "").trim(),
      display_name: String(byId("bootDisplay").value || "").trim(),
      pin
    });
    if (!out || !out.ok) return setMsg(bootstrapMsg, trText(out && out.error ? out.error : "bootstrap_failed"), true);
    setMsg(bootstrapMsg, trText("Compte admin cree"));
    await resolveEntryFlow();
  });

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const directParams = window.WILI_MYSQL_DEMO && typeof window.WILI_MYSQL_DEMO.directEntryParams === "function"
      ? window.WILI_MYSQL_DEMO.directEntryParams()
      : null;
    const out = await window.wiliApi.login({
      username: String(byId("loginUsername").value || "").trim(),
      pin: String(byId("loginPin").value || "").trim(),
      database: directParams && directParams.database ? directParams.database : ""
    });
    if (!out || !out.ok) return setMsg(loginMsg, trText(out && out.error ? out.error : "invalid_credentials"), true);
    session.user = out.data;
    userBadge.textContent = `${session.user.display_name || session.user.username} | ${getUserRoleLabel(session.user.role)}`;
    if (window.WILI_MYSQL_DEMO && typeof window.WILI_MYSQL_DEMO.ensureRequestedTemplateWorkspace === "function") {
      const workspaceOut = await window.WILI_MYSQL_DEMO.ensureRequestedTemplateWorkspace();
      if (workspaceOut && !workspaceOut.ok) return setMsg(loginMsg, workspaceOut.error || "workspace_failed", true);
    }
    showApp();
    switchView("sales");
    await refreshAll();
    renderPosSearch("");
    renderCart();
    updatePosTotals();
  });

  ui.productGridSearch?.addEventListener("input", () => {
    productGridState.page = 1;
    queueProductGridRefresh(ui.productGridSearch.value);
  });
  ui.customerGridSearch?.addEventListener("input", () => {
    queueCustomerGridRefresh();
  });
  ui.supplierGridSearch?.addEventListener("input", () => {
    queueSupplierGridRefresh();
  });
  ui.customersTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "name");
      if (customerGridState.sortKey === key) {
        customerGridState.sortDir = customerGridState.sortDir === "asc" ? "desc" : "asc";
      } else {
        customerGridState.sortKey = key;
        customerGridState.sortDir = "asc";
      }
      customerGridState.page = 1;
      customerGridState.selectedIndex = 0;
      void refreshCustomers({ reloadCache: false, skipDependentRefresh: true, page: 1 });
    });
  });
  ui.suppliersTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "name");
      if (supplierGridState.sortKey === key) {
        supplierGridState.sortDir = supplierGridState.sortDir === "asc" ? "desc" : "asc";
      } else {
        supplierGridState.sortKey = key;
        supplierGridState.sortDir = "asc";
      }
      supplierGridState.page = 1;
      supplierGridState.selectedIndex = 0;
      void refreshSuppliers({ reloadCache: false, skipDependentRefresh: true, page: 1 });
    });
  });
  ui.productsTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "name");
      if (productGridState.sortKey === key) {
        productGridState.sortDir = productGridState.sortDir === "asc" ? "desc" : "asc";
      } else {
        productGridState.sortKey = key;
        productGridState.sortDir = "asc";
      }
      productGridState.selectedIndex = 0;
      if (String(ui.productGridSearch?.value || "").trim()) {
        updateProductGrid();
      } else {
        productGridState.page = 1;
        void refreshProducts({ reloadCache: false, page: 1 });
      }
    });
  });
  ui.productsPagePrevBtn?.addEventListener("click", () => {
    if (productGridState.page <= 1) return;
    goToProductPage(productGridState.page - 1);
  });
  ui.productsPageNextBtn?.addEventListener("click", () => {
    if (productGridState.page >= Math.max(1, intQty(productGridState.totalPages || 1))) return;
    goToProductPage(productGridState.page + 1);
  });
  ui.customersPagePrevBtn?.addEventListener("click", () => {
    if (customerGridState.page <= 1) return;
    goToCustomerPage(customerGridState.page - 1);
  });
  ui.customersPageNextBtn?.addEventListener("click", () => {
    if (customerGridState.page >= Math.max(1, intQty(customerGridState.totalPages || 1))) return;
    goToCustomerPage(customerGridState.page + 1);
  });
  ui.suppliersPagePrevBtn?.addEventListener("click", () => {
    if (supplierGridState.page <= 1) return;
    goToSupplierPage(supplierGridState.page - 1);
  });
  ui.suppliersPageNextBtn?.addEventListener("click", () => {
    if (supplierGridState.page >= Math.max(1, intQty(supplierGridState.totalPages || 1))) return;
    goToSupplierPage(supplierGridState.page + 1);
  });
  ui.productsTable?.addEventListener("keydown", (e) => {
    const rows = productGridState.filtered || [];
    if (!rows.length) return;
    if (e.key === "ArrowDown") {
      e.preventDefault();
      productGridState.selectedIndex = Math.min(rows.length - 1, productGridState.selectedIndex + 1);
      updateProductGrid();
      return;
    }
    if (e.key === "ArrowUp") {
      e.preventDefault();
      productGridState.selectedIndex = Math.max(0, productGridState.selectedIndex - 1);
      updateProductGrid();
      return;
    }
    if (e.key === "ArrowLeft") {
      if (!productGridState.searchMode && productGridState.page > 1) {
        e.preventDefault();
        goToProductPage(productGridState.page - 1);
      }
      return;
    }
    if (e.key === "ArrowRight") {
      if (!productGridState.searchMode && productGridState.page < Math.max(1, intQty(productGridState.totalPages || 1))) {
        e.preventDefault();
        goToProductPage(productGridState.page + 1);
      }
      return;
    }
    if (e.key === "Enter") {
      e.preventDefault();
      const p = rows[productGridState.selectedIndex];
      if (p) fillProductFormForEdit(p);
    }
  });
  ui.alertsRefreshBtn?.addEventListener("click", renderAlerts);

  ui.historyFilterForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    applyHistoryFilters();
  });
  ui.historyStatsForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    if (typeof renderHistoryStats === "function") renderHistoryStats();
  });
  [ui.historyStatsFrom, ui.historyStatsTo, ui.historyStatsCompare].forEach((ctrl) => {
    ctrl?.addEventListener("change", () => {
      if (typeof renderHistoryStats === "function") renderHistoryStats();
    });
  });
  ui.customerTraceForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    await refreshCustomerTrace();
  });
  ui.supplierTraceForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    await refreshSupplierTrace();
  });
  ui.purchaseInlineToggleBtn?.addEventListener("click", () => {
    ui.purchaseInlineCreate.classList.toggle("hidden");
    if (!ui.purchaseInlineCreate.classList.contains("hidden")) showPurchaseInlineCreate();
  });
  ui.purchaseInlineCreateForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = String(ui.productId.value || "");
    const payload = {
      name: String(ui.purchaseNewName.value || "").trim(),
      barcode: String(ui.purchaseNewBarcode.value || "").trim(),
      reference: String(ui.purchaseNewReference?.value || "").trim(),
      cost_cents: toCents(ui.purchaseNewCost.value),
      wholesale_price_cents: toCents(ui.purchaseNewWholesale.value || ui.purchaseNewCost.value),
      retail_price_cents: toCents(ui.purchaseNewRetail.value || ui.purchaseNewCost.value),
      assisted_price_cents: toCents(ui.purchaseNewAssist.value || ui.purchaseNewRetail.value || ui.purchaseNewCost.value),
      price_cents: toCents(ui.purchaseNewRetail.value || ui.purchaseNewCost.value),
      stock_qty: 0
    };
    if (!payload.name) return;
    const out = await window.wiliDb.createProduct(payload);
    if (!out || !out.ok) return;
    const newId = String(out.data && out.data.id ? out.data.id : "");
    await refreshProducts();
    ui.purchaseInlineCreate.classList.add("hidden");
    ui.purchaseProduct.value = newId;
    ui.purchaseProductSearch.value = `${payload.name} | ${payload.barcode || "-"} | ${payload.reference || "-"}`;
    appNotify(langText("Produit cree", "تم إنشاء المنتج", "البرودوي تخلق"), false, 2200);
    setTimeout(() => ui.purchaseAddLineBtn?.focus(), 0);
  });

  ui.productAddBtn?.addEventListener("click", () => {
    resetProductForm();
    openProductModal();
    setTimeout(() => ui.productName?.focus(), 0);
  });
  ui.productModalCloseBtn?.addEventListener("click", () => {
    closeProductModal();
    resetProductForm();
  });
  ui.productModal?.addEventListener("click", (e) => {
    if (e.target === ui.productModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.productBarcodeGenerateBtn?.addEventListener("click", () => {
    const barcode = generateRandomProductBarcode();
    if (!barcode) {
      appNotify(langText("Impossible de generer un barcode unique", "\u062A\u0639\u0630\u0631 \u062A\u0648\u0644\u064A\u062F \u0628\u0627\u0631\u0643\u0648\u062F \u0641\u0631\u064A\u062F", "\u0645\u0627 \u0642\u062F\u0631\u0646\u0627\u0634 \u0646\u0648\u0644\u062F\u0648 \u0628\u0627\u0631\u0643\u0648\u062F \u0648\u062D\u064A\u062F"), true);
      return;
    }
    ui.productBarcode.value = barcode;
    ui.productBarcode.dispatchEvent(new Event("input", { bubbles: true }));
    ui.productBarcode.focus();
    ui.productBarcode.select?.();
  });
  [ui.productCost, ui.productWholesale, ui.productRetail, ui.productAssist].forEach((input) => {
    input?.addEventListener("input", () => {
      if (typeof applyProductTypeUi === "function") applyProductTypeUi();
      if (typeof setProductCostMeta === "function") setProductCostMeta();
    });
  });
  ui.productType?.addEventListener("change", () => {
    if (typeof applyProductTypeUi === "function") applyProductTypeUi();
  });
  ui.productImagePickBtn?.addEventListener("click", async () => {
    const out = await window.wiliFiles?.pickProductImage?.();
    if (out && out.ok && out.data && typeof setProductImagePreview === "function") {
      setProductImagePreview(out.data.image_path || "");
      appNotify(langText("Image produit ajoutee", "تمت إضافة صورة المنتج", "تزادت تصويرة المنتوج"));
    } else if (out && !out.canceled) {
      appNotify(String(out.error || "image_pick_failed"), true);
    }
  });
  ui.productCancelBtn.addEventListener("click", resetProductForm);
  ui.productForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = String(ui.productId.value || "");
    const isService = String(ui.productType?.value || "product") === "service";
    const detailCents = toCents(ui.productRetail.value);
    const payload = {
      name: String(ui.productName.value || "").trim(),
      barcode: String(ui.productBarcode.value || "").trim(),
      reference: String(ui.productReference?.value || "").trim(),
      product_type: isService ? "service" : "product",
      image_path: String(ui.productImagePath?.value || "").trim(),
      cost_cents: isService ? 0 : toCents(ui.productCost.value),
      wholesale_price_cents: isService ? detailCents : toCents(ui.productWholesale.value),
      retail_price_cents: detailCents,
      assisted_price_cents: isService ? detailCents : toCents(ui.productAssist.value),
      price_cents: detailCents,
      expiry_date: isService ? null : (String(ui.productExpiry.value || "").trim() || null)
    };
    if (!id) payload.stock_qty = isService ? 0 : qtyNum(ui.productStock.value);
    const out = id ? await window.wiliDb.updateProduct(id, payload) : await window.wiliDb.createProduct(payload);
    if (!out || !out.ok) return;
    if (typeof setProductCostMeta === "function" && out.data) setProductCostMeta(out.data);
    resetProductForm();
    closeProductModal();
    await refreshProducts();
    if (typeof refreshPurchaseCartProductData === "function") refreshPurchaseCartProductData(id || String((out && out.data && out.data.id) || ""));
    await refreshDashboard();
  });

  ui.historyForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    await refreshHistory();
  });

  ui.customerAddBtn?.addEventListener("click", () => {
    resetCustomerForm();
    openCustomerModal();
    setTimeout(() => ui.customerName?.focus(), 0);
  });
  ui.customerModalCloseBtn?.addEventListener("click", () => {
    closeCustomerModal();
    resetCustomerForm();
  });
  ui.customerModal?.addEventListener("click", (e) => {
    if (e.target === ui.customerModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.customerCancelBtn.addEventListener("click", resetCustomerForm);
  ui.customerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      name: String(ui.customerName.value || "").trim(),
      phone: String(ui.customerPhone.value || "").trim(),
      nif: String(ui.customerNif?.value || "").trim(),
      nis: String(ui.customerNis?.value || "").trim(),
      rc: String(ui.customerRc?.value || "").trim(),
      ai: String(ui.customerAi?.value || "").trim(),
      address: String(ui.customerAddress?.value || "").trim(),
      activity: String(ui.customerActivity?.value || "").trim(),
      commerce_name: String(ui.customerCommerceName?.value || "").trim()
    };
    const id = String(ui.customerId.value || "");
    const out = id ? await window.wiliDb.updateCustomer(id, payload) : await window.wiliDb.createCustomer(payload);
    if (!out || !out.ok) return;
    resetCustomerForm();
    closeCustomerModal();
    await refreshCustomers();
  });

  ui.customersPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportCustomersPdf();
    handlePdfResult(out, langText("Telecharger clients", "تحميل الزبائن", "تحميل لكليان"), ui.customersTableWrap);
  });
  ui.customerReturnCustomer?.addEventListener("change", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("customer");
    if (typeof refreshCustomerReturnOptions === "function") void refreshCustomerReturnOptions();
  });
  ui.customerReturnSale?.addEventListener("change", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("customer");
    if (ui.customerReturnPrice) ui.customerReturnPrice.value = "";
    if (typeof refreshCustomerReturnProductOptions === "function") void refreshCustomerReturnProductOptions();
  });
  ui.customerReturnProduct?.addEventListener("change", () => {
    const opt = ui.customerReturnProduct.selectedOptions && ui.customerReturnProduct.selectedOptions[0];
    if (ui.customerReturnPrice && opt) ui.customerReturnPrice.value = (Number(opt.dataset.unit || 0) / 100).toFixed(2);
  });
  ui.customerReturnClearBtn?.addEventListener("click", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("customer");
    if (ui.customerReturnSale) ui.customerReturnSale.value = "";
    if (ui.customerReturnQty) ui.customerReturnQty.value = "1";
    if (ui.customerReturnPrice) ui.customerReturnPrice.value = "";
    if (ui.customerReturnNote) ui.customerReturnNote.value = "";
    if (typeof refreshCustomerReturnProductOptions === "function") void refreshCustomerReturnProductOptions();
  });
  ui.customerReturnAddLineBtn?.addEventListener("click", () => {
    if (typeof addCustomerReturnLine === "function") addCustomerReturnLine();
  });
  ui.customerReturnForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (typeof submitCustomerReturn === "function") await submitCustomerReturn();
  });

  ui.supplierAddBtn?.addEventListener("click", () => {
    resetSupplierForm();
    openSupplierModal();
    setTimeout(() => ui.supplierName?.focus(), 0);
  });
  ui.supplierModalCloseBtn?.addEventListener("click", () => {
    closeSupplierModal();
    resetSupplierForm();
  });
  ui.supplierModal?.addEventListener("click", (e) => {
    if (e.target === ui.supplierModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.supplierCancelBtn.addEventListener("click", resetSupplierForm);
  ui.supplierForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      name: String(ui.supplierName.value || "").trim(),
      phone: String(ui.supplierPhone.value || "").trim(),
      nif: String(ui.supplierNif?.value || "").trim(),
      nis: String(ui.supplierNis?.value || "").trim(),
      rc: String(ui.supplierRc?.value || "").trim(),
      ai: String(ui.supplierAi?.value || "").trim(),
      address: String(ui.supplierAddress?.value || "").trim(),
      activity: String(ui.supplierActivity?.value || "").trim(),
      commerce_name: String(ui.supplierCommerceName?.value || "").trim()
    };
    const id = String(ui.supplierId.value || "");
    const out = id ? await window.wiliDb.updateSupplier(id, payload) : await window.wiliDb.createSupplier(payload);
    if (!out || !out.ok) return;
    resetSupplierForm();
    closeSupplierModal();
    await refreshSuppliers();
  });

  ui.suppliersPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportSuppliersPdf();
    handlePdfResult(out, langText("Telecharger fournisseurs", "تحميل الموردين", "تحميل لفرنسور"), ui.suppliersTableWrap);
  });
  ui.supplierReturnSupplier?.addEventListener("change", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("supplier");
    if (typeof refreshSupplierReturnOptions === "function") void refreshSupplierReturnOptions();
  });
  ui.supplierReturnPurchase?.addEventListener("change", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("supplier");
    if (ui.supplierReturnCost) ui.supplierReturnCost.value = "";
    if (typeof refreshSupplierReturnProductOptions === "function") void refreshSupplierReturnProductOptions();
  });
  ui.supplierReturnProduct?.addEventListener("change", () => {
    const opt = ui.supplierReturnProduct.selectedOptions && ui.supplierReturnProduct.selectedOptions[0];
    if (ui.supplierReturnCost && opt) ui.supplierReturnCost.value = (Number(opt.dataset.unit || 0) / 100).toFixed(2);
  });
  ui.supplierReturnClearBtn?.addEventListener("click", () => {
    if (typeof clearReturnCart === "function") clearReturnCart("supplier");
    if (ui.supplierReturnPurchase) ui.supplierReturnPurchase.value = "";
    if (ui.supplierReturnQty) ui.supplierReturnQty.value = "1";
    if (ui.supplierReturnCost) ui.supplierReturnCost.value = "";
    if (ui.supplierReturnNote) ui.supplierReturnNote.value = "";
    if (typeof refreshSupplierReturnProductOptions === "function") void refreshSupplierReturnProductOptions();
  });
  ui.supplierReturnAddLineBtn?.addEventListener("click", () => {
    if (typeof addSupplierReturnLine === "function") addSupplierReturnLine();
  });
  ui.supplierReturnForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (typeof submitSupplierReturn === "function") await submitSupplierReturn();
  });
  ui.supplierOpenFcBtn?.addEventListener("click", () => {
    switchView("purchases");
    if (ui.purchaseDocType) ui.purchaseDocType.value = "FC";
    if (ui.purchaseDocHint) {
      ui.purchaseDocHint.textContent = langText(
        "FC: facture fournisseur (R)",
        "FC: فاتورة مورد (R)",
        "FC: فاكتور فورنيسور (R)"
      );
    }
    setTimeout(() => (ui.purchaseSupplierSearch || ui.purchaseSupplier)?.focus(), 0);
  });

  ui.posSearchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    void renderPosSearch(ui.posSearchInput.value);
  });

  ui.salePriceModes.forEach((radio) => {
    radio.addEventListener("change", () => {
      repriceCartToMode();
      void renderPosSearch(ui.posSearchInput.value);
    });
  });
  ui.posSearchInput?.addEventListener("input", () => {
    posKeyboardNavigationActive = false;
    queuePosSearch(ui.posSearchInput.value);
  });
  ui.posSearchInput?.addEventListener("focus", () => void renderPosSearch(ui.posSearchInput.value));
  ui.posSearchInput?.addEventListener("click", () => void renderPosSearch(ui.posSearchInput.value));
  ui.posQuickQty?.addEventListener("input", () => {
    ui.posQuickQty.value = qtyText(Math.max(0.01, qtyNum(ui.posQuickQty.value || 1, 1)));
    if (searchRows.length) updatePosLiveBoard(searchRows[searchSelectedIndex]);
  });
  ui.posSearchInput?.addEventListener("keydown", (e) => {
    const salesShortcutFallbacks = {
      sales_focus_search: "F6",
      sales_add_selected: "F7",
      sales_print_cart: "F8",
      sales_checkout: "F9",
      sales_clear_cart: "F10",
      sales_focus_paid: "F12"
    };
    const isSalesShortcut = Object.entries(salesShortcutFallbacks).some(([actionId, fallback]) => {
      if (window.wiliShortcuts && typeof window.wiliShortcuts.matches === "function") {
        return window.wiliShortcuts.matches(e, window.wiliShortcuts.get(actionId));
      }
      return e.key === fallback && !e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey;
    });
    if (isSalesShortcut) return;
    e.stopPropagation();
      if (e.key === "ArrowDown") {
        if (!searchRows.length) return;
        e.preventDefault();
        posKeyboardNavigationActive = true;
        selectPosSearchRow(searchSelectedIndex + 1, { reveal: true });
      return;
    }
      if (e.key === "ArrowUp") {
        if (!searchRows.length) return;
        e.preventDefault();
        posKeyboardNavigationActive = true;
        selectPosSearchRow(searchSelectedIndex - 1, { reveal: true });
        return;
      }
      if (posKeyboardNavigationActive && (e.key === "+" || e.key === "Add" || (e.key === "=" && e.shiftKey))) {
        e.preventDefault();
        const selected = searchRows[searchSelectedIndex];
        if (!selected) return;
        addProductToCart(selected, 1);
        void renderPosSearch(ui.posSearchInput.value, { keepScroll: true, revealSelection: true });
        return;
      }
      if (posKeyboardNavigationActive && (e.key === "-" || e.key === "Subtract")) {
        e.preventDefault();
        const selected = searchRows[searchSelectedIndex];
        if (!selected) return;
        changeCartQty(selected.id, -1);
        void renderPosSearch(ui.posSearchInput.value, { keepScroll: true, revealSelection: true });
        return;
      }
      if (e.key === "Enter") {
      e.preventDefault();
      const rawQuery = String(ui.posSearchInput?.value || "").trim();
      if (posSearchDebounceTimer) {
        clearTimeout(posSearchDebounceTimer);
        posSearchDebounceTimer = null;
      }
      void (async () => {
        await handleExactPosLookup(rawQuery, { renderOnMiss: true });
      })();
    }
  });
  ui.posSearchInput?.addEventListener("keyup", (e) => {
    e.stopPropagation();
  });
  ui.saleDiscountType?.addEventListener("change", () => updatePosTotals());
  ui.saleDiscountValue?.addEventListener("input", () => {
    const raw = Math.max(0, Number(ui.saleDiscountValue.value || 0) || 0);
    ui.saleDiscountValue.value = String(raw);
    updatePosTotals();
  });
  ui.saleDiscountReset?.addEventListener("click", () => {
    if (ui.saleDiscountType) ui.saleDiscountType.value = "amount";
    if (ui.saleDiscountValue) ui.saleDiscountValue.value = "0";
    updatePosTotals();
  });
  const saleCounterCustomerLabel = () => trText("Client comptoir");
  const customerSearchValues = (customer) => [
    customer?.name,
    customer?.phone,
    customer?.tel,
    customer?.mobile,
    customer?.email,
    customer?.reference,
    customer?.public_ref,
    customer?.id
  ];
  const normalizeCustomerSuggestRows = (res) => {
    const data = res && res.ok ? res.data : res;
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.rows)) return data.rows;
    if (Array.isArray(data?.data)) return data.data;
    return [];
  };
  const mergeSaleCustomerRows = (rows = []) => {
    if (!Array.isArray(cache.customers)) cache.customers = [];
    (Array.isArray(rows) ? rows : []).forEach((row) => {
      if (!row || !row.id) return;
      const idx = cache.customers.findIndex((customer) => String(customer.id || "") === String(row.id || ""));
      if (idx >= 0) cache.customers[idx] = { ...cache.customers[idx], ...row };
      else cache.customers.push(row);
    });
  };
  const findSaleCustomerById = (id) => {
    const cid = String(id || "");
    if (!cid) return null;
    return (cache.customers || []).find((customer) => String(customer.id || "") === cid) || null;
  };
  const formatSaleCustomerSuggestion = (customer) => {
    if (!customer) return saleCounterCustomerLabel();
    const name = String(customer.name || "").trim() || "-";
    const meta = [customer.phone || customer.tel || customer.mobile, customer.reference || customer.public_ref || customer.email]
      .map((value) => String(value || "").trim())
      .filter(Boolean);
    return meta.length ? `${name} | ${meta.join(" | ")}` : name;
  };
  const hideSaleCustomerSuggest = () => ui.saleCustomerSuggest?.classList.add("hidden");
  const syncSaleCustomerSearch = () => {
    if (!ui.saleCustomerSearch) return;
    const customer = findSaleCustomerById(ui.saleCustomer?.value);
    if (customer) {
      ui.saleCustomerSearch.value = formatSaleCustomerSuggestion(customer);
    } else if (document.activeElement !== ui.saleCustomerSearch || !String(ui.saleCustomerSearch.value || "").trim()) {
      ui.saleCustomerSearch.value = "";
    }
  };
  window.syncSaleCustomerSearchInput = syncSaleCustomerSearch;
  const setSaleCustomerSelection = (customer, options = {}) => {
    const selected = customer && typeof customer === "object" ? customer : findSaleCustomerById(customer);
    const id = selected ? String(selected.id || "") : "";
    if (ui.saleCustomer) {
      if (id && !Array.from(ui.saleCustomer.options || []).some((option) => String(option.value || "") === id)) {
        const option = document.createElement("option");
        option.value = id;
        option.textContent = selected?.name || id;
        ui.saleCustomer.appendChild(option);
      }
      ui.saleCustomer.value = id;
    }
    if (ui.saleCustomerSearch) ui.saleCustomerSearch.value = selected ? formatSaleCustomerSuggestion(selected) : "";
    if (options.dispatch !== false) ui.saleCustomer?.dispatchEvent(new Event("change", { bubbles: true }));
  };
  const fetchSaleCustomerSuggestRows = async (rawQuery) => {
    const query = String(rawQuery || "").trim();
    if (window.wiliDb && typeof window.wiliDb.listCustomersPage === "function") {
      try {
        const out = await window.wiliDb.listCustomersPage({ q: query, limit: 100, offset: 0, sortKey: "name", sortDir: "asc" });
        const rows = normalizeCustomerSuggestRows(out);
        mergeSaleCustomerRows(rows);
        return rows;
      } catch (_err) {
        // Keep the payment field usable when a small edition exposes only cached customers.
      }
    }
    const q = query.toLowerCase();
    return (cache.customers || [])
      .filter((customer) => !q || customerSearchValues(customer).some((value) => String(value || "").toLowerCase().includes(q)))
      .slice(0, 100);
  };
  const renderSaleCustomerSuggestRows = (rows, query) => {
    if (!ui.saleCustomerSuggest || !ui.saleCustomerSearch) return;
    const q = String(query || "").trim().toLowerCase();
    const visibleRows = (Array.isArray(rows) ? rows : [])
      .filter((customer) => !q || customerSearchValues(customer).some((value) => String(value || "").toLowerCase().includes(q)))
      .slice(0, 100);
    ui.saleCustomerSuggest.innerHTML = "";
    const counterBtn = document.createElement("button");
    counterBtn.type = "button";
    counterBtn.className = "purchase-product-suggest-item";
    counterBtn.setAttribute("role", "option");
    const counterLabel = document.createElement("span");
    counterLabel.className = "purchase-product-suggest-name";
    counterLabel.textContent = saleCounterCustomerLabel();
    const counterMeta = document.createElement("span");
    counterMeta.className = "purchase-product-suggest-meta";
    counterMeta.textContent = langText("Sans client", "بدون زبون", "بلا كليان");
    counterBtn.append(counterLabel, counterMeta);
    counterBtn.addEventListener("mousedown", (event) => {
      event.preventDefault();
      setSaleCustomerSelection(null);
      hideSaleCustomerSuggest();
    });
    ui.saleCustomerSuggest.appendChild(counterBtn);
    visibleRows.forEach((customer) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "purchase-product-suggest-item";
      btn.setAttribute("role", "option");
      const label = document.createElement("span");
      label.className = "purchase-product-suggest-name";
      label.textContent = customer.name || "-";
      const marker = document.createElement("span");
      marker.className = "purchase-product-suggest-stock";
      marker.textContent = customer.phone || customer.tel || customer.mobile || customer.reference || customer.public_ref || "";
      const meta = document.createElement("span");
      meta.className = "purchase-product-suggest-meta";
      meta.textContent = [customer.email, customer.address].map((value) => String(value || "").trim()).filter(Boolean).join(" | ") || String(customer.id || "");
      btn.append(label, marker, meta);
      btn.addEventListener("mousedown", (event) => {
        event.preventDefault();
        setSaleCustomerSelection(customer);
        hideSaleCustomerSuggest();
      });
      ui.saleCustomerSuggest.appendChild(btn);
    });
    ui.saleCustomerSuggest.classList.remove("hidden");
  };
  const renderSaleCustomerSuggest = async () => {
    if (!ui.saleCustomerSuggest || !ui.saleCustomerSearch) return;
    let query = String(ui.saleCustomerSearch.value || "").trim();
    if (query === saleCounterCustomerLabel()) query = "";
    renderSaleCustomerSuggestRows(cache.customers || [], query);
    const rows = await fetchSaleCustomerSuggestRows(query);
    renderSaleCustomerSuggestRows(rows, query);
  };
  const resolveSaleCustomerFromInput = () => {
    const raw = String(ui.saleCustomerSearch?.value || "").trim();
    if (!raw || raw === saleCounterCustomerLabel()) return null;
    const current = findSaleCustomerById(ui.saleCustomer?.value);
    if (current && raw === formatSaleCustomerSuggestion(current)) return current;
    const q = raw.toLowerCase();
    return (cache.customers || []).find((customer) => customerSearchValues(customer).some((value) => String(value || "").trim().toLowerCase() === q)) || null;
  };
  ui.saleCustomerSearch?.addEventListener("focus", () => {
    ui.saleCustomerSearch?.select();
    void renderSaleCustomerSuggest();
  });
  ui.saleCustomerSearch?.addEventListener("input", () => {
    if (ui.saleCustomer) ui.saleCustomer.value = "";
    void renderSaleCustomerSuggest();
  });
  ui.saleCustomerSearch?.addEventListener("change", () => {
    const raw = String(ui.saleCustomerSearch?.value || "").trim();
    const resolved = resolveSaleCustomerFromInput();
    if (resolved || !raw) setSaleCustomerSelection(resolved);
    else if (ui.saleCustomer) ui.saleCustomer.value = "";
    hideSaleCustomerSuggest();
  });
  ui.saleCustomerSearch?.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      hideSaleCustomerSuggest();
      return;
    }
    if (event.key === "Enter") {
      event.preventDefault();
      setSaleCustomerSelection(resolveSaleCustomerFromInput());
      hideSaleCustomerSuggest();
      setTimeout(() => ui.salePaid?.focus(), 0);
      return;
    }
    if (event.key === "ArrowDown" && ui.saleCustomerSuggest && !ui.saleCustomerSuggest.classList.contains("hidden")) {
      event.preventDefault();
      ui.saleCustomerSuggest.querySelector(".purchase-product-suggest-item")?.focus();
    }
  });
  ui.saleCustomerSuggest?.addEventListener("keydown", (event) => {
    const current = document.activeElement;
    const items = Array.from(ui.saleCustomerSuggest.querySelectorAll(".purchase-product-suggest-item"));
    const idx = items.indexOf(current);
    if (event.key === "Escape") {
      event.preventDefault();
      hideSaleCustomerSuggest();
      ui.saleCustomerSearch?.focus();
    } else if (event.key === "ArrowDown") {
      event.preventDefault();
      (items[idx + 1] || items[0])?.focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      (items[idx - 1] || items[items.length - 1])?.focus();
    }
  });
  ui.saleCustomerSearch?.addEventListener("blur", () => setTimeout(hideSaleCustomerSuggest, 160));
  syncSaleCustomerSearch();
  ui.salePaidTouched = false;
  ui.salePaid.addEventListener("input", () => {
    if (!String(ui.saleCustomer?.value || "").trim()) {
      updatePosTotals();
      return;
    }
    ui.salePaidTouched = true;
    updatePosTotals();
  });
  ui.saleCustomer.addEventListener("change", () => {
    syncSaleCustomerSearch();
    ui.salePaidTouched = false;
    updatePosTotals();
    updateSaleCustomerDebtHint();
  });
  ui.saleQuoteCreateBtn?.addEventListener("click", async () => {
    await safeCreateSaleQuoteFromCart();
  });
  ui.saleQuotePdfBtn?.addEventListener("click", async () => {
    await safeExportActiveSaleQuotePdf();
  });
  ui.saleDebtCustomer.addEventListener("change", async () => {
    await refreshDebtInvoiceOptions(ui.saleDebtCustomer.value || "");
  });
  ui.saleDebtInvoice.addEventListener("change", async () => {
    const sid = String(ui.saleDebtInvoice.value || "");
    if (!sid) return;
    const out = await window.wiliDb.getSaleDetails(sid);
    if (!out || !out.ok) return;
    const inv = out.data && out.data.invoice ? out.data.invoice : null;
    if (!inv) return;
    ui.saleDebtAmount.value = money(inv.due_cents || 0);
  });
  ui.posClearBtn.addEventListener("click", confirmAndClearCart);
  ui.posClearBtn2?.addEventListener("click", confirmAndClearCart);

  ui.saleCheckoutForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (saleSubmitInFlight) return;
    if (!posCart.length) return;
    const okConfirm = await appConfirm(
      langText(
        "Confirmer validation de la vente ?",
        "تأكيد تسجيل عملية البيع؟",
        "تأكد بلي تحب تسجل هاد البيع؟"
      )
    );
    if (!okConfirm) return;
    pushUiDebugEvent("sales.submit.start", {
      cart_lines: Number(posCart.length),
      credit_mode: Number(toCents(ui.salePaid?.value || 0)) < Number(getPosCheckoutSummary().total_cents || 0)
    });
    if (isNegativeStockBlockedUi()) {
      for (const line of posCart) {
        const stock = stockForProduct(line.product_id);
        const need = qtyNum(line.qty);
        if (need > stock) {
          notifyInsufficientStock(String(line.name || "-"), stock, need);
          return;
        }
      }
    }
    saleSubmitInFlight = true;
    startSaleSubmitWatchdog();
    ui.posValidateBtn.disabled = true;
    try {
      const summary = getPosCheckoutSummary();
      const total = Number(summary.total_cents || 0);
      const isCounterCustomer = !String(ui.saleCustomer?.value || "").trim();
      const paid = isCounterCustomer ? total : Math.min(total, Math.max(0, toCents(ui.salePaid.value)));
      const autoPrintSaleCart = ui.setAutoPrintSaleCart ? String(ui.setAutoPrintSaleCart.value || "true") === "true" : true;
      const saleCartPrintPayload = {
        lines: (posCart || []).map((line) => ({ ...line })),
        totals: {
          subtotal_cents: Number(summary.subtotal_cents || 0),
          discount_cents: Number(summary.discount_cents || 0),
          total_cents: Number(summary.total_cents || 0)
        },
        ticket_paper: typeof currentPrintProfilePaper === "function" ? currentPrintProfilePaper("ticket") : ""
      };
      const salePayload = {
        customer_id: String(ui.saleCustomer.value || ""),
        items: buildPosSaleItemsPayload(summary),
        paid_cents: paid,
        allow_negative_stock: !isNegativeStockBlockedUi()
      };
      if (editingSaleId) salePayload.sale_id = String(editingSaleId || "");
      const saleAction = editingSaleId && window.wiliDb.updateSale ? window.wiliDb.updateSale(salePayload) : window.wiliDb.createSale(salePayload);
      const out = await withTimeout(saleAction, SALE_SUBMIT_TIMEOUT_MS, editingSaleId ? "sale_update_timeout" : "sale_create_timeout");
      if (!out || !out.ok) {
        ui.salesHistDetail.insertAdjacentHTML("afterbegin", row("Erreur", out && out.error ? out.error : "sale_failed"));
        return;
      }
      clearCart();
      resetSaleEditMode();
      safeClearActiveSaleQuoteSelection();
      ui.salePaid.value = "";
      ui.salePaidTouched = false;
      ui.saleCustomer.value = "";
      syncSaleCustomerSearch();
      updateSaleCustomerDebtHint();
      updatePosTotals();

      try {
        await refreshSales();
      } catch (err) {
        console.error("post_sale_journal_refresh_failed", err);
      }
      // Run expensive refresh asynchronously so repeated sales don't lock editing.
      queueSalesPostCommitRefresh();
      renderPosSearch(ui.posSearchInput?.value || "");
      if (autoPrintSaleCart) {
        setTimeout(async () => {
          const pdfOut = await window.wiliDb.exportSaleCartPdf(saleCartPrintPayload);
          if (!pdfOut || !pdfOut.ok) {
            appNotify(pdfOut && pdfOut.error ? pdfOut.error : "pdf_failed", true, 5000);
            return;
          }
          const filePath = String(pdfOut.data && pdfOut.data.file_path ? pdfOut.data.file_path : "");
          if (!filePath) {
            handlePdfResult(pdfOut, langText("Telecharger panier", "تحميل السلة", "تحميل السلة"), ui.salesHistDetail);
            return;
          }
          const printOut = typeof runPrintProfileForPdf === "function"
            ? await runPrintProfileForPdf("ticket", pdfOut, {
              title: langText("Telecharger panier", "تحميل السلة", "تحميل السلة"),
              targetEl: ui.salesHistDetail,
              fallbackPreview: false
            })
            : await window.wiliFiles.printPdf(filePath);
          if (!printOut || !printOut.ok) {
            appNotify(printOut && printOut.error ? printOut.error : "print_failed", true, 5000);
            handlePdfResult(pdfOut, langText("Telecharger panier", "تحميل السلة", "تحميل السلة"), ui.salesHistDetail);
          }
        }, 0);
      }
    } catch (err) {
      const code = String((err && err.code) || (err && err.message) || "");
      const msg = code.includes("timeout")
        ? langText("Operation lente: reessayez la validation", "العملية بطيئة: أعد المحاولة", "العملية بطيئة: عاود جرّب")
        : (err && err.message ? err.message : "sale_failed");
      ui.salesHistDetail.insertAdjacentHTML("afterbegin", row("Erreur", msg));
      sendDebugReport("sales_submit_error", err, {
        error_code: trimDebugText(code, 120),
        cart_lines: Number(posCart.length),
        credit_mode: Number(toCents(ui.salePaid?.value || 0)) < Number(getPosCheckoutSummary().total_cents || 0)
      });
      recoverSalesInteractivity("submit_error");
    } finally {
      saleSubmitInFlight = false;
      stopSaleSubmitWatchdog();
      forceEnableSalesControls();
      ui.posValidateBtn.disabled = false;
      setTimeout(() => ui.posSearchInput?.focus(), 0);
    }
  });
  ui.saleDebtForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const sid = String(ui.saleDebtInvoice.value || "");
    if (!sid) return;
    const amountCents = toCents(ui.saleDebtAmount.value);
    if (amountCents <= 0) return;
    const note = String(ui.saleDebtNote.value || "").trim();
    const out = await window.wiliDb.addSalePayment({ sale_id: sid, amount_cents: amountCents, note });
    if (!out || !out.ok) {
      showSaleDebtMsg(out && out.error ? out.error : "payment_failed", true);
      return;
    }
    const pay = out.data && out.data.payment ? out.data.payment : null;
    const inv = out.data && out.data.invoice ? out.data.invoice : null;
    if (pay && pay.id) {
      const pdf = await window.wiliDb.exportSalePaymentReceiptPdf(String(pay.id));
      handlePdfResult(pdf, langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص"), ui.saleDebtTableBody || ui.salesHistDetail);
    }
    ui.saleDebtNote.value = "";
    if (inv) {
      const statusText = String(inv.status || (Number(inv.due_cents || 0) > 0 ? "partial_paid" : "paid"));
      showSaleDebtMsg(
        langText(
          `Paiement valide: ${money(amountCents)} | Reste: ${money(inv.due_cents || 0)} | Statut: ${statusText}`,
          `تم الدفع: ${money(amountCents)} | المتبقي: ${money(inv.due_cents || 0)} | الحالة: ${statusText}`,
          `الدفعة تمت: ${money(amountCents)} | الباقي: ${money(inv.due_cents || 0)} | الحالة: ${statusText}`
        ),
        false
      );
    } else {
      showSaleDebtMsg(langText("Paiement valide", "تم الدفع بنجاح", "الدفعة تمت"), false);
    }
    await refreshAll();
    await refreshDebtInvoiceOptions(ui.saleDebtCustomer.value || "");
    salesSelectedId = sid;
    applySalesHistoryFilters(false);
  });

  ui.posPrintCartBtn?.addEventListener("click", async () => {
    if (!posCart.length) return;
    const summary = getPosCheckoutSummary();
    const out = await window.wiliDb.exportSaleCartPdf({
      lines: posCart,
      totals: {
        subtotal_cents: Number(summary.subtotal_cents || 0),
        discount_cents: Number(summary.discount_cents || 0),
        total_cents: Number(summary.total_cents || 0)
      },
      ticket_paper: typeof currentPrintProfilePaper === "function" ? currentPrintProfilePaper("ticket") : ""
    });
    handlePdfResult(out, langText("Telecharger panier", "تحميل السلة", "تحميل السلة"), ui.salesHistDetail);
  });
  ui.salesHistApplyBtn?.addEventListener("click", () => applySalesHistoryFilters(true));
  ui.salesHistSearch?.addEventListener("input", () => applySalesHistoryFilters(true));
  ui.salesHistFrom?.addEventListener("change", () => applySalesHistoryFilters(true));
  ui.salesHistTo?.addEventListener("change", () => applySalesHistoryFilters(true));
  ui.salesHistSort?.addEventListener("change", () => {
    applySalesSortModeFromSelect(ui.salesHistSort?.value || "date_desc");
    applySalesHistoryFilters(true);
  });
  ui.salesHistPageSize?.addEventListener("change", () => applySalesHistoryFilters(true));
  ui.salesHistPrevBtn?.addEventListener("click", () => {
    salesHistPage = Math.max(1, salesHistPage - 1);
    applySalesHistoryFilters(false);
  });
  ui.salesHistNextBtn?.addEventListener("click", () => {
    salesHistPage += 1;
    applySalesHistoryFilters(false);
  });
  ui.salesHistPrintSelectedBtn?.addEventListener("click", async () => {
    if (!salesSelectedId) return;
    const out = await window.wiliDb.exportSaleInvoicePdf(String(salesSelectedId), { document_mode: "bon" });
    handlePdfResult(out, langText("Bon selection", "وصل المحدد", "بون المحدد"), ui.salesHistDetail);
  });
  ui.salesHistPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportSalesHistoryPdf({
      rows: salesRowsFiltered,
      filters: {
        q: String(ui.salesHistSearch?.value || "").trim(),
        from: String(ui.salesHistFrom?.value || "").trim(),
        to: String(ui.salesHistTo?.value || "").trim(),
        sort: String(ui.salesHistSort?.value || "date_desc")
      }
    });
    handlePdfResult(out, langText("Telecharger historique", "تحميل السجل", "تحميل السجل"), ui.salesHistDetail);
  });
  ui.salesHistoryTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "");
      if (!key) return;
      if (salesJournalState.sortKey === key) {
        salesJournalState.sortDir = salesJournalState.sortDir === "asc" ? "desc" : "asc";
      } else {
        salesJournalState.sortKey = key;
        salesJournalState.sortDir = key === "created_at" ? "desc" : "asc";
      }
      if (ui.salesHistSort) {
        if (salesJournalState.sortKey === "created_at") ui.salesHistSort.value = salesJournalState.sortDir === "asc" ? "date_asc" : "date_desc";
        if (salesJournalState.sortKey === "total_cents") ui.salesHistSort.value = "total_desc";
        if (salesJournalState.sortKey === "due_cents") ui.salesHistSort.value = "due_desc";
        if (salesJournalState.sortKey === "customer_name") ui.salesHistSort.value = "customer_asc";
      }
      applySalesHistoryFilters(false);
    });
  });
  ui.salesQuoteApplyBtn?.addEventListener("click", () => applySalesQuotesFilters(true));
  ui.salesQuoteSearch?.addEventListener("input", () => applySalesQuotesFilters(true));
  ui.salesQuoteFrom?.addEventListener("change", () => applySalesQuotesFilters(true));
  ui.salesQuoteTo?.addEventListener("change", () => applySalesQuotesFilters(true));
  ui.salesQuoteStatus?.addEventListener("change", () => applySalesQuotesFilters(true));
  ui.salesQuotePageSize?.addEventListener("change", () => applySalesQuotesFilters(true));
  ui.salesQuotePrevBtn?.addEventListener("click", () => {
    salesQuotePage = Math.max(1, salesQuotePage - 1);
    applySalesQuotesFilters(false);
  });
  ui.salesQuoteNextBtn?.addEventListener("click", () => {
    salesQuotePage += 1;
    applySalesQuotesFilters(false);
  });
  ui.salesQuotesTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "");
      if (!key) return;
      if (salesQuoteState.sortKey === key) {
        salesQuoteState.sortDir = salesQuoteState.sortDir === "asc" ? "desc" : "asc";
      } else {
        salesQuoteState.sortKey = key;
        salesQuoteState.sortDir = key === "created_at" ? "desc" : "asc";
      }
      applySalesQuotesFilters(false);
    });
  });
  ui.purchasesHistApplyBtn?.addEventListener("click", () => applyPurchasesJournalFilters(true));
  ui.purchasesHistPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportPurchasesHistoryPdf({
      rows: purchaseRowsFiltered,
      filters: {
        q: String(ui.purchasesHistSearch?.value || "").trim(),
        from: String(ui.purchasesHistFrom?.value || "").trim(),
        to: String(ui.purchasesHistTo?.value || "").trim(),
        sort: String(ui.purchasesHistSort?.value || "date_desc")
      }
    });
    handlePdfResult(out, langText("Telecharger historique achats", "تحميل سجل المشتريات", "تحميل سجل المشتريات"), ui.purchasesJournalTableBody);
  });
  ui.purchasesHistSearch?.addEventListener("input", () => applyPurchasesJournalFilters(true));
  ui.purchasesHistFrom?.addEventListener("change", () => applyPurchasesJournalFilters(true));
  ui.purchasesHistTo?.addEventListener("change", () => applyPurchasesJournalFilters(true));
  ui.purchasesHistSort?.addEventListener("change", () => {
    applyPurchasesSortModeFromSelect(ui.purchasesHistSort?.value || "date_desc");
    applyPurchasesJournalFilters(true);
  });
  ui.purchasesHistPageSize?.addEventListener("change", () => applyPurchasesJournalFilters(true));
  ui.purchasesHistPrevBtn?.addEventListener("click", () => {
    purchasesHistPage = Math.max(1, purchasesHistPage - 1);
    applyPurchasesJournalFilters(false);
  });
  ui.purchasesHistNextBtn?.addEventListener("click", () => {
    purchasesHistPage += 1;
    applyPurchasesJournalFilters(false);
  });
  ui.purchasesHistPrintSelectedBtn?.addEventListener("click", async () => {
    if (!purchaseSelectedId) return;
    await exportPurchaseInvoicePdfById(String(purchaseSelectedId), ui.purchasesJournalTableBody, "bon");
  });
  ui.purchasesJournalTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "");
      if (!key) return;
      if (purchasesJournalState.sortKey === key) {
        purchasesJournalState.sortDir = purchasesJournalState.sortDir === "asc" ? "desc" : "asc";
      } else {
        purchasesJournalState.sortKey = key;
        purchasesJournalState.sortDir = key === "created_at" ? "desc" : "asc";
      }
      if (ui.purchasesHistSort) {
        if (purchasesJournalState.sortKey === "created_at") ui.purchasesHistSort.value = purchasesJournalState.sortDir === "asc" ? "date_asc" : "date_desc";
        if (purchasesJournalState.sortKey === "total_cents") ui.purchasesHistSort.value = "total_desc";
        if (purchasesJournalState.sortKey === "due_cents") ui.purchasesHistSort.value = "due_desc";
        if (purchasesJournalState.sortKey === "supplier_name") ui.purchasesHistSort.value = "supplier_asc";
      }
      applyPurchasesJournalFilters(false);
    });
  });

  window.addEventListener("keydown", (e) => {
    const activeViewId = String(document.querySelector(".view.active")?.id || "");
    const inSales = activeViewId === "view-sales";
    if (!inSales) return;
    const hitShortcut = (actionId, fallback) => {
      if (window.wiliShortcuts && typeof window.wiliShortcuts.matches === "function") {
        return window.wiliShortcuts.matches(e, window.wiliShortcuts.get(actionId));
      }
      return e.key === fallback && !e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey;
    };

    const focusAndSelect = (el) => {
      if (!el) return;
      el.focus();
      if (typeof el.select === "function") el.select();
    };
    const addSelectedProduct = () => {
      const rows = (typeof searchRows !== "undefined" && Array.isArray(searchRows)) ? searchRows : [];
      const rawIndex = typeof searchSelectedIndex !== "undefined" ? Number(searchSelectedIndex || 0) : 0;
      const idx = Math.max(0, Math.min(rows.length - 1, Number.isFinite(rawIndex) ? rawIndex : 0));
      const selected = rows[idx];
      if (!selected) {
        ui.posResults?.querySelector("[data-add-product]")?.click();
        return;
      }
      const qty = Math.max(0.01, qtyNum(ui.posQuickQty?.value || 1, 1));
      if (typeof addProductToCart === "function") addProductToCart(selected, qty);
      if (typeof selectPosSearchRow === "function") selectPosSearchRow(idx, { reveal: true });
    };

    if (hitShortcut("sales_focus_search", "F6")) {
      e.preventDefault();
      focusAndSelect(ui.posSearchInput);
      return;
    }
    if (hitShortcut("sales_add_selected", "F7")) {
      e.preventDefault();
      addSelectedProduct();
      return;
    }
    if (hitShortcut("sales_print_cart", "F8")) {
      e.preventDefault();
      ui.posPrintCartBtn?.click();
      return;
    }
    if (hitShortcut("sales_checkout", "F9")) {
      e.preventDefault();
      ui.posValidateBtn?.click();
      return;
    }
    if (hitShortcut("sales_clear_cart", "F10")) {
      e.preventDefault();
      ui.posClearBtn?.click();
      return;
    }
    if (hitShortcut("sales_focus_paid", "F12")) {
      e.preventDefault();
      focusAndSelect(ui.salePaid);
      return;
    }
  });

  ui.purchaseProduct.addEventListener("change", updatePurchaseComputed);
  const syncPurchaseDocHint = () => {
    const type = String(ui.purchaseDocType?.value || "BR").toUpperCase();
    if (!ui.purchaseDocHint) return;
    if (type === "FC") {
      ui.purchaseDocHint.textContent = langText(
        "FC: facture fournisseur (R)",
        "FC: فاتورة مورد (R)",
        "FC: فاكتور فورنيسور (R)"
      );
      return;
    }
    ui.purchaseDocHint.textContent = langText(
      "BR: achat fourniss",
      "BR: شراء من مورد",
      "BR: تتشري من فورنيسور"
    );
  };
  ui.purchaseDocType?.addEventListener("change", syncPurchaseDocHint);
  syncPurchaseDocHint();
  const purchaseMiscSupplierLabel = () => langText("Achat divers", "شراء متنوع", "شرا متنوع");
  const supplierSearchValues = (supplier) => [
    supplier?.name,
    supplier?.phone,
    supplier?.tel,
    supplier?.mobile,
    supplier?.email,
    supplier?.reference,
    supplier?.public_ref,
    supplier?.id
  ];
  const normalizeSuggestRows = (res) => {
    const data = res && res.ok ? res.data : res;
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.rows)) return data.rows;
    if (Array.isArray(data?.data)) return data.data;
    return [];
  };
  const mergePurchaseSupplierRows = (rows = []) => {
    if (!Array.isArray(cache.suppliers)) cache.suppliers = [];
    (Array.isArray(rows) ? rows : []).forEach((row) => {
      if (!row || !row.id) return;
      const idx = cache.suppliers.findIndex((supplier) => String(supplier.id || "") === String(row.id || ""));
      if (idx >= 0) cache.suppliers[idx] = { ...cache.suppliers[idx], ...row };
      else cache.suppliers.push(row);
    });
  };
  const findPurchaseSupplierById = (id) => {
    const sid = String(id || "");
    if (!sid) return null;
    return (cache.suppliers || []).find((supplier) => String(supplier.id || "") === sid) || null;
  };
  const formatPurchaseSupplierSuggestion = (supplier) => {
    if (!supplier) return purchaseMiscSupplierLabel();
    const name = String(supplier.name || "").trim() || "-";
    const meta = [supplier.phone || supplier.tel || supplier.mobile, supplier.reference || supplier.public_ref || supplier.email]
      .map((value) => String(value || "").trim())
      .filter(Boolean);
    return meta.length ? `${name} | ${meta.join(" | ")}` : name;
  };
  const hidePurchaseSupplierSuggest = () => ui.purchaseSupplierSuggest?.classList.add("hidden");
  const syncPurchaseSupplierSearch = () => {
    if (!ui.purchaseSupplierSearch) return;
    const supplier = findPurchaseSupplierById(ui.purchaseSupplier?.value);
    if (supplier) {
      ui.purchaseSupplierSearch.value = formatPurchaseSupplierSuggestion(supplier);
    } else if (document.activeElement !== ui.purchaseSupplierSearch || !String(ui.purchaseSupplierSearch.value || "").trim()) {
      ui.purchaseSupplierSearch.value = "";
    }
  };
  window.syncPurchaseSupplierSearchInput = syncPurchaseSupplierSearch;
  const setPurchaseSupplierSelection = (supplier, options = {}) => {
    const selected = supplier && typeof supplier === "object" ? supplier : findPurchaseSupplierById(supplier);
    const id = selected ? String(selected.id || "") : "";
    if (ui.purchaseSupplier) {
      if (id && !Array.from(ui.purchaseSupplier.options || []).some((option) => String(option.value || "") === id)) {
        const option = document.createElement("option");
        option.value = id;
        option.textContent = selected?.name || id;
        ui.purchaseSupplier.appendChild(option);
      }
      ui.purchaseSupplier.value = id;
    }
    if (ui.purchaseSupplierSearch) ui.purchaseSupplierSearch.value = selected ? formatPurchaseSupplierSuggestion(selected) : "";
    if (options.dispatch !== false) ui.purchaseSupplier?.dispatchEvent(new Event("change", { bubbles: true }));
  };
  const fetchPurchaseSupplierSuggestRows = async (rawQuery) => {
    const query = String(rawQuery || "").trim();
    if (window.wiliDb && typeof window.wiliDb.listSuppliersPage === "function") {
      try {
        const out = await window.wiliDb.listSuppliersPage({ q: query, limit: 100, offset: 0, sortKey: "name", sortDir: "asc" });
        const rows = normalizeSuggestRows(out);
        mergePurchaseSupplierRows(rows);
        return rows;
      } catch (_err) {
        // Fall back to the current cache when the edition does not expose paged suppliers yet.
      }
    }
    const q = query.toLowerCase();
    return (cache.suppliers || [])
      .filter((supplier) => !q || supplierSearchValues(supplier).some((value) => String(value || "").toLowerCase().includes(q)))
      .slice(0, 100);
  };
  const renderPurchaseSupplierSuggestRows = (rows, query) => {
    if (!ui.purchaseSupplierSuggest || !ui.purchaseSupplierSearch) return;
    const q = String(query || "").trim().toLowerCase();
    const miscLabel = purchaseMiscSupplierLabel();
    const showMisc = true;
    const visibleRows = (Array.isArray(rows) ? rows : [])
      .filter((supplier) => !q || supplierSearchValues(supplier).some((value) => String(value || "").toLowerCase().includes(q)))
      .slice(0, 100);
    ui.purchaseSupplierSuggest.innerHTML = "";
    if (showMisc) {
      const miscBtn = document.createElement("button");
      miscBtn.type = "button";
      miscBtn.className = "purchase-product-suggest-item";
      miscBtn.setAttribute("role", "option");
      const label = document.createElement("span");
      label.className = "purchase-product-suggest-name";
      label.textContent = miscLabel;
      const meta = document.createElement("span");
      meta.className = "purchase-product-suggest-meta";
      meta.textContent = langText("Sans fournisseur", "بدون مورد", "بلا فورنيسور");
      miscBtn.append(label, meta);
      miscBtn.addEventListener("mousedown", (event) => {
        event.preventDefault();
        setPurchaseSupplierSelection(null);
        hidePurchaseSupplierSuggest();
      });
      ui.purchaseSupplierSuggest.appendChild(miscBtn);
    }
    visibleRows.forEach((supplier) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "purchase-product-suggest-item";
      btn.setAttribute("role", "option");
      const label = document.createElement("span");
      label.className = "purchase-product-suggest-name";
      label.textContent = supplier.name || "-";
      const marker = document.createElement("span");
      marker.className = "purchase-product-suggest-stock";
      marker.textContent = supplier.phone || supplier.tel || supplier.mobile || supplier.reference || supplier.public_ref || "";
      const meta = document.createElement("span");
      meta.className = "purchase-product-suggest-meta";
      meta.textContent = [supplier.email, supplier.address].map((value) => String(value || "").trim()).filter(Boolean).join(" | ") || String(supplier.id || "");
      btn.append(label, marker, meta);
      btn.addEventListener("mousedown", (event) => {
        event.preventDefault();
        setPurchaseSupplierSelection(supplier);
        hidePurchaseSupplierSuggest();
      });
      ui.purchaseSupplierSuggest.appendChild(btn);
    });
    if (!showMisc && !visibleRows.length) {
      const empty = document.createElement("div");
      empty.className = "purchase-product-suggest-empty";
      empty.textContent = langText("Aucun fournisseur trouve", "لا يوجد مورد", "ماكانش فورنيسور");
      ui.purchaseSupplierSuggest.appendChild(empty);
    }
    ui.purchaseSupplierSuggest.classList.remove("hidden");
  };
  const renderPurchaseSupplierSuggest = async () => {
    if (!ui.purchaseSupplierSuggest || !ui.purchaseSupplierSearch) return;
    let query = String(ui.purchaseSupplierSearch.value || "").trim();
    if (query === purchaseMiscSupplierLabel()) query = "";
    renderPurchaseSupplierSuggestRows(cache.suppliers || [], query);
    const rows = await fetchPurchaseSupplierSuggestRows(query);
    renderPurchaseSupplierSuggestRows(rows, query);
  };
  const resolvePurchaseSupplierFromInput = () => {
    const raw = String(ui.purchaseSupplierSearch?.value || "").trim();
    if (!raw || raw === purchaseMiscSupplierLabel()) return null;
    const current = findPurchaseSupplierById(ui.purchaseSupplier?.value);
    if (current && raw === formatPurchaseSupplierSuggestion(current)) return current;
    const q = raw.toLowerCase();
    return (cache.suppliers || []).find((supplier) => supplierSearchValues(supplier).some((value) => String(value || "").trim().toLowerCase() === q)) || null;
  };
  ui.purchaseSupplierSearch?.addEventListener("focus", () => {
    ui.purchaseSupplierSearch?.select();
    void renderPurchaseSupplierSuggest();
  });
  ui.purchaseSupplierSearch?.addEventListener("input", () => {
    if (ui.purchaseSupplier) ui.purchaseSupplier.value = "";
    void renderPurchaseSupplierSuggest();
  });
  ui.purchaseSupplierSearch?.addEventListener("change", () => {
    const raw = String(ui.purchaseSupplierSearch?.value || "").trim();
    const resolved = resolvePurchaseSupplierFromInput();
    if (resolved || !raw) setPurchaseSupplierSelection(resolved);
    else if (ui.purchaseSupplier) ui.purchaseSupplier.value = "";
    hidePurchaseSupplierSuggest();
  });
  ui.purchaseSupplierSearch?.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      hidePurchaseSupplierSuggest();
      return;
    }
    if (event.key === "Enter") {
      event.preventDefault();
      setPurchaseSupplierSelection(resolvePurchaseSupplierFromInput());
      hidePurchaseSupplierSuggest();
      setTimeout(() => ui.purchaseProductSearch?.focus(), 0);
      return;
    }
    if (event.key === "ArrowDown" && ui.purchaseSupplierSuggest && !ui.purchaseSupplierSuggest.classList.contains("hidden")) {
      event.preventDefault();
      ui.purchaseSupplierSuggest.querySelector(".purchase-product-suggest-item")?.focus();
    }
  });
  ui.purchaseSupplierSuggest?.addEventListener("keydown", (event) => {
    const current = document.activeElement;
    const items = Array.from(ui.purchaseSupplierSuggest.querySelectorAll(".purchase-product-suggest-item"));
    const idx = items.indexOf(current);
    if (event.key === "Escape") {
      event.preventDefault();
      hidePurchaseSupplierSuggest();
      ui.purchaseSupplierSearch?.focus();
    } else if (event.key === "ArrowDown") {
      event.preventDefault();
      (items[idx + 1] || items[0])?.focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      (items[idx - 1] || items[items.length - 1])?.focus();
    }
  });
  ui.purchaseSupplierSearch?.addEventListener("blur", () => setTimeout(hidePurchaseSupplierSuggest, 160));
  syncPurchaseSupplierSearch();
  const isPurchaseProductPurchasable = (product) => product && String(product.product_type || "product").toLowerCase() !== "service";
  const formatPurchaseProductSuggestion = (product) => `${product.name || ""} | ${product.barcode || "-"} | ${product.reference || "-"}`;
  const hidePurchaseProductSuggest = () => ui.purchaseProductSuggest?.classList.add("hidden");
  const renderPurchaseProductSuggest = () => {
    if (!ui.purchaseProductSuggest || !ui.purchaseProductSearch) return;
    const query = String(ui.purchaseProductSearch.value || "").trim().toLowerCase();
    const rows = (cache.products || [])
      .filter(isPurchaseProductPurchasable)
      .filter((product) => {
        if (!query) return true;
        return [product.name, product.barcode, product.reference]
          .some((value) => String(value || "").toLowerCase().includes(query));
      })
      .slice(0, 80);
    ui.purchaseProductSuggest.innerHTML = "";
    if (!rows.length) {
      const empty = document.createElement("div");
      empty.className = "purchase-product-suggest-empty";
      empty.textContent = langText("Aucun produit trouve", "لا يوجد منتج", "ماكانش برودوي");
      ui.purchaseProductSuggest.appendChild(empty);
    } else {
      rows.forEach((product) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "purchase-product-suggest-item";
        btn.setAttribute("role", "option");
        const label = document.createElement("span");
        label.className = "purchase-product-suggest-name";
        label.textContent = product.name || "-";
        const stock = document.createElement("span");
        stock.className = "purchase-product-suggest-stock";
        stock.textContent = `${Number(product.stock_qty || 0)} stock`;
        const meta = document.createElement("span");
        meta.className = "purchase-product-suggest-meta";
        meta.textContent = `${product.barcode || "-"} | ${product.reference || "-"}`;
        btn.append(label, stock, meta);
        btn.addEventListener("mousedown", (event) => {
          event.preventDefault();
          ui.purchaseProduct.value = String(product.id || "");
          ui.purchaseProductSearch.value = formatPurchaseProductSuggestion(product);
          updatePurchaseComputed();
          hidePurchaseProductSuggest();
        });
        ui.purchaseProductSuggest.appendChild(btn);
      });
    }
    ui.purchaseProductSuggest.classList.remove("hidden");
  };
  ui.purchaseProductSearch?.addEventListener("focus", renderPurchaseProductSuggest);
  ui.purchaseProductSearch?.addEventListener("input", () => {
    ui.purchaseProduct.value = "";
    renderPurchaseProductSuggest();
  });
  ui.purchaseProductSearch?.addEventListener("change", async () => {
    const p = await resolvePurchaseProductFromInput();
    ui.purchaseProduct.value = p ? String(p.id) : "";
    updatePurchaseComputed();
    hidePurchaseProductSuggest();
  });
  const submitPurchaseLineOnEnter = (event) => {
    if (event.key !== "Enter" || event.shiftKey || event.altKey || event.ctrlKey || event.metaKey) return;
    event.preventDefault();
    ui.purchaseAddLineBtn?.click();
  };
  ui.purchaseProductSearch?.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      hidePurchaseProductSuggest();
      return;
    }
    if (event.key === "ArrowDown" && ui.purchaseProductSuggest && !ui.purchaseProductSuggest.classList.contains("hidden")) {
      event.preventDefault();
      ui.purchaseProductSuggest.querySelector(".purchase-product-suggest-item")?.focus();
      return;
    }
    submitPurchaseLineOnEnter(event);
  });
  ui.purchaseProductSuggest?.addEventListener("keydown", (event) => {
    const current = document.activeElement;
    const items = Array.from(ui.purchaseProductSuggest.querySelectorAll(".purchase-product-suggest-item"));
    const idx = items.indexOf(current);
    if (event.key === "Escape") {
      event.preventDefault();
      hidePurchaseProductSuggest();
      ui.purchaseProductSearch?.focus();
    } else if (event.key === "ArrowDown") {
      event.preventDefault();
      (items[idx + 1] || items[0])?.focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      (items[idx - 1] || items[items.length - 1])?.focus();
    }
  });
  ui.purchaseProductSearch?.addEventListener("blur", () => setTimeout(hidePurchaseProductSuggest, 160));
  ui.purchaseQty?.addEventListener("keydown", submitPurchaseLineOnEnter);
  ui.purchaseDebtSupplier?.addEventListener("change", async () => {
    await refreshPurchaseDebtInvoiceOptions(ui.purchaseDebtSupplier.value || "");
  });
  ui.purchaseDebtInvoice?.addEventListener("change", async () => {
    const pid = String(ui.purchaseDebtInvoice.value || "");
    if (!pid) return;
    const out = await window.wiliDb.getPurchaseDetails(pid);
    if (!out || !out.ok) return;
    const inv = out.data && out.data.invoice ? out.data.invoice : null;
    if (!inv) return;
    purchaseSelectedId = String(inv.id || "");
    ui.purchaseDebtAmount.value = money(inv.due_cents || 0);
    await renderPurchaseSelectedDetail();
  });
    ui.purchaseQty.addEventListener("input", updatePurchaseComputed);
    ui.purchaseSupplier?.addEventListener("change", () => {
      syncPurchaseSupplierSearch();
      purchasePaidManual = false;
      updatePurchaseComputed();
    });
    ui.purchasePaid.addEventListener("input", () => {
      purchasePaidManual = true;
      if (purchasePaidAutoFull) setPurchasePaidAutoFull(false, true);
      updatePurchaseComputed();
    });
  ui.purchasePaidAutoFull?.addEventListener("change", () => {
    setPurchasePaidAutoFull(Boolean(ui.purchasePaidAutoFull.checked), true);
  });
  ui.purchaseAddLineBtn?.addEventListener("click", async () => {
    const raw = String(ui.purchaseProductSearch.value || "").trim();
    const resolved = await resolvePurchaseProductFromInput();
    const pid = resolved && isPurchaseProductPurchasable(resolved) ? String(resolved.id) : "";
    const qty = Math.max(0.01, qtyNum(ui.purchaseQty.value || 1, 1));
    if (!pid || (resolved && !isPurchaseProductPurchasable(resolved))) {
      appNotify(
        langText(
          "Selectionnez un produit existant ou utilisez Nouveau produit rapide",
          "اختر منتجًا موجودًا أو استعمل منتج سريع جديد",
          "اختار برودوي موجود ولا استعمل برودوي سريع جديد"
        ),
        true,
        3000
      );
      setTimeout(() => ui.purchaseProductSearch?.focus(), 0);
      return;
    }
    ui.purchaseProduct.value = pid;
    if (resolved) {
      ui.purchaseProductSearch.value = `${resolved.name || ""} | ${resolved.barcode || "-"} | ${resolved.reference || "-"}`;
    }
    addPurchaseLine(pid, qty);
    ui.purchaseQty.value = "1";
  });
  ui.purchaseClearBtn?.addEventListener("click", clearPurchaseCart);

  ui.purchaseForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!purchaseCart.length) {
      appNotify(langText("Ajoutez au moins un produit a l'achat", "أضف منتج واحد على الأقل للشراء", "زيد برودوي واحد على الأقل للشرا"), true, 3500);
      return;
    }
    const supplierId = String(ui.purchaseSupplier.value || "");
    const totalCents = (purchaseCart || []).reduce((sum, line) => sum + Math.max(0, Number(line.line_total_cents || 0)), 0);
    const paid = supplierId ? toCents(ui.purchasePaid.value) : totalCents;
    const documentType = "BR";
    if (documentType === "FC" && !supplierId) {
      ui.purchaseDetailBox.insertAdjacentHTML(
        "afterbegin",
        row(
          "Erreur",
          langText(
            "Selectionnez un fournisseur pour la facture FC",
            "اختر المورد قبل تسجيل فاتورة FC",
            "اختار لفورنيسور قبل ما تسجل فاكتور FC"
          )
        )
      );
      return;
    }
    const confirmed = await appConfirm(langText(
      "Confirmer l'enregistrement de cet achat ?",
      "هل تريد تأكيد تسجيل هذا الشراء؟",
      "تأكد تسجيل هاد الشرا؟"
    ));
    if (!confirmed) return;
    const purchasePayload = {
      supplier_id: supplierId,
      document_type: documentType,
      apply_stock: documentType !== "FC",
      tax_rate_percent: documentType === "FC" ? 19 : 0,
      items: purchaseCart.map((line) => ({
        product_id: String(line.product_id || ""),
        qty: qtyNum(line.qty),
        unit_cost_cents: Number(line.unit_cost_cents || 0)
      })),
      paid_cents: paid
    };
    if (editingPurchaseId) purchasePayload.purchase_id = String(editingPurchaseId || "");
    const out = editingPurchaseId && window.wiliDb.updatePurchase
      ? await window.wiliDb.updatePurchase(purchasePayload)
      : await window.wiliDb.createPurchase(purchasePayload);
    if (!out || !out.ok) {
      appNotify(out && out.error ? out.error : "purchase_failed", true, 5000);
      return;
    }
    const wasEditingPurchase = Boolean(editingPurchaseId);
    const cartSnapshot = purchaseCart.map((line) => ({
      product_id: String(line.product_id || ""),
      qty: qtyNum(line.qty),
      unit_cost_cents: Number(line.unit_cost_cents || 0),
      product_name: String(line.name || "")
    }));
    const createdInvoice = out.data && out.data.invoice ? out.data.invoice : null;
    lastPurchaseForPdf = {
      invoice: createdInvoice ? { ...createdInvoice, doc_type: String(createdInvoice.doc_type || documentType || "BR") } : null,
      items: cartSnapshot,
      paid_cents: paid,
      document_type: documentType
    };
    purchaseSelectedId = String((out.data && out.data.invoice && out.data.invoice.id) || purchaseSelectedId || "");
    ui.purchaseQty.value = "1";
    purchasePaidManual = false;
    updatePurchaseComputed();
    clearPurchaseCart();
    updatePurchaseComputed();
    await refreshAll();
    appNotify(wasEditingPurchase
      ? langText("Modification achat enregistree", "تم حفظ تعديل الشراء", "تبديل الشرا تسجل")
      : langText("Achat enregistre", "تم تسجيل الشراء", "الشرا تسجل"), false, 3500);
    switchView("purchases");
    setTimeout(() => ui.purchaseProductSearch?.focus(), 0);
  });

  ui.purchaseDebtForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const pid = String(ui.purchaseDebtInvoice.value || "");
    if (!pid) return;
    const amountCents = toCents(ui.purchaseDebtAmount.value);
    if (amountCents <= 0) return;
    const note = String(ui.purchaseDebtNote.value || "").trim();
    const out = await window.wiliDb.addPurchasePayment({ purchase_id: pid, amount_cents: amountCents, note });
    if (!out || !out.ok) {
      ui.purchaseDetailBox.insertAdjacentHTML("afterbegin", row("Erreur", out && out.error ? out.error : "purchase_payment_failed"));
      showPurchaseDebtMsg(out && out.error ? out.error : "purchase_payment_failed", true);
      return;
    }
    const pay = out.data && out.data.payment ? out.data.payment : null;
    const inv = out.data && out.data.invoice ? out.data.invoice : null;
    if (pay && pay.id) {
      const pdf = await window.wiliDb.exportPurchasePaymentReceiptPdf(String(pay.id));
      handlePdfResult(pdf, langText("Telecharger reglement", "تحميل التسديد", "تحميل خلاص"), ui.purchaseDetailBox);
    }
    ui.purchaseDebtNote.value = "";
    if (inv) {
      const statusText = String(inv.status || (Number(inv.due_cents || 0) > 0 ? "partial_paid" : "paid"));
      showPurchaseDebtMsg(
        langText(
          `Paiement valide: ${money(amountCents)} | Reste: ${money(inv.due_cents || 0)} | Statut: ${statusText}`,
          `تم الدفع: ${money(amountCents)} | المتبقي: ${money(inv.due_cents || 0)} | الحالة: ${statusText}`,
          `الدفعة تمت: ${money(amountCents)} | الباقي: ${money(inv.due_cents || 0)} | الحالة: ${statusText}`
        ),
        false
      );
    } else {
      showPurchaseDebtMsg(langText("Paiement valide", "تم الدفع بنجاح", "الدفعة تمت"), false);
    }
    purchaseSelectedId = pid;
    await refreshAll();
    await refreshPurchaseDebtInvoiceOptions(ui.purchaseDebtSupplier.value || "");
    await renderPurchaseSelectedDetail();
  });

  const buildPurchaseCartPdfPayload = (forcedDocType = "") => {
    if (!purchaseCart.length) return null;
    const draftDocType = String(forcedDocType || "BR").toUpperCase() === "FC" ? "FC" : "BR";
    const draftSupplierId = String(ui.purchaseSupplier?.value || "").trim();
    const roundCurrencyUnit = (cents) => Math.max(0, Math.round(Number(cents || 0) / 100) * 100);
    const calcLineTotal = (unitCostCents, qty) => Math.max(0, Math.round(Number(unitCostCents || 0) * qtyNum(qty)));
    const baseItems = purchaseCart.map((line) => ({
      product_id: String(line.product_id || ""),
      qty: qtyNum(line.qty),
      unit_cost_cents: Number(line.unit_cost_cents || 0),
      product_name: String(line.name || "")
    }));
    const sourceSubtotal = baseItems.reduce((sum, it) => sum + calcLineTotal(it.unit_cost_cents, it.qty), 0);

    let draftItems = baseItems.map((it) => ({ ...it }));
    if (draftDocType === "FC") {
      draftItems = baseItems.map((it) => ({
        ...it,
        unit_cost_cents: roundCurrencyUnit(Number(it.unit_cost_cents || 0) / 1.19)
      }));

      const bestIndex = draftItems.reduce((best, it, idx, arr) => {
        if (best < 0) return idx;
        const currentWeight = qtyNum(arr[best] && arr[best].qty);
        const nextWeight = qtyNum(it && it.qty);
        return nextWeight > currentWeight ? idx : best;
      }, -1);

      if (bestIndex >= 0) {
        let bestItems = draftItems.map((it) => ({ ...it }));
        let bestSubtotal = bestItems.reduce((sum, it) => sum + calcLineTotal(it.unit_cost_cents, it.qty), 0);
        let bestTax = Math.round((bestSubtotal * 19) / 100);
        let bestTotal = bestSubtotal + bestTax;
        let bestDiff = Math.abs(sourceSubtotal - bestTotal);

        for (let step = 0; step < 50; step += 1) {
          const direction = bestTotal < sourceSubtotal ? 100 : -100;
          const candidateItems = bestItems.map((it) => ({ ...it }));
          const nextUnit = Math.max(0, Number(candidateItems[bestIndex].unit_cost_cents || 0) + direction);
          if (nextUnit === Number(candidateItems[bestIndex].unit_cost_cents || 0)) break;
          candidateItems[bestIndex].unit_cost_cents = nextUnit;
          const candidateSubtotal = candidateItems.reduce((sum, it) => sum + calcLineTotal(it.unit_cost_cents, it.qty), 0);
          const candidateTax = Math.round((candidateSubtotal * 19) / 100);
          const candidateTotal = candidateSubtotal + candidateTax;
          const candidateDiff = Math.abs(sourceSubtotal - candidateTotal);
          if (candidateDiff <= bestDiff) {
            bestItems = candidateItems;
            bestSubtotal = candidateSubtotal;
            bestTax = candidateTax;
            bestTotal = candidateTotal;
            bestDiff = candidateDiff;
            if (candidateDiff === 0) break;
          } else {
            break;
          }
        }
        draftItems = bestItems;
      }
    }

    const draftSubtotal = draftItems.reduce((sum, it) => sum + calcLineTotal(it.unit_cost_cents, it.qty), 0);
    const draftTaxRate = draftDocType === "FC" ? 19 : 0;
    const draftTax = Math.round((draftSubtotal * draftTaxRate) / 100);
    const draftTotal = draftSubtotal + draftTax;
    const draftPaid = Math.max(0, toCents(ui.purchasePaid?.value || 0));
    return {
      invoice: {
        id: "",
        created_at: new Date().toISOString(),
        supplier_id: draftSupplierId,
        doc_type: draftDocType,
        subtotal_cents: draftSubtotal,
        discount_cents: 0,
        net_total_cents: draftSubtotal,
        tax_rate_percent: draftTaxRate,
        tax_cents: draftTax,
        total_cents: draftTotal,
        paid_cents: Math.min(draftTotal, draftPaid),
        due_cents: Math.max(0, draftTotal - draftPaid)
      },
      items: draftItems,
      paid_cents: Math.min(draftTotal, draftPaid),
      document_type: draftDocType,
      source_total_cents: sourceSubtotal
    };
  };

  const exportPurchaseCartPdf = async (forcedDocType = "BR", label = langText("Telecharger achat", "تحميل الشراء", "تحميل لشرا")) => {
    const payload = buildPurchaseCartPdfPayload(forcedDocType);
    if (!payload || !payload.invoice) {
      ui.purchaseDetailBox.insertAdjacentHTML(
        "afterbegin",
        row("Erreur", langText("Panier achat vide", "سلة الشراء فارغة", "سلة الشرا فارغة"))
      );
      return;
    }
    const out = await window.wiliDb.exportPurchasePdf(payload);
    handlePdfResult(out, label, ui.purchaseDetailBox);
  };

  ui.purchasePdfFcBtn?.addEventListener("click", async () => {
    await exportPurchaseCartPdf("FC", "Facture fournisseur (R)");
  });

  ui.financeForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const party = typeof selectedFinanceEntryPartyPayload === "function" ? selectedFinanceEntryPartyPayload() : { party_key: "", party_name: "" };
    if (ui.financeEntryParty && ui.financeEntryParty.required && !String(ui.financeEntryParty.value || "")) {
      appNotify(langText("Choisissez un client ou fournisseur", "???? ???? ?? ????", "????? ????? ??? ??????"), true, 2200);
      return;
    }
    const out = await window.wiliDb.addFinanceEntry({ entry_type: String(ui.financeType.value || "income"), amount_cents: toCents(ui.financeAmount.value), note: String(ui.financeNote.value || "").trim(), party_key: party.party_key || "", party_name: party.party_name || "" });
    if (!out || !out.ok) return;
    ui.financeAmount.value = "";
    ui.financeNote.value = "";
    if (ui.financeEntryPartyMode) ui.financeEntryPartyMode.value = "anonymous";
    if (typeof fillFinanceEntryPartyOptions === "function") fillFinanceEntryPartyOptions();
    await refreshFinance();
    await refreshDashboard();
  });
  ui.financeEntryPartyMode?.addEventListener("change", () => {
    if (typeof fillFinanceEntryPartyOptions === "function") fillFinanceEntryPartyOptions();
  });
  ui.financeAnalysisForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    if (typeof renderFinanceAnalysis === "function") renderFinanceAnalysis();
  });
  [ui.financeAnalysisFrom, ui.financeAnalysisTo].forEach((ctrl) => {
    ctrl?.addEventListener("change", () => {
      if (typeof renderFinanceAnalysis === "function") renderFinanceAnalysis();
    });
  });
  ui.financeFilterForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    applyFinanceFilters(true);
  });
  ui.financeApplyBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    applyFinanceFilters(true);
  });
  [ui.financeParty, ui.financeDay, ui.financeFrom, ui.financeTo, ui.financePageSize].forEach((ctrl) => {
    ctrl?.addEventListener("change", () => applyFinanceFilters(true));
  });
  ui.financeSearch?.addEventListener("input", () => applyFinanceFilters(true));
  ui.financeTable?.querySelectorAll("thead th[data-sort]").forEach((th) => {
    th.addEventListener("click", () => {
      const key = String(th.getAttribute("data-sort") || "").trim();
      if (!key) return;
      if (financeJournalState.sortKey === key) {
        financeJournalState.sortDir = financeJournalState.sortDir === "asc" ? "desc" : "asc";
      } else {
        financeJournalState.sortKey = key;
        financeJournalState.sortDir = ["created_at", "amount_cents", "qty_total"].includes(key) ? "desc" : "asc";
      }
      applyFinanceFilters(true);
    });
  });
  ui.financePrevBtn?.addEventListener("click", () => {
    financePage = Math.max(1, financePage - 1);
    renderFinanceList(false);
  });
  ui.financeNextBtn?.addEventListener("click", () => {
    const pageSize = Math.max(1, intQty(ui.financePageSize?.value || 20));
    const totalPages = Math.max(1, Math.ceil((financeRowsFiltered || []).length / pageSize));
    financePage = Math.min(totalPages, financePage + 1);
    renderFinanceList(false);
  });

  ui.reportForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const day = String(ui.reportDate.value || "").trim();
    if (!day) return;
    const out = await window.wiliDb.getDailyReport(`${day}T00:00:00.000Z`);
    if (!out || !out.ok) return;
    const r = out.data;
    ui.reportBox.innerHTML = [
      row("Date", r.day),
      `<div class="row"><div><strong>${esc(trText("Ventes"))}</strong></div><div>${amountChip(r.sales_total_cents || 0, "in")}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Achats"))}</strong></div><div>${amountChip(r.purchases_total_cents || 0, "out")}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Profit"))}</strong></div><div>${amountChip(r.estimated_profit_cents || 0, (Number(r.estimated_profit_cents || 0) >= 0 ? "in" : "out"))}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Entree caisse"))}</strong></div><div>${amountChip(r.cash_in_cents || 0, "in")}</div></div>`,
      `<div class="row"><div><strong>${esc(trText("Sortie caisse"))}</strong></div><div>${amountChip(r.cash_out_cents || 0, "out")}</div></div>`
    ].join("");
  });

  ui.reportPdfBtn?.addEventListener("click", async () => {
    const day = String(ui.reportDate.value || "").trim();
    if (!day) return;
    const out = await window.wiliDb.exportDailyReportPdf(`${day}T00:00:00.000Z`);
    handlePdfResult(out, langText("Telecharger rapport", "تحميل التقرير", "تحميل التقرير"), ui.reportExportBox);
  });
  ui.reportDebtCustomersPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportCustomersDebtPdf();
    handlePdfResult(out, langText("Telecharger credit clients", "تحميل كريدي الزبائن", "تحميل كريدي لكليان"), ui.reportExportBox);
  });
  ui.reportDebtSuppliersPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportSuppliersDebtPdf();
    handlePdfResult(out, langText("Telecharger credit fournisseurs", "تحميل كريدي الموردين", "تحميل كريدي لفرنسور"), ui.reportExportBox);
  });
  ui.auditForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    await refreshAudit();
  });
  ui.auditPrevBtn?.addEventListener("click", () => {
    auditPage = Math.max(1, auditPage - 1);
    renderAuditList(false);
  });
  ui.auditNextBtn?.addEventListener("click", () => {
    const totalPages = Math.max(1, Math.ceil((auditRows || []).length / AUDIT_PAGE_SIZE));
    auditPage = Math.min(totalPages, auditPage + 1);
    renderAuditList(false);
  });
  ui.auditPdfBtn?.addEventListener("click", async () => {
    const out = await window.wiliDb.exportAuditPdf({
      q: String(ui.auditSearch?.value || "").trim(),
      action: String(ui.auditAction?.value || "").trim(),
      from: String(ui.auditFrom?.value || "").trim(),
      to: String(ui.auditTo?.value || "").trim()
    });
    handlePdfResult(out, langText("Telecharger audit", "تحميل التدقيق", "تحميل التدقيق"), ui.auditStats);
  });

  ui.settingsForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const typographyPrefs = currentUiTypographyPrefs();
    const printProfiles = typeof readPrintProfileSettingsFromUi === "function" ? readPrintProfileSettingsFromUi() : null;
    const out = await window.wiliDb.updateSettings({
      allow_negative_stock: ui.setAllowNegativeStock.value === "true",
      auto_settle_documents: ui.setAutoSettleDocuments ? String(ui.setAutoSettleDocuments.value || "true") !== "false" : true,
      auto_print_sale_cart: printProfiles ? Boolean(printProfiles.ticket && printProfiles.ticket.enabled) : (ui.setAutoPrintSaleCart ? String(ui.setAutoPrintSaleCart.value || "true") === "true" : true),
      print_profiles: printProfiles,
      currency: String(ui.setCurrency.value || "DZD").trim() || "DZD",
      ui_font_size: typographyPrefs.ui_font_size,
      ui_font_bold: typographyPrefs.ui_font_bold,
      ui_display_profile: normalizeUiDisplayProfile(ui.setUiDisplayProfile?.value || "1080"),
      invoice_tax_rate_percent: Math.max(0, Math.min(100, Number(ui.setInvoiceTaxRate?.value || 19) || 0)),
      product_images_dir: String(ui.setProductImagesDir?.value || "").trim(),
      comptoir_layout: String(ui.setComptoirLayout?.value || "table").trim() || "table",
      barcode_label_show_name: ui.setBarcodeShowName ? Boolean(ui.setBarcodeShowName.checked) : true,
      barcode_label_show_ref: ui.setBarcodeShowRef ? Boolean(ui.setBarcodeShowRef.checked) : true,
      barcode_label_show_price: ui.setBarcodeShowPrice ? Boolean(ui.setBarcodeShowPrice.checked) : false,
      store_name: String(ui.setStoreName.value || "").trim(),
      store_activity: String(ui.setStoreActivity.value || "").trim(),
      store_address: String(ui.setStoreAddress.value || "").trim(),
      store_phone: String(ui.setStorePhone.value || "").trim(),
      store_email: String(ui.setStoreEmail.value || "").trim(),
      store_nif: String(ui.setStoreNif.value || "").trim(),
      store_nis: String(ui.setStoreNis.value || "").trim(),
      store_rc: String(ui.setStoreRc.value || "").trim(),
      store_ai: String(ui.setStoreAi.value || "").trim()
    });
    if (out && out.ok) {
      if (window.wiliApi && typeof window.wiliApi.applyDisplayProfile === "function") {
        await window.wiliApi.applyDisplayProfile();
      }
      uiTypographyState.lastSavedKey = uiTypographyKey(typographyPrefs);
      await refreshSettings();
      showSettingsSaveMsg(false);
      return;
    }
    showSettingsSaveMsg(true);
  });

  ui.setUiFontSize?.addEventListener("change", () => {
    applyUiTypography(currentUiTypographyPrefs());
    scheduleUiTypographyPersist();
  });
  ui.setUiFontBold?.addEventListener("change", () => {
    applyUiTypography(currentUiTypographyPrefs());
    scheduleUiTypographyPersist();
  });
  ui.setComptoirLayout?.addEventListener("change", () => {
    if (typeof renderComptoirLayoutPreview === "function") renderComptoirLayoutPreview(ui.setComptoirLayout.value);
    if (typeof renderPosSearch === "function") renderPosSearch(ui.posSearchInput?.value || "");
    if (typeof renderCart === "function") renderCart();
  });
  ui.productImagesDirPickBtn?.addEventListener("click", async () => {
    const out = await window.wiliFiles?.pickProductImagesDir?.();
    if (!out || !out.ok) {
      if (out && out.error && out.error !== "folder_pick_cancelled") appNotify(out.error, true, 3200);
      return;
    }
    if (ui.setProductImagesDir) ui.setProductImagesDir.value = String((out.data || {}).dir_path || "");
    if (ui.productImagesDirHint) ui.productImagesDirHint.textContent = langText("Dossier personnalise", "مجلد مخصص", "دوسي مخصص");
  });
  ui.productImagesDirDefaultBtn?.addEventListener("click", async () => {
    const out = await window.wiliFiles?.getProductImagesDefaultDir?.();
    if (!out || !out.ok) {
      appNotify(out && out.error ? out.error : "default_folder_unavailable", true, 3200);
      return;
    }
    const dirPath = String((out.data || {}).dir_path || "");
    if (ui.setProductImagesDir) ui.setProductImagesDir.value = dirPath;
    if (ui.productImagesDirHint) ui.productImagesDirHint.textContent = langText(`Dossier par defaut: ${dirPath}`, `المجلد الافتراضي: ${dirPath}`, `الدوسي ديفو: ${dirPath}`);
  });

  ui.langForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (uiTypographyState.pendingTimer) {
      clearTimeout(uiTypographyState.pendingTimer);
      uiTypographyState.pendingTimer = null;
    }
    const lang = String(ui.setLanguage.value || "fr").toLowerCase();
    const safeLang = (lang === "ar" || lang === "dz") ? lang : "fr";
    const typographyPrefs = currentUiTypographyPrefs();
    const out = await window.wiliDb.updateSettings({
      language: safeLang,
      ui_font_size: typographyPrefs.ui_font_size,
      ui_font_bold: typographyPrefs.ui_font_bold,
      ui_display_profile: normalizeUiDisplayProfile(ui.setUiDisplayProfile?.value || "1080")
    });
    if (!out || !out.ok) return;
    if (window.wiliApi && typeof window.wiliApi.applyDisplayProfile === "function") {
      await window.wiliApi.applyDisplayProfile();
    }
    uiTypographyState.lastSavedKey = uiTypographyKey(typographyPrefs);
    session.language = safeLang;
    applyUiTypography(typographyPrefs);
    applyLanguage();
    setHeader(activeView);
    await refreshAll();
  });

  ui.backupCreateBtn.addEventListener("click", async () => {
    ui.backupCreateBtn.disabled = true;
    try {
      const out = await window.wiliDb.createBackup({ scope: "all" });
      ui.backupBox.innerHTML = row("Backup", out && out.ok ? `${t("backup_type_full")} | ${out.data.file_path}` : (out.error || "backup_failed"));
    } finally {
      ui.backupCreateBtn.disabled = false;
    }
  });

  ui.backupImportBtn.addEventListener("click", async () => {
    const ok = await appConfirm(
      langText(
        "Restaurer une sauvegarde remplacera les donnees locales actuelles. Continuer ?",
        "استرجاع نسخة احتياطية سيستبدل البيانات المحلية الحالية. هل تريد المتابعة؟",
        "ترجيع سوفغارد يبدل الداتا الحالية. نكمل؟"
      ),
      {
        title: langText("Restaurer sauvegarde", "استرجاع نسخة احتياطية", "ترجيع سوفغارد"),
        okText: langText("Restaurer", "استرجاع", "رجّع"),
        cancelText: langText("Annuler", "إلغاء", "حبس")
      }
    );
    if (!ok) return;
    ui.backupImportBtn.disabled = true;
    try {
      const out = await window.wiliDb.pickAndRestoreBackup();
      ui.backupBox.innerHTML = row("Import", out && out.ok ? out.data.file_path : (out.error || "restore_failed"));
      if (out && out.ok) await resolveEntryFlow();
    } finally {
      ui.backupImportBtn.disabled = false;
    }
  });

  ui.userForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!hasPermission("users.manage")) {
      showUserSaveMsg(true, "forbidden");
      return;
    }
    if (uiTypographyState.pendingTimer) {
      clearTimeout(uiTypographyState.pendingTimer);
      uiTypographyState.pendingTimer = null;
      await persistUiTypographyPrefs();
    }
    const payload = {
      username: String(ui.userName.value || "").trim(),
      display_name: String(ui.userDisplayName.value || "").trim(),
      role: String(ui.userRole.value || "seller"),
      pin: String(ui.userPin.value || "").trim()
    };
    if (!payload.username) {
      showUserSaveMsg(true, "username_required");
      return;
    }
    if (!payload.pin || payload.pin.length < 4) {
      showUserSaveMsg(true, "pin_too_short");
      return;
    }
    const submitBtn = ui.userForm.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
    try {
      const out = await window.wiliApi.createUser(payload);
      if (!out || !out.ok) {
        showUserSaveMsg(true, out && out.error ? out.error : "");
        return;
      }
      ui.userName.value = "";
      ui.userDisplayName.value = "";
      populateUserRoleSelect(ui.userRole, "seller");
      ui.userPin.value = "";
      showUserSaveMsg(false);
      await refreshSettings();
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
}
