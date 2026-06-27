"use strict";

const WILI_SHORTCUT_STORAGE_KEY = "wili.shortcuts.v1";

const WILI_SHORTCUT_ACTIONS = [
  { id: "domain_comptoir", groupKey: "shortcut_group_navigation", labelKey: "shortcut_domain_comptoir", defaultKey: "F1", scope: "global" },
  { id: "domain_stock", groupKey: "shortcut_group_navigation", labelKey: "shortcut_domain_stock", defaultKey: "F2", scope: "global" },
  { id: "domain_customers", groupKey: "shortcut_group_navigation", labelKey: "shortcut_domain_customers", defaultKey: "F3", scope: "global" },
  { id: "domain_suppliers", groupKey: "shortcut_group_navigation", labelKey: "shortcut_domain_suppliers", defaultKey: "F4", scope: "global" },
  { id: "domain_settings", groupKey: "shortcut_group_navigation", labelKey: "shortcut_domain_settings", defaultKey: "F5", scope: "global" },
  { id: "sales_focus_search", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_focus_search", defaultKey: "F6", scope: "sales" },
  { id: "sales_add_selected", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_add_selected", defaultKey: "F7", scope: "sales" },
  { id: "sales_print_cart", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_print_cart", defaultKey: "F8", scope: "sales" },
  { id: "sales_checkout", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_checkout", defaultKey: "F9", scope: "sales" },
  { id: "sales_clear_cart", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_clear_cart", defaultKey: "F10", scope: "sales" },
  { id: "sales_focus_paid", groupKey: "shortcut_group_comptoir", labelKey: "shortcut_sales_focus_paid", defaultKey: "F12", scope: "sales" }
];

const WILI_REQUIRED_SHORTCUT_DEFAULTS = {
  sales_checkout: "F9"
};

function shortcutDefaults() {
  return Object.fromEntries(WILI_SHORTCUT_ACTIONS.map((item) => [item.id, item.defaultKey]));
}

function normalizeShortcutValue(value) {
  const raw = String(value || "").trim();
  if (!raw) return "";
  const parts = raw.split("+").map((x) => x.trim()).filter(Boolean);
  const mods = [];
  let key = "";
  parts.forEach((part) => {
    const upper = part.toUpperCase();
    if (upper === "CTRL" || upper === "CONTROL") mods.push("Ctrl");
    else if (upper === "ALT") mods.push("Alt");
    else if (upper === "SHIFT") mods.push("Shift");
    else if (upper === "META" || upper === "CMD" || upper === "WIN") mods.push("Meta");
    else key = upper.length === 1 ? upper : part;
  });
  const ordered = ["Ctrl", "Alt", "Shift", "Meta"].filter((mod) => mods.includes(mod));
  return key ? [...ordered, key].join("+") : "";
}

function shortcutFromEvent(event) {
  const key = String(event.key || "");
  if (!key || ["Control", "Shift", "Alt", "Meta"].includes(key)) return "";
  const base = key === " " ? "Space" : (key.length === 1 ? key.toUpperCase() : key);
  return normalizeShortcutValue(`${event.ctrlKey ? "Ctrl+" : ""}${event.altKey ? "Alt+" : ""}${event.shiftKey ? "Shift+" : ""}${event.metaKey ? "Meta+" : ""}${base}`);
}

function shortcutMatches(event, shortcut) {
  const normalized = normalizeShortcutValue(shortcut);
  if (!normalized) return false;
  const parts = normalized.split("+");
  const key = parts[parts.length - 1];
  const has = (mod) => parts.includes(mod);
  if (Boolean(event.ctrlKey) !== has("Ctrl")) return false;
  if (Boolean(event.altKey) !== has("Alt")) return false;
  if (Boolean(event.shiftKey) !== has("Shift")) return false;
  if (Boolean(event.metaKey) !== has("Meta")) return false;
  if (/^F\d{1,2}$/i.test(key)) return String(event.key || "").toUpperCase() === key.toUpperCase();
  if (key === "Space") return event.key === " ";
  return String(event.key || "").toUpperCase() === key.toUpperCase() || String(event.code || "").toUpperCase() === key.toUpperCase();
}

function loadShortcutMap() {
  const defaults = shortcutDefaults();
  try {
    const parsed = JSON.parse(window.localStorage.getItem(WILI_SHORTCUT_STORAGE_KEY) || "{}");
    const out = { ...defaults };
    Object.keys(out).forEach((id) => {
      if (Object.prototype.hasOwnProperty.call(parsed, id)) out[id] = normalizeShortcutValue(parsed[id]);
    });
    Object.entries(WILI_REQUIRED_SHORTCUT_DEFAULTS).forEach(([id, fallback]) => {
      if (!out[id]) out[id] = fallback;
    });
    return out;
  } catch (_err) {
    return defaults;
  }
}

function saveShortcutMap(map) {
  const defaults = shortcutDefaults();
  const clean = {};
  Object.keys(defaults).forEach((id) => {
    clean[id] = normalizeShortcutValue(map && Object.prototype.hasOwnProperty.call(map, id) ? map[id] : defaults[id]);
  });
  window.localStorage.setItem(WILI_SHORTCUT_STORAGE_KEY, JSON.stringify(clean));
  window.dispatchEvent(new CustomEvent("wili:shortcuts-changed", { detail: clean }));
  return clean;
}

function resetShortcutMap() {
  window.localStorage.removeItem(WILI_SHORTCUT_STORAGE_KEY);
  const defaults = shortcutDefaults();
  window.dispatchEvent(new CustomEvent("wili:shortcuts-changed", { detail: defaults }));
  return defaults;
}

function getShortcut(actionId) {
  return loadShortcutMap()[actionId] || "";
}

function setShortcut(actionId, shortcut) {
  const map = loadShortcutMap();
  if (!Object.prototype.hasOwnProperty.call(map, actionId)) return map;
  map[actionId] = normalizeShortcutValue(shortcut);
  return saveShortcutMap(map);
}

function shortcutConflicts(map = loadShortcutMap()) {
  const seen = new Map();
  const conflicts = new Set();
  Object.entries(map).forEach(([id, value]) => {
    const key = normalizeShortcutValue(value);
    if (!key) return;
    if (seen.has(key)) {
      conflicts.add(id);
      conflicts.add(seen.get(key));
      return;
    }
    seen.set(key, id);
  });
  return conflicts;
}

window.wiliShortcuts = {
  actions: WILI_SHORTCUT_ACTIONS,
  defaults: shortcutDefaults,
  load: loadShortcutMap,
  save: saveShortcutMap,
  reset: resetShortcutMap,
  get: getShortcut,
  set: setShortcut,
  conflicts: shortcutConflicts,
  normalize: normalizeShortcutValue,
  fromEvent: shortcutFromEvent,
  matches: shortcutMatches
};
