(function(){
'use strict';
var D=document;
function q(s,r){return (r||D).querySelector(s)}
function qa(s,r){return Array.prototype.slice.call((r||D).querySelectorAll(s))}
function mobile(){return window.matchMedia('(max-width:991.98px)').matches}
function stripData(el){if(!el||!el.attributes)return;Array.from(el.attributes).forEach(function(a){if(/^data-(wpbb|menu|nav|toggle|offcanvas|collapse)/i.test(a.name))el.removeAttribute(a.name)})}
function headerBottom(){var h=q('[data-site-header],.wp-theme-site-header');var n=h?Math.max(0,Math.round(h.getBoundingClientRect().bottom)):68;D.documentElement.style.setProperty('--suite-v104-nav-top',n+'px');return n}
function removeCookieUi(){qa('.wp-theme-cookie-banner,.wp-theme-cookie-settings,[data-cookie-settings-dialog]').forEach(function(el){el.remove()});qa('button[data-cookie-settings]').forEach(function(el){el.remove()})}
function hydrateGalleries(){
 var cfg=window.wpbbSuiteV101||{};var imgs=Array.isArray(cfg.gallery)?cfg.gallery:[];
 if(imgs.length){qa('.wp-theme-gallery-section .wpbb-swiper--gallery .wpbb-swiper-slide').forEach(function(slide,i){var info=imgs[i%imgs.length];if(!info||!info.url)return;var media=q('.wpbb-swiper-slide__media',slide);if(!media){media=D.createElement('div');media.className='wpbb-swiper-slide__media';slide.insertBefore(media,slide.firstChild)}var im=q('img',media);if(!im){im=D.createElement('img');media.appendChild(im)}im.src=info.url;im.alt=info.title||'';im.loading='eager';im.decoding='async';im.removeAttribute('srcset');im.removeAttribute('sizes');im.style.opacity='1';im.style.visibility='visible'})}
 var payload=window.wpbbChildSectorGalleries||{};var path=location.pathname.replace(/\/$/,'');var data=payload[path]||payload[location.pathname]||null;
 qa('.wp-theme-item-gallery--single').forEach(function(gallery){var stage=q('.wp-theme-item-gallery__stage img',gallery),thumbs=qa('.wp-theme-item-gallery__thumb',gallery);if(data&&Array.isArray(data.images)&&data.images.length){if(stage){stage.src=data.images[0].display||data.images[0].full||stage.src;stage.loading='eager';stage.removeAttribute('srcset');stage.removeAttribute('sizes')}thumbs.forEach(function(b,i){var x=data.images[i],im=q('img',b);if(!x){b.hidden=true;return}b.hidden=false;if(!im){im=D.createElement('img');b.appendChild(im)}im.src=x.thumb||x.display||x.full;im.alt='';im.loading='eager';im.decoding='async'})}qa('img',gallery).forEach(function(im){im.style.opacity='1';im.style.visibility='visible';im.style.filter='none'})})
}
function setupMenu(){
 var btn=q('.wp-theme-menu-toggle'),nav=qa('.wp-theme-primary-navigation,[data-primary-navigation],#wp-theme-mobile-navigation').find(function(n){return q('.wp-theme-primary-menu',n)})||null;if(!btn||!nav)return;
 var parent=nav.parentNode,next=nav.nextSibling,placeholder=D.createComment('wpbb-v104-nav-home');parent.insertBefore(placeholder,nav);
 var fresh=btn.cloneNode(true);stripData(fresh);fresh.classList.add('wpbb-v104-menu-toggle');fresh.removeAttribute('hidden');fresh.setAttribute('aria-expanded','false');fresh.setAttribute('aria-label','Open menu');btn.parentNode.replaceChild(fresh,btn);btn=fresh;
 qa('.wp-theme-submenu-toggle,.wpbb-v97-submenu-toggle,.wpbb-suite-v98-submenu-toggle,.wpbb-jobs-v97-submenu-toggle,.wpbb-v99-submenu-toggle,.wpbb-v100-submenu-toggle,.wpbb-v101-submenu-toggle,.wpbb-v104-submenu-toggle',nav).forEach(function(x){x.remove()});
 qa('.wp-theme-primary-menu li',nav).forEach(function(li){li.classList.remove('is-submenu-open','is-mega-open','is-v99-submenu-open','is-v100-submenu-open','is-v101-submenu-open','is-v104-submenu-open');var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child)return;var t=D.createElement('button');t.type='button';t.className='wpbb-v104-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Open submenu');li.insertBefore(t,child);t.addEventListener('click',function(e){if(!mobile())return;e.preventDefault();e.stopImmediatePropagation();var open=!li.classList.contains('is-v104-submenu-open');li.classList.toggle('is-v104-submenu-open',open);t.setAttribute('aria-expanded',open?'true':'false');t.setAttribute('aria-label',open?'Close submenu':'Open submenu')},true)});
 var overlay=q('.wpbb-v104-mobile-overlay');if(!overlay){overlay=D.createElement('div');overlay.className='wpbb-v104-mobile-overlay';overlay.setAttribute('aria-hidden','true');D.body.appendChild(overlay)}
 function move(){if(mobile()){if(nav.parentNode!==D.body)D.body.appendChild(nav);nav.classList.add('wpbb-v104-mobile-nav')}else{if(nav.parentNode===D.body){if(next&&next.parentNode===parent)parent.insertBefore(nav,next);else parent.insertBefore(nav,placeholder.nextSibling)}nav.classList.remove('wpbb-v104-mobile-nav')}}
 function closeSubs(){qa('.is-v104-submenu-open',nav).forEach(function(li){li.classList.remove('is-v104-submenu-open');var t=q(':scope > .wpbb-v104-submenu-toggle',li);if(t){t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Open submenu')}})}
 function setOpen(open){open=!!open&&mobile();move();D.body.classList.toggle('wpbb-v104-menu-open',open);D.body.classList.remove('wpbb-v101-menu-open','wpbb-v100-menu-open','wpbb-v99-menu-open','wpbb-suite-v98-menu-open','wpbb-sector-mobile-open','wp-theme-menu-open');btn.setAttribute('aria-expanded',open?'true':'false');btn.setAttribute('aria-label',open?'Close menu':'Open menu');nav.removeAttribute('hidden');if(mobile())nav.setAttribute('aria-hidden',open?'false':'true');else nav.removeAttribute('aria-hidden');overlay.setAttribute('aria-hidden',open?'false':'true');if(!open)closeSubs();headerBottom()}
 btn.addEventListener('click',function(e){if(!mobile())return;e.preventDefault();e.stopImmediatePropagation();setOpen(!D.body.classList.contains('wpbb-v104-menu-open'))},true);
 overlay.addEventListener('click',function(){setOpen(false)});
 qa('.wp-theme-primary-menu a',nav).forEach(function(a){a.addEventListener('click',function(){if(!mobile())return;var li=a.parentElement;if(!li||!q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li))setOpen(false)})});
 D.addEventListener('keydown',function(e){if(e.key==='Escape')setOpen(false)});
 var timer=0;function sync(){clearTimeout(timer);timer=setTimeout(function(){move();headerBottom();if(!mobile())setOpen(false)},30)}window.addEventListener('resize',sync,{passive:true});window.addEventListener('orientationchange',sync,{passive:true});
 move();setOpen(false);headerBottom();
}
function boot(){removeCookieUi();setupMenu();hydrateGalleries();setTimeout(function(){removeCookieUi();hydrateGalleries();headerBottom()},400);setTimeout(removeCookieUi,1300)}
if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
