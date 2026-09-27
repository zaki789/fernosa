(() => {
  const input = document.getElementById('fernosaSearchInput');
  const dropdown = document.getElementById('fernosaSearchDropdown');
  if (!input || !dropdown) return;

  const ajaxUrl = (window.FERNOSA && FERNOSA.ajaxUrl) ? FERNOSA.ajaxUrl : '/wp-admin/admin-ajax.php';
  const nonce = (window.FERNOSA && FERNOSA.searchNonce) ? FERNOSA.searchNonce : '';
  const homeUrl = (document.documentElement && document.documentElement.getAttribute('data-home-url')) || (window.location.origin + '/');

  let lastQ = '';
  let timer = null;
  let isOpen = false;

  const esc = (s) => String(s || '').replace(/[&<>"']/g, (c) => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
  }[c]));

  const postAjax = async (action, data) => {
    const body = new URLSearchParams();
    body.set('action', action);
    body.set('nonce', nonce);
    Object.entries(data || {}).forEach(([k,v]) => body.set(k, String(v)));
    const res = await fetch(ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    });
    return res.json();
  };

  const open = () => {
    dropdown.hidden = false;
    dropdown.setAttribute('aria-hidden', 'false');
    isOpen = true;
  };

  const close = () => {
    dropdown.hidden = true;
    dropdown.setAttribute('aria-hidden', 'true');
    dropdown.innerHTML = '';
    isOpen = false;
  };

  const render = (items, q) => {
    if (!items || !items.length) {
      dropdown.innerHTML = `<div class="fg-search-empty">محصولی برای «${esc(q)}» پیدا نشد.</div>`;
      open();
      return;
    }

    dropdown.innerHTML = items.map(item => {
      const img = item.image ? `<img class="fg-search-img" src="${esc(item.image)}" alt="">` : `<div class="fg-search-img ph"></div>`;
      const disabled = item.inStock ? '' : 'disabled aria-disabled="true"';
      const stockLabel = item.inStock ? '' : `<span class="fg-search-oos">ناموجود</span>`;
      return `
        <div class="fg-search-item" data-id="${item.id}">
          ${img}
          <button type="button" class="fg-search-go" data-go="${item.id}">
            <div class="fg-search-title">${esc(item.title)} ${stockLabel}</div>
            <div class="fg-search-price">${item.priceHtml || ''}</div>
          </button>
          <button type="button" class="fg-search-add" data-add="${item.id}" ${disabled} title="افزودن به سبد">
            <i class="fa-solid fa-plus"></i>
          </button>
        </div>
      `;
    }).join('');
    open();
  };

  const doSearch = async (q) => {
    lastQ = q;
    try {
      const json = await postAjax('fernosa_product_search', { q, limit: 8 });
      if (!json || !json.success) return render([], q);
      render(json.data.items || [], q);
    } catch (e) {
      render([], q);
    }
  };

  const smoothScrollToProduct = (pid) => {
    const el = document.getElementById(`fg-product-${pid}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return true;
    }
    return false;
  };

  const tryScrollFromHash = () => {
    const hash = window.location.hash || '';
    if (!hash.startsWith('#fg-product-')) return;
    const pid = parseInt(hash.replace('#fg-product-', ''), 10);
    if (!pid) return;

    // retry for a short window because products may lazy-load
    let tries = 0;
    const t = setInterval(() => {
      tries++;
      if (smoothScrollToProduct(pid) || tries > 30) {
        clearInterval(t);
      }
    }, 300);
  };

  // On page load, try to scroll if hash exists
  window.addEventListener('load', tryScrollFromHash);

  input.addEventListener('input', () => {
    const q = input.value.trim();
    if (timer) clearTimeout(timer);

    if (q.length < 2) {
      close();
      return;
    }

    timer = setTimeout(() => doSearch(q), 220);
  });

  input.addEventListener('focus', () => {
    const q = input.value.trim();
    if (q.length >= 2 && !isOpen) doSearch(q);
  });

  document.addEventListener('click', (e) => {
    if (!e.target) return;

    const addBtn = e.target.closest('[data-add]');
    if (addBtn) {
      const pid = parseInt(addBtn.getAttribute('data-add'), 10);
      if (!pid) return;
      addBtn.classList.add('is-loading');
      // Reuse existing cart qty ajax if present
      const body = new URLSearchParams();
      body.set('action', 'fernosa_set_cart_qty');
      body.set('nonce', (window.FERNOSA && FERNOSA.nonce) ? FERNOSA.nonce : '');
      body.set('product_id', String(pid));
      body.set('qty', '1');
      fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin'
      }).then(r => r.json()).then(json => {
        if (json && json.success) {
          document.querySelectorAll('.cart-count').forEach(el => el.textContent = String(json.data.count || 0));
        }
      }).finally(() => addBtn.classList.remove('is-loading'));
      return;
    }

    const goBtn = e.target.closest('[data-go]');
    if (goBtn) {
      const pid = parseInt(goBtn.getAttribute('data-go'), 10);
      if (!pid) return;

      close();
      // If product is on the page, scroll; otherwise go to home with hash
      if (!smoothScrollToProduct(pid)) {
        const base = (window.FERNOSA && FERNOSA.homeUrl) ? FERNOSA.homeUrl : '/';
        window.location.href = `${base}#fg-product-${pid}`;
      }
      return;
    }

    // click outside
    if (!e.target.closest('.fg-ajax-search')) close();
  });

  input.form && input.form.addEventListener('submit', (e) => {
    // If ajax dropdown is open, prevent ugly full page search unless user explicitly hits Enter with no dropdown
    if (isOpen) {
      e.preventDefault();
      // if first item exists, go to it
      const first = dropdown.querySelector('[data-go]');
      if (first) first.click();
    }
  });

  // ESC closes
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
})();
