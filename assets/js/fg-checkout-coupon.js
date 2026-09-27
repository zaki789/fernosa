(function(){
  function ready(fn){ if(document.readyState!=='loading'){fn();} else {document.addEventListener('DOMContentLoaded',fn);} }
  ready(function(){
    var btn = document.querySelector('[data-fg-coupon-toggle]');
    var form = document.querySelector('[data-fg-coupon-form]');
    if(!btn || !form) return;
    btn.addEventListener('click', function(){
      var isOpen = form.style.display !== 'none';
      form.style.display = isOpen ? 'none' : 'block';
      if(!isOpen){
        var input = form.querySelector('#coupon_code');
        if(input) setTimeout(function(){ input.focus(); }, 50);
      }
    });
  });
})();