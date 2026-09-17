(function(){
  'use strict';
  var D=document, root=D.documentElement;
  var config=window.wpbbSuiteV140||{};

  function q(sel,ctx){return (ctx||D).querySelector(sel)}
  function qa(sel,ctx){return Array.prototype.slice.call((ctx||D).querySelectorAll(sel))}

  function measureGrid(){
    var candidates=[
      q('.wp-theme-header-main > .container'),
      q('.wp-theme-header-main .container'),
      q('.wp-theme-site-header .container'),
      q('.wp-theme-site-footer .container')
    ].filter(Boolean);
    var best=null;
    candidates.some(function(el){
      var r=el.getBoundingClientRect();
      if(r.width>320 && r.width<=window.innerWidth+2){best=r;return true}
      return false;
    });
    if(!best)return;
    var left=Math.max(16,Math.round(best.left));
    var right=Math.max(16,Math.round(window.innerWidth-best.right));
    root.style.setProperty('--wpbb-v140-left',left+'px');
    root.style.setProperty('--wpbb-v140-right',right+'px');
    root.style.setProperty('--wpbb-v140-grid-width',Math.round(best.width)+'px');
  }

  function outerSectionFor(node,main){
    var sel='.wpbb-v136-align-section:not(.wpbb-v136-hero-shell),.wpbb-v67-section-shell:not(.wpbb-v136-hero-shell),.wp-theme-section-shell:not(.wpbb-v136-hero-shell),.wp-theme-services-section,.wp-theme-about-section,.wp-theme-industries-section,.wp-theme-case-studies-section,.wp-theme-gallery-section,.wp-theme-process-section,.wp-theme-faq-section,.wp-theme-blog-preview-section,.wpbb-sector-proof-band';
    var found=node.closest(sel);
    if(found && main.contains(found)){
      var parent=found.parentElement && found.parentElement.closest(sel);
      if(parent && main.contains(parent) && !parent.classList.contains('wpbb-v136-hero-shell'))return parent;
      return found;
    }
    return null;
  }

  function markSections(){
    var main=q('#wp-theme-main'); if(!main)return;
    var selectors=[
      '.wpbb-v136-align-section:not(.wpbb-v136-hero-shell)',
      '.wpbb-v67-section-shell:not(.wpbb-v136-hero-shell)',
      '.wp-theme-section-shell:not(.wpbb-v136-hero-shell)',
      '.wp-theme-home-stats','.wpbb-sector-proof-band',
      '.wp-theme-case-studies-section','.wp-theme-gallery-section',
      '.wp-theme-process-section','.wp-theme-faq-section',
      '.wp-theme-blog-preview-section','.wp-theme-blog-preview-container'
    ].join(',');
    qa(selectors,main).forEach(function(el){
      if(el.closest('.wpbb-v136-hero-shell'))return;
      var owner=outerSectionFor(el,main)||el;
      owner.classList.add('wpbb-v140-section');
    });
    qa('.wp-theme-section-heading,.wpbb-v62-section-heading',main).forEach(function(h){
      if(h.closest('.wpbb-v136-hero-shell'))return;
      var owner=outerSectionFor(h,main);
      if(owner)owner.classList.add('wpbb-v140-section');
    });
  }

  function markComponentGrids(){
    var main=q('#wp-theme-main'); if(!main)return;
    qa('.wpbb-v140-section',main).forEach(function(section){
      qa('.row,.wpbb-row',section).forEach(function(row){
        if(row.classList.contains('swiper-wrapper')||row.closest('.wpbb-v136-hero-shell,.swiper,.wpbb-swiper,.woocommerce'))return;
        var nestedOwner=row.parentElement && row.parentElement.closest('.wpbb-v140-section');
        if(nestedOwner && nestedOwner!==section)return;
        var parentRow=row.parentElement && row.parentElement.closest('.row,.wpbb-row');
        if(parentRow && section.contains(parentRow))return;
        var kids=Array.prototype.filter.call(row.children,function(n){return n.nodeType===1 && getComputedStyle(n).display!=='none'});
        if(kids.length<2||kids.length>4)return;
        row.classList.add('wpbb-v140-component-grid');
        row.style.setProperty('--wpbb-v140-cols',String(kids.length));
      });
    });
  }

  function replacePlaceholders(){
    var main=q('#wp-theme-main'); if(!main)return;
    var cards=config.cards||[]; if(!cards.length)return;
    var titleNodes=qa('h1,h2,h3,h4,h5,h6,.wpbb-icon-card__title,.wp-theme-sector-card__title',main).filter(function(el){return el.textContent.trim()==='Card title'});
    var textNodes=qa('p,.wpbb-icon-card__text,.wp-theme-sector-card__text',main).filter(function(el){return el.textContent.trim()==='Add a short description.'});
    titleNodes.slice(0,cards.length).forEach(function(el,i){el.textContent=cards[i].title});
    textNodes.slice(0,cards.length).forEach(function(el,i){el.textContent=cards[i].text});
    titleNodes.forEach(function(el){
      var card=el.closest('.wpbb-icon-card,.wp-theme-sector-card,.wpbb-sector-proof-card,[class*="icon-card"]');
      if(card){
        var row=card.parentElement && card.parentElement.parentElement;
        if(row && row.children.length>=2 && row.children.length<=4)row.classList.add('wpbb-v140-sector-proof-grid');
      }
    });
  }

  function sync(){measureGrid();markSections();markComponentGrids();replacePlaceholders()}
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn()}
  ready(function(){
    sync();
    setTimeout(sync,250);setTimeout(sync,900);
    var t=0;
    window.addEventListener('resize',function(){clearTimeout(t);t=setTimeout(sync,100)},{passive:true});
    var main=q('#wp-theme-main');
    if(main && 'MutationObserver' in window){
      var stopTimer;
      var mo=new MutationObserver(function(){sync();clearTimeout(stopTimer);stopTimer=setTimeout(function(){mo.disconnect()},2500)});
      mo.observe(main,{childList:true,subtree:true});
      stopTimer=setTimeout(function(){mo.disconnect()},3000);
    }
  });
})();
