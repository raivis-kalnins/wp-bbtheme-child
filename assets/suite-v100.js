(function(){
 'use strict';
 function q(s,r){return (r||document).querySelector(s)}
 function qa(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))}
 function mobile(){return window.matchMedia('(max-width:1199.98px)').matches}
 function removeThemeCookieUi(){
   qa('.wp-theme-cookie-banner,.wp-theme-cookie-settings,[data-cookie-settings-dialog]').forEach(function(el){el.remove()});
   qa('button[data-cookie-settings]').forEach(function(el){el.remove()});
 }
 function syncTop(){var h=q('[data-site-header],.wp-theme-site-header');if(h)document.documentElement.style.setProperty('--suite-v100-nav-top',Math.max(0,Math.round(h.getBoundingClientRect().bottom))+'px')}
 function closeSubs(nav){qa('.is-v100-submenu-open',nav).forEach(function(li){li.classList.remove('is-v100-submenu-open','is-v99-submenu-open','is-submenu-open','is-mega-open');var b=q(':scope > .wpbb-v100-submenu-toggle',li);if(b)b.setAttribute('aria-expanded','false')})}
 function setOpen(open,button,nav){document.body.classList.toggle('wpbb-v100-menu-open',!!open);document.body.classList.remove('wpbb-v99-menu-open','wpbb-suite-v98-menu-open','wpbb-sector-mobile-open','wp-theme-menu-open');if(button)button.setAttribute('aria-expanded',open?'true':'false');if(nav){nav.removeAttribute('hidden');if(mobile())nav.setAttribute('aria-hidden',open?'false':'true');else nav.removeAttribute('aria-hidden')}if(!open&&nav)closeSubs(nav);syncTop()}
 function bootMenu(){
   var old=q('.wp-theme-menu-toggle'),nav=q('[data-primary-navigation],#wp-theme-mobile-navigation');if(!old||!nav)return;
   qa('.wp-theme-submenu-toggle,.wpbb-v97-submenu-toggle,.wpbb-suite-v98-submenu-toggle,.wpbb-jobs-v97-submenu-toggle,.wpbb-v99-submenu-toggle,.wpbb-v100-submenu-toggle',nav).forEach(function(x){x.remove()});
   qa('.wp-theme-primary-menu li',nav).forEach(function(li){li.classList.remove('is-submenu-open','is-mega-open','is-v99-submenu-open','is-v100-submenu-open')});
   var button=old.cloneNode(true);old.parentNode.replaceChild(button,old);button.removeAttribute('data-wpbb-sector97');button.removeAttribute('data-wpbb-jobs-v97');button.removeAttribute('hidden');
   qa('.wp-theme-primary-menu li',nav).forEach(function(li){
     var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child)return;
     var t=document.createElement('button');t.type='button';t.className='wpbb-v100-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Toggle submenu');li.insertBefore(t,child);
     t.addEventListener('click',function(e){if(!mobile())return;e.preventDefault();e.stopPropagation();var open=!li.classList.contains('is-v100-submenu-open');li.classList.toggle('is-v100-submenu-open',open);t.setAttribute('aria-expanded',open?'true':'false')});
   });
   button.addEventListener('click',function(e){if(!mobile())return;e.preventDefault();e.stopPropagation();setOpen(!document.body.classList.contains('wpbb-v100-menu-open'),button,nav)});
   var overlay=q('.wp-theme-header-overlay');if(overlay)overlay.addEventListener('click',function(){setOpen(false,button,nav)});
   qa('.wp-theme-primary-menu a',nav).forEach(function(a){a.addEventListener('click',function(){if(!mobile())return;var li=a.parentElement;if(!li||!q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li))setOpen(false,button,nav)})});
   document.addEventListener('keydown',function(e){if(e.key==='Escape')setOpen(false,button,nav)});
   window.addEventListener('resize',function(){syncTop();if(!mobile())setOpen(false,button,nav)},{passive:true});
   window.addEventListener('orientationchange',syncTop,{passive:true});
   nav.removeAttribute('hidden');syncTop();setOpen(false,button,nav);
 }
 function boot(){removeThemeCookieUi();if(window.MutationObserver){var mo=new MutationObserver(function(){removeThemeCookieUi()});mo.observe(document.documentElement,{childList:true,subtree:true})}bootMenu();setTimeout(removeThemeCookieUi,250);setTimeout(removeThemeCookieUi,1200)}
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
