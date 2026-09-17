(function(){
'use strict';
function moveInsightActions(){
  document.querySelectorAll('.wp-theme-insights-section').forEach(function(section){
    var preview=section.querySelector('.wp-theme-blog-preview-container')||section.querySelector('.wp-theme-blog-preview');
    if(!preview)return;
    var action=section.querySelector('.wp-theme-blog-preview-actions');
    if(action){ if(action.previousElementSibling!==preview) preview.insertAdjacentElement('afterend',action); return; }
    var row=section.querySelector('.wp-theme-section-heading');
    var holder=row&&row.querySelector('.wp-theme-buttons,.wp-block-buttons');
    if(!holder){
      var buttons=Array.prototype.slice.call(section.querySelectorAll('a,button')).filter(function(el){
        return /view\s+all\s+(articles|posts|news|guides|advice)/i.test((el.textContent||'').trim());
      });
      if(!buttons.length)return;
      var button=buttons[0];
      holder=button.closest('.wp-theme-buttons,.wp-block-buttons')||button.parentElement;
    }
    if(!holder)return;
    var wrap=document.createElement('div');
    wrap.className='container wp-theme-blog-preview-actions';
    wrap.appendChild(holder);
    preview.insertAdjacentElement('afterend',wrap);
    row=section.querySelector('.wp-theme-section-heading');
    if(row){
      Array.prototype.slice.call(row.children).forEach(function(child){if(!child.textContent.trim()&&!child.querySelector('*'))child.remove();});
    }
  });
}
function repairBrokenThemeImages(){
  document.querySelectorAll('#wp-theme-main img').forEach(function(img){
    if(img.complete&&img.naturalWidth===0){img.classList.add('wpbb-image-load-error');}
    img.addEventListener('error',function(){img.classList.add('wpbb-image-load-error');},{once:true});
  });
}
function run(){moveInsightActions();repairBrokenThemeImages();}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',run);else run();
window.addEventListener('load',run,{once:true});
})();
