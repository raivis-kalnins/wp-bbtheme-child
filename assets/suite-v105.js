(function(){
'use strict';
var D=document;
function q(s,r){return (r||D).querySelector(s)}
function qa(s,r){return Array.prototype.slice.call((r||D).querySelectorAll(s))}
function mobile(){return window.matchMedia('(max-width:991.98px)').matches}
function stripIds(root){if(!root)return; if(root.id)root.removeAttribute('id');qa('[id]',root).forEach(function(el){el.removeAttribute('id')})}
function legalAndContactClasses(){
 var path=(location.pathname||'').toLowerCase(),title=(D.title||'').toLowerCase();
 if(/privacy|terms|condition|cookie|cookies|legal/.test(path+' '+title)||q('#wp-theme-main .wp-theme-legal-section'))D.body.classList.add('wpbb-legal-page');
 if(/\/contact\/?$/.test(path)||q('#wp-theme-main .wp-theme-contact-section,#wp-theme-main .wpbb-jobs-contact-main'))D.body.classList.add('wpbb-contact-page');
 if(q('#wp-theme-main .wpbb-jobs-contact-main'))D.body.classList.add('wpbb-jobs-contact-page');
}
function removeCookieUi(){qa('.wp-theme-cookie-banner,.wp-theme-cookie-settings,[data-cookie-settings-dialog]').forEach(function(el){el.remove()});qa('button[data-cookie-settings]').forEach(function(el){el.remove()})}
function hydrateGalleries(){
 var cfg=window.wpbbSuiteV101||{};var imgs=Array.isArray(cfg.gallery)?cfg.gallery:[];
 if(imgs.length){qa('.wp-theme-gallery-section .wpbb-swiper--gallery .wpbb-swiper-slide').forEach(function(slide,i){var info=imgs[i%imgs.length];if(!info||!info.url)return;var media=q('.wpbb-swiper-slide__media',slide);if(!media){media=D.createElement('div');media.className='wpbb-swiper-slide__media';slide.insertBefore(media,slide.firstChild)}var im=q('img',media);if(!im){im=D.createElement('img');media.appendChild(im)}im.src=info.url;im.alt=info.title||'';im.loading='eager';im.decoding='async';im.removeAttribute('srcset');im.removeAttribute('sizes');im.style.opacity='1';im.style.visibility='visible'})}
 var payload=window.wpbbChildSectorGalleries||{};var path=(location.pathname||'').replace(/\/$/,'');var data=payload[path]||payload[location.pathname]||null;
 qa('.wp-theme-item-gallery--single').forEach(function(gallery){var stage=q('.wp-theme-item-gallery__stage img',gallery),thumbs=qa('.wp-theme-item-gallery__thumb',gallery);if(data&&Array.isArray(data.images)&&data.images.length){if(stage){stage.src=data.images[0].display||data.images[0].full||stage.src;stage.loading='eager';stage.removeAttribute('srcset');stage.removeAttribute('sizes')}thumbs.forEach(function(b,i){var x=data.images[i],im=q('img',b);if(!x){b.hidden=true;return}b.hidden=false;if(!im){im=D.createElement('img');b.appendChild(im)}im.src=x.thumb||x.display||x.full;im.alt='';im.loading='eager';im.decoding='async'})}qa('img',gallery).forEach(function(im){im.style.opacity='1';im.style.visibility='visible';im.style.filter='none'})})
}
function headerBottom(){var h=q('[data-site-header],.wp-theme-site-header');var n=h?Math.max(0,Math.round(h.getBoundingClientRect().bottom)):68;D.documentElement.style.setProperty('--suite-v105-nav-top',n+'px');return n}
function setupMenu(){
 var source=q('.wp-theme-primary-navigation .wp-theme-primary-menu')||q('.wp-theme-primary-menu');if(!source)return;
 var actions=q('.wp-theme-header-actions')||q('.wp-theme-header-main__inner');if(!actions)return;
 var old=q('.wp-theme-menu-toggle,.wpbb-v104-menu-toggle,.wpbb-v105-menu-toggle',actions);
 var btn=D.createElement('button');btn.type='button';btn.className='wpbb-v105-menu-toggle';btn.setAttribute('aria-expanded','false');btn.setAttribute('aria-label','Open menu');btn.setAttribute('aria-controls','wpbb-v105-mobile-drawer');
 if(old&&old.parentNode)old.parentNode.replaceChild(btn,old);else actions.appendChild(btn);
 var drawer=q('#wpbb-v105-mobile-drawer');if(drawer)drawer.remove();drawer=D.createElement('nav');drawer.id='wpbb-v105-mobile-drawer';drawer.className='wpbb-v105-mobile-drawer';drawer.setAttribute('aria-label','Mobile navigation');drawer.setAttribute('aria-hidden','true');
 var menu=source.cloneNode(true);stripIds(menu);menu.className='wpbb-v105-drawer-menu';qa('.wp-theme-submenu-toggle,.wpbb-v97-submenu-toggle,.wpbb-suite-v98-submenu-toggle,.wpbb-jobs-v97-submenu-toggle,.wpbb-v99-submenu-toggle,.wpbb-v100-submenu-toggle,.wpbb-v101-submenu-toggle,.wpbb-v104-submenu-toggle,.wpbb-v105-submenu-toggle',menu).forEach(function(x){x.remove()});
 qa('li',menu).forEach(function(li){li.classList.remove('is-submenu-open','is-mega-open','is-v99-submenu-open','is-v100-submenu-open','is-v101-submenu-open','is-v104-submenu-open','is-v105-submenu-open');var child=q(':scope > .sub-menu, :scope > .wp-theme-mega-menu',li);if(!child)return;var t=D.createElement('button');t.type='button';t.className='wpbb-v105-submenu-toggle';t.setAttribute('aria-expanded','false');t.setAttribute('aria-label','Open submenu');li.insertBefore(t,child);t.addEventListener('click',function(e){e.preventDefault();e.stopImmediatePropagation();var open=!li.classList.contains('is-v105-submenu-open');li.classList.toggle('is-v105-submenu-open',open);t.setAttribute('aria-expanded',open?'true':'false');t.setAttribute('aria-label',open?'Close submenu':'Open submenu')},true)});
 drawer.appendChild(menu);D.body.appendChild(drawer);
 var overlay=q('.wpbb-v105-mobile-overlay');if(overlay)overlay.remove();overlay=D.createElement('div');overlay.className='wpbb-v105-mobile-overlay';overlay.setAttribute('aria-hidden','true');D.body.appendChild(overlay);
 function closeSubs(){qa('.is-v105-submenu-open',drawer).forEach(function(li){li.classList.remove('is-v105-submenu-open');var t=q(':scope > .wpbb-v105-submenu-toggle',li);if(t)t.setAttribute('aria-expanded','false')})}
 function setOpen(open){open=!!open&&mobile();D.body.classList.toggle('wpbb-v105-menu-open',open);D.body.classList.remove('wpbb-v104-menu-open','wpbb-v101-menu-open','wpbb-v100-menu-open','wpbb-v99-menu-open','wpbb-suite-v98-menu-open','wpbb-sector-mobile-open','wp-theme-menu-open');btn.setAttribute('aria-expanded',open?'true':'false');btn.setAttribute('aria-label',open?'Close menu':'Open menu');drawer.setAttribute('aria-hidden',open?'false':'true');overlay.setAttribute('aria-hidden',open?'false':'true');if(!open)closeSubs();headerBottom()}
 btn.addEventListener('click',function(e){e.preventDefault();e.stopImmediatePropagation();setOpen(!D.body.classList.contains('wpbb-v105-menu-open'))},true);overlay.addEventListener('click',function(){setOpen(false)});qa('a',menu).forEach(function(a){a.addEventListener('click',function(){setOpen(false)})});D.addEventListener('keydown',function(e){if(e.key==='Escape')setOpen(false)});
 var timer=0;function sync(){clearTimeout(timer);timer=setTimeout(function(){headerBottom();if(!mobile())setOpen(false)},40)}window.addEventListener('resize',sync,{passive:true});window.addEventListener('orientationchange',sync,{passive:true});headerBottom();setOpen(false);
}
function normalizeWoo(){
 qa('#wp-theme-main.wp-theme-woo-legacy--catalog .iws-product-filter label').forEach(function(label){if(q('input[type="checkbox"],input[type="radio"]',label))label.classList.add('wpbb-v105-filter-pill')});
 qa('#wp-theme-main.wp-theme-woo-legacy--catalog li.product').forEach(function(card){var im=q('img',card);if(!im)return;var a=im.closest('a');if(a&&card.contains(a))a.classList.add('wpbb-v105-product-image-link')});
 qa('#wp-theme-main.wp-theme-woo-legacy--account .woocommerce').forEach(function(w){if(q('.woocommerce-MyAccount-navigation',w))w.classList.add('wpbb-v105-account-grid')});
}
function moveBlogActions(){
 qa('#wp-theme-main .wp-theme-blog-section').forEach(function(section){if(q('.wpbb-v105-blog-more',section))return;var heading=q('.wp-theme-blog-section-heading',section);if(!heading)return;var candidates=qa('a,.wp-block-button,.wp-block-buttons',heading);var action=candidates.find(function(el){return /view\s+all|all\s+(articles|insights|news|advice)|more\s+(articles|insights|news|advice)/i.test((el.textContent||'').trim())});if(!action)return;while(action.parentElement&&action.parentElement!==heading&&action.parentElement.children.length===1&&/wp-block-buttons/.test(action.parentElement.className||''))action=action.parentElement;var host=q(':scope > .container',section)||q('.container',section)||section;var wrap=D.createElement('div');wrap.className='wpbb-v105-blog-more';wrap.appendChild(action);host.appendChild(wrap)})
}
function watchDynamic(){var root=q('#wp-theme-main');if(!root||!window.MutationObserver)return;var timer=0;var ob=new MutationObserver(function(){clearTimeout(timer);timer=setTimeout(function(){normalizeWoo();moveBlogActions();hydrateGalleries()},80)});ob.observe(root,{childList:true,subtree:true})}
function boot(){legalAndContactClasses();removeCookieUi();setupMenu();normalizeWoo();moveBlogActions();hydrateGalleries();watchDynamic();setTimeout(function(){legalAndContactClasses();removeCookieUi();normalizeWoo();moveBlogActions();hydrateGalleries();headerBottom()},450);setTimeout(function(){removeCookieUi();normalizeWoo();moveBlogActions()},1400)}
if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
