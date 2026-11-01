<?php $pageTitle='My keys | HEX CHEATS SHOP';$activePage='keys';require __DIR__.'/_head.php'; ?>
<section class="hx-page-heading"><h1>My Keys</h1></section>
<section id="keysList" class="hx-list"><div class="hx-loading">Loading key history…</div></section>
<script>
(()=>{
  const box=document.getElementById('keysList');
  const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const selectKey=button=>{
    const code=button.closest('.hx-key-card')?.querySelector('code');
    if(!code)return;
    const range=document.createRange();range.selectNodeContents(code);
    const selection=window.getSelection();selection?.removeAllRanges();selection?.addRange(range);
  };
  const fallbackCopy=value=>{
    const area=document.createElement('textarea');area.value=value;area.setAttribute('readonly','');
    area.style.cssText='position:fixed;left:-9999px;top:0;opacity:0';document.body.append(area);area.select();area.setSelectionRange(0,area.value.length);
    let copied=false;try{copied=document.execCommand('copy')}catch{}area.remove();return copied;
  };
  const bindCopy=()=>box.querySelectorAll('[data-copy]').forEach(button=>{
    button.addEventListener('click',async()=>{
      if(button.disabled)return;
      const value=button.dataset.copy||'',label='Copy key';button.disabled=true;button.textContent='Copying…';button.removeAttribute('title');
      let copied=false;
      try{if(navigator.clipboard?.writeText){await navigator.clipboard.writeText(value);copied=true}else copied=fallbackCopy(value)}catch{}
      if(!copied)copied=fallbackCopy(value);
      if(copied)button.textContent='Copied';else{button.textContent=label;button.title='Select the key to copy';selectKey(button)}
      window.setTimeout(()=>{button.disabled=false;button.textContent=label;if(copied)button.removeAttribute('title')},1200);
    });
  });
  async function load(){
    try{
      const response=await fetch('/api/store/orders',{credentials:'same-origin'}),data=await response.json();
      if(response.status===401){location.href='/login?next='+encodeURIComponent(location.pathname+location.search);return}
      if(!response.ok)throw Error(data.error||'Could not load key history.');
      const rows=(data.orders||[]).filter(order=>order.delivery_key);
      box.innerHTML=rows.length?rows.map(order=>`<article class="hx-panel hx-key-card"><div><b>${esc(order.product_name||'Product')} · ${esc(order.duration||'')}</b><div class="hx-muted">${new Date(Number(order.created_at)*1000).toLocaleString()} · ${esc(order.order_id||'')}</div></div><code>${esc(order.delivery_key)}</code><button class="hx-button hx-secondary" type="button" data-copy="${esc(order.delivery_key)}" aria-label="Copy key">Copy key</button></article>`).join(''):'<section class="hx-panel">No keys yet.</section>';
      bindCopy();
    }catch(error){
      box.innerHTML=`<section class="hx-panel hx-load-error" role="alert"><span>${esc(error.message||'Could not load keys.')}</span><button class="hx-button hx-secondary" type="button" data-keys-retry>Retry</button></section>`;
      box.querySelector('[data-keys-retry]')?.addEventListener('click',load);
    }
  }
  load();
})();
</script>
<?php require __DIR__.'/_foot.php'; ?>
