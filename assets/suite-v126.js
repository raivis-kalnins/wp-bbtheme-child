/* WP BBTheme child suite 3.8.11.26 - runtime component marking, hero sources and pagination. */
(function(){
  'use strict';
  var W=window,D=document;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function isCell(el){if(!el||!el.classList)return false;return el.classList.contains('wpbb-column')||el.classList.contains('wp-block-wpbb-column')||/(^|\s)col-(?:\S+)/.test(el.className||'');}
  function cellFor(node,scope){var p=node;while(p&&p!==scope&&p!==D.body){if(isCell(p))return p;p=p.parentElement;}return node;}
  function markRows(selector,className,scopeSelector,cellClass){
    var scope=scopeSelector?D.querySelector(scopeSelector):D.querySelector('#wp-theme-main');if(!scope)return;
    qa(selector,scope).forEach(function(card){
      var cell=cellFor(card,scope),row=cell&&cell.parentElement;if(!row||!scope.contains(row))return;
      row.classList.add(className);
      if(cellClass)cell.classList.add(cellClass);
    });
  }
  function normaliseGrids(){
    var main=D.querySelector('#wp-theme-main');if(!main)return;
    markRows('.wp-theme-process-section .wp-theme-process-card','wpbb-v126-process-grid','#wp-theme-main');
    qa('.wp-theme-process-section .wp-theme-process-card',main).forEach(function(card){card.classList.add('wpbb-v126-process-card');});
    markRows('.wp-theme-services-section .wpbb-icon-card, .wp-theme-industries-section .wpbb-icon-card, .wp-theme-case-studies-section .wp-theme-case-card','wpbb-v126-card-grid','#wp-theme-main');
    markRows('.wpbb-sector-proof-band .wpbb-sector-proof-card','wpbb-v126-proof-grid','#wp-theme-main');
    markRows('.wp-theme-home-stats .wpbb-fun-fact, .wp-theme-home-stats .wp-theme-sector-proof__item','wpbb-v126-stat-grid','#wp-theme-main');
    markRows('.wp-theme-home-product-catalogue .wpbb-catalogue-card','wpbb-v126-catalogue-grid','#wp-theme-main','wpbb-v126-catalogue-cell');
  }

  function heroBlocks(){return qa('#wp-theme-main .wpbb-swiper--hero');}
  function swiperElement(block){
    if(!block)return null;
    if(block.classList&&block.classList.contains('swiper'))return block;
    var el=block.querySelector('.swiper');
    if(el)return el;
    if(block.querySelector('.swiper-wrapper'))return block;
    return null;
  }
  function liveSwiper(block,el){return (el&&el.swiper)||(block&&block.swiper)||(block&&block.querySelector('.swiper')&&block.querySelector('.swiper').swiper)||null;}
  function realSlides(el){
    var list=qa('.swiper-wrapper > .swiper-slide',el);if(!list.length)list=qa('.swiper-slide',el);
    var map={},out=[];
    list.forEach(function(slide,i){
      if(slide.classList.contains('swiper-slide-duplicate')&&!slide.hasAttribute('data-swiper-slide-index'))return;
      var raw=slide.getAttribute('data-swiper-slide-index'),key=raw!==null?'i'+raw:'d'+i;
      if(map[key])return;map[key]=1;out.push(slide);
    });
    return out;
  }
  function sourceIndex(slide,domIndex){var raw=slide&&slide.getAttribute('data-swiper-slide-index'),i=parseInt(raw,10);return isFinite(i)&&i>=0?i:domIndex;}
  function forceHeroSources(block,el){
    var cfg=W.wpbbSuiteV126||{},urls=Array.isArray(cfg.heroUrls)?cfg.heroUrls:[];
    if(!urls.length)return;
    var slides=qa('.swiper-wrapper > .swiper-slide',el);if(!slides.length)slides=qa('.swiper-slide',el);
    slides.forEach(function(slide,domIndex){
      var img=slide.querySelector('.wpbb-swiper-slide__media img, img.wpbb-swiper-slide__image');if(!img)return;
      var i=sourceIndex(slide,domIndex),wanted=urls[i%urls.length];if(!wanted)return;
      if(img.getAttribute('src')!==wanted)img.setAttribute('src',wanted);
      ['srcset','sizes','data-src','data-srcset','data-sizes','data-lazy-src','data-lazy-srcset'].forEach(function(name){img.removeAttribute(name);});
      img.decoding='async';img.loading=i===0?'eager':'lazy';
      if(i===0){try{img.fetchPriority='high';}catch(e){img.setAttribute('fetchpriority','high');}}
    });
  }
  function activeIndex(sw,count){var i=sw&&Number.isFinite(sw.realIndex)?sw.realIndex:(sw&&Number.isFinite(sw.activeIndex)?sw.activeIndex:0);i=((i%count)+count)%count;return i;}
  function paint(pager,sw,count){var active=activeIndex(sw,count);qa('.wpbb-v126-hero-bullet',pager).forEach(function(b,i){var on=i===active;b.classList.toggle('is-active',on);b.setAttribute('aria-current',on?'true':'false');});}
  function removeLegacyPagers(block){
    var selector='.wpbb-v123-hero-pagination,.wpbb-v124-hero-pagination,.wpbb-v121-pagination,.wpbb-v120-pagination';
    qa(selector,block).forEach(function(old){if(!old.classList.contains('wpbb-v126-hero-pagination')&&old.parentNode)old.parentNode.removeChild(old);});
    var parent=block&&block.parentElement;
    if(parent)qa(selector,parent).forEach(function(old){if(!block.contains(old)&&!old.classList.contains('wpbb-v126-hero-pagination')&&old.parentNode)old.parentNode.removeChild(old);});
  }
  function ensurePager(block,el){
    var count=realSlides(el).length;if(count<2){block.classList.remove('wpbb-v126-has-pagination');return;}
    removeLegacyPagers(block);
    var pager=block.querySelector(':scope > .wpbb-v126-hero-pagination');
    if(!pager){pager=D.createElement('div');pager.className='wpbb-v126-hero-pagination';block.appendChild(pager);}
    block.classList.add('wpbb-v126-has-pagination');
    pager.setAttribute('role','group');pager.setAttribute('aria-label','Hero pagination');
    if(qa('.wpbb-v126-hero-bullet',pager).length!==count){
      pager.innerHTML='';
      for(var i=0;i<count;i++){
        var b=D.createElement('button');b.type='button';b.className='wpbb-v126-hero-bullet';b.setAttribute('data-wpbb-v126-slide',String(i));b.setAttribute('aria-label','Go to slide '+(i+1));pager.appendChild(b);
      }
    }
    var sw=liveSwiper(block,el);
    if(!pager.dataset.wpbbV126Click){
      pager.dataset.wpbbV126Click='1';
      pager.addEventListener('click',function(event){
        var b=event.target&&event.target.closest?event.target.closest('[data-wpbb-v126-slide]'):null;if(!b)return;
        var i=parseInt(b.getAttribute('data-wpbb-v126-slide'),10)||0,live=liveSwiper(block,swiperElement(block));
        if(live){try{if(typeof live.slideToLoop==='function')live.slideToLoop(i);else if(typeof live.slideTo==='function')live.slideTo(i);}catch(e){}}
        paint(pager,live,count);
      });
    }
    if(sw&&pager._wpbbV126Swiper!==sw){
      pager._wpbbV126Swiper=sw;
      if(typeof sw.on==='function'){
        var update=function(){paint(pager,sw,count);};
        try{sw.on('slideChange',update);sw.on('realIndexChange',update);sw.on('transitionEnd',update);}catch(e){}
      }
      try{
        if(sw.params){sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;}
        if(sw.autoplay&&typeof sw.autoplay.start==='function'&&!sw.autoplay.running)sw.autoplay.start();
      }catch(e){}
    }
    paint(pager,sw,count);
  }
  function tuneHero(block){var el=swiperElement(block);if(!el)return;forceHeroSources(block,el);ensurePager(block,el);}
  function tuneAll(){normaliseGrids();heroBlocks().forEach(tuneHero);}
  ready(function(){
    tuneAll();[100,350,800,1500,2600].forEach(function(ms){setTimeout(tuneAll,ms);});
    if('MutationObserver' in W){
      try{var root=D.querySelector('#wp-theme-main')||D.body,queued=false;if(root)new MutationObserver(function(){if(queued)return;queued=true;setTimeout(function(){queued=false;tuneAll();},90);}).observe(root,{childList:true,subtree:true,attributes:true,attributeFilter:['src','srcset']});}catch(e){}
    }
  });
  W.addEventListener('load',tuneAll,{once:true});
})();
