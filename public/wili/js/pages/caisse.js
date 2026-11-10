"use strict";

(function initCaissePage() {
  const $ = (id) => document.getElementById(id);
  let lastCaisseState = null;

  function L(fr, ar, dz) {
    if (typeof langText === "function") return langText(fr, ar, dz);
    return fr;
  }

  function moneyText(centsValue) {
    if (typeof money === "function") return money(centsValue || 0);
    return (Number(centsValue || 0) / 100).toFixed(2);
  }

  function html(text) {
    if (typeof esc === "function") return esc(text);
    return String(text == null ? "" : text).replace(/[&<>"']/g, (ch) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;"
    }[ch]));
  }

  function dateText(value) {
    if (typeof fmtDateTime === "function") return fmtDateTime(value || "");
    return String(value || "-").replace("T", " ").slice(0, 19) || "-";
  }

  function parseMoneyInput(input) {
    const raw = String(input && input.value != null ? input.value : "").trim().replace(",", ".");
    const n = Number(raw);
    return Number.isFinite(n) ? Math.max(0, Math.round(n * 100)) : 0;
  }

  function notify(message, isError = false) {
    const text = String(message || "");
    if (typeof appNotify === "function") appNotify(text, isError, isError ? 5200 : 2600);
    else if (isError) console.error(text);
  }

  function metric(label, value, extraClass = "") {
    return `<div class="caisse-metric ${html(extraClass)}"><span>${html(label)}</span><strong>${html(value)}</strong></div>`;
  }

  function statusPill(session) {
    const open = session && String(session.status || "") === "open";
    return `<span class="caisse-pill ${open ? "open" : "closed"}">${open ? L("Ouverte", "مفتوح", "محلولة") : L("Fermee", "مغلق", "مسدودة")}</span>`;
  }

  function renderDashboardCaisse(state) {
    const box = $("dashCaisseStatus");
    if (!box) return;
    const current = state && state.current ? state.current : null;
    if (current) {
      box.innerHTML = `
        <div class="row">
          <div><strong>${html(L("Caisse ouverte", "الصندوق مفتوح", "لاكاص محلولة"))}</strong><div class="small">${html(L("Depuis", "منذ", "من"))} ${html(dateText(current.opened_at))}</div></div>
          <div>${moneyText(current.expected_cash_cents)}</div>
        </div>
      `;
    } else {
      const last = state && Array.isArray(state.sessions) ? state.sessions[0] : null;
      box.innerHTML = `
        <div class="row">
          <div><strong>${html(L("Caisse fermee", "الصندوق مغلق", "لاكاص مسدودة"))}</strong><div class="small">${last ? `${html(L("Derniere", "آخر جلسة", "آخر مرة"))}: ${html(dateText(last.closed_at || last.opened_at))}` : html(L("Aucune session", "لا توجد جلسة", "ماكان حتى سيونس"))}</div></div>
          <div>-</div>
        </div>
      `;
    }
  }

  function renderCaisseState(state) {
    const statusBox = $("caisseStatusBox");
    const metricsBox = $("caisseMetrics");
    const historyBody = $("caisseHistoryBody");
    const openForm = $("caisseOpenForm");
    const closeForm = $("caisseCloseForm");
    const current = state && state.current ? state.current : null;
    const sessions = state && Array.isArray(state.sessions) ? state.sessions : [];

    if (statusBox) {
      statusBox.innerHTML = current
        ? `
          <div class="caisse-status-row">
            <div>
              <strong>Journee en cours ${statusPill(current)}</strong>
              <div class="small">${html(L("Ouverte le", "فُتح في", "تحلت في"))} ${html(dateText(current.opened_at))}${current.opened_by ? ` | ${html(current.opened_by)}` : ""}</div>
            </div>
            <div class="caisse-expected">${moneyText(current.expected_cash_cents)}</div>
          </div>
        `
        : `
          <div class="caisse-status-row">
            <div>
              <strong>${html(L("Aucune session ouverte", "لا توجد جلسة مفتوحة", "ماكان حتى سيونس محلولة"))} ${statusPill({ status: "closed" })}</strong>
              <div class="small">${html(L("Ouvrez la caisse avant le debut de journee.", "افتح الصندوق قبل بداية اليوم.", "حل لاكاص قبل ما تبدا النهار."))}</div>
            </div>
          </div>
        `;
    }

    if (metricsBox) {
      const source = current || sessions[0] || {};
      metricsBox.innerHTML = [
        metric(L("Fond initial", "الرصيد الافتتاحي", "الفون الاولاني"), moneyText(source.opening_cash_cents || 0)),
        metric(L("Entrees", "المداخيل", "الدخول"), moneyText(source.income_cents || 0), "in"),
        metric(L("Sorties", "المصاريف", "الخروج"), moneyText(source.expense_cents || 0), "out"),
        metric(L("Cash attendu", "المبلغ المتوقع", "الكاش المتوقع"), moneyText(source.expected_cash_cents || 0), "net")
      ].join("");
    }

    if (openForm) openForm.classList.toggle("hidden", Boolean(current));
    if (closeForm) closeForm.classList.toggle("hidden", !current);
    const counted = $("caisseCountedAmount");
    if (counted && current && !counted.value) counted.value = moneyText(current.expected_cash_cents);

    if (historyBody) {
      historyBody.innerHTML = sessions.length
        ? sessions.map((s) => {
          const diff = Number(s.difference_cents || 0);
          const diffClass = diff === 0 ? "net" : (diff > 0 ? "in" : "out");
          return `
            <tr>
              <td>${html(dateText(s.opened_at))}</td>
              <td>${html(s.closed_at ? dateText(s.closed_at) : "-")}</td>
              <td>${statusPill(s)}</td>
              <td>${moneyText(s.opening_cash_cents)}</td>
              <td>${moneyText(s.income_cents)}</td>
              <td>${moneyText(s.expense_cents)}</td>
              <td>${moneyText(s.expected_cash_cents)}</td>
              <td>${moneyText(s.counted_cash_cents)}</td>
              <td><span class="amount-chip ${diffClass}">${moneyText(diff)}</span></td>
            </tr>
          `;
        }).join("")
        : `<tr><td colspan="9" class="empty">${html(L("Aucune session caisse", "لا توجد جلسات صندوق", "ماكان حتى سيونس لاكاص"))}</td></tr>`;
    }
  }

  async function refreshCaisseState() {
    if (!window.wiliDb || typeof window.wiliDb.getCaisseState !== "function") return null;
    const out = await window.wiliDb.getCaisseState("main");
    if (!out || !out.ok) {
      renderDashboardCaisse({ current: null, sessions: [] });
      const statusBox = $("caisseStatusBox");
      if (statusBox) statusBox.innerHTML = `<div class="small">${html(L("Caisse indisponible", "الصندوق غير متاح", "لاكاص ما راهيش واجدة"))}: ${html(out && out.error ? out.error : "erreur")}</div>`;
      return null;
    }
    const state = out.data || { current: null, sessions: [] };
    lastCaisseState = state;
    renderDashboardCaisse(state);
    renderCaisseState(state);
    return state;
  }

  async function submitOpen(event) {
    event.preventDefault();
    const btn = $("caisseOpenBtn");
    if (btn) btn.disabled = true;
    try {
      const out = await window.wiliDb.openCaisse({
        box_id: "main",
        opening_cash_cents: parseMoneyInput($("caisseOpeningAmount")),
        note: String($("caisseOpenNote")?.value || "")
      });
      if (!out || !out.ok) throw new Error(out && out.error ? out.error : "caisse_open_failed");
      if ($("caisseOpeningAmount")) $("caisseOpeningAmount").value = "";
      if ($("caisseOpenNote")) $("caisseOpenNote").value = "";
      notify(L("Caisse ouverte", "تم فتح الصندوق", "لاكاص تحلت"));
      await refreshCaisseState();
    } catch (err) {
      notify(String(err && err.message ? err.message : err), true);
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  async function submitClose(event) {
    event.preventDefault();
    const btn = $("caisseCloseBtn");
    if (btn) btn.disabled = true;
    try {
      const out = await window.wiliDb.closeCaisse({
        box_id: "main",
        counted_cash_cents: parseMoneyInput($("caisseCountedAmount")),
        note: String($("caisseCloseNote")?.value || "")
      });
      if (!out || !out.ok) throw new Error(out && out.error ? out.error : "caisse_close_failed");
      if ($("caisseCountedAmount")) $("caisseCountedAmount").value = "";
      if ($("caisseCloseNote")) $("caisseCloseNote").value = "";
      const row = out.data || {};
      notify(`${L("Fin de jour enregistree. Difference", "تم تسجيل نهاية اليوم. الفرق", "تسجلت فين دو جور. الفرق")}: ${moneyText(row.difference_cents || 0)}`, Number(row.difference_cents || 0) !== 0);
      await refreshCaisseState();
    } catch (err) {
      notify(String(err && err.message ? err.message : err), true);
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  function bindCaisseUi() {
    $("caisseOpenForm")?.addEventListener("submit", submitOpen);
    $("caisseCloseForm")?.addEventListener("submit", submitClose);
    $("caisseRefreshBtn")?.addEventListener("click", () => refreshCaisseState());
    $("dashCaisseOpenBtn")?.addEventListener("click", () => {
      if (typeof switchView === "function") switchView("caisse");
      setTimeout(() => $("caisseOpeningAmount")?.focus(), 0);
    });
    $("dashCaisseCloseBtn")?.addEventListener("click", () => {
      if (typeof switchView === "function") switchView("caisse");
      setTimeout(() => $("caisseCountedAmount")?.focus(), 0);
    });
  }

  window.refreshCaisseState = refreshCaisseState;
  window.rerenderCaisseTranslations = () => {
    if (!lastCaisseState) return;
    renderDashboardCaisse(lastCaisseState);
    renderCaisseState(lastCaisseState);
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bindCaisseUi, { once: true });
  } else {
    bindCaisseUi();
  }
})();
