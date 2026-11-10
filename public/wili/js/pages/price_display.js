let priceDisplayMonitors = [];
let priceDisplayWindowsRows = [];
let priceDisplayScanners = [];

function scannerPublicLabel(scanner, index = 0) {
  const clean = (v) => String(v || "")
    .replace(/\(COM\d+\)/ig, "")
    .replace(/\bCOM\d+\b/ig, "")
    .replace(/\s{2,}/g, " ")
    .trim();
  const name = clean(scanner && (scanner.friendly_name || scanner.manufacturer || ""));
  if (name) return name;
  return langText(`Scanner ${index + 1}`, `السكانر ${index + 1}`, `سكانر ${index + 1}`);
}

function scannerPublicId(scanner, index = 0) {
  const raw = String(
    (scanner && (scanner.serial_number || scanner.path || scanner.friendly_name)) || `scanner-${index + 1}`
  );
  const compact = raw.replace(/[^a-zA-Z0-9]/g, "").toUpperCase();
  const suffix = compact.slice(-6) || String(index + 1).padStart(2, "0");
  return `SCN-${suffix}`;
}

function priceDisplayModeText(mode) {
  const key = String(mode || "detail");
  if (key === "gros") return t("price_mode_gros");
  if (key === "remise") return t("price_mode_remise");
  return t("price_mode_detail");
}

function showPriceDisplayMsg(text, isError = false) {
  if (!ui.priceDisplayMsg) return;
  ui.priceDisplayMsg.textContent = trText(String(text || ""));
  ui.priceDisplayMsg.classList.toggle("error", Boolean(isError));
  ui.priceDisplayMsg.classList.remove("hidden");
  setTimeout(() => {
    if (!ui.priceDisplayMsg) return;
    ui.priceDisplayMsg.classList.add("hidden");
    ui.priceDisplayMsg.classList.remove("error");
    ui.priceDisplayMsg.textContent = "";
  }, 3200);
}

function renderPriceDisplayWindowsTable() {
  if (!ui.priceDisplayWindows) return;
  if (!priceDisplayWindowsRows.length) {
    ui.priceDisplayWindows.innerHTML = `<tr><td colspan="5" class="empty">${t("no_data")}</td></tr>`;
    return;
  }
  ui.priceDisplayWindows.innerHTML = priceDisplayWindowsRows.map((w) => `
    <tr>
      <td><strong>${Number(w.window_id || 0)}</strong></td>
      <td>${esc(w.display_label || "-")}</td>
      <td>${esc(priceDisplayModeText(w.mode))}</td>
      <td>${esc(fmtDateTime(w.created_at || ""))}</td>
      <td><button class="btn-mini" data-price-window-close="${Number(w.window_id || 0)}">${langText("Fermer", "اغلاق", "فرمي")}</button></td>
    </tr>
  `).join("");
  ui.priceDisplayWindows.querySelectorAll("[data-price-window-close]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const wid = Number(btn.getAttribute("data-price-window-close") || 0);
      if (!wid) return;
      const out = await window.wiliDisplay.closeWindow(wid);
      if (!out || !out.ok) {
        showPriceDisplayMsg(out && out.error ? out.error : "close_failed", true);
        return;
      }
      await refreshPriceDisplayPanel();
    });
  });
}

function renderPriceDisplayMonitors() {
  if (!ui.priceDisplayMonitor) return;
  const wanted = String(ui.priceDisplayMonitor.value || "");
  ui.priceDisplayMonitor.innerHTML = (priceDisplayMonitors || []).map((m) => `<option value="${esc(m.id)}">${esc(m.label || m.id)}</option>`).join("");
  if (wanted && priceDisplayMonitors.some((m) => String(m.id || "") === wanted)) {
    ui.priceDisplayMonitor.value = wanted;
    return;
  }
  const preferred = priceDisplayMonitors.find((m) => !m.is_primary) || priceDisplayMonitors[0];
  if (preferred) ui.priceDisplayMonitor.value = String(preferred.id || "");
}

