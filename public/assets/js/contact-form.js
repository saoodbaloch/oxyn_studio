(() => {
  const form=document.querySelector('[data-contact-form]'); if(!form) return;
  const status=form.querySelector('[data-contact-status]'); const button=form.querySelector('button[type="submit"]');
  form.addEventListener('submit',async(e)=>{e.preventDefault(); if(!form.reportValidity()) return; const original=button.textContent; button.disabled=true; button.textContent='Sending…'; status.textContent=''; status.className='contact-form__status';
    try{const r=await fetch(form.action,{method:'POST',body:new FormData(form),headers:{'Accept':'application/json'}}); const data=await r.json(); if(!r.ok||!data.ok) throw new Error(data.message||'Could not send.'); status.textContent=data.message; status.classList.add('is-success'); form.reset();}
    catch(err){status.textContent=err.message||'Could not send your request.'; status.classList.add('is-error');}
    finally{button.disabled=false; button.textContent=original;}
  });
})();
