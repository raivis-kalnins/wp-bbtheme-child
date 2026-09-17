/* WP BBTheme child suite 3.8.11.17 - exact mega-menu placement, hero autoplay and consent persistence. */
(function(){
  'use strict';
  var D=document,H=D.documentElement;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function desktop(){return window.matchMedia('(min-width: 992px)').matches;}

  var CONSENT_KEY='wpbb_child_cookie_consent';
  function readConsent(){
    try{if(localStorage.getItem(CONSENT_KEY)==='accepted'||localStorage.getItem('wpbb_v116_cookie_consent')==='accepted'||localStorage.getItem('wpbb_v117_cookie_consent')==='accepted')return true;}catch(e){}
    try{return /(?:^|;\s*)(?:wpbb_child_cookie_consent|wpbb_v116_cookie_consent|wpbb_v117_cookie_consent)=accepted(?:;|$)/.test(D.cookie||'');}catch(e){return false;}
  }
  function markConsent(){
    try{localStorage.setItem(CONSENT_KEY,'accepted');localStorage.setItem('wpbb_v117_cookie_consent','accepted');}catch(e){}
    try{D.cookie='wpbb_child_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');}catch(e){}
    H.classList.add('wpbb-v116-consent-accepted','wpbb-v117-consent-accepted');
  }
  if(readConsent())H.classList.add('wpbb-v116-consent-accepted','wpbb-v117-consent-accepted');
  D.addEventListener('click',function(e){
    var n=e.target&&e.target.closest?e.target.closest('button,a,[role="button"]'):null;if(!n)return;
    var probe=((n.getAttribute('data-cookie-action')||'')+' '+(n.getAttribute('data-consent-action')||'')+' '+(n.id||'')+' '+(n.className||'')+' '+(n.textContent||'')).toLowerCase().replace(/\s+/g,' ').trim();
    var root=n.closest('[class*="cookie"],[id*="cookie"],[class*="consent"],[id*="consent"],[data-wpbb-cookie-consent],[data-cookie-consent-banner]');
    if(/\b(accept all|accept all cookies|accept cookies|allow all|allow cookies|agree all|accept-all|accept_all|consent-accept)\b/.test(probe)||(root&&/^accept(?: all)?(?: cookies)?$/i.test(String(n.textContent||'').trim())))markConsent();
  },true);

  function menuHeader(menu){
    var li=menu&&menu.parentElement;
    var header=li&&li.closest?li.closest('.wp-theme-site-header,#wp-theme-header,header'):null;
    return header||D.querySelector('.wp-theme-site-header,#wp-theme-header,header.site-header,header');
  }
  function positionMenu(menu){
    if(!menu||!desktop()){if(menu)menu.style.removeProperty('top');return;}
    var header=menuHeader(menu);if(!header)return;
    var rect=header.getBoundingClientRect();
    var bottom=Math.ceil(rect.bottom);
    // If the matched header is only an inner shell, include visible sibling/header bars.
    qa('.wp-theme-utility-bar,.wp-theme-header-main,.wp-theme-header-main__inner',header).forEach(function(el){var r=el.getBoundingClientRect();if(r.bottom>bottom)bottom=Math.ceil(r.bottom);});
    if(bottom>0){
      var top=Math.max(0,bottom-4);
      menu.style.setProperty('top',top+'px','important');
      H.style.setProperty('--wp-theme-header-bottom',bottom+'px');
    }
  }
  function positionAll(){qa('.wp-theme-mega-menu').forEach(positionMenu);}
  function bindMega(){
    qa('.wp-theme-mega-menu').forEach(function(menu){
      if(menu.dataset.wpbbV117MegaBound)return;menu.dataset.wpbbV117MegaBound='1';
      var li=menu.parentElement;
      if(li){li.addEventListener('pointerenter',function(){positionMenu(menu);},{passive:true});li.addEventListener('focusin',function(){positionMenu(menu);});}
      menu.addEventListener('pointerenter',function(){positionMenu(menu);},{passive:true});
    });
    positionAll();
  }
  var raf=0;function queuePosition(){if(raf)return;raf=requestAnimationFrame(function(){raf=0;positionAll();});}

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

  ready(function(){bindMega();tuneHeroAutoplay();setTimeout(positionAll,80);setTimeout(positionAll,500);setTimeout(positionAll,1500);setTimeout(tuneHeroAutoplay,700);setTimeout(tuneHeroAutoplay,2200);});
  window.addEventListener('resize',queuePosition,{passive:true});
  window.addEventListener('scroll',queuePosition,{passive:true});
  window.addEventListener('load',function(){positionAll();tuneHeroAutoplay();});
  if('ResizeObserver' in window)ready(function(){var h=D.querySelector('.wp-theme-site-header,#wp-theme-header,header.site-header,header');if(h){try{new ResizeObserver(queuePosition).observe(h);}catch(e){}}});
})();
