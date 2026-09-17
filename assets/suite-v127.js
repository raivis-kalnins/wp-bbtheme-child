/* WP BBTheme child suite 3.8.11.27 - final live-regression hardening. */
(function(W,D){'use strict';
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return[];}}
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function isCell(el){if(!el||!el.classList)return false;return el.classList.contains('wpbb-column')||el.classList.contains('wp-block-wpbb-column')||/(^|\s)col-(?:\S+)/.test(el.className||'');}
  function directCells(row){return row?Array.prototype.slice.call(row.children||[]).filter(isCell):[];}
  var CARD_SELECTOR='.wpbb-icon-card,.wp-theme-sector-card,.wp-theme-process-card,.wp-theme-case-card,.wpbb-sector-proof-card,.card';
  function firstCard(cell){if(!cell)return null;var direct=Array.prototype.slice.call(cell.children||[]);for(var i=0;i<direct.length;i++){if(direct[i].matches&&direct[i].matches(CARD_SELECTOR))return direct[i];}return cell.querySelector(CARD_SELECTOR);}
  function excludedRow(row){return !!(row&&row.closest&&row.closest('.wp-theme-home-product-catalogue,.woocommerce,.wp-theme-woo-legacy,.wpbb-swiper,.swiper,.wp-theme-sector-media-text,.wpbb-sector-finder,.wpbb-v97-hero-finder,.wp-theme-contact-form'));
  }
  function markRow(row,kind){
    if(!row||excludedRow(row))return false;
    var cells=directCells(row),count=cells.length;if(count<2||count>4)return false;
    var cards=cells.map(firstCard);if(cards.some(function(c){return !c;}))return false;
    row.classList.add(kind==='process'?'wpbb-v127-process-grid':(kind==='proof'?'wpbb-v127-proof-grid':'wpbb-v127-safe-card-grid'));
    row.setAttribute('data-wpbb-v127-cols',String(count));
    cells.forEach(function(cell,i){
      cell.classList.add('wpbb-v127-grid-cell');
      if(kind==='process')cell.classList.add('wpbb-v127-process-cell');
      cards[i].classList.add('wpbb-v127-grid-card');
      if(kind==='process')cards[i].classList.add('wpbb-v127-process-card');
    });
    return true;
  }
  function normaliseGrids(){
    var main=D.querySelector('#wp-theme-main');if(!main)return;
    qa('.wp-theme-process-section',main).forEach(function(section){
      qa('.row,.wpbb-row',section).some(function(row){return markRow(row,'process');});
    });
    qa('.wpbb-sector-proof-band',main).forEach(function(section){
      qa('.row,.wpbb-row',section).some(function(row){return markRow(row,'proof');});
    });
    qa('.row,.wpbb-row',main).forEach(function(row){
      if(row.classList.contains('wpbb-v127-process-grid')||row.classList.contains('wpbb-v127-proof-grid'))return;
      markRow(row,'safe');
    });
  }

  function heroBlocks(){return qa('#wp-theme-main .wpbb-swiper--hero');}
  function swiperElement(block){
    if(!block)return null;
    if(block.classList&&block.classList.contains('swiper'))return block;
    var el=block.querySelector('.swiper');if(el)return el;
    if(block.querySelector('.swiper-wrapper'))return block;
    return null;
  }
  function liveSwiper(block,el){return (el&&el.swiper)||(block&&block.swiper)||(block&&block.querySelector('.swiper')&&block.querySelector('.swiper').swiper)||null;}
  function configuredUrls(){var cfg=W.wpbbSuiteV127||{},urls=Array.isArray(cfg.heroUrls)?cfg.heroUrls:[];return urls.filter(Boolean);}
  function physicalSlides(el){var list=qa('.swiper-wrapper > .swiper-slide',el);if(!list.length)list=qa('.swiper-slide',el);return list;}
  function uniqueSlideIndexCount(el){
    var seen={};physicalSlides(el).forEach(function(slide){var raw=slide.getAttribute('data-swiper-slide-index'),i=parseInt(raw,10);if(isFinite(i)&&i>=0)seen[i]=1;});
    return Object.keys(seen).length;
  }
  function logicalCount(block,el){
    var urls=configuredUrls(),sw=liveSwiper(block,el),count=Math.max(urls.length,uniqueSlideIndexCount(el));
    if(sw){
      if(sw.virtual&&Array.isArray(sw.virtual.slides))count=Math.max(count,sw.virtual.slides.length);
      if(sw.params&&Array.isArray(sw.params.slides))count=Math.max(count,sw.params.slides.length);
    }
    if(!count)count=physicalSlides(el).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');}).length;
    return Math.max(0,count);
  }
  function forceHeroSources(block,el){
    var urls=configuredUrls();if(!urls.length)return;
    var slides=physicalSlides(el),unique=uniqueSlideIndexCount(el)>1;
    slides.forEach(function(slide,domIndex){
      var img=slide.querySelector('.wpbb-swiper-slide__media img, img.wpbb-swiper-slide__image, img');if(!img)return;
      var raw=parseInt(slide.getAttribute('data-swiper-slide-index'),10),i=(unique&&isFinite(raw)&&raw>=0)?raw:(domIndex%urls.length),wanted=urls[i%urls.length];if(!wanted)return;
      if(img.getAttribute('src')!==wanted)img.setAttribute('src',wanted);
      ['srcset','sizes','data-src','data-srcset','data-sizes','data-lazy-src','data-lazy-srcset'].forEach(function(name){img.removeAttribute(name);});
      img.decoding='async';img.loading=i===0?'eager':'lazy';
      if(i===0){try{img.fetchPriority='high';}catch(e){img.setAttribute('fetchpriority','high');}}
    });
  }
  function activeIndex(sw,count){var i=sw&&Number.isFinite(sw.realIndex)?sw.realIndex:(sw&&Number.isFinite(sw.activeIndex)?sw.activeIndex:0);if(!count)return 0;return ((i%count)+count)%count;}
  function paint(pager,sw,count){var active=activeIndex(sw,count);qa('.wpbb-v127-hero-bullet',pager).forEach(function(b,i){var on=i===active;b.classList.toggle('is-active',on);b.setAttribute('aria-current',on?'true':'false');});}
  function retirePagers(block){
    var selector='.wpbb-v120-pagination,.wpbb-v121-pagination,.wpbb-v123-hero-pagination,.wpbb-v124-hero-pagination,.wpbb-v126-hero-pagination';
    qa(selector,block).forEach(function(old){old.classList.add('wpbb-v127-retired-pager');});
    var parent=block&&block.parentElement;if(parent)qa(selector,parent).forEach(function(old){if(!old.classList.contains('wpbb-v127-hero-pagination'))old.classList.add('wpbb-v127-retired-pager');});
  }
  function ensurePager(block,el){
    var count=logicalCount(block,el);retirePagers(block);
    if(count<2){block.classList.remove('wpbb-v127-has-pagination');var lone=block.querySelector(':scope > .wpbb-v127-hero-pagination');if(lone)lone.remove();return;}
    var pager=block.querySelector(':scope > .wpbb-v127-hero-pagination');
    if(!pager){pager=D.createElement('div');pager.className='wpbb-v127-hero-pagination';block.appendChild(pager);}
    block.classList.add('wpbb-v127-has-pagination');
    pager.setAttribute('role','group');pager.setAttribute('aria-label','Hero pagination');
    if(qa('.wpbb-v127-hero-bullet',pager).length!==count){
      pager.innerHTML='';
      for(var i=0;i<count;i++){var b=D.createElement('button');b.type='button';b.className='wpbb-v127-hero-bullet';b.setAttribute('data-wpbb-v127-slide',String(i));b.setAttribute('aria-label','Go to slide '+(i+1));pager.appendChild(b);}
    }
    if(!pager.dataset.wpbbV127Click){
      pager.dataset.wpbbV127Click='1';
      pager.addEventListener('click',function(event){
        var b=event.target&&event.target.closest?event.target.closest('[data-wpbb-v127-slide]'):null;if(!b)return;
        var i=parseInt(b.getAttribute('data-wpbb-v127-slide'),10)||0,live=liveSwiper(block,swiperElement(block));
        if(live){try{if(typeof live.slideToLoop==='function')live.slideToLoop(i);else if(typeof live.slideTo==='function')live.slideTo(i);}catch(e){}}
        paint(pager,live,count);
      });
    }
    var sw=liveSwiper(block,el);
    if(sw&&pager._wpbbV127Swiper!==sw){
      pager._wpbbV127Swiper=sw;
      if(typeof sw.on==='function'){
        var update=function(){var fresh=logicalCount(block,el);if(fresh!==qa('.wpbb-v127-hero-bullet',pager).length){ensurePager(block,el);return;}paint(pager,sw,fresh);};
        try{sw.on('init',update);sw.on('slidesLengthChange',update);sw.on('slideChange',update);sw.on('realIndexChange',update);sw.on('transitionEnd',update);}catch(e){}
      }
      try{
        if(sw.params){sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;}
        if(sw.autoplay&&typeof sw.autoplay.start==='function'&&!sw.autoplay.running)sw.autoplay.start();
      }catch(e){}
    }
    paint(pager,sw,count);
  }
  function tuneHero(block){var el=swiperElement(block);if(!el)return;forceHeroSources(block,el);ensurePager(block,el);}
  function markAutomotiveHeading(){
    var body=D.body;if(!body||!body.classList.contains('wpbb-sector-premium-automotive'))return;
    qa('#wp-theme-main h1,#wp-theme-main h2,#wp-theme-main h3,#wp-theme-main h4').forEach(function(h){
      var t=String(h.textContent||'').replace(/\s+/g,' ').trim().toLowerCase();
      if(t.indexOf('new cars, used stock, rentals, parts and service without separate websites')===0)h.classList.add('wpbb-v127-auto-industries-heading');
    });
  }
  function tuneAll(){normaliseGrids();markAutomotiveHeading();heroBlocks().forEach(tuneHero);}
  ready(function(){
    tuneAll();[80,220,500,900,1500,2600,4200].forEach(function(ms){setTimeout(tuneAll,ms);});
    if('MutationObserver' in W){
      try{var root=D.querySelector('#wp-theme-main')||D.body,queued=false;if(root)new MutationObserver(function(){if(queued)return;queued=true;setTimeout(function(){queued=false;tuneAll();},90);}).observe(root,{childList:true,subtree:true,attributes:true,attributeFilter:['class','src','srcset','data-swiper-slide-index']});}catch(e){}
    }
  });
  W.addEventListener('load',tuneAll,{once:true});
})(window,document);
