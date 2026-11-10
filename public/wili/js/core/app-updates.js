(function () {
  const CURRENT_VERSION = "1.0.0";
  const SERVER_BASE = "https://icoderdz.cv/iCoderSuite/license-server";
  const byId = (id) => document.getElementById(id);
  const updateBtn = byId("updateBtn");
  const modal = byId("updateModal");
  const closeBtn = byId("updateCloseBtn");
  const checkBtn = byId("updateCheckBtn");
  const statusText = byId("updateStatusText");
  const resultBox = byId("updateResultBox");
  const downloadBtn = byId("updateDownloadBtn");
  document.querySelectorAll(".app-version-badge").forEach((el) => {
    el.textContent = `V${CURRENT_VERSION}`;
  });
  if (!updateBtn || !modal) return;

  const logoText = String(document.querySelector(".logo")?.textContent || "Wili M").toUpperCase();
  const edition = logoText.includes(" L") ? "l" : (logoText.includes(" M") ? "m" : "s");
  if (typeof iconSvg === "function" && !updateBtn.querySelector("svg")) {
    updateBtn.insertAdjacentHTML("afterbegin", iconSvg("refresh"));
    updateBtn.classList.add("with-icon");
  }
  const tr = (fr, ar, dz) => typeof langText === "function" ? langText(fr, ar, dz || ar) : fr;
  const updateLabel = () => tr("Mise a jour", "التحديثات", "التحديثات");
  const setOpen = (open) => modal.classList.toggle("hidden", !open);
  const setResult = (html) => { if (resultBox) resultBox.innerHTML = html; };
  updateBtn.title = updateLabel();

  async function resolveReleaseDistribution() {
    if (edition !== "l") {
      return { channel: edition, label: edition.toUpperCase() };
    }
    try {
      const out = await window.wiliSetup?.getDistribution?.();
      const mode = out && out.ok ? String(out.data?.mode || "server").toLowerCase() : "server";
      return mode === "client"
        ? { channel: "l_client", label: "L Client" }
        : { channel: "l_server", label: "L Server" };
    } catch (_err) {
      return { channel: "l_server", label: "L Server" };
    }
  }

  async function fetchLatest() {
    const distribution = await resolveReleaseDistribution();
    const url = `${SERVER_BASE}/api/v1/app-updates?app=wili&channel=${encodeURIComponent(distribution.channel)}&edition=${encodeURIComponent(edition)}&version=${encodeURIComponent(CURRENT_VERSION)}&ts=${Date.now()}`;
    const res = await fetch(url, { cache: "no-store", headers: { Accept: "application/json" } });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data.ok) {
      const err = new Error(data.error || `HTTP ${res.status}`);
      err.status = res.status;
      throw err;
    }
    return { data, distribution };
  }

  async function checkUpdates() {
    if (downloadBtn) downloadBtn.classList.add("hidden");
    if (statusText) statusText.textContent = tr("Recherche du serveur de mise a jour...", "جاري البحث عن خادم التحديث...", "نقلبو على سيرفر التحديث...");
    setResult(`<div class="server-status-line"><span>${updateLabel()}</span><strong>${tr("Verification", "جاري الفحص", "جاري الفحص")}</strong></div>`);
    try {
      const response = await fetchLatest();
      const data = response.data;
      const distribution = response.distribution;
      const latest = data.latest || {};
      const available = Boolean(data.update_available);
      setResult(`
        <div class="update-result-grid">
          <div class="server-status-line"><span>${tr("Edition", "النسخة", "النسخة")}</span><strong>${distribution.label}</strong></div>
          <div class="server-status-line"><span>${tr("Version actuelle", "الإصدار الحالي", "الفيرسيون الحالي")}</span><strong>V${CURRENT_VERSION}</strong></div>
          <div class="server-status-line"><span>${tr("Derniere version", "آخر إصدار", "آخر فيرسيون")}</span><strong>V${latest.version || CURRENT_VERSION}</strong></div>
          <div class="server-status-line"><span>${tr("Serveur", "الخادم", "السيرفر")}</span><strong>icoderdz.cv</strong></div>
          <div class="server-status-line ${available ? "ok" : ""}"><span>${tr("Etat", "الحالة", "الحالة")}</span><strong>${available ? tr("Mise a jour disponible", "يوجد تحديث", "كاين تحديث") : tr("A jour", "محدث", "راه محدث")}</strong></div>
        </div>
      `);
      if (statusText) statusText.textContent = available ? tr("Une nouvelle version est disponible.", "يوجد إصدار جديد.", "كاين فيرسيون جديد.") : tr("Votre application est deja a jour.", "التطبيق محدث حالياً.", "التطبيق راه محدث.");
      if (available && latest.download_url && downloadBtn) {
        downloadBtn.href = latest.download_url;
        downloadBtn.classList.remove("hidden");
      }
    } catch (err) {
      if (statusText) statusText.textContent = tr("Service de mise a jour indisponible.", "خدمة التحديث غير متاحة حالياً.", "سيرفيس التحديث ما راهوش متاح.");
      setResult(`<div class="server-status-line bad"><span>${updateLabel()}</span><strong>${String(err && err.message ? err.message : err || "update_service_unavailable")}</strong></div>`);
    }
  }

  updateBtn.addEventListener("click", () => { setOpen(true); checkUpdates(); });
  closeBtn?.addEventListener("click", () => setOpen(false));
  checkBtn?.addEventListener("click", checkUpdates);
  modal.addEventListener("click", (e) => { if (e.target === modal) setOpen(false); });
})();
