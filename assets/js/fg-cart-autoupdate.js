
(function(){
  var debounceTimer = null;

  function qs(sel, root){ return (root||document).querySelector(sel); }
  function qsa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

  function setBusy(isBusy){
    document.body.classList.toggle('fg-cart-busy', !!isBusy);
  }

  function buildFormData(form){
    var fd = new FormData(form);
    // Woo checks presence of update_cart, value doesn't matter
    if(!fd.has('update_cart')) fd.append('update_cart', '1');
    return fd;
  }

  function fetchAndReplaceCart(){
    var form = qs('form.woocommerce-cart-form');
    if(!form) return;

    // Snapshot current qty values to avoid "jumping" on fast UI changes
    var qtySnapshot = {};
    qsa('input.qty', form).forEach(function(inp){
      var name = inp.getAttribute('name') || '';
      qtySnapshot[name] = inp.value;
    });

    setBusy(true);

    var fd = buildFormData(form);

    fetch(form.getAttribute('action') || window.location.href, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      cache: 'no-store'
    }).then(function(r){ return r.text(); })
      .then(function(html){
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');

        var newForm = qs('form.woocommerce-cart-form', doc);
        var oldForm = qs('form.woocommerce-cart-form');
        if(newForm && oldForm){
          oldForm.replaceWith(newForm);
        }

        var newTotals = qs('.cart-collaterals', doc);
        var oldTotals = qs('.cart-collaterals');
        if(newTotals && oldTotals){
          oldTotals.replaceWith(newTotals);
        }

        // If server response didn't reflect the last UI value (race), re-apply snapshot safely
        var currentForm = qs('form.woocommerce-cart-form');
        if(currentForm){
          qsa('input.qty', currentForm).forEach(function(inp){
            var name = inp.getAttribute('name') || '';
            if(qtySnapshot[name] != null){
              inp.value = qtySnapshot[name];
            }
          });
        }

        if(window.jQuery && jQuery(document.body).trigger){
          jQuery(document.body).trigger('wc_fragment_refresh');
          jQuery(document.body).trigger('updated_cart_totals');
        }
      })
      .catch(function(){})
      .finally(function(){
        setBusy(false);
        bind(); // re-bind events after DOM swap
      });
  }

  function scheduleUpdate(){
    if(debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchAndReplaceCart, 650);
  }

  function bind(){
    // Bind to qty inputs in cart
    qsa('form.woocommerce-cart-form input.qty').forEach(function(input){
      input.removeEventListener('change', scheduleUpdate);
      input.removeEventListener('input', scheduleUpdate);
      input.addEventListener('change', scheduleUpdate);
      input.addEventListener('input', scheduleUpdate);
    });

    // Plus/minus buttons: wait a bit longer so the input value is definitely updated
    var form = qs('form.woocommerce-cart-form');
    if(form){
      form.onclick = function(e){
        var t = e.target;
        if(!t) return;
        if(t.matches('.plus, .minus, .qty-plus, .qty-minus, [data-qty-plus], [data-qty-minus]')){
          setTimeout(scheduleUpdate, 250);
        }
      };
    }
  }

  function init(){
    if(!document.body.classList.contains('woocommerce-cart')) return;
    bind();
  }

  if(document.readyState !== 'loading') init();
  else document.addEventListener('DOMContentLoaded', init);
})();
