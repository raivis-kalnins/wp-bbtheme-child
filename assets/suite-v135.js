(function(W,D){
  'use strict';
  var cfg=W.wpbbSuiteV135||{};
  var heroUrls=Array.isArray(cfg.heroUrls)?cfg.heroUrls.filter(Boolean):[];
  var fallback=Array.isArray(cfg.fallbackCards)?cfg.fallbackCards:[];
  var theme=String(cfg.themeKey||'sector');
  function q(s,r){try{return (r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return [];}}
  function kids(n){return n?Array.prototype.slice.call(n.children||[]):[];}
  function txt(n){return String(n&&n.textContent||'').replace(/\s+/g,' ').trim();}
  function norm(s){return String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').replace(/\s+/g,' ').trim();}
  function markBody(){if(D.body){D.body.classList.add('wpbb-v135','wpbb-v135-theme-'+theme.replace(/[^a-z0-9_-]/gi,'-'));}}
  function hide(n){if(n&&n.classList)n.classList.add('wpbb-v135-hidden');}
  function unhide(n){if(n&&n.classList)n.classList.remove('wpbb-v135-hidden');}

  function removeBrokenBlockText(){
    var main=q('#wp-theme-main'); if(!main||!D.createTreeWalker)return;
    var walker=D.createTreeWalker(main,NodeFilter.SHOW_TEXT); var doomed=[],n;
    while((n=walker.nextNode())){
      var t=String(n.nodeValue||'');
      if(/wp:wpbb\/icon-card|svgCode|wpbb-sector-proof-card/.test(t) && /<!--|<!–|wp:wpbb/.test(t))doomed.push(n);
    }
    doomed.forEach(function(n){if(n.parentNode)n.parentNode.removeChild(n);});
  }

  function heroBlocks(){
    return qa('#wp-theme-main .wpbb-swiper--hero,#wp-theme-main .wp-theme-sector-hero .swiper,#wp-theme-main .wp-theme-hero .swiper').filter(function(b,i,a){
      return a.indexOf(b)===i && (!b.closest('.wpbb-swiper--hero') || b.classList.contains('wpbb-swiper--hero'));
    });
  }
  function heroWrapper(block){
    if(!block)return null;
    return block.closest('.wp-theme-sector-hero,.wp-theme-hero,.wp-theme-section-shell,section')||block.parentElement||block;
  }
  function slides(block){return qa('.swiper-slide',block).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});}
  function forceImg(img,url,i){
    if(!img||!url)return;
    img.src=url;img.removeAttribute('srcset');img.removeAttribute('sizes');img.loading='eager';img.decoding='async';
    try{img.fetchPriority=i===0?'high':'auto';}catch(e){}
  }
  function repairHero(){
    var blocks=heroBlocks(); if(!blocks.length)return;
    blocks.forEach(function(block,i){
      if(i>0){
        hide(block);
        var wrap=heroWrapper(block);
        if(wrap && wrap!==block){
          var visible=kids(wrap).filter(function(c){return c!==block && !c.classList.contains('wpbb-v135-hidden') && txt(c);});
          if(!visible.length)hide(wrap);
        }
        return;
      }
      unhide(block); block.classList.add('wpbb-v135-hero');
      var host=block.parentElement||block;host.classList.add('wpbb-v135-hero-host');
      var ss=slides(block);
      ss.forEach(function(s,idx){
        var img=q('.wpbb-swiper-slide__media img,.wp-theme-hero__media img,.wpbb-hero-media img,img',s);
        if(img && heroUrls.length)forceImg(img,heroUrls[idx%heroUrls.length],idx);
      });
      qa('.wpbb-v134-hero-pagination,.wpbb-v133-hero-pagination,.wpbb-v128-hero-pagination,.wpbb-v127-hero-pagination,.wpbb-v126-hero-pagination,.swiper-pagination',host).forEach(hide);
      var count=Math.max(3,Math.min(3,heroUrls.length||ss.length||3));
      var pager=q('.wpbb-v135-hero-pagination',host);
      if(!pager){
        pager=D.createElement('div');pager.className='wpbb-v135-hero-pagination';pager.setAttribute('aria-label','Hero slides');host.appendChild(pager);
        for(var bi=0;bi<count;bi++){
          (function(index){
            var b=D.createElement('button');b.type='button';b.className='wpbb-v135-hero-bullet';b.setAttribute('aria-label','Go to slide '+(index+1));
            b.addEventListener('click',function(){
              var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block);var sw=(node&&node.swiper)||block.swiper;
              if(sw&&typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(sw&&typeof sw.slideTo==='function')sw.slideTo(index);
              paint();
            });pager.appendChild(b);
          })(bi);
        }
      }
      function paint(){
        var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block),sw=(node&&node.swiper)||block.swiper,idx=0;
        if(sw&&typeof sw.realIndex==='number')idx=((sw.realIndex%count)+count)%count;
        else{var a=q('.swiper-slide-active',block),arr=slides(block),p=arr.indexOf(a);if(p>=0)idx=p%count;}
        qa('.wpbb-v135-hero-bullet',pager).forEach(function(b,j){b.classList.toggle('is-active',j===idx);});
      }
      paint();
      var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block),sw=(node&&node.swiper)||block.swiper;
      if(sw&&typeof sw.on==='function'&&!block.__wpbbV135Bound){block.__wpbbV135Bound=true;sw.on('slideChange',paint);sw.on('transitionEnd',paint);}
    });
  }

  function processSections(){
    var items=qa('#wp-theme-main .wp-theme-process-section');
    qa('#wp-theme-main .wp-theme-sector-process-grid').forEach(function(g){var s=g.closest('.wp-theme-process-section');if(s&&items.indexOf(s)<0)items.push(s);});
    return items;
  }
  function processData(){
    var out=[];
    for(var i=0;i<3;i++){
      var f=fallback[i]||[];
      out.push([String(f[0]||('0'+(i+1))).replace(/^([1-9])$/,'0$1'),String(f[1]||('Step '+(i+1))),String(f[2]||'')]);
    }
    return out;
  }
  function repairProcess(){
    processSections().forEach(function(section){
      section.classList.add('wpbb-v135-process-section','wpbb-v135-section-wide');
      // Hide every legacy/injected process row on every pass. Older scripts can add them late.
      qa('.wp-theme-sector-process-grid,.wpbb-v134-process-grid,.wpbb-v133-process-grid,.wpbb-v128-process-grid,.wpbb-v127-process-grid,.wpbb-v126-process-grid,.wpbb-v125-process-grid,.row,.wpbb-row',section).forEach(function(n){
        if(n.classList.contains('wpbb-v135-process-grid'))return;
        if(q('.wp-theme-process-card,.wp-theme-process-badge,.wpbb-icon-card,.wp-theme-sector-card,.wpbb-badge',n) || /discover|design|deliver|find|describe|schedule|choose|learn|check|compare|book|order/i.test(txt(n))) hide(n);
      });
      qa('.wp-theme-process-card,.wp-theme-process-badge,.wpbb-v126-process-card,.wpbb-v127-process-card,.wpbb-v128-process-card',section).forEach(function(n){if(!n.closest('.wpbb-v135-process-grid'))hide(n.closest('.wpbb-column,[class*="col-"]')||n);});
      var grid=q(':scope > .wpbb-v135-process-grid',section)||q('.wpbb-v135-process-grid',section);
      if(!grid){
        grid=D.createElement('div');grid.className='wpbb-v135-process-grid';
        processData().forEach(function(item){
          var card=D.createElement('article');card.className='wpbb-v135-process-card';
          var badge=D.createElement('span');badge.className='wpbb-v135-process-badge';badge.textContent=item[0];
          var h=D.createElement('h3');h.textContent=item[1];
          var p=D.createElement('p');p.textContent=item[2];
          card.appendChild(badge);card.appendChild(h);card.appendChild(p);grid.appendChild(card);
        });
        section.appendChild(grid);
      }
    });
  }

  function repairStatsAndProof(){
    qa('#wp-theme-main .wp-theme-sector-proof,#wp-theme-main .wp-theme-home-stats').forEach(function(scope){
      var cards=qa('.wp-theme-sector-proof__item,.wpbb-fun-fact',scope).filter(function(c,i,a){return a.indexOf(c)===i;});
      if(cards.length<2)return;
      var host=cards[0].parentElement;
      while(host&&host!==scope&&host!==D.body){var direct=kids(host).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});if(direct.length>=2)break;host=host.parentElement;}
      host=host||scope;host.classList.add('wpbb-v135-stats-grid');
      kids(host).forEach(function(c){if(cards.some(function(x){return c===x||c.contains(x);})){c.classList.add('wpbb-v135-grid-cell');}});
      cards.forEach(function(c){c.classList.add('wpbb-v135-stat-card');});
    });
  }

  function repairSectionAlignment(){
    if(theme!=='business'&&theme!=='building-services'&&theme!=='building')return;
    qa('#wp-theme-main .wp-theme-section-shell,#wp-theme-main .wp-theme-services-section,#wp-theme-main .wp-theme-industries-section,#wp-theme-main .wp-theme-gallery-section,#wp-theme-main .wp-theme-insights-section,#wp-theme-main .wp-theme-blog-section').forEach(function(s){s.classList.add('wpbb-v135-align-section');});
    qa('#wp-theme-main .wp-theme-case-grid,#wp-theme-main .wp-theme-case-studies-grid,#wp-theme-main .wp-theme-sector-services,#wp-theme-main .wp-theme-sector-industries').forEach(function(g){
      var count=kids(g).filter(function(c){return txt(c)||q('img,article,.card',c);}).length;
      if(count>=2){g.classList.add('wpbb-v135-card-grid','wpbb-v135-cols-'+Math.min(count,4));kids(g).forEach(function(c){c.classList.add('wpbb-v135-grid-cell');});}
    });
  }

  function cleanupDuplicateFinders(){
    var main=q('#wp-theme-main');if(!main)return;
    var hero=heroBlocks()[0];
    var forms=qa('form',main).filter(function(f){var s=norm(txt(f));return /search|find service|location/.test(s)||q('input[type="search"]',f);});
    var seen=false;
    forms.forEach(function(f){
      var nearHero=hero&&hero.contains(f);
      if(nearHero){seen=true;return;}
      var shell=f.closest('.wpbb-v97-hero-finder,.wp-theme-sector-hero,.wp-theme-hero,.wp-theme-section-shell,section');
      if(!shell)return;
      var h=q('h1,h2,h3',shell),n=norm(txt(h));
      if((theme==='building-services'||theme==='building') && /plumbing electrical and property work|find the right/.test(n) && q('.wpbb-swiper--hero,.swiper',shell))hide(shell);
    });
  }

  function run(){markBody();removeBrokenBlockText();repairHero();cleanupDuplicateFinders();repairProcess();repairStatsAndProof();repairSectionAlignment();}
  var pending=false;function schedule(){if(pending)return;pending=true;W.setTimeout(function(){pending=false;run();},40);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,250);W.setTimeout(run,900);W.setTimeout(run,1800);});
  if(W.MutationObserver)new MutationObserver(function(ms){if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))schedule();}).observe(D.documentElement,{childList:true,subtree:true});
})(window,document);
