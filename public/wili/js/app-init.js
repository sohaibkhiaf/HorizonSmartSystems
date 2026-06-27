function toLocalDayIso(value = new Date()) {
  const d = value instanceof Date ? value : new Date(value);
  if (!(d instanceof Date) || Number.isNaN(d.getTime())) return "";
  const local = new Date(d.getTime() - (d.getTimezoneOffset() * 60000));
  return local.toISOString().slice(0, 10);
}

function applyDefaultDateRanges() {
  const now = new Date();
  const toIso = toLocalDayIso(now);
  const fromDate = new Date(now);
  fromDate.setDate(fromDate.getDate() - 365);
  const fromIso = toLocalDayIso(fromDate);
  const pairs = [
    [ui.historyFrom, ui.historyTo],
    [ui.historyStatsFrom, ui.historyStatsTo],
    [ui.lossFilterFrom, ui.lossFilterTo],
    [ui.customerTraceFrom, ui.customerTraceTo],
    [ui.supplierTraceFrom, ui.supplierTraceTo],
    [ui.salesHistFrom, ui.salesHistTo],
    [ui.purchasesHistFrom, ui.purchasesHistTo],
    [ui.financeAnalysisFrom, ui.financeAnalysisTo],
    [ui.financeFrom, ui.financeTo],
    [ui.auditFrom, ui.auditTo]
  ];
  for (const [fromEl, toEl] of pairs) {
    if (fromEl && !String(fromEl.value || "").trim()) fromEl.value = fromIso;
    if (toEl && !String(toEl.value || "").trim()) toEl.value = toIso;
  }
  if (ui.reportDate && !String(ui.reportDate.value || "").trim()) ui.reportDate.value = toIso;
}

function activateSettingsTab(tabKey = "general") {
  const root = byId("view-settings");
  if (!root) return;
  const wanted = String(tabKey || "general");
  root.querySelectorAll("[data-settings-tab]").forEach((btn) => {
    btn.classList.toggle("active", String(btn.getAttribute("data-settings-tab") || "") === wanted);
  });
  root.querySelectorAll("[data-settings-pane]").forEach((pane) => {
    pane.classList.toggle("active", String(pane.getAttribute("data-settings-pane") || "") === wanted);
  });
}

function initSettingsTabs() {
  const root = byId("view-settings");
  if (!root || root.dataset.tabsReady === "1") return;
  root.dataset.tabsReady = "1";
  root.querySelectorAll("[data-settings-tab]").forEach((btn) => {
    btn.addEventListener("click", () => activateSettingsTab(btn.getAttribute("data-settings-tab") || "general"));
  });
  activateSettingsTab(root.querySelector("[data-settings-tab].active")?.getAttribute("data-settings-tab") || "general");
}

function activateFinanceTab(tabKey = "treasury") {
  const root = byId("view-finance");
  if (!root) return;
  const wanted = String(tabKey || "treasury");
  root.querySelectorAll("[data-finance-tab]").forEach((btn) => {
    btn.classList.toggle("active", String(btn.getAttribute("data-finance-tab") || "") === wanted);
  });
  root.querySelectorAll("[data-finance-pane]").forEach((pane) => {
    pane.classList.toggle("active", String(pane.getAttribute("data-finance-pane") || "") === wanted);
  });
}

function initFinanceTabs() {
  const root = byId("view-finance");
  if (!root || root.dataset.financeTabsReady === "1") return;
  root.dataset.financeTabsReady = "1";
  root.querySelectorAll("[data-finance-tab]").forEach((btn) => {
    btn.addEventListener("click", () => activateFinanceTab(btn.getAttribute("data-finance-tab") || "treasury"));
  });
  activateFinanceTab(root.querySelector("[data-finance-tab].active")?.getAttribute("data-finance-tab") || "treasury");
}

function activateHistoryTab(tabKey = "stats") {
  const root = byId("view-history");
  if (!root) return;
  const wanted = String(tabKey || "stats");
  root.querySelectorAll("[data-history-tab]").forEach((btn) => {
    btn.classList.toggle("active", String(btn.getAttribute("data-history-tab") || "") === wanted);
  });
  root.querySelectorAll("[data-history-pane]").forEach((pane) => {
    pane.classList.toggle("active", String(pane.getAttribute("data-history-pane") || "") === wanted);
  });
}

function initHistoryTabs() {
  const root = byId("view-history");
  if (!root || root.dataset.historyTabsReady === "1") return;
  root.dataset.historyTabsReady = "1";
  root.querySelectorAll("[data-history-tab]").forEach((btn) => {
    btn.addEventListener("click", () => activateHistoryTab(btn.getAttribute("data-history-tab") || "stats"));
  });
  activateHistoryTab(root.querySelector("[data-history-tab].active")?.getAttribute("data-history-tab") || "stats");
}

function activateLossTab(tabKey = "entry") {
  const root = byId("view-losses");
  if (!root) return;
  const wanted = String(tabKey || "entry");
  root.querySelectorAll("[data-loss-tab]").forEach((btn) => {
    btn.classList.toggle("active", String(btn.getAttribute("data-loss-tab") || "") === wanted);
  });
  root.querySelectorAll("[data-loss-pane]").forEach((pane) => {
    pane.classList.toggle("active", String(pane.getAttribute("data-loss-pane") || "") === wanted);
  });
}

function initLossTabs() {
  const root = byId("view-losses");
  if (!root || root.dataset.lossTabsReady === "1") return;
  root.dataset.lossTabsReady = "1";
  root.querySelectorAll("[data-loss-tab]").forEach((btn) => {
    btn.addEventListener("click", () => activateLossTab(btn.getAttribute("data-loss-tab") || "entry"));
  });
  activateLossTab(root.querySelector("[data-loss-tab].active")?.getAttribute("data-loss-tab") || "entry");
}

