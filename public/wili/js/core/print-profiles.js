"use strict";

const WILI_PRINT_PROFILE_DEFINITIONS = [
  {
    id: "ticket",
    title: "Ticket caisse",
    titleAr: "تذكرة الصندوق",
    titleDz: "تيكي كيس",
    description: "Ticket court apres validation Comptoir.",
    descriptionAr: "تذكرة قصيرة بعد تأكيد البيع.",
    descriptionDz: "تيكي قصير بعد البيع."
  },
  {
    id: "delivery",
    title: "Bon livraison",
    titleAr: "وصل التسليم",
    titleDz: "بون ليفريزون",
    description: "BL / Bon VCC et documents de sortie client.",
    descriptionAr: "وصل التسليم ووثائق خروج السلع للزبون.",
    descriptionDz: "بون ليفريزون ووثائق خروج السلعة."
  },
  {
    id: "invoice",
    title: "Facture",
    titleAr: "فاتورة",
    titleDz: "فاكتورة",
    description: "Factures vente/achat avec TVA et details.",
    descriptionAr: "فواتير البيع والشراء مع TVA والتفاصيل.",
    descriptionDz: "فاكتورات البيع والشرا بالتفاصيل."
  },
  {
    id: "barcode",
    title: "Barcode",
    titleAr: "باركود",
    titleDz: "باركود",
    description: "Etiquettes produits et codes-barres.",
    descriptionAr: "ملصقات المنتجات والباركود.",
    descriptionDz: "ليتيكات تع المنتجات والباركود."
  }
];

function printProfileText(def, key) {
  if (typeof langText === "function") {
    if (key === "description") return langText(def.description, def.descriptionAr, def.descriptionDz);
    return langText(def.title, def.titleAr, def.titleDz);
  }
  return key === "description" ? def.description : def.title;
}

function defaultPrintProfiles(legacyAutoPrint = true) {
  return {
    ticket: { enabled: legacyAutoPrint !== false, mode: "preview", copies: 1, paper: "ticket_58", printers: [] },
    delivery: { enabled: false, mode: "preview", copies: 1, paper: "a4", printers: [] },
    invoice: { enabled: false, mode: "preview", copies: 1, paper: "a4", printers: [] },
    barcode: { enabled: false, mode: "preview", copies: 1, paper: "barcode_50x30", printers: [] }
  };
}

function normalizePrintProfileValue(value, fallback) {
  const src = value && typeof value === "object" ? value : {};
  const mode = String(src.mode || fallback.mode || "preview").trim().toLowerCase();
  const copiesRaw = Number(src.copies ?? fallback.copies ?? 1);
  const paperRaw = String(src.paper || fallback.paper || "a4").trim().toLowerCase();
  const allowedPapers = new Set(["ticket_58", "ticket_80", "a4", "a5", "barcode_40x20", "barcode_50x30", "barcode_60x40"]);
  const printers = Array.isArray(src.printers)
    ? src.printers.map((x) => String(x || "").trim()).filter(Boolean)
    : [];
  return {
    enabled: src.enabled == null ? Boolean(fallback.enabled) : Boolean(src.enabled),
    mode: mode === "silent" ? "silent" : "preview",
    copies: Number.isFinite(copiesRaw) ? Math.max(1, Math.min(10, Math.trunc(copiesRaw))) : 1,
    paper: allowedPapers.has(paperRaw) ? paperRaw : (fallback.paper || "a4"),
    printers: [...new Set(printers)]
  };
}

function normalizePrintProfiles(raw, legacyAutoPrint = true) {
  const defaults = defaultPrintProfiles(legacyAutoPrint);
  const src = raw && typeof raw === "object" ? raw : {};
  const out = {};
  for (const def of WILI_PRINT_PROFILE_DEFINITIONS) {
    out[def.id] = normalizePrintProfileValue(src[def.id], defaults[def.id]);
  }
  return out;
}

function printProfilesFromSettings(settings = {}) {
  return normalizePrintProfiles(settings.print_profiles, settings.auto_print_sale_cart !== false);
}

