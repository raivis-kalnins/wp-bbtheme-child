(function(){
  'use strict';
  var D=document, root=D.documentElement, config=window.wpbbSuiteV141||{};
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
    root.style.setProperty('--wpbb-v141-left',Math.max(16,Math.round(best.left))+'px');
    root.style.setProperty('--wpbb-v141-right',Math.max(16,Math.round(window.innerWidth-best.right))+'px');
  }

  function removeBroadV140Grids(){
    var main=q('#wp-theme-main'); if(!main)return;
    qa('.wpbb-v140-component-grid',main).forEach(function(row){row.classList.remove('wpbb-v140-component-grid')});
  }

  function cardTitles(){
    return (config.cards||[]).map(function(card){return String(card&&card.title||'').trim()}).filter(Boolean);
  }

  function replaceClientFallback(){
    var main=q('#wp-theme-main'); if(!main)return;
    var cards=config.cards||[]; if(!cards.length)return;
    var titles=qa('h1,h2,h3,h4,h5,h6,.wpbb-icon-card__title,.wp-theme-sector-card__title',main).filter(function(el){return el.textContent.trim()==='Card title'});
    var texts=qa('p,.wpbb-icon-card__text,.wp-theme-sector-card__text',main).filter(function(el){return el.textContent.trim()==='Add a short description.'});
    titles.forEach(function(el,i){el.textContent=cards[i%cards.length].title});
    texts.forEach(function(el,i){el.textContent=cards[i%cards.length].text});
  }

  function markSectorProofGrid(){
    var main=q('#wp-theme-main'); if(!main)return;
    var expected=cardTitles(); if(!expected.length)return;
    var rows=[];

    qa('.wpbb-v141-sector-proof-card,.wpbb-icon-card,.wp-theme-sector-card,.wpbb-sector-proof-card,[class*="icon-card"]',main).forEach(function(card){
      var heading=q('h1,h2,h3,h4,h5,h6,.wpbb-icon-card__title,.wp-theme-sector-card__title',card);
      if(!card.classList.contains('wpbb-v141-sector-proof-card') && (!heading || expected.indexOf(heading.textContent.trim())===-1))return;
      var node=card;
      while(node && node!==main && !(node.matches && node.matches('.row,.wpbb-row,.wpbb-v62-card-grid'))){node=node.parentElement}
      if(node && rows.indexOf(node)===-1)rows.push(node);
    });

    rows.forEach(function(row){
      var matches=qa('.wpbb-v141-sector-proof-card,.wpbb-icon-card,.wp-theme-sector-card,.wpbb-sector-proof-card,[class*="icon-card"]',row).filter(function(card){
        var heading=q('h1,h2,h3,h4,h5,h6,.wpbb-icon-card__title,.wp-theme-sector-card__title',card);
        return card.classList.contains('wpbb-v141-sector-proof-card') || (heading && expected.indexOf(heading.textContent.trim())!==-1);
      });
      if(matches.length>=2 && matches.length<=4)row.classList.add('wpbb-v141-sector-proof-grid');
    });
  }

  function sync(){measureGrid();removeBroadV140Grids();replaceClientFallback();markSectorProofGrid()}
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn()}
  ready(function(){
    sync();
    setTimeout(sync,250);setTimeout(sync,900);setTimeout(sync,1800);
    var timer=0;
    window.addEventListener('resize',function(){clearTimeout(timer);timer=setTimeout(sync,100)},{passive:true});
    var main=q('#wp-theme-main');
    if(main && 'MutationObserver' in window){
      var stopTimer;
      var mo=new MutationObserver(function(){sync();clearTimeout(stopTimer);stopTimer=setTimeout(function(){mo.disconnect()},3200)});
      mo.observe(main,{childList:true,subtree:true});
      stopTimer=setTimeout(function(){mo.disconnect()},3600);
    }
  });
})();
