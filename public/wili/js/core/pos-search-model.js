(function () {
  "use strict";

  const NUMERIC_KEYS = new Set(["stock_qty", "retail_price_cents", "wholesale_price_cents", "assisted_price_cents"]);

  function sortRows(rows, state = {}) {
    const key = String(state.key || "name");
    const dir = state.dir === "desc" ? -1 : 1;
    return (Array.isArray(rows) ? rows : []).slice().sort((a, b) => {
      if (NUMERIC_KEYS.has(key)) {
        return (Number(a && a[key] ? a[key] : 0) - Number(b && b[key] ? b[key] : 0)) * dir;
      }
      return String((a && a[key]) || "").localeCompare(String((b && b[key]) || ""), undefined, { sensitivity: "base" }) * dir;
    });
  }

  function clampIndex(index, rows) {
    const length = Array.isArray(rows) ? rows.length : 0;
    if (!length) return 0;
    return Math.max(0, Math.min(length - 1, Number(index) || 0));
  }

  function moveSelection(currentIndex, delta, rows) {
    return clampIndex((Number(currentIndex) || 0) + Number(delta || 0), rows);
  }

  function toggleSortState(state, key) {
    const nextKey = String(key || "name");
    const current = state && typeof state === "object" ? state : { key: "name", dir: "asc" };
    if (current.key === nextKey) {
      current.dir = current.dir === "asc" ? "desc" : "asc";
    } else {
      current.key = nextKey;
      current.dir = "asc";
    }
    return current;
  }

  function keyActionFromEvent(event) {
    if (!event) return "";
    if (event.key === "ArrowDown") return "move_down";
    if (event.key === "ArrowUp") return "move_up";
    if (event.key === "Enter") return "add_selected";
    if (event.key === "+" || event.key === "Add" || (event.key === "=" && event.shiftKey)) return "increment_selected";
    if (event.key === "-" || event.key === "Subtract") return "decrement_selected";
    return "";
  }

  function handleKeyEvent(event, options = {}) {
    const rows = Array.isArray(options.rows) ? options.rows : [];
    if (!rows.length) return false;
    const action = keyActionFromEvent(event);
    if (!action) return false;
    if (event && typeof event.preventDefault === "function") event.preventDefault();
    if (typeof options.setKeyboardActive === "function") options.setKeyboardActive(true);

    const currentIndex = clampIndex(options.selectedIndex, rows);
    if (action === "move_down") {
      options.selectRow?.(moveSelection(currentIndex, 1, rows), { reveal: true });
      return true;
    }
    if (action === "move_up") {
      options.selectRow?.(moveSelection(currentIndex, -1, rows), { reveal: true });
      return true;
    }

    const selected = rows[currentIndex];
    if (!selected) return true;
    if (action === "add_selected") {
      options.addSelected?.(selected, Math.max(0.01, Number(options.quickQty || 1) || 1));
      options.selectRow?.(currentIndex, { reveal: true });
      options.focusSearch?.();
      return true;
    }
    if (action === "increment_selected") {
      options.addSelected?.(selected, 1);
      options.selectRow?.(currentIndex, { reveal: true });
      options.focusSearch?.();
      return true;
    }
    if (action === "decrement_selected") {
      options.decrementSelected?.(selected);
      options.selectRow?.(currentIndex, { reveal: true });
      options.focusSearch?.();
      return true;
    }
    return false;
  }

  window.WiliPosSearchModel = {
    clampIndex,
    handleKeyEvent,
    keyActionFromEvent,
    moveSelection,
    sortRows,
    toggleSortState
  };
}());
