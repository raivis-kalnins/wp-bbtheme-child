(function(){
  'use strict';
  function q(s,r){return (r||document).querySelector(s)}
  function qa(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))}
  function syncTop(){var h=q('[data-site-header],.wp-theme-site-header');if(!h)return;document.documentElement.style.setProperty('--sector-mobile-nav-top',Math.max(0,Math.round(h.getBoundingClientRect().bottom))+'px')}
  function closeMenu(){var b=q('.wp-theme-menu-toggle'),n=q('[data-primary-navigation],#wp-theme-mobile-navigation');document.body.classList.remove('wpbb-sector-mobile-open','wp-theme-menu-open');if(b)b.setAttribute('aria-expanded','false');if(n)n.setAttribute('aria-hidden','true')}
  function bindMenu(){
    var b=q('.wp-theme-menu-toggle'),n=q('[data-primary-navigation],#wp-theme-mobile-navigation');if(!b||!n)return;
    syncTop();window.addEventListener('resize',syncTop,{passive:true});window.addEventListener('orientationchange',syncTop,{passive:true});
    if(b.dataset.wpbbSector97!=='1'){b.dataset.wpbbSector97='1';b.addEventListener('click',function(){window.setTimeout(function(){var open=document.body.classList.contains('wp-theme-menu-open')||b.getAttribute('aria-expanded')==='true';document.body.classList.toggle('wpbb-sector-mobile-open',open);n.setAttribute('aria-hidden',open?'false':'true');syncTop()},0)})}
    qa('.wp-theme-primary-menu li').forEach(function(li){
      var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child||q(':scope > .wpbb-v97-submenu-toggle',li))return;
      var t=document.createElement('button');t.type='button';t.className='wpbb-v97-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Toggle submenu');li.insertBefore(t,child);
      t.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();var o=!li.classList.contains('is-submenu-open');li.classList.toggle('is-submenu-open',o);t.setAttribute('aria-expanded',o?'true':'false')});
    });
    qa('.wp-theme-primary-menu a').forEach(function(a){a.addEventListener('click',function(){if(window.matchMedia('(max-width:1199.98px)').matches&&!a.parentElement.classList.contains('menu-item-has-children'))closeMenu()})});
    var ov=q('.wp-theme-header-overlay');if(ov)ov.addEventListener('click',closeMenu);
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeMenu()});
  }
  function boot(){bindMenu()}
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
