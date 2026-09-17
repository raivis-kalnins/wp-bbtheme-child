/* WP BBTheme child suite 3.8.11.18 - nav-trigger mega positioning, direct hero enforcement, autoplay and consent persistence. */
(function(){
  'use strict';
  var D=document,H=D.documentElement;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function desktop(){return window.matchMedia('(min-width: 992px)').matches;}

  var CONSENT_KEY='wpbb_child_cookie_consent';
  function readConsent(){
    try{if(['accepted','all','allow'].indexOf(localStorage.getItem(CONSENT_KEY))>-1||localStorage.getItem('wpbb_v116_cookie_consent')==='accepted'||localStorage.getItem('wpbb_v117_cookie_consent')==='accepted')return true;}catch(e){}
    try{return /(?:^|;\s*)(?:wpbb_child_cookie_consent|wpbb_v116_cookie_consent|wpbb_v117_cookie_consent)=accepted(?:;|$)/.test(D.cookie||'');}catch(e){return false;}
  }
  function markConsent(){
    try{localStorage.setItem(CONSENT_KEY,'accepted');localStorage.setItem('wpbb_v118_cookie_consent','accepted');}catch(e){}
    try{D.cookie='wpbb_child_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');}catch(e){}
    H.classList.add('wpbb-v118-consent-accepted');
  }
  if(readConsent())H.classList.add('wpbb-v118-consent-accepted');
  D.addEventListener('click',function(e){
    var n=e.target&&e.target.closest?e.target.closest('button,a,[role="button"]'):null;if(!n)return;
    var probe=((n.getAttribute('data-cookie-action')||'')+' '+(n.getAttribute('data-consent-action')||'')+' '+(n.id||'')+' '+(n.className||'')+' '+(n.textContent||'')).toLowerCase().replace(/\s+/g,' ').trim();
    var root=n.closest('[class*="cookie"],[id*="cookie"],[class*="consent"],[id*="consent"],[data-wpbb-cookie-consent],[data-cookie-consent-banner]');
    if(/\b(accept all|accept all cookies|accept cookies|allow all|allow cookies|agree all|accept-all|accept_all|consent-accept)\b/.test(probe)||(root&&/^accept(?: all)?(?: cookies)?$/i.test(String(n.textContent||'').trim())))markConsent();
  },true);

  function menuTrigger(menu){
    var li=menu&&menu.parentElement;if(!li)return null;
    try{return li.querySelector(':scope > a, :scope > button, :scope > .wp-theme-nav-link')||li;}catch(e){return li.querySelector('a,button')||li;}
  }
  function positionMenu(menu){
    if(!menu)return;
    if(!desktop()){menu.style.removeProperty('top');return;}
    var trigger=menuTrigger(menu);if(!trigger)return;
    var r=trigger.getBoundingClientRect();
    var li=menu.parentElement, lr=li&&li.getBoundingClientRect?li.getBoundingClientRect():r;
    var bottom=Math.ceil(Math.max(r.bottom,lr.bottom)-2);
    if(bottom>0){menu.style.setProperty('top',bottom+'px','important');H.style.setProperty('--wpbb-v118-mega-top',bottom+'px');}
  }
  function positionAll(){qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(positionMenu);}
  function bindMega(){
    qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(function(menu){
      if(menu.dataset.wpbbV118MegaBound)return;menu.dataset.wpbbV118MegaBound='1';
      var li=menu.parentElement;
      if(li){li.addEventListener('pointerenter',function(){positionMenu(menu);},{passive:true});li.addEventListener('focusin',function(){positionMenu(menu);});}
      menu.addEventListener('pointerenter',function(){positionMenu(menu);},{passive:true});
    });
    positionAll();
  }
  var raf=0;function queuePosition(){if(raf)return;raf=requestAnimationFrame(function(){raf=0;positionAll();});}

  function tuneHero(){
    qa('#wp-theme-main .wpbb-swiper--hero .swiper').forEach(function(el){
      var sw=el.swiper;if(!sw||!sw.params)return;
      try{
        sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};
        sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;
        if(sw.params.pagination&&typeof sw.params.pagination==='object')sw.params.pagination.clickable=true;
        if(sw.autoplay&&typeof sw.autoplay.start==='function'&&!sw.autoplay.running)sw.autoplay.start();
      }catch(e){}
    });
    qa('#wp-theme-main .wpbb-swiper--hero .wpbb-swiper-slide__media img').forEach(function(img){
      // The v118 URLs are direct child assets. Do not allow an old WP srcset derivative to replace them.
      if((img.currentSrc||img.src||'').indexOf('/hero-v118/')>-1||(img.getAttribute('src')||'').indexOf('/hero-v118/')>-1){img.removeAttribute('srcset');img.removeAttribute('sizes');}
    });
  }

  ready(function(){bindMega();tuneHero();setTimeout(positionAll,80);setTimeout(positionAll,450);setTimeout(positionAll,1400);setTimeout(tuneHero,650);setTimeout(tuneHero,2100);});
  window.addEventListener('resize',queuePosition,{passive:true});
  window.addEventListener('scroll',queuePosition,{passive:true});
  window.addEventListener('load',function(){positionAll();tuneHero();});
  if('ResizeObserver' in window)ready(function(){qa('.wp-theme-primary-menu>li').forEach(function(li){try{new ResizeObserver(queuePosition).observe(li);}catch(e){}});});
})();
