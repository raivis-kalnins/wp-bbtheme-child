(function(W,D){
  'use strict';
  var cfg=W.wpbbSuiteV134||{};
  var heroUrls=Array.isArray(cfg.heroUrls)?cfg.heroUrls.filter(Boolean):[];
  var fallback=Array.isArray(cfg.fallbackCards)?cfg.fallbackCards:[];
  function q(s,r){try{return(r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return[];}}
  function kids(n){return n?Array.prototype.slice.call(n.children||[]):[];}
  function txt(n){return String(n&&n.textContent||'').replace(/\s+/g,' ').trim();}
  function norm(v){return String(v||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').replace(/\s+/g,' ').trim();}
  function uniq(a){return a.filter(function(x,i){return x&&a.indexOf(x)===i;});}
  function isMain(n){return n&&n.closest&&n.closest('#wp-theme-main');}
  function markBody(){if(D.body)D.body.classList.add('wpbb-v134','wpbb-v134-theme-'+String(cfg.themeKey||'sector').replace(/[^a-z0-9_-]/gi,'-'));}

  function commonHost(cards,root){
    cards=uniq(cards); if(cards.length<2)return null;
    var p=cards[0].parentElement,depth=0;
    while(p&&p!==D.body&&depth<8){
      if(root&&root!==p&&!root.contains(p)){break;}
      var direct=kids(p).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
      if(direct.length>=Math.min(cards.length,2))return p;
      p=p.parentElement;depth++;
    }
    return null;
  }
  function addGrid(host,cards,maxCols){
    if(!host||!cards||cards.length<2)return;
    cards=uniq(cards);
    var direct=kids(host).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
    if(direct.length<2)return;
    var n=Math.max(2,Math.min(maxCols||cards.length,direct.length,6));
    host.classList.add('wpbb-v134-grid','wpbb-v134-cols-'+n);
    direct.forEach(function(cell){cell.classList.add('wpbb-v134-grid-cell');});
    kids(host).forEach(function(cell){
      if(direct.indexOf(cell)!==-1)return;
      if(!txt(cell)&&!q('img,svg,form,input,select,button,video,iframe',cell))cell.classList.add('wpbb-v134-empty-shell');
    });
    cards.forEach(function(card){card.classList.add('wpbb-v134-equal-card');});
  }

  function repairDuplicateFinders(){
    var heads=qa('#wp-theme-main h1,#wp-theme-main h2,#wp-theme-main h3,#wp-theme-main h4').filter(function(h){
      var n=norm(txt(h));
      return n==='search new used and rental vehicles'||n==='find a course that matches the skill you want to build'||n==='find the right trade for the job';
    });
    var seen={};
    heads.forEach(function(h){
      var key=norm(txt(h));
      if(!seen[key]){seen[key]=h;return;}
      var shell=h.closest('.wpbb-sector-finder,.wp-theme-section-shell,.wp-block-group,section,.wpbb-row,.row')||h.parentElement;
      if(shell&&qa('input,select,button,form',shell).length)shell.classList.add('wpbb-v134-duplicate-finder');
    });
  }

  function repairRow31(){
    var row=D.getElementById('wpbb-row-31'); if(!row||!isMain(row))return;
    var cells=kids(row).filter(function(x){return x.nodeType===1;}).slice(0,3);
    if(cells.length<2)return;
    row.classList.add('wpbb-v134-grid','wpbb-v134-cols-3');
    cells.forEach(function(cell,i){
      cell.classList.add('wpbb-v134-grid-cell');
      var card=q('.wp-theme-sector-card,.wpbb-icon-card,.wpbb-card,.card,[class*="card"]',cell)||cell.firstElementChild||cell;
      if(card)card.classList.add('wpbb-v134-equal-card');
      if(fallback[i]){
        var title=q('.wpbb-icon-card__title,.card-title,h2,h3,h4,h5,h6,strong',card||cell);
        var body=q('.wpbb-icon-card__text,.card-text,p',card||cell);
        if(title&&(norm(txt(title))==='card title'||!txt(title)))title.textContent=fallback[i][1]||fallback[i][0]||('Step '+(i+1));
        if(body&&(norm(txt(body))==='add a short description'||!txt(body)))body.textContent=fallback[i][2]||'';
      }
    });
  }

  function sectionCards(section,selector){return uniq(qa(selector,section).filter(function(card){return isMain(card)&&!card.closest('.wpbb-v134-process-grid');}));}
  function markKnownGrids(){
    qa('#wp-theme-main .wp-theme-services-section,#wp-theme-main .wp-theme-industries-section,#wp-theme-main .wp-theme-section-shell').forEach(function(section){
      if(section.classList.contains('wp-theme-process-section'))return;
      var cards=sectionCards(section,'.wp-theme-sector-card');
      if(cards.length>=2&&cards.length<=6){var host=commonHost(cards,section);if(host)addGrid(host,cards,cards.length>=4?4:3);}
    });
    qa('#wp-theme-main .wp-theme-case-grid,#wp-theme-main .wp-theme-case-studies-grid').forEach(function(host){
      var cards=sectionCards(host,'.wp-theme-case-card'); if(cards.length>=2)addGrid(host,cards,Math.min(3,cards.length));
    });
    qa('#wp-theme-main .wp-theme-insights-section').forEach(function(section){
      var cards=sectionCards(section,'.wp-theme-blog-card'); if(cards.length>=2){var host=commonHost(cards,section);if(host)addGrid(host,cards,Math.min(3,cards.length));}
    });
    qa('#wp-theme-main .wp-theme-sector-media-text').forEach(function(row){row.classList.add('wpbb-v134-media-grid');kids(row).forEach(function(c){c.classList.add('wpbb-v134-grid-cell');});});
  }

  function repairStats(){
    qa('#wp-theme-main .wp-theme-sector-proof,#wp-theme-main .wp-theme-home-stats').forEach(function(scope){
      var cards=uniq(qa('.wp-theme-sector-proof__item,.wpbb-fun-fact',scope));
      if(cards.length<2)return;
      var host=commonHost(cards,scope)||scope;
      host.classList.add('wpbb-v134-stats-grid');
      kids(host).forEach(function(cell){if(cards.some(function(c){return cell===c||cell.contains(c);})){cell.classList.add('wpbb-v134-grid-cell');}});
      cards.forEach(function(c){c.classList.add('wpbb-v134-stat-card');});
    });
  }

  function processSource(section){
    return q('.wp-theme-sector-process-grid',section)||q('.row,.wpbb-row',section)||section;
  }
  function parseProcessCards(source){
    var candidates=uniq(qa('.wp-theme-process-card,.wp-theme-sector-card,.wpbb-icon-card,.card',source)).filter(function(c){
      return q('h2,h3,h4,h5,h6,.card-title,.wpbb-icon-card__title',c)&&!c.closest('.wpbb-v134-process-grid');
    });
    if(candidates.length<3){
      var cells=kids(source).filter(function(c){return q('h2,h3,h4,h5,h6,.card-title,.wpbb-icon-card__title',c);});
      candidates=cells;
    }
    return candidates.slice(0,3);
  }
  function repairProcess(){
    var sections=uniq(qa('#wp-theme-main .wp-theme-process-section,#wp-theme-main .wp-theme-sector-process-grid').map(function(x){return x.classList.contains('wp-theme-sector-process-grid')?(x.closest('.wp-theme-process-section')||x):x;}));
    sections.forEach(function(section){
      if(section.getAttribute('data-wpbb-v134-process')==='1')return;
      var source=processSource(section); if(!source)return;
      var cards=parseProcessCards(source);
      var data=[];
      for(var i=0;i<3;i++){
        var card=cards[i];
        var title=card?q('h2,h3,h4,h5,h6,.card-title,.wpbb-icon-card__title',card):null;
        var body=card?q('p,.card-text,.wpbb-icon-card__text',card):null;
        var badge=card?q('.wp-theme-process-badge,[class*="process-badge"],[class*="step-badge"],.wpbb-badge,.wp-theme-card-number',card):null;
        var fb=fallback[i]||[];
        data.push([
          (badge&&txt(badge).length<=6?txt(badge):(fb[0]||('0'+(i+1)))).replace(/^([1-9])$/,'0$1'),
          (title&&norm(txt(title))!=='card title'?txt(title):(fb[1]||('Step '+(i+1)))),
          (body&&norm(txt(body))!=='add a short description'?txt(body):(fb[2]||''))
        ]);
      }
      if(!cards.length&&!fallback.length)return;
      var grid=D.createElement('div');grid.className='wpbb-v134-process-grid';
      data.forEach(function(item){
        var card=D.createElement('article');card.className='wpbb-v134-process-card';
        var badge=D.createElement('span');badge.className='wpbb-v134-process-badge';badge.textContent=item[0];
        var h=D.createElement('h3');h.textContent=item[1];
        var p=D.createElement('p');p.textContent=item[2];
        card.appendChild(badge);card.appendChild(h);card.appendChild(p);grid.appendChild(card);
      });
      if(source!==section)source.classList.add('wpbb-v134-source-hidden');
      else cards.forEach(function(c){var cell=c.closest('.wpbb-column,[class*="col-"]')||c;c.classList.add('wpbb-v134-source-hidden');if(cell!==c)cell.classList.add('wpbb-v134-source-hidden');});
      if(source.parentNode)source.parentNode.insertBefore(grid,source.nextSibling);else section.appendChild(grid);
      section.setAttribute('data-wpbb-v134-process','1');
    });
  }

  function productCards(section){
    return uniq(qa('.wpbb-catalogue-card,.product.type-product,.type-product.product,.iws-product-card,.product-card',section)).filter(function(c){return !c.closest('.wpbb-v134-product-grid');});
  }
  function repairHomeCatalogue(){
    qa('#wp-theme-main .wp-theme-home-product-catalogue').forEach(function(section){
      if(section.getAttribute('data-wpbb-v134-catalogue')==='1')return;
      var cards=productCards(section); if(cards.length<2)return;
      var grid=D.createElement('div');grid.className='wpbb-v134-product-grid';
      var anchor=commonHost(cards,section)||section;
      if(anchor!==section&&anchor.parentNode){anchor.parentNode.insertBefore(grid,anchor);anchor.classList.add('wpbb-v134-source-hidden');}
      else section.appendChild(grid);
      cards.forEach(function(card){
        var move=card.closest('li.product,.wpbb-column,[class*="col-"]')||card;
        if(move===anchor)move=card;
        move.classList.add('wpbb-v134-product-card');grid.appendChild(move);
      });
      section.setAttribute('data-wpbb-v134-catalogue','1');
    });
  }

  function forceImage(img,url,index){
    if(!img||!url)return;
    img.setAttribute('src',url);img.removeAttribute('srcset');img.removeAttribute('sizes');img.setAttribute('loading','eager');img.setAttribute('decoding','async');img.setAttribute('fetchpriority',index===0?'high':'auto');
  }
  function heroBlocks(){
    var blocks=qa('#wp-theme-main .wpbb-swiper--hero,#wp-theme-main .wp-theme-sector-hero .swiper,#wp-theme-main .wp-theme-hero .swiper');
    return uniq(blocks.filter(function(b){return !b.closest('.wpbb-swiper--hero')||b.classList.contains('wpbb-swiper--hero');}));
  }
  function physicalSlides(block){return qa('.swiper-slide',block).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});}
  function swiperNode(block){return block&&block.classList.contains('swiper')?block:(q('.swiper',block)||block);}
  function activeIndex(block,count){
    var node=swiperNode(block),sw=(node&&node.swiper)||block.swiper;
    if(sw&&typeof sw.realIndex==='number')return ((sw.realIndex%count)+count)%count;
    var slides=physicalSlides(block),active=q('.swiper-slide-active',block),i=slides.indexOf(active);return i<0?0:i%count;
  }
  function repairHero(){
    heroBlocks().forEach(function(block){
      block.classList.add('wpbb-v134-hero');
      var host=block.parentElement||block;host.classList.add('wpbb-v134-hero-host');
      var slides=physicalSlides(block);
      slides.forEach(function(slide,i){
        var img=q('.wpbb-swiper-slide__media img,.wp-theme-hero__media img,.wpbb-hero-media img,img',slide);
        if(img&&heroUrls.length)forceImage(img,heroUrls[i%heroUrls.length],i);
      });
      qa('.swiper-pagination,.wpbb-v120-pagination,.wpbb-v121-pagination,.wpbb-v123-hero-pagination,.wpbb-v124-hero-pagination,.wpbb-v126-hero-pagination,.wpbb-v127-hero-pagination,.wpbb-v128-hero-pagination,.wpbb-v133-hero-pagination',host).forEach(function(p){if(!p.classList.contains('wpbb-v134-hero-pagination'))p.classList.add('wpbb-v134-hidden');});
      var count=heroUrls.length>=3?3:Math.max(2,slides.length);
      if(count<2)return;
      var pager=q('.wpbb-v134-hero-pagination',host);
      if(!pager){
        pager=D.createElement('div');pager.className='wpbb-v134-hero-pagination';pager.setAttribute('role','tablist');pager.setAttribute('aria-label','Hero slides');host.appendChild(pager);
        for(var i=0;i<count;i++)(function(index){
          var b=D.createElement('button');b.type='button';b.className='wpbb-v134-hero-bullet';b.setAttribute('aria-label','Go to slide '+(index+1));
          b.addEventListener('click',function(){var node=swiperNode(block),sw=(node&&node.swiper)||block.swiper;if(sw&&typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(sw&&typeof sw.slideTo==='function')sw.slideTo(index);paint();});
          pager.appendChild(b);
        })(i);
      }
      function paint(){var idx=activeIndex(block,count);qa('.wpbb-v134-hero-bullet',pager).forEach(function(b,i){b.classList.toggle('is-active',i===idx);});}
      paint();
      var node=swiperNode(block),sw=(node&&node.swiper)||block.swiper;
      if(sw&&typeof sw.on==='function'&&!block.__wpbbV134Bound){block.__wpbbV134Bound=true;sw.on('slideChange',paint);sw.on('transitionEnd',paint);}
    });
  }

  function repairShop(){
    qa('#wp-theme-main ul.products,#wp-theme-main .products').forEach(function(g){
      var products=kids(g).filter(function(x){return x.matches&&x.matches('li.product,.product');});
      if(products.length>=2){g.classList.add('wpbb-v134-shop-grid');products.forEach(function(p){p.classList.add('wpbb-v134-shop-product');});}
    });
    qa('#wp-theme-main .iws-compare-icon').forEach(function(icon){icon.setAttribute('aria-hidden','true');});
  }
  function repairProductPage(){
    var main=q('#wp-theme-main.wp-theme-woo-legacy--product,#wp-theme-main .wpbb-complete-product');if(!main)return;
    var shell=main.matches&&main.matches('.wpbb-complete-product')?main:(q('.wpbb-complete-product',main)||main);
    shell.classList.add('wpbb-v134-product-page');
    var top=q('.wpbb-complete-product__main,.product.type-product,.product',shell);if(top)top.classList.add('wpbb-v134-product-main');
  }
  function repairCart(){
    var main=q('#wp-theme-main.wp-theme-woo-legacy--cart,#wp-theme-main .woocommerce-cart-form');if(!main)return;
    var root=main.matches&&main.matches('.woocommerce-cart-form')?main.closest('.woocommerce'):q('.woocommerce',main)||main;
    var form=q('.woocommerce-cart-form',root),tot=q('.cart-collaterals',root);if(!form||!tot)return;
    var existing=form.closest('.wpbb-v134-cart-layout');if(existing)return;
    var wrap=D.createElement('div');wrap.className='wpbb-v134-cart-layout';form.parentNode.insertBefore(wrap,form);wrap.appendChild(form);wrap.appendChild(tot);
  }
  function removeCheckoutConsent(form){
    qa('[class*="newsletter"][class*="consent"],[class*="newslatter"][class*="consent"],label,p,.form-row,.woocommerce-form-row',form).forEach(function(n){
      var t=norm(txt(n));if(t.indexOf('i agree to receive occasional email updates')===-1&&t.indexOf('send me wordpress newsletter updates')===-1&&t.indexOf('newsletter updates by email')===-1)return;
      var box=n.matches&&n.matches('.form-row,.woocommerce-form-row')?n:(n.closest('.form-row,.woocommerce-form-row')||n);if(box&&box!==form)box.remove();
    });
  }
  function repairCheckout(){
    var main=q('#wp-theme-main.wp-theme-woo-legacy--checkout,#wp-theme-main form.checkout.woocommerce-checkout');if(!main)return;
    var form=main.matches&&main.matches('form.checkout.woocommerce-checkout')?main:q('form.checkout.woocommerce-checkout',main);if(!form)return;
    removeCheckoutConsent(form);
    if(q('.wpbb-v134-checkout-layout',form))return;
    var customer=q('#customer_details,.col2-set',form),heading=q('#order_review_heading',form),review=q('#order_review,.woocommerce-checkout-review-order',form);if(!customer||!review)return;
    var layout=D.createElement('div');layout.className='wpbb-v134-checkout-layout';
    var left=D.createElement('div');left.className='wpbb-v134-checkout-left';var right=D.createElement('aside');right.className='wpbb-v134-checkout-right';
    customer.parentNode.insertBefore(layout,customer);layout.appendChild(left);layout.appendChild(right);left.appendChild(customer);if(heading)right.appendChild(heading);right.appendChild(review);
  }
  function repairAccount(){
    qa('#wp-theme-main .woocommerce').forEach(function(w){
      if(q('.woocommerce-MyAccount-navigation',w)&&q('.woocommerce-MyAccount-content',w))w.classList.add('wpbb-v134-account-grid');
      var login=q('.u-columns.col2-set',w);if(login)login.classList.add('wpbb-v134-login-grid');
    });
  }

  function run(){markBody();repairDuplicateFinders();repairRow31();repairProcess();repairStats();markKnownGrids();repairHomeCatalogue();repairHero();repairShop();repairProductPage();repairCart();repairCheckout();repairAccount();}
  var queued=false;function schedule(){if(queued)return;queued=true;W.setTimeout(function(){queued=false;run();},30);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,260);W.setTimeout(run,900);});
  if(W.MutationObserver)new MutationObserver(function(ms){if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))schedule();}).observe(D.documentElement,{childList:true,subtree:true});
})(window,document);
