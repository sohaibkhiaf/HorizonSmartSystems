(function () {
  "use strict";

  function qtyNum(value, fallback = 0) {
    const n = Math.round((Number(value) || Number(fallback) || 0) * 100) / 100;
    return Number.isFinite(n) ? n : Number(fallback) || 0;
  }

  function centsFromValue(value) {
    return Math.max(0, Math.round((Number(value) || 0) * 100));
  }

  function computeSummary(cart, options = {}) {
    const lines = Array.isArray(cart) ? cart : [];
    const subtotal = lines.reduce((sum, line) => sum + Number(line && line.line_total_cents || 0), 0);
    const discountType = String(options.discountType || "amount") === "percent" ? "percent" : "amount";
    const discountRaw = Math.max(0, Number(options.discountRaw || 0) || 0);
    let discount = 0;
    if (subtotal > 0 && discountRaw > 0) {
      if (discountType === "percent") {
        discount = Math.round((subtotal * Math.min(100, discountRaw)) / 100);
      } else {
        discount = centsFromValue(discountRaw);
      }
    }
    discount = Math.max(0, Math.min(subtotal, discount));
    const total = Math.max(0, subtotal - discount);
    const hasPaid = String(options.paidValue || "").trim() !== "";
    const rawPaid = centsFromValue(options.paidValue || 0);
    const paid = hasPaid ? Math.min(total, rawPaid) : total;
    const due = Math.max(0, total - paid);
    return {
      qty: lines.reduce((sum, line) => sum + qtyNum(line && line.qty), 0),
      subtotal_cents: subtotal,
      discount_cents: discount,
      total_cents: total,
      paid_cents: paid,
      due_cents: due,
      discount_type: discountType,
      discount_raw_value: discountRaw
    };
  }

  function buildSaleItemsPayload(cart, summary = computeSummary(cart)) {
    const baseLines = (Array.isArray(cart) ? cart : []).map((line) => {
      const qty = qtyNum(line && line.qty);
      const unit = Number(line && line.unit_price_cents || 0);
      return {
        product_id: String(line && line.product_id || ""),
        qty,
        unit_price_cents: unit,
        line_subtotal_cents: Math.max(0, Math.round(qty * unit))
      };
    }).filter((line) => line.product_id && line.qty > 0);

    const totalDiscount = Math.max(0, Number(summary && summary.discount_cents || 0));
    const subtotal = Math.max(0, Number(summary && summary.subtotal_cents || 0));
    if (!baseLines.length || totalDiscount <= 0 || subtotal <= 0) {
      return baseLines.map((line) => ({
        product_id: line.product_id,
        qty: line.qty,
        unit_price_cents: line.unit_price_cents,
        discount_cents: 0
      }));
    }

    let remainingDiscount = Math.min(totalDiscount, subtotal);
    return baseLines.map((line, idx) => {
      let lineDiscount = 0;
      if (remainingDiscount > 0 && line.line_subtotal_cents > 0) {
        if (idx === baseLines.length - 1) {
          lineDiscount = Math.min(line.line_subtotal_cents, remainingDiscount);
        } else {
          lineDiscount = Math.round((totalDiscount * line.line_subtotal_cents) / subtotal);
          lineDiscount = Math.max(0, Math.min(line.line_subtotal_cents, lineDiscount, remainingDiscount));
        }
      }
      remainingDiscount = Math.max(0, remainingDiscount - lineDiscount);
      return {
        product_id: line.product_id,
        qty: line.qty,
        unit_price_cents: line.unit_price_cents,
        discount_cents: lineDiscount
      };
    });
  }

  window.WiliPosCheckoutModel = {
    buildSaleItemsPayload,
    computeSummary
  };
}());