function currentPrintProfilesFromUi() {
  const cachedSettings = (typeof cache !== "undefined" && cache && cache.settings) ? cache.settings : {};
  if (!ui || !ui.printProfilesGrid || !ui.printProfilesGrid.querySelector("[data-print-profile-card]")) {
    return printProfilesFromSettings(cachedSettings);
  }
  const legacyAuto = ui.setAutoPrintSaleCart ? String(ui.setAutoPrintSaleCart.value || "true") === "true" : true;
  const profiles = defaultPrintProfiles(legacyAuto);
  for (const def of WILI_PRINT_PROFILE_DEFINITIONS) {
    const card = ui.printProfilesGrid.querySelector(`[data-print-profile-card="${def.id}"]`);
    if (!card) continue;
    const enabled = card.querySelector("[data-print-profile-enabled]")?.checked ?? profiles[def.id].enabled;
    const mode = String(card.querySelector("[data-print-profile-mode]")?.value || profiles[def.id].mode);
    const copies = Number(card.querySelector("[data-print-profile-copies]")?.value || profiles[def.id].copies || 1);
    const paper = String(card.querySelector("[data-print-profile-paper]")?.value || profiles[def.id].paper || "a4");
    const printers = Array.from(card.querySelectorAll("[data-print-profile-printer]:checked"))
      .map((x) => String(x.value || "").trim())
      .filter(Boolean);
    profiles[def.id] = normalizePrintProfileValue({ enabled, mode, copies, paper, printers }, profiles[def.id]);
  }
  return normalizePrintProfiles(profiles, legacyAuto);
}

function readPrintProfileSettingsFromUi() {
  const profiles = currentPrintProfilesFromUi();
  if (ui?.setAutoPrintSaleCart) ui.setAutoPrintSaleCart.value = String(profiles.ticket.enabled);
  return profiles;
}