function renderPriceDisplayScanners() {
  if (!ui.priceDisplayScanners) return;
  const windowsOpts = [`<option value="">${esc(langText("Aucune", "لا شيء", "والو"))}</option>`]
    .concat(priceDisplayWindowsRows.map((w) => `<option value="${Number(w.window_id || 0)}">#${Number(w.window_id || 0)} - ${esc(w.display_label || "-")}</option>`))
    .join("");
  if (!priceDisplayScanners.length) {
    ui.priceDisplayScanners.innerHTML = `<tr><td colspan="4" class="empty">${langText("Aucun scanner detecte", "لا يوجد سكانر مكتشف", "ماكان حتى سكانر")}</td></tr>`;
  } else {
    ui.priceDisplayScanners.innerHTML = priceDisplayScanners.map((s, idx) => {
      const path = String(s.path || "");
      const isConnected = Boolean(s.connected);
      const stateTxt = isConnected
        ? langText("Connecte", "متصل", "كونكتي")
        : langText("Deconnecte", "غير متصل", "ماشي كونكتي");
      const stateClass = isConnected ? "ok" : "out";
      const deviceLabel = scannerPublicLabel(s, idx);
      const deviceId = scannerPublicId(s, idx);
      const selectedWid = Number(s.assigned_window_id || 0);
      return `
      <tr data-scanner-path="${esc(path)}">
        <td>
          <strong>${esc(deviceLabel)}</strong>
          <div class="small">${esc(deviceId)}</div>
        </td>
        <td><span class="chip chip-${stateClass}">${stateTxt}</span></td>
        <td>
          <select data-scan-window="${esc(path)}">
            ${windowsOpts}
          </select>
        </td>
        <td class="scanner-actions">
          <button type="button" class="btn-mini" data-scan-bind="${esc(path)}">${langText("Lier", "ربط", "ربط")}</button>
          <button type="button" class="btn-mini" data-scan-unbind="${esc(path)}">${langText("Delier", "فصل الربط", "نحي الربط")}</button>
          <button type="button" class="btn-mini danger" data-scan-disconnect="${esc(path)}">${langText("Deconnecter", "قطع الاتصال", "ديسكونيكتي")}</button>
        </td>
      </tr>`;
    }).join("");
    ui.priceDisplayScanners.querySelectorAll("select[data-scan-window]").forEach((sel) => {
      const path = String(sel.getAttribute("data-scan-window") || "");
      const s = priceDisplayScanners.find((x) => String(x.path || "") === path);
      const val = Number(s && s.assigned_window_id ? s.assigned_window_id : 0);
      sel.value = val ? String(val) : "";
    });
  }
  ui.priceDisplayScanners.querySelectorAll("[data-scan-bind]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!isAdminUser()) return;
      const path = String(btn.getAttribute("data-scan-bind") || "").trim();
      if (!path) return;
      const sel = Array.from(ui.priceDisplayScanners.querySelectorAll("select[data-scan-window]"))
        .find((x) => String(x.getAttribute("data-scan-window") || "") === path);
      const windowId = Number(sel && sel.value ? sel.value : 0);
      if (!windowId) {
        showPriceDisplayMsg(langText("Choisir une fenetre avant liaison", "اختر نافذة قبل الربط", "اختار نافذة قبل الربط"), true);
        return;
      }
      const out = await window.wiliDisplay.bindScanner({
        path,
        window_id: windowId,
        baud_rate: Number(ui.priceDisplayScannerBaud?.value || 115200)
      });
      if (!out || !out.ok) {
        showPriceDisplayMsg(out && out.error ? out.error : "scanner_bind_failed", true);
        await refreshPriceDisplayPanel();
        return;
      }
      showPriceDisplayMsg(langText("Scanner lie a la fenetre", "تم ربط السكانر بالنافذة", "السكانر تربط مع النافذة"), false);
      await refreshPriceDisplayPanel();
    });
  });
  ui.priceDisplayScanners.querySelectorAll("[data-scan-unbind]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!isAdminUser()) return;
      const path = String(btn.getAttribute("data-scan-unbind") || "").trim();
      if (!path) return;
      const out = await window.wiliDisplay.unbindScanner({ path });
      if (!out || !out.ok) {
        showPriceDisplayMsg(out && out.error ? out.error : "scanner_unbind_failed", true);
        await refreshPriceDisplayPanel();
        return;
      }
      showPriceDisplayMsg(langText("Liaison supprimee", "تم حذف الربط", "الربط تنحى"), false);
      await refreshPriceDisplayPanel();
    });
  });
  ui.priceDisplayScanners.querySelectorAll("[data-scan-disconnect]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!isAdminUser()) return;
      const path = String(btn.getAttribute("data-scan-disconnect") || "").trim();
      if (!path) return;
      const out = await window.wiliDisplay.disconnectScanner({ path });
      if (!out || !out.ok) {
        showPriceDisplayMsg(out && out.error ? out.error : "scanner_disconnect_failed", true);
        await refreshPriceDisplayPanel();
        return;
      }
      showPriceDisplayMsg(langText("Scanner deconnecte", "تم فصل السكانر", "السكانر تفصل"), false);
      await refreshPriceDisplayPanel();
    });
  });

  if (!ui.priceDisplaySerialStatus) return;
  const connected = priceDisplayScanners.filter((s) => s && s.connected).length;
  const bound = priceDisplayScanners.filter((s) => Number(s && s.assigned_window_id ? s.assigned_window_id : 0) > 0).length;
  ui.priceDisplaySerialStatus.textContent = langText(
    `Scanners detectes: ${priceDisplayScanners.length} | Connectes: ${connected} | Lies: ${bound}`,
    `السكانرات المكتشفة: ${priceDisplayScanners.length} | المتصلة: ${connected} | المربوطة: ${bound}`,
    `سكانرات متكشفين: ${priceDisplayScanners.length} | كونكتي: ${connected} | مربوطين: ${bound}`
  );
}

