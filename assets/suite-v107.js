(function(){
'use strict';
var D=document,CFG=window.wpbbSuiteV107||{};
function q(s,r){return (r||D).querySelector(s)}
function qa(s,r){return Array.prototype.slice.call((r||D).querySelectorAll(s))}
function esc(s){return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]})}

function ajaxSearchForms(){
 var forms=[];
 qa('.wp-theme-header-search-panel form,.wpbb-v97-hero-finder__form,.wp-theme-hero form[role="search"],header form[role="search"],.wpbb-jobs-search').forEach(function(f){if(forms.indexOf(f)<0)forms.push(f)});
 forms.forEach(function(form){
  if(form.dataset.wpbbV107Ajax==='1')return;
  var input=q('input[type="search"][name="s"],input[name="s"],input[type="search"][name="job_keyword"]',form);if(!input)return;
  form.dataset.wpbbV107Ajax='1';form.classList.add('wpbb-v107-ajax-search');input.setAttribute('autocomplete','off');input.setAttribute('aria-autocomplete','list');input.setAttribute('aria-expanded','false');
  var tfa=form.closest('.tfa-ajax-search');
  var host=tfa||input.parentElement||form;host.classList.add('wpbb-v107-search-host');
  var box=tfa?q('.tfa-ajax-search__results',tfa):null;
  if(!box){box=D.createElement('div');box.className='wpbb-v107-search-results';box.hidden=true;box.setAttribute('aria-live','polite');host.appendChild(box)}else{box.classList.add('wpbb-v107-search-results','wpbb-v107-search-results--native')}
  box.setAttribute('role','listbox');
  var ctl=null,timer=0,last='';
  function close(){box.hidden=true;box.innerHTML='';input.setAttribute('aria-expanded','false')}
  function render(items,term){
   if(!items.length){box.innerHTML='<div class="wpbb-v107-search-empty">'+esc(CFG.noResults||'No matching results.')+'</div>';box.hidden=false;input.setAttribute('aria-expanded','true');return}
   var html='<div class="wpbb-v107-search-list">';
   items.forEach(function(it){html+='<a class="wpbb-v107-search-result" href="'+esc(it.url)+'" role="option">'+(it.thumb?'<img src="'+esc(it.thumb)+'" alt="" loading="lazy">':'<span class="wpbb-v107-search-result__mark" aria-hidden="true"></span>')+'<span class="wpbb-v107-search-result__body"><strong>'+esc(it.title)+'</strong><small>'+esc(it.type)+'</small>'+(it.excerpt?'<span>'+esc(it.excerpt)+'</span>':'')+'</span></a>'});
   var action=form.getAttribute('action')||location.pathname||'/';var key=input.name==='job_keyword'?'job_keyword':'s';
   html+='</div><a class="wpbb-v107-search-all" href="'+esc(action)+(action.indexOf('?')>=0?'&':'?')+key+'='+encodeURIComponent(term)+'">'+esc(CFG.viewAll||'View all results')+' →</a>';
   box.innerHTML=html;box.hidden=false;input.setAttribute('aria-expanded','true')
  }
  function run(){
   var term=(input.value||'').trim(),loc=q('input[name="location"]',form);if(term.length<(parseInt(CFG.minChars,10)||2)){close();return}if(term===last&&box.innerHTML&&!box.hidden)return;last=term;
   if(ctl)ctl.abort();ctl=window.AbortController?new AbortController():null;
   box.innerHTML='<div class="wpbb-v107-search-empty">'+esc(CFG.searching||'Searching…')+'</div>';box.hidden=false;input.setAttribute('aria-expanded','true');
   var params=new URLSearchParams({action:CFG.action||'wpbb_v107_search',nonce:CFG.nonce||'',q:term,location:loc?loc.value||'':''});
   fetch(CFG.ajaxUrl||'/wp-admin/admin-ajax.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:params.toString(),signal:ctl?ctl.signal:undefined}).then(function(r){return r.json()}).then(function(res){var items=res&&res.success&&res.data&&Array.isArray(res.data.items)?res.data.items:[];render(items,term)}).catch(function(err){if(err&&err.name==='AbortError')return;close()})
  }
  input.addEventListener('input',function(){clearTimeout(timer);timer=setTimeout(run,180)});
  input.addEventListener('focus',function(){if((input.value||'').trim().length>=(parseInt(CFG.minChars,10)||2))run()});
  input.addEventListener('keydown',function(e){if(e.key==='Escape')close()});
  D.addEventListener('pointerdown',function(e){if(!host.contains(e.target))close()},{passive:true});
 })
}

function normalizeWoo(){
 var main=q('#wp-theme-main');if(!main)return;
 qa('.woocommerce-MyAccount-navigation',main).forEach(function(nav){var w=nav.closest('.woocommerce');if(w)w.classList.add('wpbb-v107-account-grid')});
 qa('.woocommerce-cart-form',main).forEach(function(f){var w=f.closest('.woocommerce');if(w)w.classList.add('wpbb-v107-cart-grid')});
 qa('form.checkout.woocommerce-checkout',main).forEach(function(f){f.classList.add('wpbb-v107-checkout-grid')});
 qa('.woocommerce-product-gallery',main).forEach(function(g){g.style.setProperty('opacity','1','important');g.style.setProperty('visibility','visible','important')});
 qa('.woocommerce-product-gallery img',main).forEach(function(im){im.style.setProperty('opacity','1','important');im.style.setProperty('visibility','visible','important')});
 qa('.iws-product-filter label',main).forEach(function(label){if(q('input[type="checkbox"],input[type="radio"]',label))label.classList.add('wpbb-v107-filter-pill')});
 qa('li.product,.iws-product-card',main).forEach(function(card){var im=q('img',card);if(im){var a=im.closest('a');if(a&&card.contains(a))a.classList.add('wpbb-v107-product-image-link')}})
}

function moveBlogActions(){
 qa('#wp-theme-main .wp-theme-blog-section,#wp-theme-main section').forEach(function(section){
  var text=(section.textContent||'').replace(/\s+/g,' ').trim();if(!/latest (thinking|guides|news)|career advice/i.test(text))return;
  var action=qa('a,.wp-block-button__link,.btn',section).find(function(el){return /^(view|see|browse)\s+(all|every)|all\s+(articles|insights|news|advice)|more\s+(articles|insights|news|advice)/i.test((el.textContent||'').trim())});if(!action)return;
  if(action.closest('.wpbb-v107-blog-more'))return;
  var mover=action,parent=action.parentElement;if(parent&&/wp-block-button/.test(parent.className||''))mover=parent;if(mover.parentElement&&/wp-block-buttons/.test(mover.parentElement.className||'')&&mover.parentElement.children.length===1)mover=mover.parentElement;
  var wrap=D.createElement('div');wrap.className='wpbb-v107-blog-more';wrap.appendChild(mover);
  var container=q(':scope > .container',section)||q('.container',section)||section;container.appendChild(wrap)
 })
}

function watch(){var root=q('#wp-theme-main')||D.body;if(!window.MutationObserver||!root)return;var t=0;var ob=new MutationObserver(function(){clearTimeout(t);t=setTimeout(function(){ajaxSearchForms();normalizeWoo();moveBlogActions()},100)});ob.observe(root,{childList:true,subtree:true})}
function boot(){ajaxSearchForms();normalizeWoo();moveBlogActions();watch();setTimeout(function(){ajaxSearchForms();normalizeWoo();moveBlogActions()},500);setTimeout(function(){ajaxSearchForms();normalizeWoo();moveBlogActions()},1500)}
if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
