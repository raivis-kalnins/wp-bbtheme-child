/* WP BBTheme child suite 3.8.11.19 - closer mega menus, no-flash consent persistence and hero runtime guard. */
(function(){
  'use strict';
  var D=document,H=D.documentElement;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function desktop(){return window.matchMedia('(min-width: 992px)').matches;}

  var CONSENT_KEY='wpbb_child_cookie_consent';
  var CONSENT_KEYS=[CONSENT_KEY,'wpbb_v116_cookie_consent','wpbb_v117_cookie_consent','wpbb_v118_cookie_consent','wpbb_v119_cookie_consent','wpbb_cookie_consent','wp_theme_cookie_consent','cookie_consent','cookieConsent','cookie_consent_status','cookieConsentStatus','wpbb_consent','wpbb_consent_status','wpbb_cookie_consent_status'];
  function acceptedValue(v){v=String(v==null?'':v).trim();return /^(accepted?|all|allow(?:ed)?|granted|true|1|yes)$/i.test(v)||(/^[{[]/.test(v)&&/(accepted?|granted|allow(?:ed)?)[\"']?\s*[:=]\s*[\"']?(?:true|1|yes|all)/i.test(v));}
  function readConsent(){
    var i,n,v;
    try{
      for(i=0;i<CONSENT_KEYS.length;i++)if(acceptedValue(localStorage.getItem(CONSENT_KEYS[i])))return true;
      for(i=0;i<localStorage.length;i++){n=localStorage.key(i)||'';if(/cookie|consent/i.test(n)&&acceptedValue(localStorage.getItem(n)))return true;}
    }catch(e){}
    try{
      var parts=(D.cookie||'').split(';');
      for(i=0;i<parts.length;i++){var p=parts[i].split('='),name=(p.shift()||'').trim();v=decodeURIComponent(p.join('=')||'');if(/cookie|consent/i.test(name)&&acceptedValue(v))return true;}
    }catch(e){}
    return false;
  }
  function hideConsent(){
    H.classList.add('wpbb-v119-consent-accepted');
    qa('.wpbb-cookie-consent,.wpbb-cookie-consent-banner,.wpbb-cookie-consent__banner,.wpbb-cookie-banner,[data-wpbb-cookie-consent],[data-wpbb-cookie-banner],[data-cookie-consent-banner],.wp-theme-cookie-banner,[class*="cookie-banner"],[class*="cookie_banner"],[id*="cookie-banner"],[id*="cookie_banner"],[class*="cookie-consent"],[class*="consent-banner"],[id*="cookie-consent"],[id*="consent-banner"],#cookie-law-info-bar,.cookie-notice-container,.cky-consent-container,.cmplz-cookiebanner').forEach(function(n){n.hidden=true;n.setAttribute('aria-hidden','true');});
  }
  function markConsent(){
    try{localStorage.setItem(CONSENT_KEY,'accepted');localStorage.setItem('wpbb_v119_cookie_consent','accepted');}catch(e){}
    try{D.cookie='wpbb_child_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');}catch(e){}
    hideConsent();
  }
  if(readConsent())hideConsent();
  D.addEventListener('click',function(e){
    var n=e.target&&e.target.closest?e.target.closest('button,a,[role="button"],input[type="button"],input[type="submit"]'):null;if(!n)return;
    var probe=((n.getAttribute('data-cookie-action')||'')+' '+(n.getAttribute('data-consent-action')||'')+' '+(n.id||'')+' '+(typeof n.className==='string'?n.className:'')+' '+(n.value||'')+' '+(n.textContent||'')).toLowerCase().replace(/\s+/g,' ').trim();
    var root=n.closest('[class*="cookie"],[id*="cookie"],[class*="consent"],[id*="consent"],[data-wpbb-cookie-consent],[data-cookie-consent-banner]');
    if(/\b(accept all|accept all cookies|accept cookies|allow all|allow cookies|agree all|accept-all|accept_all|consent-accept|cookies accepted)\b/.test(probe)||(root&&/^accept(?: all)?(?: cookies)?$/i.test(String(n.textContent||n.value||'').trim())))markConsent();
  },true);

  function directTrigger(menu){
    var li=menu&&menu.parentElement;if(!li)return null;
    try{return li.querySelector(':scope > a, :scope > button, :scope > .wp-theme-nav-link')||li;}catch(e){return li.querySelector('a,button')||li;}
  }
  function positionMenu(menu){
    if(!menu)return;
    if(!desktop()){menu.style.removeProperty('top');menu.style.removeProperty('transform');return;}
    var trigger=directTrigger(menu);if(!trigger)return;
    var r=trigger.getBoundingClientRect();
    // Deliberate 10px overlap removes the visual air-gap and creates a reliable hover bridge.
    var top=Math.max(0,Math.round(r.bottom-10));
    menu.style.setProperty('top',top+'px','important');
    menu.style.setProperty('left','50%','important');
    menu.style.setProperty('transform','translateX(-50%)','important');
    H.style.setProperty('--wpbb-v119-mega-top',top+'px');
  }
  function positionAll(){qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(positionMenu);}
  function bindMega(){
    qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(function(menu){
      if(menu.dataset.wpbbV119MegaBound)return;menu.dataset.wpbbV119MegaBound='1';
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
      if((img.currentSrc||img.src||'').indexOf('/hero-v118/')>-1||(img.getAttribute('src')||'').indexOf('/hero-v118/')>-1){img.removeAttribute('srcset');img.removeAttribute('sizes');img.loading='eager';img.decoding='async';}
    });
  }

  ready(function(){
    bindMega();tuneHero();if(readConsent())hideConsent();
    setTimeout(positionAll,50);setTimeout(positionAll,250);setTimeout(positionAll,900);setTimeout(positionAll,1800);
    setTimeout(tuneHero,450);setTimeout(tuneHero,1600);
    if('MutationObserver' in window){try{new MutationObserver(function(){if(readConsent())hideConsent();}).observe(D.documentElement,{childList:true,subtree:true});}catch(e){}}
  });
  window.addEventListener('resize',queuePosition,{passive:true});
  window.addEventListener('scroll',queuePosition,{passive:true});
  window.addEventListener('load',function(){positionAll();tuneHero();if(readConsent())hideConsent();});
})();
