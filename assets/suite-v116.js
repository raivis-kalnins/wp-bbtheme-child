/* WP BBTheme child suite 3.8.11.16 - measured mega-menu, persistent consent, hero autoplay and quote fallback. */
(function(){
  'use strict';
  var D=document, H=D.documentElement;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}

  /* Persist our own marker in addition to the consent component's storage so a
     rebuilt/reset demo cannot forget an already accepted decision on reload. */
  var CONSENT_KEY='wpbb_child_cookie_consent';
  function readConsent(){
    try{if(localStorage.getItem(CONSENT_KEY)==='accepted'||localStorage.getItem('wpbb_v116_cookie_consent')==='accepted')return true;}catch(e){}
    try{return /(?:^|;\s*)(?:wpbb_child_cookie_consent|wpbb_v116_cookie_consent)=accepted(?:;|$)/.test(D.cookie||'');}catch(e){return false;}
  }
  function markConsent(){
    try{localStorage.setItem(CONSENT_KEY,'accepted');}catch(e){}
    try{D.cookie='wpbb_child_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');}catch(e){}
    H.classList.add('wpbb-v116-consent-accepted');
  }
  if(readConsent())H.classList.add('wpbb-v116-consent-accepted');
  D.addEventListener('click',function(e){
    var n=e.target&&e.target.closest?e.target.closest('button,a,[role="button"]'):null;if(!n)return;
    var probe=((n.getAttribute('data-cookie-action')||'')+' '+(n.getAttribute('data-consent-action')||'')+' '+(n.id||'')+' '+(n.className||'')+' '+(n.textContent||'')).toLowerCase().replace(/\s+/g,' ').trim();
    var consentRoot=n.closest('[class*="cookie"],[id*="cookie"],[class*="consent"],[id*="consent"],[data-wpbb-cookie-consent],[data-cookie-consent-banner]');
    if(/\b(accept all|accept all cookies|accept cookies|allow all|allow cookies|agree all|accept-all|accept_all|consent-accept)\b/.test(probe)||(consentRoot&&/^accept(?: all)?(?: cookies)?$/i.test(String(n.textContent||'').trim())))markConsent();
  },true);

  function measureHeader(){
    if(!window.matchMedia('(min-width: 992px)').matches)return;
    var header=D.querySelector('[data-site-header],.wp-theme-site-header,#wp-theme-header,header.site-header,.wp-theme-header-main');if(!header)return;
    var rect=header.getBoundingClientRect();var bottom=Math.max(0,Math.round(rect.bottom));
    if(bottom)H.style.setProperty('--wp-theme-header-bottom',bottom+'px');
  }
  var measureQueued=false;
  function queueMeasure(){if(measureQueued)return;measureQueued=true;requestAnimationFrame(function(){measureQueued=false;measureHeader();});}

  function tuneHeroAutoplay(){
    qa('#wp-theme-main .wpbb-swiper--hero .swiper').forEach(function(el){
      var sw=el.swiper;if(!sw||!sw.params)return;
      try{
        sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};
        sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;
        if(sw.params.pagination&&typeof sw.params.pagination==='object')sw.params.pagination.clickable=true;
        if(sw.autoplay&&typeof sw.autoplay.start==='function'&&!sw.autoplay.running)sw.autoplay.start();
      }catch(e){}
    });
  }

  function quoteTriggers(){
    var nodes=qa('.wp-theme-quote-float,[class*="quote-float"],[class*="quote-floating"],[data-quote-drawer-toggle],[data-quote-url]');
    if(nodes.length)return nodes;
    return qa('a,button').filter(function(n){return /^my\s+quote\b/i.test(String(n.textContent||'').trim());});
  }
  function quoteCount(trigger){
    var c=trigger&&trigger.querySelector?trigger.querySelector('.wp-theme-quote-count,.count,[data-quote-count]'):null;
    var text=String(c?c.textContent:(trigger?trigger.textContent:'')||'');var m=text.match(/\d+/);return m?parseInt(m[0],10)||0:0;
  }
  function initQuote(){
    var triggers=quoteTriggers();if(!triggers.length)return;
    var drawer=D.querySelector('.wp-theme-quote-drawer,.wp-theme-quote-sidebar');
    var backdrop=D.querySelector('.wp-theme-quote-drawer-backdrop');
    if(!backdrop){backdrop=D.createElement('div');backdrop.className='wp-theme-quote-drawer-backdrop';backdrop.hidden=true;D.body.appendChild(backdrop);}
    if(!drawer){
      var first=triggers[0],href=(first.getAttribute&&first.getAttribute('data-quote-url'))||'/request-a-quote/';
      drawer=D.createElement('aside');drawer.className='wp-theme-quote-drawer wp-theme-quote-drawer--v116';drawer.setAttribute('aria-hidden','true');
      drawer.innerHTML='<div class="wp-theme-quote-drawer__head"><div><span class="wp-theme-sector-eyebrow">Quote</span><h2>Your quote</h2></div><button type="button" class="wp-theme-quote-drawer__close" aria-label="Close quote">×</button></div><div class="wp-theme-quote-drawer__body"></div><div class="wp-theme-quote-drawer__actions"><a class="wp-theme-btn wp-theme-btn--primary" href="'+String(href).replace(/"/g,'&quot;')+'">Review quote</a></div>';
      D.body.appendChild(drawer);
    }
    var body=drawer.querySelector('.wp-theme-quote-drawer__body');
    function render(trigger){if(!body)return;var n=quoteCount(trigger);body.innerHTML='<p class="wp-theme-quote-drawer__count"><strong>'+n+'</strong> item'+(n===1?'':'s')+' currently saved for quotation.</p><p>Review the selected items, add your contact details and send one quotation request when you are ready.</p>';}
    function close(){drawer.classList.remove('is-open');drawer.setAttribute('aria-hidden','true');backdrop.hidden=true;D.body.classList.remove('wp-theme-quote-drawer-open');}
    function open(ev,trigger){if(ev)ev.preventDefault();render(trigger);backdrop.hidden=false;drawer.setAttribute('aria-hidden','false');D.body.classList.add('wp-theme-quote-drawer-open');requestAnimationFrame(function(){drawer.classList.add('is-open');});}
    triggers.forEach(function(t){if(t.dataset.wpbbV116QuoteBound)return;t.dataset.wpbbV116QuoteBound='1';t.addEventListener('click',function(e){open(e,t);});});
    if(!backdrop.dataset.wpbbV116QuoteBound){backdrop.dataset.wpbbV116QuoteBound='1';backdrop.addEventListener('click',close);}
    qa('.wp-theme-quote-drawer__close,[data-quote-drawer-close]',drawer).forEach(function(b){if(b.dataset.wpbbV116QuoteBound)return;b.dataset.wpbbV116QuoteBound='1';b.addEventListener('click',close);});
    if(!D.body.dataset.wpbbV116QuoteKeys){D.body.dataset.wpbbV116QuoteKeys='1';D.addEventListener('keydown',function(e){if(e.key==='Escape'&&drawer.classList.contains('is-open'))close();});}
  }

  ready(function(){measureHeader();tuneHeroAutoplay();setTimeout(measureHeader,100);setTimeout(initQuote,120);setTimeout(initQuote,900);setTimeout(tuneHeroAutoplay,700);setTimeout(tuneHeroAutoplay,2200);});
  window.addEventListener('resize',queueMeasure,{passive:true});window.addEventListener('scroll',queueMeasure,{passive:true});
  window.addEventListener('load',function(){measureHeader();tuneHeroAutoplay();});
})();
