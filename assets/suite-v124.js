/* WP BBTheme child suite 3.8.11.24 - one native hero pager + authoritative full-res hero sources. */
(function(){
  'use strict';
  var D=document,W=window;
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}
  function qa(sel,root){return Array.prototype.slice.call((root||D).querySelectorAll(sel));}
  function heroBlocks(){return qa('#wp-theme-main .wpbb-swiper--hero');}
  function swiperElement(block){
    if(!block)return null;
    if(block.matches&&block.matches('.swiper'))return block;
    return block.querySelector&&block.querySelector('.swiper');
  }
  function slides(swiperEl){
    if(!swiperEl)return [];
    var all=qa('.swiper-wrapper > .swiper-slide',swiperEl);
    if(!all.length)all=qa('.swiper-slide',swiperEl);
    var seen={},out=[];
    all.forEach(function(slide,index){
      if(slide.classList.contains('swiper-slide-duplicate'))return;
      var raw=slide.getAttribute('data-swiper-slide-index');
      var key=(raw===null||raw==='')?'dom-'+index:raw;
      if(seen[key])return;seen[key]=1;out.push(slide);
    });
    return out.length?out:all;
  }
  function liveSwiper(block,el){return (el&&el.swiper)||(block&&block.swiper)||null;}
  function activeIndex(sw,count){
    if(!count)return 0;
    var n=sw&&typeof sw.realIndex==='number'?sw.realIndex:(sw&&typeof sw.activeIndex==='number'?sw.activeIndex:0);
    n=parseInt(n,10);if(!isFinite(n)||n<0)n=0;return n%count;
  }
  function paint(pager,sw,count){
    var active=activeIndex(sw,count);
    qa('[data-wpbb-v124-slide]',pager).forEach(function(b,i){
      var on=i===active;b.classList.toggle('is-active',on);b.classList.toggle('swiper-pagination-bullet-active',on);
      if(on)b.setAttribute('aria-current','true');else b.removeAttribute('aria-current');
    });
  }
  function buildFallback(pager,count){
    if(pager.querySelector('.swiper-pagination-bullet')&&pager.children.length===count){
      qa('.swiper-pagination-bullet',pager).forEach(function(b,i){b.setAttribute('data-wpbb-v124-slide',String(i));});
      return;
    }
    pager.innerHTML='';
    for(var i=0;i<count;i++){
      var b=D.createElement('button');b.type='button';b.className='swiper-pagination-bullet wpbb-v124-hero-bullet';
      b.setAttribute('data-wpbb-v124-slide',String(i));b.setAttribute('aria-label','Go to slide '+(i+1));pager.appendChild(b);
    }
  }
  function ensurePager(block,el){
    var count=slides(el).length;if(count<2)return;
    // Remove the old external strip from v123. The compact v124 pager stays inside Swiper.
    var sib=block.nextElementSibling;
    while(sib&&sib.classList&&sib.classList.contains('wpbb-v123-hero-pagination')){var next=sib.nextElementSibling;sib.remove();sib=next;}
    qa('.wpbb-v123-hero-pagination',block.parentNode||D).forEach(function(old){if(old!==block&&old.parentNode===block.parentNode)old.remove();});

    var pager=el.querySelector('.swiper-pagination');
    if(!pager){pager=D.createElement('div');pager.className='swiper-pagination wpbb-v124-hero-pagination';el.appendChild(pager);}
    pager.classList.add('wpbb-v124-hero-pagination');
    pager.setAttribute('role','group');pager.setAttribute('aria-label','Hero pagination');
    if(pager.parentNode!==el)el.appendChild(pager);

    var sw=liveSwiper(block,el);
    if(sw&&sw.params){
      try{
        var p=sw.params.pagination&&typeof sw.params.pagination==='object'?sw.params.pagination:{};
        p.el=pager;p.clickable=true;sw.params.pagination=p;
        if(sw.originalParams){var op=sw.originalParams.pagination&&typeof sw.originalParams.pagination==='object'?sw.originalParams.pagination:{};op.el=pager;op.clickable=true;sw.originalParams.pagination=op;}
        if(sw.pagination){
          sw.pagination.el=pager;
          if(!pager.dataset.wpbbV124NativeInit){pager.dataset.wpbbV124NativeInit='1';if(typeof sw.pagination.init==='function')sw.pagination.init();if(typeof sw.pagination.render==='function')sw.pagination.render();}
          if(typeof sw.pagination.update==='function')sw.pagination.update();
        }
      }catch(e){}
    }
    buildFallback(pager,count);
    if(!pager.dataset.wpbbV124Click){
      pager.dataset.wpbbV124Click='1';
      pager.addEventListener('click',function(event){
        var b=event.target&&event.target.closest?event.target.closest('[data-wpbb-v124-slide]'):null;if(!b)return;
        var i=parseInt(b.getAttribute('data-wpbb-v124-slide'),10)||0;var live=liveSwiper(block,swiperElement(block));
        if(live){try{if(typeof live.slideToLoop==='function')live.slideToLoop(i);else if(typeof live.slideTo==='function')live.slideTo(i);}catch(e){}}
        paint(pager,live,count);
      });
    }
    if(sw&&pager._wpbbV124Swiper!==sw){
      pager._wpbbV124Swiper=sw;
      if(typeof sw.on==='function'){var update=function(){paint(pager,sw,count);};try{sw.on('slideChange',update);sw.on('realIndexChange',update);sw.on('transitionEnd',update);}catch(e){}}
    }
    paint(pager,sw,count);
  }
  function forceHeroSources(block,el){
    var cfg=W.wpbbSuiteV124||{},urls=Array.isArray(cfg.heroUrls)?cfg.heroUrls.filter(Boolean):[];
    var list=qa('.swiper-wrapper > .swiper-slide',el);if(!list.length)list=qa('.swiper-slide',el);
    list.forEach(function(slide,domIndex){
      var img=slide.querySelector('.wpbb-swiper-slide__media img');if(!img)return;
      var raw=slide.getAttribute('data-swiper-slide-index'),i=parseInt(raw,10);if(!isFinite(i)||i<0)i=domIndex;
      if(urls.length){var wanted=urls[i%urls.length];if(wanted&&img.getAttribute('src')!==wanted)img.setAttribute('src',wanted);}
      ['srcset','sizes','data-src','data-srcset','data-sizes'].forEach(function(name){img.removeAttribute(name);});
      img.decoding='async';img.loading=i===0?'eager':'lazy';
      if(i===0){try{img.fetchPriority='high';}catch(e){img.setAttribute('fetchpriority','high');}}
    });
  }
  function tune(block){
    var el=swiperElement(block);if(!el)return;
    forceHeroSources(block,el);ensurePager(block,el);
    var sw=liveSwiper(block,el);
    if(sw&&sw.params){try{sw.params.autoplay=sw.params.autoplay&&typeof sw.params.autoplay==='object'?sw.params.autoplay:{};sw.params.autoplay.delay=8500;sw.params.autoplay.disableOnInteraction=false;sw.params.autoplay.pauseOnMouseEnter=true;}catch(e){}}
  }
  function tuneAll(){heroBlocks().forEach(tune);}
  ready(function(){
    tuneAll();[120,420,900,1700].forEach(function(ms){setTimeout(tuneAll,ms);});
    if('MutationObserver' in W){try{var root=D.querySelector('#wp-theme-main')||D.body,queued=false;if(root)new MutationObserver(function(){if(queued)return;queued=true;setTimeout(function(){queued=false;tuneAll();},80);}).observe(root,{childList:true,subtree:true,attributes:true,attributeFilter:['src','srcset']});}catch(e){}}
  });
  W.addEventListener('load',tuneAll,{once:true});
})();
