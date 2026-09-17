/* WP BBTheme child suite 3.8.11.21 - hero source-quality guard and deterministic pagination fallback. */
(function(){
  'use strict';
  var D=document;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function realSlides(swiperEl){var slides=qa('.swiper-wrapper > .swiper-slide',swiperEl).filter(function(slide){return !slide.classList.contains('swiper-slide-duplicate');});return slides.length?slides:qa('.swiper-slide',swiperEl);}
  function currentIndex(sw){if(!sw)return 0;var value=typeof sw.realIndex==='number'?sw.realIndex:sw.activeIndex;return typeof value==='number'&&value>=0?value:0;}
  function swiperFor(node){var direct=node&&node.closest?node.closest('.swiper'):null;if(direct&&direct.swiper)return direct.swiper;var block=node&&node.closest?node.closest('.wpbb-swiper-block,.wpbb-swiper--hero'):null;var el=block&&block.matches&&block.matches('.swiper')?block:(block&&block.querySelector?block.querySelector('.swiper'):null);return el&&el.swiper?el.swiper:null;}
  function paintFallback(pagination,sw,count){
    if(!pagination||count<2)return;
    if(!pagination.querySelector('.swiper-pagination-bullet')){
      pagination.innerHTML='';
      for(var i=0;i<count;i++){var bullet=D.createElement('button');bullet.type='button';bullet.className='swiper-pagination-bullet';bullet.setAttribute('aria-label','Go to slide '+(i+1));bullet.setAttribute('data-wpbb-v121-slide',String(i));pagination.appendChild(bullet);}
    }
    var update=function(){var index=currentIndex(sw)%count;qa('.swiper-pagination-bullet',pagination).forEach(function(bullet,i){var active=i===index;bullet.classList.toggle('swiper-pagination-bullet-active',active);if(active)bullet.setAttribute('aria-current','true');else bullet.removeAttribute('aria-current');});};
    if(!pagination.dataset.wpbbV121ClickBound){pagination.dataset.wpbbV121ClickBound='1';pagination.addEventListener('click',function(event){var bullet=event.target&&event.target.closest?event.target.closest('[data-wpbb-v121-slide]'):null;if(!bullet)return;var index=parseInt(bullet.getAttribute('data-wpbb-v121-slide'),10)||0;var live=swiperFor(pagination);if(live){try{if(typeof live.slideToLoop==='function')live.slideToLoop(index);else if(typeof live.slideTo==='function')live.slideTo(index);}catch(e){}}});}
    if(sw&&typeof sw.on==='function'&&!pagination.dataset.wpbbV121ChangeBound){pagination.dataset.wpbbV121ChangeBound='1';try{sw.on('slideChange',update);sw.on('realIndexChange',update);}catch(e){}}
    update();
  }
  function ensurePagination(swiperEl){
    if(!swiperEl)return;var block=swiperEl.closest('.wpbb-swiper-block')||swiperEl.parentElement;if(!block)return;var slides=realSlides(swiperEl),count=slides.length;if(count<2)return;
    var pagination=block.querySelector('.swiper-pagination');
    if(!pagination){pagination=D.createElement('div');pagination.className='swiper-pagination wpbb-v121-pagination';pagination.setAttribute('aria-label','Hero pagination');swiperEl.appendChild(pagination);}else{pagination.classList.add('wpbb-v121-pagination');if(!swiperEl.contains(pagination))swiperEl.appendChild(pagination);}
    var sw=swiperEl.swiper;
    if(sw&&sw.params){try{var params=sw.params.pagination&&typeof sw.params.pagination==='object'?sw.params.pagination:{};params.el=pagination;params.clickable=true;sw.params.pagination=params;if(sw.originalParams){var original=sw.originalParams.pagination&&typeof sw.originalParams.pagination==='object'?sw.originalParams.pagination:{};original.el=pagination;original.clickable=true;sw.originalParams.pagination=original;}if(sw.pagination){sw.pagination.el=pagination;if(!pagination.dataset.wpbbV121NativeInit){pagination.dataset.wpbbV121NativeInit='1';if(typeof sw.pagination.init==='function')sw.pagination.init();if(typeof sw.pagination.render==='function')sw.pagination.render();}if(typeof sw.pagination.update==='function')sw.pagination.update();}}catch(e){}}
    paintFallback(pagination,sw,count);
  }
  function tuneHeroImages(){qa('#wp-theme-main .wpbb-swiper--hero .wpbb-swiper-slide__media img').forEach(function(img,index){var src=img.getAttribute('src')||'';if(src.indexOf('/hero-v118/')>-1||(img.currentSrc||'').indexOf('/hero-v118/')>-1){img.removeAttribute('srcset');img.removeAttribute('sizes');}if(index===0){img.loading='eager';try{img.fetchPriority='high';}catch(e){img.setAttribute('fetchpriority','high');}}img.decoding='async';});}
  function tuneHeroes(){tuneHeroImages();qa('#wp-theme-main .wpbb-swiper--hero .swiper').forEach(function(el){ensurePagination(el);var sw=el.swiper;if(sw&&sw.params){try{sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;if(sw.autoplay&&typeof sw.autoplay.start==='function'&&!sw.autoplay.running)sw.autoplay.start();}catch(e){}}});}
  ready(function(){tuneHeroes();setTimeout(tuneHeroes,180);setTimeout(tuneHeroes,650);setTimeout(tuneHeroes,1600);if('MutationObserver' in window){try{var root=D.querySelector('#wp-theme-main')||D.body;if(root)new MutationObserver(function(){tuneHeroes();}).observe(root,{childList:true,subtree:true});}catch(e){}}});
  window.addEventListener('load',tuneHeroes,{once:true});
})();
