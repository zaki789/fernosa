/* Fernosa Gelato — main.js
 * هدر/دراور موبایل، آکاردئون منو، مودال محصول، کنترل سبد خرید (AJAX واقعی)،
 * لود تنبل محصولات و نمای تب/کارت دسته‌بندی‌ها.
 */
(() => {
  const header = document.getElementById('siteHeader');
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const drawer = document.getElementById('mobileDrawer');
  const closeBtn = document.getElementById('closeDrawerBtn');

  const onScroll = () => {
    if (!header) return;
    const sc = window.scrollY || document.documentElement.scrollTop;
    header.classList.toggle('is-scrolled', sc > 12);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  const openDrawer = () => {
    if (!drawer) return;
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    document.documentElement.style.overflow = 'hidden';
  };
  const closeDrawer = () => {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    document.documentElement.style.overflow = '';
  };

  if (hamburgerBtn) hamburgerBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (drawer) drawer.addEventListener('click', (e) => {
    if (e.target === drawer) closeDrawer();
  });

  const acc = document.getElementById('menuAccordion');
  if (acc) {
    acc.addEventListener('click', (e) => {
      const btn = e.target.closest('.accordion-header');
      if (!btn) return;
      const item = btn.closest('.accordion-item');
      if (!item) return;

      [...acc.querySelectorAll('.accordion-item')].forEach(it => {
        if (it !== item) {
          it.classList.remove('is-open');
          const b = it.querySelector('.accordion-header');
          if (b) b.setAttribute('aria-expanded', 'false');
        }
      });

      const isOpen = item.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-scroll]');
    if (!btn) return;
    const sel = btn.getAttribute('data-scroll');
    const el = document.querySelector(sel);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  const io = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
    entries.forEach(ent => {
      if (ent.isIntersecting) {
        ent.target.classList.add('is-visible');
        io.unobserve(ent.target);
      }
    });
  }, { threshold: 0.12 }) : null;

  document.querySelectorAll('.reveal').forEach(el => {
    if (io) io.observe(el);
    else el.classList.add('is-visible');
  });

  // ---- Product modal (no page navigation) ----
  const modal = document.getElementById('fgModal');
  const modalImg = document.getElementById('fgModalImg');

  const openModal = (data) => {
    if (!modal) return;
    if (modalImg) {
      modalImg.src = data.image || '';
      modalImg.alt = data.title || '';
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.documentElement.style.overflow = 'hidden';
  };

  const closeModal = () => {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.documentElement.style.overflow = '';
  };

  // Open modal from any element: data-fg-modal-image
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-fg-modal-image]');
    if (!t) return;
    e.preventDefault();
    openModal({
      title: t.getAttribute('data-title') || '',
      image: t.getAttribute('data-image') || '',
    });
  });

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target.closest('[data-fg-close]')) closeModal();
    });
  }
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
  });

  document.addEventListener('click', (e) => {
    const card = e.target.closest('[data-product-modal]');
    if (!card) return;
    // Ignore clicks on add-to-cart buttons/links
    if (e.target.closest('.add_to_cart_button, .single_add_to_cart_button, .ajax_add_to_cart, form, input, button.btn')) {
      return;
    }
    // Only open when clicking on media or title button
    const isMedia = e.target.closest('.product-media');
    const isTitle = e.target.closest('[data-open-modal]');
    if (!isMedia && !isTitle) return;

    e.preventDefault();
    openModal({
      title: card.getAttribute('data-title'),
      image: card.getAttribute('data-image'),
    });
  });
  // ---- Lazy load: fetch products in small batches while scrolling ----
  const makeAllVisible = (root) => {
    if (!root) return;
    root.querySelectorAll('.reveal').forEach(el => el.classList.add('is-visible'));
  };

  const loadMoreForWrap = async (wrap) => {
    if (!wrap) return;
    if ((wrap.dataset.loading || '0') === '1') return;
    if ((wrap.dataset.hasMore || '0') !== '1') return;

    const termId = wrap.dataset.termId;
    const offset = parseInt(wrap.dataset.offset || '0', 10) || 0;
    const limit  = parseInt(wrap.dataset.limit  || '9', 10) || 9;

    const grid = wrap.parentElement ? wrap.parentElement.querySelector('.product-cards') : null;
    const loadingEl = wrap.querySelector('.fg-loading');

    wrap.dataset.loading = '1';
    if (loadingEl) loadingEl.removeAttribute('hidden');

    try {
      const json = await postAjax('fernosa_load_products', {
        term_id: termId,
        offset: offset,
        limit: limit
      }, 'productsNonce');

      if (json && json.success && json.data && grid) {
        const html = json.data.html || '';
        if (html.trim()) {
          const tmp = document.createElement('div');
          tmp.innerHTML = html;
          makeAllVisible(tmp);
          while (tmp.firstChild) {
            const node = tmp.firstChild;
            if (node.nodeType === 1 && node.matches && node.matches('.product-card[data-product-id]')) {
              const pid = node.getAttribute('data-product-id');
              if (pid && grid.querySelector('.product-card[data-product-id="' + pid + '"]')) {
                tmp.removeChild(node);
                continue;
              }
            }
            grid.appendChild(node);
          }
        }

        wrap.dataset.offset  = String(json.data.next_offset || (offset + limit));
        wrap.dataset.hasMore = String(json.data.has_more ? '1' : '0');
      } else {
        // If server errors, stop trying repeatedly
        wrap.dataset.hasMore = '0';
      }
    } catch (_) {
      // In case of network error, allow retry on next intersection
    }

    if (loadingEl) loadingEl.setAttribute('hidden', 'hidden');
    wrap.dataset.loading = '0';
  };

  // ---- Tabs / Cards category navigation ----
  const setupTabsMenu = () => {
    const nav = document.getElementById('fgCatNav');
    const panelsWrap = document.getElementById('fgTabPanels');
    if (!nav || !panelsWrap) return;

    const setActive = (termId) => {
      nav.querySelectorAll('[data-fg-tab]').forEach(btn => {
        const isOn = btn.getAttribute('data-fg-tab') === String(termId);
        btn.classList.toggle('is-active', isOn);
        btn.setAttribute('aria-selected', isOn ? 'true' : 'false');
      });
      panelsWrap.querySelectorAll('[data-fg-panel]').forEach(p => {
        const isOn = p.getAttribute('data-fg-panel') === String(termId);
        p.classList.toggle('is-active', isOn);
      });

      // Trigger load for the active panel immediately (in case it's short)
      const panel = panelsWrap.querySelector('[data-fg-panel="' + termId + '"]');
      if (panel) {
        const wrap = panel.querySelector('[data-fg-loadwrap]');
        if (wrap) loadMoreForWrap(wrap);
      }
    };

    // Default first active
    const first = nav.querySelector('[data-fg-tab]');
    if (first) setActive(first.getAttribute('data-fg-tab'));

    nav.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-fg-tab]');
      if (!btn) return;
      const termId = btn.getAttribute('data-fg-tab');
      setActive(termId);

      // Smooth scroll into panels area for cards mode on mobile
      const panelTop = panelsWrap.getBoundingClientRect().top + window.scrollY;
      if (panelTop > 0) window.scrollTo({ top: panelTop - 120, behavior: 'smooth' });
    });

    // Lazy load sentinel inside active panel
    const io = ('IntersectionObserver' in window) ? new IntersectionObserver((entries) => {
      entries.forEach(ent => {
        if (!ent.isIntersecting) return;
        const wrap = ent.target.closest('[data-fg-loadwrap]');
        const panel = ent.target.closest('[data-fg-panel]');
        if (!wrap || !panel) return;
        if (panel.classList.contains('is-active')) loadMoreForWrap(wrap);
      });
    }, { root: null, rootMargin: '260px 0px', threshold: 0.01 }) : null;

    panelsWrap.querySelectorAll('.fg-sentinel').forEach(s => {
      if (io) io.observe(s);
      else {
        const wrap = s.closest('[data-fg-loadwrap]');
        if (wrap) loadMoreForWrap(wrap);
      }
    });
  };

  // ---- Accordion lazy load ----
  const setupLazyLoadForAccordion = () => {
    const acc = document.getElementById('menuAccordion');
    if (!acc) return;

    const io = ('IntersectionObserver' in window) ? new IntersectionObserver((entries) => {
      entries.forEach(ent => {
        if (!ent.isIntersecting) return;
        const wrap = ent.target.closest('[data-fg-loadwrap]');
        // Only load when its accordion item is open
        const item = ent.target.closest('.accordion-item');
        if (item && item.classList.contains('is-open')) {
          loadMoreForWrap(wrap);
        }
      });
    }, { root: null, rootMargin: '260px 0px', threshold: 0.01 }) : null;

    const observeOpenPanel = () => {
      const openItem = acc.querySelector('.accordion-item.is-open');
      if (!openItem) return;

      // Smooth scroll to opened category header
      const headerBtn = openItem.querySelector('.accordion-header');
      if (headerBtn) headerBtn.scrollIntoView({ behavior: 'smooth', block: 'start' });

      const wrap = openItem.querySelector('[data-fg-loadwrap]');
      const sentinel = wrap ? wrap.querySelector('[data-fg-sentinel]') : null;
      if (io && sentinel) io.observe(sentinel);

      // If first batch is small but viewport is tall, trigger one immediate prefetch
      if (wrap && (wrap.dataset.hasMore || '0') === '1') {
        setTimeout(() => {
          const rect = wrap.getBoundingClientRect();
          if (rect.top < window.innerHeight) loadMoreForWrap(wrap);
        }, 120);
      }
    };

    // Initial
    setTimeout(observeOpenPanel, 60);

    // Re-evaluate after accordion toggles (our other listener runs first)
    acc.addEventListener('click', (e) => {
      if (!e.target.closest('.accordion-header')) return;
      setTimeout(observeOpenPanel, 30);
    });
  };

  setupLazyLoadForAccordion();
  setupTabsMenu();
})();
