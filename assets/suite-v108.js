(function(){
'use strict';
var D=document;
function q(s,r){return (r||D).querySelector(s)}
function qa(s,r){return Array.prototype.slice.call((r||D).querySelectorAll(s))}
function add(el,c){if(el&&!el.classList.contains(c))el.classList.add(c)}
function closestWoo(el){return el?el.closest('.woocommerce'):null}
function normalizeFilter(main){
 qa('.iws-product-filter label',main).forEach(function(label){
  var cb=q('input[type="checkbox"]',label); if(!cb)return;
  add(label,'wpbb-v108-filter-pill'); add(cb,'wpbb-v108-filter-checkbox');
 });
}
function normalizeCards(main){
 qa('li.product,.iws-product-card',main).forEach(function(card){
  add(card,'wpbb-v108-product-card');
  var img=q('img',card); if(img){var a=img.closest('a');if(a&&card.contains(a))add(a,'wpbb-v108-product-image-link')}
  qa('button,a,[role="button"]',card).forEach(function(el){
   var cn=String(el.className||'').toLowerCase(), act=String(el.getAttribute('data-action')||'').toLowerCase();
   if(/compare|quick[-_ ]?view|quickview|actions-toggle|more-toggle|product-card__menu/.test(cn+' '+act))add(el,'wpbb-v108-round-action');
  });
  qa('.tfa-wcqb-loop-wrap,.button,.wp-element-button',card).forEach(function(el){if(el.parentElement===card)add(el,'wpbb-v108-card-action')});
 });
}
function normalizeWoo(){
 var main=q('#wp-theme-main'); if(!main)return;
 qa('.woocommerce-cart-form',main).forEach(function(form){var w=closestWoo(form);if(w)add(w,'wpbb-v108-cart-grid')});
 qa('.woocommerce-MyAccount-navigation',main).forEach(function(nav){var w=closestWoo(nav);if(w)add(w,'wpbb-v108-account-grid')});
 qa('form.checkout.woocommerce-checkout',main).forEach(function(form){add(form,'wpbb-v108-checkout-grid')});
 normalizeFilter(main); normalizeCards(main);
}
function boot(){normalizeWoo();setTimeout(normalizeWoo,250);setTimeout(normalizeWoo,900);if(window.MutationObserver){var root=q('#wp-theme-main')||D.body,t=0;new MutationObserver(function(){clearTimeout(t);t=setTimeout(normalizeWoo,80)}).observe(root,{childList:true,subtree:true})}}
if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
})();
