(function(){
'use strict';
var D=document,B=D.body,CFG=window.wpbbSuiteV101||{};
function q(s,r){return (r||D).querySelector(s)}
function qa(s,r){return Array.prototype.slice.call((r||D).querySelectorAll(s))}
function isMobile(){return window.matchMedia('(max-width:991.98px)').matches}
function navTop(){var h=q('[data-site-header],.wp-theme-site-header');var top=h?Math.max(0,Math.round(h.getBoundingClientRect().bottom)):76;D.documentElement.style.setProperty('--suite-v101-nav-top',top+'px')}
function findNav(){var cs=qa('.wp-theme-primary-navigation,[data-primary-navigation],#wp-theme-mobile-navigation');return cs.find(function(n){return q('.wp-theme-primary-menu',n)})||cs[0]||null}
function closeSubs(nav){if(!nav)return;qa('.is-v101-submenu-open',nav).forEach(function(li){li.classList.remove('is-v101-submenu-open');var t=q(':scope > .wpbb-v101-submenu-toggle',li);if(t)t.setAttribute('aria-expanded','false')})}
function setupMenu(){
 var btn=q('.wp-theme-menu-toggle'),nav=findNav();if(!btn||!nav)return;
 nav.classList.add('wpbb-v101-mobile-nav');nav.removeAttribute('hidden');
 var fresh=btn.cloneNode(true);fresh.classList.add('wpbb-v101-menu-toggle');fresh.removeAttribute('hidden');btn.parentNode.replaceChild(fresh,btn);btn=fresh;
 qa('.wp-theme-submenu-toggle,.wpbb-v97-submenu-toggle,.wpbb-suite-v98-submenu-toggle,.wpbb-jobs-v97-submenu-toggle,.wpbb-v99-submenu-toggle,.wpbb-v100-submenu-toggle,.wpbb-v101-submenu-toggle',nav).forEach(function(x){x.remove()});
 qa('.wp-theme-primary-menu li',nav).forEach(function(li){li.classList.remove('is-submenu-open','is-mega-open','is-v99-submenu-open','is-v100-submenu-open','is-v101-submenu-open');var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child)return;var t=D.createElement('button');t.type='button';t.className='wpbb-v101-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Toggle submenu');li.insertBefore(t,child);t.addEventListener('click',function(e){if(!isMobile())return;e.preventDefault();e.stopPropagation();var open=!li.classList.contains('is-v101-submenu-open');li.classList.toggle('is-v101-submenu-open',open);t.setAttribute('aria-expanded',open?'true':'false')})});
 var overlay=q('.wpbb-v101-mobile-overlay');if(!overlay){overlay=D.createElement('div');overlay.className='wpbb-v101-mobile-overlay';overlay.setAttribute('aria-hidden','true');D.body.appendChild(overlay)}
 function setOpen(open){open=!!open&&isMobile();B.classList.toggle('wpbb-v101-menu-open',open);B.classList.remove('wpbb-v100-menu-open','wpbb-v99-menu-open','wpbb-suite-v98-menu-open','wpbb-sector-mobile-open','wp-theme-menu-open');btn.setAttribute('aria-expanded',open?'true':'false');nav.setAttribute('aria-hidden',isMobile()?(open?'false':'true'):'false');overlay.setAttribute('aria-hidden',open?'false':'true');if(!open)closeSubs(nav);navTop()}
 btn.addEventListener('click',function(e){if(!isMobile())return;e.preventDefault();e.stopPropagation();setOpen(!B.classList.contains('wpbb-v101-menu-open'))});
 overlay.addEventListener('click',function(){setOpen(false)});
 qa('.wp-theme-primary-menu a',nav).forEach(function(a){a.addEventListener('click',function(){if(!isMobile())return;var li=a.parentElement;if(!li||!q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li))setOpen(false)})});
 D.addEventListener('keydown',function(e){if(e.key==='Escape')setOpen(false)});
 function resize(){navTop();if(!isMobile())setOpen(false)} window.addEventListener('resize',resize,{passive:true});window.addEventListener('orientationchange',resize,{passive:true});
 setOpen(false);navTop();
}
function hydrateHomeGallery(){var imgs=Array.isArray(CFG.gallery)?CFG.gallery:[];if(!imgs.length)return;qa('.wp-theme-gallery-section .wpbb-swiper--gallery .wpbb-swiper-slide').forEach(function(slide,i){var info=imgs[i%imgs.length];if(!info||!info.url)return;var media=q('.wpbb-swiper-slide__media',slide);if(!media){media=D.createElement('div');media.className='wpbb-swiper-slide__media';slide.insertBefore(media,slide.firstChild)}var im=q('img',media);if(!im){im=D.createElement('img');media.appendChild(im)}im.src=info.url;im.alt=info.title||q('.wpbb-swiper-slide__title',slide)?.textContent||'';im.loading='eager';im.decoding='async';im.removeAttribute('srcset');im.removeAttribute('sizes')})}
function hydrateSingleGallery(){var payload=window.wpbbChildSectorGalleries||{};var path=location.pathname.replace(/\/$/,'');var data=payload[path]||payload[location.pathname]||null;qa('.wp-theme-item-gallery--single').forEach(function(gallery){var stage=q('.wp-theme-item-gallery__stage img',gallery),thumbs=qa('.wp-theme-item-gallery__thumb',gallery);if(data&&Array.isArray(data.images)&&data.images.length){if(stage){stage.src=data.images[0].display||data.images[0].full;stage.loading='eager'}thumbs.forEach(function(b,i){var im=q('img',b),x=data.images[i];if(!x){b.style.display='none';return}if(!im){im=D.createElement('img');b.appendChild(im)}im.src=x.thumb||x.display||x.full;im.alt='';im.loading='eager';im.decoding='async'})}else{thumbs.forEach(function(b){var im=q('img',b);if(im){im.loading='eager';im.style.opacity='1';im.style.visibility='visible';setTimeout(function(){if(im.complete&&!im.naturalWidth)b.style.display='none'},600)}else b.style.display='none'})}})}
function cleanupImages(){qa('.wp-theme-gallery-section img,.wp-theme-item-gallery--single img').forEach(function(im){im.style.opacity='1';im.style.visibility='visible';im.style.filter='none'})}
function boot(){setupMenu();hydrateHomeGallery();hydrateSingleGallery();cleanupImages();setTimeout(function(){hydrateHomeGallery();hydrateSingleGallery();cleanupImages()},500)}
if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();