async function refreshPriceDisplayPanel() {
  if (!window.wiliDisplay || !ui.priceDisplayMonitor || !ui.priceDisplayWindows) return;
  if (!isAdminUser()) {
    priceDisplayMonitors = [];
    priceDisplayWindowsRows = [];
    priceDisplayScanners = [];
    renderPriceDisplayMonitors();
    renderPriceDisplayWindowsTable();
    renderPriceDisplayScanners();
    return;
  }
  const [targetsOut, windowsOut] = await Promise.all([
    window.wiliDisplay.listTargets(),
    window.wiliDisplay.listWindows()
  ]);
  if (!targetsOut || !targetsOut.ok) {
    showPriceDisplayMsg(targetsOut && targetsOut.error ? targetsOut.error : "display_targets_failed", true);
    return;
  }
  if (!windowsOut || !windowsOut.ok) {
    showPriceDisplayMsg(windowsOut && windowsOut.error ? windowsOut.error : "display_windows_failed", true);
    return;
  }
  priceDisplayMonitors = Array.isArray(targetsOut.data) ? targetsOut.data : [];
  priceDisplayWindowsRows = Array.isArray(windowsOut.data) ? windowsOut.data : [];
  try {
    const scannersOut = await window.wiliDisplay.listScanners();
    if (scannersOut && scannersOut.ok) {
      priceDisplayScanners = Array.isArray(scannersOut.data) ? scannersOut.data : [];
    } else {
      priceDisplayScanners = [];
    }
  } catch (_e) {
    priceDisplayScanners = [];
    if (ui.priceDisplaySerialStatus) {
      ui.priceDisplaySerialStatus.textContent = langText("Driver serial indisponible", "تعريف serial غير متوفر", "درايفر serial ماكانش");
    }
  }
  renderPriceDisplayMonitors();
  renderPriceDisplayWindowsTable();
  renderPriceDisplayScanners();
}

function bindPriceDisplayControls() {
  if (!window.wiliDisplay || !ui.priceDisplayForm || ui.priceDisplayForm.dataset.bound === "1") return;
  ui.priceDisplayForm.dataset.bound = "1";
  ui.priceDisplayForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!isAdminUser()) return;
    const payload = {
      display_id: String(ui.priceDisplayMonitor?.value || "").trim(),
      mode: String(ui.priceDisplayMode?.value || "detail").trim(),
      count: Math.max(1, Math.min(4, intQty(ui.priceDisplayCount?.value || 1)))
    };
    if (ui.priceDisplayCount) ui.priceDisplayCount.value = String(payload.count);
    const out = await window.wiliDisplay.openWindows(payload);
    if (!out || !out.ok) {
      showPriceDisplayMsg(out && out.error ? out.error : "display_open_failed", true);
      return;
    }
    await refreshPriceDisplayPanel();
    showPriceDisplayMsg(langText("Fenetre(s) ouverte(s)", "تم فتح النوافذ", "لفناتر تحلو"), false);
  });
  ui.priceDisplayRefreshBtn?.addEventListener("click", async () => {
    await refreshPriceDisplayPanel();
  });
  ui.priceDisplayCloseAllBtn?.addEventListener("click", async () => {
    if (!isAdminUser()) return;
    const out = await window.wiliDisplay.closeAllWindows();
    if (!out || !out.ok) {
      showPriceDisplayMsg(out && out.error ? out.error : "display_close_all_failed", true);
      return;
    }
    await refreshPriceDisplayPanel();
    showPriceDisplayMsg(langText("Toutes les fenetres sont fermees", "تم اغلاق كل النوافذ", "تسدو كامل لفناتر"), false);
  });
  ui.priceDisplayScannerDetectBtn?.addEventListener("click", async () => {
    await refreshPriceDisplayPanel();
    showPriceDisplayMsg(langText("Detection terminee", "اكتمل الكشف", "الكشف سالا"), false);
  });
}
