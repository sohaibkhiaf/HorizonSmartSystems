/*
 * Legacy compatibility entrypoint.
 * Runtime logic is split across ./js/* files and loaded directly by index.html.
 * If a legacy HTML still includes only renderer.js, this loader injects scripts in order.
 */
(function loadSplitRenderer() {
  if (window.__wiliRendererSplitLoaded) return;
  window.__wiliRendererSplitLoaded = true;

  const scripts = [
    './js/core/bootstrap.js',
    './js/core/navigation.js',
    './js/pages/catalog.js',
    './js/pages/sales.js',
    './js/pages/purchases_finance.js',
    './js/app-init.js'
  ];

  // If split scripts already exist in DOM (new index.html), do nothing.
  const alreadyLoaded = scripts.some((src) =>
    document.querySelector(`script[src="${src}"]`)
  );
  if (alreadyLoaded) return;

  const appendNext = (index) => {
    if (index >= scripts.length) return;
    const script = document.createElement('script');
    script.src = scripts[index];
    script.async = false;
    script.onload = () => appendNext(index + 1);
    script.onerror = () => console.error('[WILI] Failed to load', scripts[index]);
    document.body.appendChild(script);
  };

  appendNext(0);
})();
