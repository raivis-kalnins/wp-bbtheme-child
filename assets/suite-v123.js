/* WP BBTheme child suite 3.8.11.23 - authoritative hero sources + external pagination. */
(function(){
  'use strict';
  var D=document,W=window;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function heroBlocks(){return qa('#wp-theme-main .wpbb-swiper--hero');}
  function swiperElement(block){
    if(!block)return null;
    if(block.matches&&block.matches('.swiper'))return block;
    var nested=block.querySelector&&block.querySelector('.swiper');
    if(nested)return nested;
    return block.querySelector&&block.querySelector('.swiper-wrapper')?block:null;
  }
  function uniqueSlides(swiperEl){
    if(!swiperEl)return [];
    var all=qa('.swiper-wrapper > .swiper-slide',swiperEl);
    if(!all.length)all=qa('.swiper-slide',swiperEl);
    var seen={},result=[];
    all.forEach(function(slide,index){
      if(slide.classList.contains('swiper-slide-duplicate'))return;
      var key=slide.getAttribute('data-swiper-slide-index');
      if(key===null||key==='')key='dom-'+index;
      if(seen[key])return;
      seen[key]=true;result.push(slide);
    });
    return result.length?result:all;
  }
  function liveSwiper(block,swiperEl){return (swiperEl&&swiperEl.swiper)||(block&&block.swiper)||null;}
  function activeIndex(sw,count){
    if(!count)return 0;
    var value=sw&&typeof sw.realIndex==='number'?sw.realIndex:(sw&&typeof sw.activeIndex==='number'?sw.activeIndex:0);
    value=parseInt(value,10);if(!isFinite(value)||value<0)value=0;return value%count;
  }
  function paint(pager,sw,count){
    var active=activeIndex(sw,count);
    qa('.wpbb-v123-hero-bullet',pager).forEach(function(bullet,index){
      var on=index===active;bullet.classList.toggle('is-active',on);
      if(on)bullet.setAttribute('aria-current','true');else bullet.removeAttribute('aria-current');
    });
  }
  function bindPager(block,swiperEl,pager,count){
    if(!pager.dataset.wpbbV123Click){
      pager.dataset.wpbbV123Click='1';
      pager.addEventListener('click',function(event){
        var bullet=event.target&&event.target.closest?event.target.closest('.wpbb-v123-hero-bullet'):null;
        if(!bullet)return;
        var index=parseInt(bullet.getAttribute('data-slide'),10)||0;
        var sw=liveSwiper(block,swiperElement(block));
        if(!sw)return;
        try{if(typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(typeof sw.slideTo==='function')sw.slideTo(index);}catch(e){}
        paint(pager,sw,count);
      });
    }
    var sw=liveSwiper(block,swiperEl);
    if(sw&&pager._wpbbV123Swiper!==sw){
      pager._wpbbV123Swiper=sw;
      if(typeof sw.on==='function'){
        var update=function(){paint(pager,sw,count);};
        try{sw.on('slideChange',update);sw.on('realIndexChange',update);sw.on('transitionEnd',update);}catch(e){}
      }
    }
    paint(pager,sw,count);
  }
  function ensurePager(block,swiperEl){
    var slides=uniqueSlides(swiperEl),count=slides.length;if(count<2)return;
    var pager=block.nextElementSibling;
    if(!pager||!pager.classList.contains('wpbb-v123-hero-pagination')){
      pager=D.createElement('div');
      pager.className='wpbb-v123-hero-pagination';
      pager.setAttribute('role','group');
      pager.setAttribute('aria-label','Hero pagination');
      if(block.parentNode)block.parentNode.insertBefore(pager,block.nextSibling);
    }
    if(pager.children.length!==count){
      pager.innerHTML='';
      for(var i=0;i<count;i++){
        var button=D.createElement('button');button.type='button';button.className='wpbb-v123-hero-bullet';
        button.setAttribute('data-slide',String(i));button.setAttribute('aria-label','Go to slide '+(i+1));pager.appendChild(button);
      }
    }
    block.classList.add('wpbb-v123-has-external-pager');
    bindPager(block,swiperEl,pager,count);
  }
  function forceHeroSources(block,swiperEl){
    var cfg=W.wpbbSuiteV123||{},urls=Array.isArray(cfg.heroUrls)?cfg.heroUrls.filter(Boolean):[];
    var slides=qa('.swiper-wrapper > .swiper-slide',swiperEl);
    if(!slides.length)slides=qa('.swiper-slide',swiperEl);
    slides.forEach(function(slide,domIndex){
      var img=slide.querySelector('.wpbb-swiper-slide__media img');if(!img)return;
      var raw=slide.getAttribute('data-swiper-slide-index'),index=parseInt(raw,10);
      if(!isFinite(index)||index<0)index=domIndex;
      if(urls.length){
        var wanted=urls[index%urls.length];
        if(wanted&&img.getAttribute('src')!==wanted)img.setAttribute('src',wanted);
      }
      img.removeAttribute('srcset');img.removeAttribute('sizes');
      img.decoding='async';
      if(index===0){img.loading='eager';try{img.fetchPriority='high';}catch(e){img.setAttribute('fetchpriority','high');}}
    });
  }
  function tuneHero(block){
    var el=swiperElement(block);if(!el)return;
    forceHeroSources(block,el);ensurePager(block,el);
    var sw=liveSwiper(block,el);
    if(sw&&sw.params){
      try{sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;}catch(e){}
    }
  }
  function tuneAll(){heroBlocks().forEach(tuneHero);}
  ready(function(){
    tuneAll();[120,420,900,1800].forEach(function(ms){setTimeout(tuneAll,ms);});
    if('MutationObserver' in W){
      try{var root=D.querySelector('#wp-theme-main')||D.body;if(root){var queued=false;new MutationObserver(function(){if(queued)return;queued=true;setTimeout(function(){queued=false;tuneAll();},60);}).observe(root,{childList:true,subtree:true});}}catch(e){}
    }
  });
  W.addEventListener('load',tuneAll,{once:true});
})();
