(function(){
'use strict';
function markMegaMenus(){
  document.querySelectorAll('.wp-theme-primary-menu > li').forEach(function(li){
    var mega=null;
    try{mega=li.querySelector(':scope > .wp-theme-mega-menu');}catch(e){mega=null;}
    if(!mega)return;
    li.classList.add('wpbb-v111-has-mega');
    var legacy=null;
    try{legacy=li.querySelector(':scope > .sub-menu');}catch(e){legacy=null;}
    if(legacy){legacy.setAttribute('aria-hidden','true');legacy.setAttribute('inert','');}
  });
}
function run(){markMegaMenus();}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',run,{once:true});else run();
window.addEventListener('load',run,{once:true});
})();
