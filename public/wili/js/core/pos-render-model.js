(function () {
  "use strict";

  function stockQtyText(value, fallback = 0) {
    const raw = value === "" || value == null ? fallback : value;
    const n = Number(raw);
    if (!Number.isFinite(n)) return String(fallback || 0);
    return Number.isInteger(n) ? String(n) : String(n);
  }

  function findProduct(products, productId) {
    const id = String(productId || "");
    return (Array.isArray(products) ? products : []).find((p) => String(p && p.id) === id) || {};
  }

  function cartLineView(line, products) {
    const source = line && typeof line === "object" ? line : {};
    const product = findProduct(products, source.product_id);
    const stockQty = Number(product.stock_qty || 0);
    return {
      product_id: String(source.product_id || ""),
      name: String(source.name || product.name || "-"),
      barcode: String(source.barcode || product.barcode || ""),
      reference: String(source.reference || product.reference || ""),
      product_type: String(source.product_type || product.product_type || "product"),
      image_path: String(source.image_path || product.image_path || ""),
      mode: String(source.price_mode || "retail"),
      qty: Number(source.qty || 0),
      unit_price_cents: Math.max(0, Number(source.unit_price_cents || 0)),
      line_total_cents: Math.max(0, Number(source.line_total_cents || 0)),
      stock_qty: stockQty,
      stock_text: stockQtyText(stockQty)
    };
  }

  function searchProductView(product) {
    const source = product && typeof product === "object" ? product : {};
    const stockQty = Number(source.stock_qty || 0);
    return {
      id: String(source.id || ""),
      name: String(source.name || "-"),
      barcode: String(source.barcode || ""),
      reference: String(source.reference || ""),
      product_type: String(source.product_type || "product"),
      image_path: String(source.image_path || ""),
      stock_qty: stockQty,
      stock_text: stockQtyText(stockQty),
      stock_class: stockQty <= 0 ? "stock-zero" : "",
      retail_price_cents: Math.max(0, Number(source.retail_price_cents || 0)),
      wholesale_price_cents: Math.max(0, Number(source.wholesale_price_cents || 0)),
      assisted_price_cents: Math.max(0, Number(source.assisted_price_cents || 0))
    };
  }

  window.WiliPosRenderModel = {
    cartLineView,
    searchProductView,
    stockQtyText
  };
}());
