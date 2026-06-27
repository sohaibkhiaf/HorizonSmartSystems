(function initUiPatterns() {
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const add = (nodes, cls) => nodes.forEach((n) => n && n.classList && n.classList.add(cls));

  const toolbars = qsa(
    ".products-toolbar, .entity-toolbar, .sales-history-toolbar, .sales-history-pager, .pager-row, .purchase-checkout-bar"
  );
  add(toolbars, "ui-toolbar");

  add(qsa(".products-toolbar-search, .entity-toolbar > input"), "ui-toolbar-fill");
  add(qsa(".products-toolbar-right, .entity-toolbar-right, .purchase-right-actions"), "ui-toolbar-right");
  add(qsa(".sales-history-pager, .pager-row"), "ui-pager-actions");

  add(qsa("button.btn, button.btn-mini, button.btn-square, button.btn-compact"), "ui-btn");

  const modals = qsa(".pdf-modal");
  add(modals, "ui-modal");
  add(qsa(".pdf-modal-card"), "ui-modal-card");
  add(qsa(".pdf-modal-head"), "ui-modal-head");
  add(qsa(".pdf-modal-body"), "ui-modal-body");
  add(qsa(".pdf-modal-foot"), "ui-modal-foot");

  const confirmModal = document.getElementById("confirmModal");
  const barcodeSizeModal = document.getElementById("barcodeSizeModal");
  if (confirmModal) confirmModal.classList.add("ui-modal-confirm");
  if (barcodeSizeModal) barcodeSizeModal.classList.add("ui-modal-confirm");
})();
