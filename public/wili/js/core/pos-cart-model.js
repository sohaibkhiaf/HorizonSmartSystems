(function () {
  "use strict";

  function qtyNum(value, fallback = 0) {
    const n = Math.round((Number(value) || Number(fallback) || 0) * 100) / 100;
    return Number.isFinite(n) ? n : Number(fallback) || 0;
  }

  function findLine(cart, productId) {
    return (Array.isArray(cart) ? cart : []).find((line) => String(line.product_id) === String(productId || ""));
  }

  function removeLine(cart, productId) {
    const idx = (Array.isArray(cart) ? cart : []).findIndex((line) => String(line.product_id) === String(productId || ""));
    if (idx >= 0) cart.splice(idx, 1);
    return idx >= 0;
  }

  function isBlockedByStock(nextQty, stockQty, blockNegativeStock) {
    return Boolean(blockNegativeStock) && qtyNum(nextQty) > Number(stockQty || 0);
  }

  function isServiceProduct(product) {
    return String(product && product.product_type || "").toLowerCase() === "service";
  }

  function addProduct(cart, product, options = {}) {
    if (!Array.isArray(cart)) throw new Error("cart_required");
    const p = product && typeof product === "object" ? product : null;
    if (!p || !p.id) return { ok: false, error: "product_required" };
    const qtyToAdd = Math.max(0.01, qtyNum(options.qtyToAdd, 1));
    const unitPriceCents = Math.max(0, Number(options.unitPriceCents || 0));
    const mode = String(options.mode || "retail");
    const existing = findLine(cart, p.id);
    const nextQty = qtyNum((existing ? qtyNum(existing.qty) : 0) + qtyToAdd);
    if (!isServiceProduct(p) && isBlockedByStock(nextQty, options.stockQty, options.blockNegativeStock)) {
      return { ok: false, error: "insufficient_stock", product: p, requestedQty: nextQty, stockQty: Number(options.stockQty || 0) };
    }
    if (existing) {
      existing.qty = nextQty;
      existing.price_mode = mode;
      existing.unit_price_cents = unitPriceCents;
      existing.line_total_cents = Math.round(existing.qty * existing.unit_price_cents);
      return { ok: true, line: existing, product: p, action: "updated" };
    }
    const line = {
      product_id: p.id,
      name: p.name,
      barcode: p.barcode,
      reference: p.reference,
      product_type: String(p.product_type || "product"),
      image_path: String(p.image_path || ""),
      price_mode: mode,
      qty: qtyToAdd,
      unit_price_cents: unitPriceCents,
      line_total_cents: Math.round(qtyToAdd * unitPriceCents)
    };
    cart.push(line);
    return { ok: true, line, product: p, action: "added" };
  }

  function changeQty(cart, productId, options = {}) {
    if (!Array.isArray(cart)) throw new Error("cart_required");
    const line = findLine(cart, productId);
    if (!line) return { ok: false, error: "line_not_found" };
    const delta = Number(options.delta || 0);
    const nextQty = qtyNum(qtyNum(line.qty) + delta);
    if (delta > 0 && !isServiceProduct(line) && isBlockedByStock(nextQty, options.stockQty, options.blockNegativeStock)) {
      return { ok: false, error: "insufficient_stock", line, requestedQty: nextQty, stockQty: Number(options.stockQty || 0) };
    }
    if (nextQty <= 0) {
      removeLine(cart, productId);
      return { ok: true, removed: true, line };
    }
    line.qty = nextQty;
    line.line_total_cents = Math.round(line.qty * line.unit_price_cents);
    return { ok: true, line };
  }

  function setQty(cart, productId, nextQty, options = {}) {
    if (!Array.isArray(cart)) throw new Error("cart_required");
    const line = findLine(cart, productId);
    if (!line) return { ok: false, error: "line_not_found" };
    const q = Math.max(0, qtyNum(nextQty));
    if (q > 0 && !isServiceProduct(line) && isBlockedByStock(q, options.stockQty, options.blockNegativeStock)) {
      return { ok: false, error: "insufficient_stock", line, requestedQty: q, stockQty: Number(options.stockQty || 0) };
    }
    if (q <= 0) {
      removeLine(cart, productId);
      return { ok: true, removed: true, line };
    }
    line.qty = q;
    line.line_total_cents = Math.round(line.qty * line.unit_price_cents);
    return { ok: true, line };
  }

  function setUnitPrice(cart, productId, nextPriceCents) {
    const line = findLine(cart, productId);
    if (!line) return { ok: false, error: "line_not_found" };
    line.unit_price_cents = Math.max(0, Number(nextPriceCents || 0));
    line.line_total_cents = Math.round(qtyNum(line.qty) * line.unit_price_cents);
    return { ok: true, line };
  }

  function setLineTotal(cart, productId, nextTotalCents, options = {}) {
    const line = findLine(cart, productId);
    if (!line) return { ok: false, error: "line_not_found" };
    const total = Math.max(0, Number(nextTotalCents || 0));
    if (options.adjustQtyFromUnitPrice) {
      const unit = Math.max(0, Number(line.unit_price_cents || 0));
      if (unit <= 0) {
        line.qty = 1;
        line.line_total_cents = 0;
        return { ok: true, line };
      }
      const nextQty = Math.max(0.01, qtyNum(total / unit, 1));
      if (!isServiceProduct(line) && isBlockedByStock(nextQty, options.stockQty, options.blockNegativeStock)) {
        return { ok: false, error: "insufficient_stock", line, requestedQty: nextQty, stockQty: Number(options.stockQty || 0) };
      }
      line.qty = nextQty;
      line.line_total_cents = Math.round(nextQty * unit);
      return { ok: true, line };
    }
    const q = Math.max(0.01, qtyNum(line.qty, 1));
    line.line_total_cents = total;
    line.unit_price_cents = Math.round(total / q);
    return { ok: true, line };
  }

  window.WiliPosCartModel = {
    addProduct,
    changeQty,
    findLine,
    removeLine,
    setLineTotal,
    setQty,
    setUnitPrice
  };
}());
