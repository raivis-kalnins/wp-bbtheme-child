(function(){
  'use strict';
  function q(s,r){return (r||document).querySelector(s)}
  function qa(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))}
  function isMobile(){return window.matchMedia('(max-width:1199.98px)').matches}
  function syncTop(){var h=q('[data-site-header],.wp-theme-site-header');if(h){document.documentElement.style.setProperty('--suite-v99-nav-top',Math.max(0,Math.round(h.getBoundingClientRect().bottom))+'px')}}
  function closeAllSubs(nav){qa('.is-v99-submenu-open',nav).forEach(function(li){li.classList.remove('is-v99-submenu-open','is-submenu-open','is-mega-open');var b=q(':scope > .wpbb-v99-submenu-toggle',li);if(b)b.setAttribute('aria-expanded','false')})}
  function setOpen(open,button,nav){document.body.classList.toggle('wpbb-v99-menu-open',!!open);document.body.classList.remove('wpbb-suite-v98-menu-open','wpbb-sector-mobile-open');if(button)button.setAttribute('aria-expanded',open?'true':'false');if(nav){if(isMobile())nav.setAttribute('aria-hidden',open?'false':'true');else nav.removeAttribute('aria-hidden');}if(!open&&nav)closeAllSubs(nav);syncTop()}
  function cleanLegacy(nav){
    qa('.wpbb-v97-submenu-toggle,.wpbb-suite-v98-submenu-toggle,.wpbb-jobs-v97-submenu-toggle',nav).forEach(function(x){x.remove()});
    qa('.wp-theme-primary-menu li',nav).forEach(function(li){li.classList.remove('is-submenu-open','is-mega-open')});
  }
  function boot(){
    var oldButton=q('.wp-theme-menu-toggle'),nav=q('[data-primary-navigation],#wp-theme-mobile-navigation');if(!oldButton||!nav)return;
    cleanLegacy(nav);
    /* Clone the menu trigger to discard handlers added by earlier finishing layers. */
    var button=oldButton.cloneNode(true);oldButton.parentNode.replaceChild(button,oldButton);button.removeAttribute('data-wpbb-sector97');button.removeAttribute('data-wpbb-jobs-v97');
    qa('.wp-theme-primary-menu li',nav).forEach(function(li){
      var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child)return;
      var old=q(':scope > .wp-theme-submenu-toggle',li);if(old)old.setAttribute('aria-hidden','true');
      var t=document.createElement('button');t.type='button';t.className='wpbb-v99-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Toggle submenu');li.insertBefore(t,child);
      t.addEventListener('click',function(e){if(!isMobile())return;e.preventDefault();e.stopPropagation();var open=!li.classList.contains('is-v99-submenu-open');li.classList.toggle('is-v99-submenu-open',open);li.classList.toggle('is-submenu-open',open);li.classList.toggle('is-mega-open',open);t.setAttribute('aria-expanded',open?'true':'false')});
    });
    button.addEventListener('click',function(e){if(!isMobile())return;e.preventDefault();e.stopPropagation();setOpen(!document.body.classList.contains('wpbb-v99-menu-open'),button,nav)});
    var overlay=q('.wp-theme-header-overlay');if(overlay)overlay.addEventListener('click',function(){setOpen(false,button,nav)});
    qa('.wp-theme-primary-menu a',nav).forEach(function(a){a.addEventListener('click',function(){if(!isMobile())return;var li=a.parentElement;if(!li||(!q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li)))setOpen(false,button,nav)})});
    document.addEventListener('keydown',function(e){if(e.key==='Escape')setOpen(false,button,nav)});
    window.addEventListener('resize',function(){syncTop();if(!isMobile())setOpen(false,button,nav)},{passive:true});
    window.addEventListener('orientationchange',syncTop,{passive:true});
    syncTop();setOpen(false,button,nav);
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
