function bindDebugCollectors() {
  const onUiEvent = (kind, e) => {
    try {
      const target = e && e.target ? e.target : null;
      if (!target) return;
      const salesScope = target.closest ? target.closest("#view-sales") : null;
      const isSalesEvent = Boolean(salesScope);
      if (!isSalesEvent && kind !== "window.error" && kind !== "window.unhandledrejection") return;
      const meta = getDebugTargetMeta(target);
      const details = {
        ...meta,
        key: trimDebugText(e && e.key ? e.key : "", 40),
        value_preview: target && typeof target.value !== "undefined" ? trimDebugText(target.value, 80) : ""
      };
      pushUiDebugEvent(kind, details);
    } catch (_err) {}
  };

  document.addEventListener("click", (e) => onUiEvent("ui.click", e), true);
  document.addEventListener("input", (e) => onUiEvent("ui.input", e), true);
  document.addEventListener("focusin", (e) => onUiEvent("ui.focusin", e), true);
  document.addEventListener("keydown", (e) => onUiEvent("ui.keydown", e), true);
}

function bindNav() {
  const isEditableKeyTarget = (evt) => {
    const el = (evt && evt.target && evt.target.nodeType === 1 ? evt.target : document.activeElement);
    if (!el) return false;
    if (el.isContentEditable) return true;
    const tag = String(el.tagName || "").toLowerCase();
    if (tag === "textarea" || tag === "select") return true;
    if (tag !== "input") return false;
    const type = String(el.getAttribute("type") || "text").toLowerCase();
    const nonText = new Set(["button", "checkbox", "radio", "range", "color", "file", "submit", "reset", "image"]);
    return !nonText.has(type);
  };
  const shortcutMatches = (evt, actionId, fallback) => {
    if (window.wiliShortcuts && typeof window.wiliShortcuts.matches === "function") {
      return window.wiliShortcuts.matches(evt, window.wiliShortcuts.get(actionId));
    }
    return evt.key === fallback && !evt.altKey && !evt.ctrlKey && !evt.metaKey && !evt.shiftKey;
  };
  const openDomainFromShortcut = (evt, actionId, domain) => {
    const fallback = { domain_comptoir: "F1", domain_stock: "F2", domain_customers: "F3", domain_suppliers: "F4", domain_settings: "F5" }[actionId] || "";
    if (!shortcutMatches(evt, actionId, fallback)) return false;
    evt.preventDefault();
    if (!canAccessDomain(domain)) return true;
    activeDomain = domain;
    renderDomainState();
    renderSubnav();
    switchView(firstAccessibleViewForDomain(domain));
    return true;
  };

  document.querySelectorAll(".domain-btn").forEach((btn) => btn.addEventListener("click", () => {
    const domain = String(btn.getAttribute("data-domain") || "comptoir");
    if (!canAccessDomain(domain)) return;
    if (activeDomain === domain) return;
    activeDomain = domain;
    renderDomainState();
    renderSubnav();
    switchView(firstAccessibleViewForDomain(domain));
  }));
  document.querySelectorAll(".qa-btn").forEach((btn) => btn.addEventListener("click", () => {
    const view = String(btn.getAttribute("data-view") || "dashboard");
    if (views.includes(view) && canAccessView(view)) switchView(view);
  }));
  ui.pdfPreviewCloseBtn?.addEventListener("click", closePdfPreview);
  ui.pdfPreviewModal?.addEventListener("click", (e) => {
    if (e.target === ui.pdfPreviewModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.journalDetailCloseBtn?.addEventListener("click", closeJournalDetailModal);
  ui.journalDetailModal?.addEventListener("click", (e) => {
    if (e.target === ui.journalDetailModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.confirmOkBtn?.addEventListener("click", () => closeAppConfirm(true));
  ui.confirmCancelBtn?.addEventListener("click", () => closeAppConfirm(false));
  ui.returnOkBtn?.addEventListener("click", () => {
    const qty = Math.max(0.01, qtyNum(ui.returnQtyInput?.value || 1, 1));
    const note = String(ui.returnNoteInput?.value || "").trim();
    closeReturnModal({ qty, note });
  });
  ui.returnCancelBtn?.addEventListener("click", () => closeReturnModal(null));
  ui.userPinOkBtn?.addEventListener("click", () => {
    const pin = String(ui.userPinValue?.value || "").trim();
    const confirmPin = String(ui.userPinConfirm?.value || "").trim();
    if (!pin || pin.length < 4) {
      appNotify(langText("PIN minimum 4 chiffres", "\u064A\u062C\u0628 \u0623\u0646 \u064A\u0643\u0648\u0646 PIN \u0645\u0646 4 \u0623\u0631\u0642\u0627\u0645 \u0639\u0644\u0649 \u0627\u0644\u0623\u0642\u0644", "\u0644\u0627\u0632\u0645 PIN \u064A\u0643\u0648\u0646 \u0641\u064A\u0647 4 \u0623\u0631\u0642\u0627\u0645 \u0639\u0644\u0649 \u0627\u0644\u0623\u0642\u0644"), true, 3200);
      ui.userPinValue?.focus();
      return;
    }
    if (pin !== confirmPin) {
      appNotify(langText("PIN et confirmation differents", "PIN \u0648\u0627\u0644\u062A\u0623\u0643\u064A\u062F \u063A\u064A\u0631 \u0645\u062A\u0637\u0627\u0628\u0642\u064A\u0646", "PIN \u0648\u0627\u0644\u062A\u0623\u0643\u064A\u062F \u0645\u0627\u0634\u064A \u0643\u064A\u0641 \u0643\u064A\u0641"), true, 3200);
      ui.userPinConfirm?.focus();
      return;
    }
    closeUserPinModal(pin);
  });
  ui.userPinCancelBtn?.addEventListener("click", () => closeUserPinModal(null));
  ui.barcodeSizeSmallBtn?.addEventListener("click", () => closeBarcodeSizePicker("small"));
  ui.barcodeSizeMediumBtn?.addEventListener("click", () => closeBarcodeSizePicker("medium"));
  ui.barcodeSizeLargeBtn?.addEventListener("click", () => closeBarcodeSizePicker("large"));
  ui.barcodeSizeCancelBtn?.addEventListener("click", () => closeBarcodeSizePicker(""));
  ui.confirmModal?.addEventListener("click", (e) => {
    if (e.target === ui.confirmModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.returnModal?.addEventListener("click", (e) => {
    if (e.target === ui.returnModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.userPinModal?.addEventListener("click", (e) => {
    if (e.target === ui.userPinModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.barcodeSizeModal?.addEventListener("click", (e) => {
    if (e.target === ui.barcodeSizeModal) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
  ui.journalDetailPdfBtn?.addEventListener("click", async () => {
    const id = String(journalDetailContext.id || "");
    const type = String(journalDetailContext.type || "");
    if (!id || !type) return;
    if (type === "sale") {
      const out = await window.wiliDb.exportSaleInvoicePdf(id, { document_mode: "bon" });
      handlePdfResult(out, langText("Bon de Livraison", "\u0648\u0635\u0644 \u062a\u0633\u0644\u064a\u0645", "\u0628\u0648\u0646 \u0644\u064a\u0641\u0631\u064a\u0632\u0648\u0646"), ui.journalDetailBody);
      return;
    }
    const detailsOut = await window.wiliDb.getPurchaseDetails(id);
    if (!detailsOut || !detailsOut.ok) return;
    const d = detailsOut.data || {};
    const items = Array.isArray(d.items) ? d.items.map((it) => ({
      product_id: String(it.product_id || ""),
      qty: qtyNum(it.qty),
      unit_cost_cents: Number(it.unit_cost_cents || 0),
      product_name: String(it.product_name || it.product_id || "-")
    })) : [];
    const out = await window.wiliDb.exportPurchasePdf({
      invoice: d.invoice || {},
      items,
      paid_cents: Number((d.invoice || {}).paid_cents || 0),
      document_mode: "bon"
    });
    handlePdfResult(out, langText("Bon de Reception", "\u0648\u0635\u0644 \u0627\u0633\u062a\u0644\u0627\u0645", "\u0628\u0648\u0646 \u0631\u064a\u0633\u0628\u0633\u064a\u0648\u0646"), ui.journalDetailBody);
  });
  ui.pdfPrintBtn?.addEventListener("click", async () => {
    if (!lastPdfPath && !pdfEditMode) return;
    if (!pdfEditMode && lastPdfPath) {
      const out = typeof printPdfPathWithProfiles === "function"
        ? await printPdfPathWithProfiles(lastPdfPath, {
          title: ui.pdfPreviewTitle?.textContent || "PDF",
          suggestedName: lastPdfName || "document.pdf",
          manual: true
        })
        : await window.wiliFiles.printPdf(lastPdfPath, { silent: false });
      if (!out || !out.ok) {
        appNotify(out && out.error ? out.error : "print_failed", true);
      }
      return;
    }
    try {
      const win = ui.pdfPreviewFrame?.contentWindow;
      if (win && typeof win.print === "function") {
        win.focus();
        win.print();
        return;
      }
    } catch (_err) {
      // fallback below
    }
    if (!lastPdfPath) return;
    const out = await window.wiliFiles.printPdf(lastPdfPath, { silent: false });
    if (!out || !out.ok) {
      appNotify(out && out.error ? out.error : "print_failed", true);
    }
  });
  ui.pdfEditBtn?.addEventListener("click", () => {
    enterPdfEditMode();
  });
  ui.pdfResetBtn?.addEventListener("click", () => {
    resetPdfEditor();
  });
  ui.pdfSaveAsBtn?.addEventListener("click", async () => {
    if (pdfEditMode) {
      const html = getEditedPdfHtml();
      if (!html) {
        appNotify("pdf_edit_empty", true);
        return;
      }
      const editedOut = await window.wiliFiles.saveEditedPdf(html, lastPdfName || "document.pdf");
      if (!editedOut || !editedOut.ok) {
        appNotify(editedOut && editedOut.error ? editedOut.error : "save_failed", true);
        return;
      }
      appNotify(langText("PDF sauvegarde", "تم حفظ PDF", "تسجّل PDF"));
      return;
    }
    if (!lastPdfPath) return;
    const out = await window.wiliFiles.saveCopyAs(lastPdfPath, lastPdfName || "document.pdf");
    if (!out || !out.ok) {
      appNotify(out && out.error ? out.error : "save_failed", true);
      return;
    }
    appNotify(langText("PDF sauvegarde", "تم حفظ PDF", "تسجّل PDF"));
  });
  ui.notifBtn.addEventListener("click", () => switchView("alerts"));
  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && barcodeSizeResolver) {
      closeBarcodeSizePicker("");
      return;
    }
    if (e.key === "Escape" && confirmResolver) {
      closeAppConfirm(false);
      return;
    }
    if (e.key === "Escape" && returnResolver) {
      closeReturnModal(null);
      return;
    }
    if (e.key === "Escape" && activeView === "sales" && !saleSubmitInFlight) {
      // Emergency unlock path for any unexpected UI lock.
      recoverSalesInteractivity("escape");
    }
    if (isEditableKeyTarget(e)) return;
    if (e.shiftKey && !e.altKey && !e.ctrlKey && !e.metaKey) {
      for (const [view, spec] of Object.entries(VIEW_SHORTCUTS)) {
        if (!canAccessView(view)) continue;
        const isFn = String(spec.key || "").startsWith("F");
        const hit = isFn ? (e.key === spec.key) : (e.code === spec.key);
        if (!hit) continue;
        if (views.includes(view)) {
          e.preventDefault();
          switchView(view);
          return;
        }
      }
    }
    if (openDomainFromShortcut(e, "domain_comptoir", "comptoir")) return;
    if (openDomainFromShortcut(e, "domain_stock", "stock")) return;
    if (openDomainFromShortcut(e, "domain_customers", "customers")) return;
    if (openDomainFromShortcut(e, "domain_suppliers", "suppliers")) return;
    if (openDomainFromShortcut(e, "domain_settings", "settings")) return;
  });

  window.addEventListener("error", (evt) => {
    const err = evt && evt.error ? evt.error : new Error(String((evt && evt.message) || "window_error"));
    pushUiDebugEvent("window.error", {
      message: trimDebugText((evt && evt.message) || "", 240),
      filename: trimDebugText((evt && evt.filename) || "", 220),
      lineno: Number((evt && evt.lineno) || 0),
      colno: Number((evt && evt.colno) || 0)
    });
    sendDebugReport("window_error", err, {
      filename: trimDebugText((evt && evt.filename) || "", 220),
      lineno: Number((evt && evt.lineno) || 0),
      colno: Number((evt && evt.colno) || 0)
    });
    if (activeView === "sales") recoverSalesInteractivity("runtime_error");
  });
  window.addEventListener("unhandledrejection", (evt) => {
    const reason = evt && evt.reason ? evt.reason : "unhandledrejection";
    const err = reason instanceof Error ? reason : new Error(trimDebugText(String(reason || "unhandledrejection"), 600));
    pushUiDebugEvent("window.unhandledrejection", {
      reason: trimDebugText(String(reason && reason.message ? reason.message : reason), 260)
    });
    sendDebugReport("unhandled_rejection", err, {});
    if (activeView === "sales") recoverSalesInteractivity("promise_error");
  });

}
