"use strict";

function renderShortcutSettings() {
  const body = byId("shortcutTableBody");
  if (!body || !window.wiliShortcuts) return;
  const filter = String(byId("shortcutSearch")?.value || "").trim().toLowerCase();
  const map = window.wiliShortcuts.load();
  const defaults = window.wiliShortcuts.defaults();
  const conflicts = window.wiliShortcuts.conflicts(map);
  const rows = window.wiliShortcuts.actions.filter((item) => {
    if (!filter) return true;
    return `${t(item.groupKey)} ${t(item.labelKey)} ${map[item.id] || ""}`.toLowerCase().includes(filter);
  });
  const text = (value) => value ? String(value) : "-";
  body.innerHTML = rows.map((item) => {
    const current = map[item.id] || "";
    const conflict = conflicts.has(item.id);
    return `<tr class="${conflict ? "shortcut-conflict" : ""}" data-shortcut-row="${esc(item.id)}">
      <td><strong>${esc(t(item.groupKey))}</strong></td>
      <td>${esc(t(item.labelKey))}</td>
      <td><input class="shortcut-key-input" data-shortcut-input="${esc(item.id)}" value="${esc(text(current))}" readonly></td>
      <td><span class="hot">${esc(defaults[item.id] || "-")}</span></td>
      <td><span class="shortcut-status">${conflict ? esc(t("settings_shortcuts_conflict")) : esc(t("settings_shortcuts_ok"))}</span></td>
      <td><div class="qty-actions">
        <button type="button" class="btn-mini shortcut-action-btn" data-shortcut-capture="${esc(item.id)}">${esc(t("settings_shortcuts_change"))}</button>
        <button type="button" class="btn-mini shortcut-toggle-btn ${current ? "" : "is-off"}" data-shortcut-toggle="${esc(item.id)}">${current ? esc(t("settings_shortcuts_off")) : esc(t("settings_shortcuts_on"))}</button>
      </div></td>
    </tr>`;
  }).join("") || `<tr><td colspan="6" class="empty">${esc(t("settings_shortcuts_empty"))}</td></tr>`;
  bindShortcutRows();
}

function bindShortcutRows() {
  const body = byId("shortcutTableBody");
  if (!body || body.dataset.bound === "1") return;
  body.dataset.bound = "1";
  body.addEventListener("click", (event) => {
    const capture = event.target?.closest?.("[data-shortcut-capture]");
    const toggle = event.target?.closest?.("[data-shortcut-toggle]");
    if (capture) {
      const id = capture.getAttribute("data-shortcut-capture");
      const input = body.querySelector(`[data-shortcut-input="${CSS.escape(id)}"]`);
      if (input) {
        input.value = t("settings_shortcuts_press");
        input.focus();
        input.dataset.capturing = "1";
      }
      return;
    }
    if (toggle) {
      const id = toggle.getAttribute("data-shortcut-toggle");
      const current = window.wiliShortcuts.get(id);
      const defaults = window.wiliShortcuts.defaults();
      window.wiliShortcuts.set(id, current ? "" : defaults[id] || "");
      renderShortcutSettings();
      appNotify(current ? t("settings_shortcuts_disabled") : t("settings_shortcuts_enabled"), false, 1800);
    }
  });
  body.addEventListener("keydown", (event) => {
    const input = event.target?.closest?.("[data-shortcut-input]");
    if (!input || input.dataset.capturing !== "1") return;
    event.preventDefault();
    const shortcut = window.wiliShortcuts.fromEvent(event);
    if (!shortcut) return;
    input.dataset.capturing = "0";
    window.wiliShortcuts.set(input.getAttribute("data-shortcut-input"), shortcut);
    renderShortcutSettings();
    appNotify(t("settings_shortcuts_saved"), false, 1800);
  });
}

function refreshShortcutHotLabels() {
  if (!window.wiliShortcuts) return;
  const map = window.wiliShortcuts.load();
  const pairs = { comptoir: "domain_comptoir", stock: "domain_stock", customers: "domain_customers", suppliers: "domain_suppliers", settings: "domain_settings" };
  Object.entries(pairs).forEach(([domain, actionId]) => {
    const hot = document.querySelector(`.domain-btn[data-domain="${domain}"] .hot`);
    if (hot) hot.textContent = map[actionId] || "";
  });
  document.querySelectorAll("[data-shortcut-hot]").forEach((hot) => {
    const actionId = String(hot.getAttribute("data-shortcut-hot") || "");
    hot.textContent = map[actionId] || "";
  });
}

function initShortcutSettings() {
  if (!window.wiliShortcuts) return;
  byId("shortcutSearch")?.addEventListener("input", renderShortcutSettings);
  byId("shortcutResetBtn")?.addEventListener("click", async () => {
    const ok = typeof appConfirm === "function" ? await appConfirm(t("settings_shortcuts_reset_confirm")) : true;
    if (!ok) return;
    window.wiliShortcuts.reset();
    renderShortcutSettings();
    appNotify(t("settings_shortcuts_reset_done"), false, 2200);
  });
  window.addEventListener("wili:shortcuts-changed", () => {
    renderShortcutSettings();
    refreshShortcutHotLabels();
  });
  refreshShortcutHotLabels();
  renderShortcutSettings();
}