async function resolveEntryFlow() {
  const appState = await window.wiliApi.getState();
  if (!appState || !appState.activated) {
    const reason = String(appState && appState.reason ? appState.reason : "");
    if (reason) setMsg(loginMsg, `Demo indisponible: ${reason}`, true);
    showGate("login");
    return;
  }

  let users = null;
  try {
    users = await window.wiliApi.hasUsers();
  } catch (err) {
    setMsg(loginMsg, String(err && err.message ? err.message : err || "Erreur base locale"), true);
    showGate("login");
    return;
  }
  if (users && !users.ok) {
    setMsg(loginMsg, users.error || "Erreur base locale", true);
    showGate("login");
    return;
  }
  if (!users || !users.ok || !users.data || !users.data.has_users) {
    showGate("bootstrap");
    return;
  }

  if (!session.user) {
    showGate("login");
    return;
  }

  showApp();
}

function prefillLoginFromUrl() {
  if (!window.WILI_MYSQL_DEMO || typeof window.WILI_MYSQL_DEMO.directEntryParams !== "function") {
    return false;
  }
  const params = window.WILI_MYSQL_DEMO.directEntryParams();
  if (!params) return false;
  const userInput = byId("loginUsername");
  const pinInput = byId("loginPin");
  if (userInput) userInput.value = params.user;
  if (pinInput) pinInput.value = params.pin || "test";
  setMsg(loginMsg, `Demo ${params.database}: identifiants prets, cliquez sur Entrer.`);
  setTimeout(() => byId("loginForm")?.querySelector('button[type="submit"]')?.focus?.(), 0);
  return true;
}

window.wiliApi.onNavigate((route) => {
  const r = String(route || "");
  if (r === "activate") return showGate("login");
  if (!session.user) return;
  if (views.includes(r) && canAccessView(r)) switchView(r);
});

let dbChangedRefreshTimer = null;
let dbChangedRefreshInFlight = false;
let dbChangedRefreshPending = false;

async function runDbChangedRefresh() {
  if (!session.user) return;
  if (dbChangedRefreshInFlight) {
    dbChangedRefreshPending = true;
    return;
  }
  dbChangedRefreshInFlight = true;
  try {
    await refreshAll();
    if (activeView === "sales") {
      await renderPosSearch(ui.posSearchInput?.value || "");
      renderCart();
      updatePosTotals();
    }
  } catch (err) {
    console.error("db_changed_refresh_failed", err);
  } finally {
    dbChangedRefreshInFlight = false;
    if (dbChangedRefreshPending) {
      dbChangedRefreshPending = false;
      scheduleDbChangedRefresh();
    }
  }
}

function scheduleDbChangedRefresh() {
  if (!session.user) return;
  if (dbChangedRefreshTimer) clearTimeout(dbChangedRefreshTimer);
  dbChangedRefreshTimer = setTimeout(() => {
    dbChangedRefreshTimer = null;
    void runDbChangedRefresh();
  }, 120);
}

function bindDbChangedRefresh() {
  if (!window.wiliApi || typeof window.wiliApi.onDbChanged !== "function") return;
  window.wiliApi.onDbChanged(() => scheduleDbChangedRefresh());
}

function withStartupTimeout(promise, ms = 1200) {
  return Promise.race([
    Promise.resolve(promise),
    new Promise((resolve) => setTimeout(() => resolve(null), ms))
  ]);
}

(async () => {
  bindNav();
  bindForms();
  bindDbChangedRefresh();
  if (typeof bindPrintProfileSettingsActions === "function") bindPrintProfileSettingsActions();
  if (typeof bindPriceDisplayControls === "function") bindPriceDisplayControls();
  bindDebugCollectors();
  initSettingsTabs();
  initFinanceTabs();
  initHistoryTabs();
  initLossTabs();
  if (typeof initShortcutSettings === "function") initShortcutSettings();
  applyDefaultDateRanges();
  showGate("login");
  prefillLoginFromUrl();

  let settings = null;
  try {
    settings = await withStartupTimeout(window.wiliDb.getSettings(), 1200);
  } catch (_err) {
    settings = null;
  }
  cache.settings = settings && settings.ok && settings.data ? { ...(settings.data || {}) } : {};
  if (settings && settings.ok && settings.data && (settings.data.language === "fr" || settings.data.language === "ar" || settings.data.language === "dz")) {
    session.language = settings.data.language;
  } else {
    session.language = "fr";
  }
  if (settings && settings.ok && settings.data) {
    applyUiTypography(settings.data);
    uiTypographyState.lastSavedKey = uiTypographyKey({
      ui_font_size: normalizeUiFontScale(settings.data.ui_font_size || "medium"),
      ui_font_bold: Boolean(settings.data.ui_font_bold)
    });
  } else {
    applyUiTypography({ ui_font_size: "medium", ui_font_bold: false });
    uiTypographyState.lastSavedKey = uiTypographyKey({ ui_font_size: "medium", ui_font_bold: false });
  }

  applyLanguage();
  renderDomainState();
  renderSubnav();
  await resolveEntryFlow();

  if (session.user) {
    await refreshAll();
    renderPosSearch("");
    renderCart();
    updatePosTotals();
  }
  setInterval(() => {
    const now = new Date();
    ui.sbClock.textContent = now.toLocaleTimeString();
  }, 1000);
  updateStatusBar(activeView);
})();

