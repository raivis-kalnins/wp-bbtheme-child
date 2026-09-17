/* WP BBTheme child suite 3.8.11.15 - resilient quote drawer + slow hero autoplay. */
(function(){
  'use strict';
  function ready(fn){ if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',fn,{once:true}); else fn(); }
  function tuneHeroAutoplay(){
    document.querySelectorAll('#wp-theme-main .wpbb-swiper--hero .swiper').forEach(function(el){
      var sw=el.swiper; if(!sw||!sw.params) return;
      try{
        sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};
        sw.params.autoplay.delay=8500; sw.params.autoplay.disableOnInteraction=false; sw.params.autoplay.pauseOnMouseEnter=true;
        if(sw.autoplay&&typeof sw.autoplay.start==='function') sw.autoplay.start();
      }catch(e){}
    });
  }
  function quoteTrigger(){
    var selectors=['.wp-theme-quote-float','[class*="quote-float"]','[class*="quote-floating"]','[data-quote-url]','[data-quote-count]'];
    var found=document.querySelector(selectors.join(',')); if(found) return found;
    return Array.prototype.find.call(document.querySelectorAll('a,button'),function(n){return /^my\s+quote\b/i.test(String(n.textContent||'').trim());})||null;
  }
  function initQuote(){
    if(!document.body.classList.contains('wpbb-request-quote-enabled')) return;
    if(document.querySelector('.wp-theme-quote-drawer')) return; // existing theme implementation already works.
    var trigger=quoteTrigger(); if(!trigger) return;
    var link=trigger.matches('a[href]')?trigger:trigger.querySelector('a[href]');
    var href=(link&&link.getAttribute('href'))||trigger.getAttribute('data-quote-url')||'/request-a-quote/';
    function count(){ var c=trigger.querySelector('.wp-theme-quote-count,.count,[data-quote-count]'); var m=String(c?c.textContent:trigger.textContent||'').match(/\d+/); return m?parseInt(m[0],10)||0:0; }
    var backdrop=document.createElement('div'); backdrop.className='wp-theme-quote-drawer-backdrop'; backdrop.hidden=true;
    var drawer=document.createElement('aside'); drawer.className='wp-theme-quote-drawer wp-theme-quote-drawer--v115'; drawer.setAttribute('aria-hidden','true');
    drawer.innerHTML='<div class="wp-theme-quote-drawer__head"><div><span class="wp-theme-sector-eyebrow">Quote</span><h2>Your quote</h2></div><button type="button" class="wp-theme-quote-drawer__close" aria-label="Close quote">×</button></div><div class="wp-theme-quote-drawer__body"></div><div class="wp-theme-quote-drawer__actions"><a class="wp-theme-btn wp-theme-btn--primary" href="'+href.replace(/"/g,'&quot;')+'">Review quote</a></div>';
    document.body.appendChild(backdrop); document.body.appendChild(drawer);
    var body=drawer.querySelector('.wp-theme-quote-drawer__body');
    function render(){ var n=count(); body.innerHTML='<p class="wp-theme-quote-drawer__count"><strong>'+n+'</strong> item'+(n===1?'':'s')+' currently saved for quotation.</p><p>Review the selected items, add your contact details and send one quotation request when you are ready.</p>'; }
    function close(){drawer.classList.remove('is-open');drawer.setAttribute('aria-hidden','true');backdrop.hidden=true;document.body.classList.remove('wp-theme-quote-drawer-open');}
    function open(ev){if(ev)ev.preventDefault();render();backdrop.hidden=false;requestAnimationFrame(function(){drawer.classList.add('is-open');});drawer.setAttribute('aria-hidden','false');document.body.classList.add('wp-theme-quote-drawer-open');}
    trigger.addEventListener('click',open); backdrop.addEventListener('click',close); drawer.querySelector('.wp-theme-quote-drawer__close').addEventListener('click',close);
    document.addEventListener('keydown',function(e){if(e.key==='Escape'&&drawer.classList.contains('is-open'))close();});
  }
  ready(function(){initQuote(); tuneHeroAutoplay(); setTimeout(tuneHeroAutoplay,900); setTimeout(tuneHeroAutoplay,2500);});
})();
