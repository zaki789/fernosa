
(function(){
  function ready(fn){ if(document.readyState!=='loading'){fn();} else document.addEventListener('DOMContentLoaded',fn); }

  function setHidden(name, val){
    var el = document.querySelector('input[name="'+name+'"]');
    if(el) el.value = String(val);
  }

  function setAddress(val){
    var addr = document.querySelector('#billing_address_1');
    if(addr && val){
      addr.value = val;
      addr.dispatchEvent(new Event('change', {bubbles:true}));
    }
  }

  function reverseGeocode(lat,lng){
    // Nominatim reverse geocode (client-side). Works on real site with internet.
    var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=fa&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
    return fetch(url, {headers:{'Accept':'application/json'}}).then(r=>r.json()).then(function(j){
      if(j && (j.display_name || (j.address && j.address.road))){
        return j.display_name || '';
      }
      return '';
    }).catch(function(){ return ''; });
  }

  function mountBox(afterEl){
    var wrap = document.createElement('div');
    wrap.className = 'fg-map-wrap';
    wrap.innerHTML = `
      <div class="fg-eta">
        <div class="fg-eta-title"><i class="fa-solid fa-truck-fast"></i> زمان تقریبی رسیدن</div>
        <div class="fg-eta-bar"><div class="fg-eta-fill" data-fg-eta-fill></div></div>
        <div class="fg-eta-text" data-fg-eta-text></div>
      </div>
      <div style="height:10px"></div>
      <div id="fgCheckoutMap"></div>
      <p class="fg-map-hint">لوکیشن را روی نقشه انتخاب کنید تا آدرس به صورت خودکار وارد شود.</p>
    `;
    afterEl.parentNode.insertBefore(wrap, afterEl.nextSibling);
    return wrap;
  }

  function animateETA(minutes){
    var fill = document.querySelector('[data-fg-eta-fill]');
    var txt  = document.querySelector('[data-fg-eta-text]');
    if(!fill || !txt) return;
    var m = parseInt(minutes || 45, 10);
    txt.textContent = 'حدود ' + m + ' دقیقه';
    // Animate to 100% over a short time (visual), not real-time.
    fill.animate([{width:'0%'},{width:'100%'}], {duration: 1200, fill:'forwards', easing:'ease-out'});
  }

  ready(function(){
    // Insert after delivery slot field if exists
    var slotRow = document.querySelector('#fg_delivery_slot_field');
    if(!slotRow) return;

    var wrap = mountBox(slotRow);

    var lat = (window.FG_DELIVERY && FG_DELIVERY.lat) ? parseFloat(FG_DELIVERY.lat) : 35.7219;
    var lng = (window.FG_DELIVERY && FG_DELIVERY.lng) ? parseFloat(FG_DELIVERY.lng) : 51.4697;

    animateETA((window.FG_DELIVERY && FG_DELIVERY.eta) ? FG_DELIVERY.eta : 45);

    if(typeof L === 'undefined') return;
    var map = L.map('fgCheckoutMap', {scrollWheelZoom:false}).setView([lat,lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: ''}).addTo(map);

    var marker = L.marker([lat,lng], {draggable:true}).addTo(map);

    function commit(ll){
      var la = ll.lat, lo = ll.lng;
      setHidden('fg_map_lat', la.toFixed(6));
      setHidden('fg_map_lng', lo.toFixed(6));
      reverseGeocode(la, lo).then(function(addr){
        if(addr) setAddress(addr);
      });
    }

    marker.on('dragend', function(){ commit(marker.getLatLng()); });
    map.on('click', function(e){
      marker.setLatLng(e.latlng);
      commit(e.latlng);
    });

    // initial
    commit(marker.getLatLng());
  });
})();