function escapePrintProfileValue(value) {
  if (typeof esc === "function") return esc(value);
  return String(value == null ? "" : value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function renderPrinterChoices(profileId, selectedPrinters, printerList) {
  const selected = new Set((selectedPrinters || []).map((x) => String(x || "").trim()).filter(Boolean));
  if (!printerList.length) {
    return `<div class="print-empty">${escapePrintProfileValue(langText("Aucune imprimante detectee", "لم يتم العثور على طابعة", "ماكانش طابعة"))}</div>`;
  }
  return printerList.map((printer) => {
    const name = String(printer.name || printer.displayName || "").trim();
    if (!name) return "";
    const isDefault = Boolean(printer.isDefault || printer.default);
    return `
      <label class="print-printer-choice">
        <input type="checkbox" data-print-profile-printer="${escapePrintProfileValue(profileId)}" value="${escapePrintProfileValue(name)}" ${selected.has(name) ? "checked" : ""}>
        <span>${escapePrintProfileValue(name)}</span>
        ${isDefault ? `<em>${escapePrintProfileValue(langText("Defaut", "افتراضية", "ديفو"))}</em>` : ""}
      </label>
    `;
  }).join("");
}

async function listPrintProfilePrinters() {
  if (!window.wiliFiles || typeof window.wiliFiles.listPrinters !== "function") return [];
  const out = await window.wiliFiles.listPrinters();
  if (!out || !out.ok) throw new Error(out && out.error ? out.error : "printers_list_failed");
  const data = out.data || {};
  const printers = Array.isArray(data) ? data : (Array.isArray(data.printers) ? data.printers : []);
  return printers.map((printer) => ({
    name: String((printer && (printer.name || printer.displayName || printer.display_name)) || "").trim(),
    displayName: String((printer && (printer.displayName || printer.display_name || printer.name)) || "").trim(),
    description: String((printer && printer.description) || "").trim(),
    status: Number((printer && printer.status) || 0),
    isDefault: Boolean(printer && (printer.isDefault || printer.is_default || printer.default))
  })).filter((printer) => printer.name);
}

async function refreshPrintProfilesSettingsUi(settings = {}) {
  if (!ui || !ui.printProfilesGrid) return;
  const profiles = printProfilesFromSettings(settings);
  if (ui.setAutoPrintSaleCart) ui.setAutoPrintSaleCart.value = String(profiles.ticket.enabled);
  let printers = [];
  try {
    printers = await listPrintProfilePrinters();
    if (ui.printPrinterStatus) {
      ui.printPrinterStatus.textContent = printers.length
        ? langText(`${printers.length} imprimante(s) detectee(s)`, `${printers.length} طابعة`, `${printers.length} طابعة`)
        : langText("Aucune imprimante detectee", "لم يتم العثور على طابعة", "ماكانش طابعة");
    }
  } catch (err) {
    if (ui.printPrinterStatus) ui.printPrinterStatus.textContent = err && err.message ? err.message : "printers_list_failed";
  }
  ui.printProfilesGrid.innerHTML = WILI_PRINT_PROFILE_DEFINITIONS.map((def) => {
    const profile = profiles[def.id] || defaultPrintProfiles()[def.id];
    return `
      <article class="print-profile-card" data-print-profile-card="${escapePrintProfileValue(def.id)}">
        <div class="print-profile-head">
          <div>
            <strong>${escapePrintProfileValue(printProfileText(def, "title"))}</strong>
            <span>${escapePrintProfileValue(printProfileText(def, "description"))}</span>
          </div>
          <label class="print-toggle">
            <input type="checkbox" data-print-profile-enabled ${profile.enabled ? "checked" : ""}>
            <span>${escapePrintProfileValue(profile.enabled ? langText("ON", "تشغيل", "ON") : langText("OFF", "إيقاف", "OFF"))}</span>
          </label>
        </div>
        <div class="print-profile-controls">
          <label>
            <span>${escapePrintProfileValue(langText("Mode", "الوضع", "المود"))}</span>
            <select data-print-profile-mode>
              <option value="preview" ${profile.mode !== "silent" ? "selected" : ""}>${escapePrintProfileValue(langText("Apercu / dialogue", "معاينة / نافذة", "معاينة"))}</option>
              <option value="silent" ${profile.mode === "silent" ? "selected" : ""}>${escapePrintProfileValue(langText("Direct silencieux", "طباعة مباشرة", "طبع مباشر"))}</option>
            </select>
          </label>
          <label>
            <span>${escapePrintProfileValue(langText("Copies", "عدد النسخ", "كوبيات"))}</span>
            <input type="number" min="1" max="10" step="1" data-print-profile-copies value="${escapePrintProfileValue(profile.copies || 1)}">
          </label>
          <label>
            <span>${escapePrintProfileValue(langText("Taille", "الحجم", "السومة"))}</span>
            <select data-print-profile-paper>
              <option value="ticket_58" ${profile.paper === "ticket_58" ? "selected" : ""}>Ticket 58mm</option>
              <option value="ticket_80" ${profile.paper === "ticket_80" ? "selected" : ""}>Ticket 80mm</option>
              <option value="a4" ${profile.paper === "a4" ? "selected" : ""}>A4</option>
              <option value="a5" ${profile.paper === "a5" ? "selected" : ""}>A5</option>
              <option value="barcode_40x20" ${profile.paper === "barcode_40x20" ? "selected" : ""}>Barcode 40x20</option>
              <option value="barcode_50x30" ${profile.paper === "barcode_50x30" ? "selected" : ""}>Barcode 50x30</option>
              <option value="barcode_60x40" ${profile.paper === "barcode_60x40" ? "selected" : ""}>Barcode 60x40</option>
            </select>
          </label>
        </div>
        <div class="print-printers-list">
          ${renderPrinterChoices(def.id, profile.printers, printers)}
        </div>
      </article>
    `;
  }).join("");
  ui.printProfilesGrid.querySelectorAll("[data-print-profile-enabled]").forEach((box) => {
    box.addEventListener("change", () => {
      const label = box.closest(".print-toggle")?.querySelector("span");
      if (label) label.textContent = box.checked ? langText("ON", "تشغيل", "ON") : langText("OFF", "إيقاف", "OFF");
      const profilesNext = currentPrintProfilesFromUi();
      if (ui.setAutoPrintSaleCart) ui.setAutoPrintSaleCart.value = String(profilesNext.ticket.enabled);
    });
  });
}

function inferPrintProfileIdFromPdf(input = {}) {
  const haystack = [
    input.title,
    input.filePath,
    input.suggestedName,
    input.name
  ].map((x) => String(x || "").toLowerCase()).join(" ");
  if (/barcode|code[-_\s]*barre|code barre|etiquette|label/.test(haystack)) return "barcode";
  if (/ticket|panier|caisse|cart-/.test(haystack)) return "ticket";
  if (/facture|invoice|proforma|recu|reçu|paiement|payment/.test(haystack)) return "invoice";
  if (/\b(bl|br|rc|rf|vcc)\b|bon|livraison|reception|réception|retour|delivery/.test(haystack)) return "delivery";
  return "invoice";
}

function printProfileOptions(profileKey, profile, overrides = {}) {
  return {
    silent: Boolean(overrides.silent),
    deviceName: String(overrides.deviceName || ""),
    printBackground: overrides.printBackground != null ? Boolean(overrides.printBackground) : false,
    paper: String((profile && profile.paper) || overrides.paper || ""),
    documentKind: String(profileKey || "invoice")
  };
}

function currentPrintProfilePaper(profileId) {
  const profileKey = String(profileId || "").trim();
  if (!profileKey) return "";
  const profiles = currentPrintProfilesFromUi();
  return String((profiles[profileKey] && profiles[profileKey].paper) || "");
}

async function printPdfPathWithProfiles(filePath, options = {}) {
  const src = String(filePath || "").trim();
  if (!src) return { ok: false, error: "pdf_file_not_found" };
  const profileId = options.profileId || inferPrintProfileIdFromPdf({
    title: options.title,
    filePath: src,
    suggestedName: options.suggestedName
  });
  return runPrintProfileForPdf(profileId, { ok: true, data: { file_path: src } }, {
    ...options,
    manual: options.manual !== false,
    fallbackPreview: options.fallbackPreview === true
  });
}

async function runPrintProfileForPdf(profileId, pdfOut, options = {}) {
  const profileKey = String(profileId || "ticket").trim() || "ticket";
  const profiles = currentPrintProfilesFromUi();
  const profile = profiles[profileKey] || defaultPrintProfiles()[profileKey] || defaultPrintProfiles().ticket;
  const manual = Boolean(options.manual);
  if (!profile.enabled && !manual) return { ok: true, skipped: true };
  const filePath = String(pdfOut && pdfOut.data && pdfOut.data.file_path ? pdfOut.data.file_path : "");
  if (!filePath) return { ok: false, error: "pdf_file_not_found" };
  if (!window.wiliFiles || typeof window.wiliFiles.printPdf !== "function") return { ok: false, error: "print_api_unavailable" };
  const printers = profile.enabled && Array.isArray(profile.printers) ? profile.printers.filter(Boolean) : [];
  if (!printers.length) {
    const dialogOut = await window.wiliFiles.printPdf(filePath, printProfileOptions(profileKey, profile, { silent: false }));
    if (dialogOut && dialogOut.ok) return { ok: true, dialog: true, reason: "printer_not_configured" };
    const error = dialogOut && dialogOut.error ? dialogOut.error : "print_failed";
    if (options.fallbackPreview !== false && typeof handlePdfResult === "function") {
      handlePdfResult(pdfOut, options.title || "PDF", options.targetEl || null);
      return { ok: true, preview: true, reason: "printer_not_configured" };
    }
    return { ok: false, error };
  }
  const copies = Math.max(1, Math.min(10, Number(profile.copies || 1) || 1));
  const silent = true;
  const jobs = printers;
  for (let copy = 0; copy < copies; copy += 1) {
    for (const deviceName of jobs) {
      const printOut = await window.wiliFiles.printPdf(filePath, printProfileOptions(profileKey, profile, {
        silent,
        deviceName: String(deviceName || ""),
        printBackground: false
      }));
      if (!printOut || !printOut.ok) {
        const error = printOut && printOut.error ? printOut.error : "print_failed";
        if (typeof sendDebugReport === "function") {
          sendDebugReport("print_profile_failed", { message: error }, {
            profile_id: profileKey,
            device_name: String(deviceName || ""),
            silent
          });
        }
        if (typeof appNotify === "function") appNotify(error, true, 5000);
        if (options.fallbackPreview !== false && typeof handlePdfResult === "function") {
          handlePdfResult(pdfOut, options.title || "PDF", options.targetEl || null);
        }
        return { ok: false, error };
      }
    }
  }
  return { ok: true, data: { printed_jobs: jobs.length * copies } };
}

function bindPrintProfileSettingsActions() {
  if (ui?.printPrintersRefreshBtn) {
    ui.printPrintersRefreshBtn.addEventListener("click", async () => {
      const stRes = await window.wiliDb.getSettings();
      await refreshPrintProfilesSettingsUi(stRes && stRes.ok ? (stRes.data || {}) : {});
    });
  }
  if (ui?.printProfilesGrid && !ui.printProfilesGrid.querySelector("[data-print-profile-card]")) {
    setTimeout(async () => {
      try {
        const stRes = await window.wiliDb.getSettings();
        await refreshPrintProfilesSettingsUi(stRes && stRes.ok ? (stRes.data || {}) : {});
      } catch (_err) {
        await refreshPrintProfilesSettingsUi((typeof cache !== "undefined" && cache && cache.settings) ? cache.settings : {});
      }
    }, 0);
  }
}
