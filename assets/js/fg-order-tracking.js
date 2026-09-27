
(function(){
  function q(sel){ return document.querySelector(sel); }
  function clamp(n,a,b){ return Math.max(a, Math.min(b,n)); }
  function minsToText(m){
    m = Math.max(0, Math.round(m));
    if (m < 60) return 'حدود ' + m + ' دقیقه';
    var h = Math.floor(m/60), r = m%60;
    if (r===0) return 'حدود ' + h + ' ساعت';
    return 'حدود ' + h + ' ساعت و ' + r + ' دقیقه';
  }

  function setSteps(step){
    var list = q('.fg-steps');
    var fill = q('[data-fg-fill]');
    if(!list || !fill) return;
    var order = ['preparing','picked','onway','delivered'];
    var idx = order.indexOf(step);
    if(idx < 0) idx = 0;

    list.querySelectorAll('li').forEach(function(li){
      li.classList.remove('is-done','is-current');
      var s = li.getAttribute('data-step');
      var i = order.indexOf(s);
      if(i < idx) li.classList.add('is-done');
      if(i === idx) li.classList.add('is-current');
      if(i >= 0 && idx === 3) li.classList.add('is-done');
    });

    var pct = (idx / (order.length-1)) * 100;
    fill.style.width = clamp(pct, 0, 100) + '%';
  }

  function osrmDuration(origin, dest){
    if(!origin || !dest || !origin.lat || !origin.lng || !dest.lat || !dest.lng) return Promise.resolve(null);
    var url = 'https://router.project-osrm.org/route/v1/driving/'
      + encodeURIComponent(origin.lng) + ',' + encodeURIComponent(origin.lat) + ';'
      + encodeURIComponent(dest.lng) + ',' + encodeURIComponent(dest.lat)
      + '?overview=false';
    return fetch(url).then(r=>r.json()).then(function(j){
      if(j && j.routes && j.routes[0] && typeof j.routes[0].duration === 'number'){
        return j.routes[0].duration / 60; // minutes
      }
      return null;
    }).catch(function(){ return null; });
  }

  function init(){
    if(typeof FG_TRACK === 'undefined') return;
    setSteps(FG_TRACK.step || 'preparing');

    var etaEl = q('[data-fg-eta]');
    if(!etaEl) return;

    var prep = parseInt(FG_TRACK.prep || 0, 10) || 0;
    // If picked up, prep is done (simplified)
    var step = FG_TRACK.step || 'preparing';
    var prepRemaining = (step === 'preparing') ? prep : 0;

    osrmDuration(FG_TRACK.origin, FG_TRACK.dest).then(function(travel){
      if(travel == null) {
        // fallback: use admin ETA minutes if OSRM not reachable
        var fallback = (parseInt(FG_TRACK.prep||0,10)||0) + 45;
        etaEl.textContent = minsToText(fallback);
        return;
      }
      var total = prepRemaining + travel;
      etaEl.textContent = minsToText(total);
    });
  }

  if(document.readyState !== 'loading') init();
  else document.addEventListener('DOMContentLoaded', init);
})();
