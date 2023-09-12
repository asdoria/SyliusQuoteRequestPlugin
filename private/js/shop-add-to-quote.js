
document.addEventListener('DOMContentLoaded', () => {
  const addToQuote = document.querySelector("#asdoria-shop-quote-request-add-to-quote");
  if(!addToQuote) return;
  console.log('addToQuote', addToQuote);
  addToQuote.addEventListener('click',  (event) => {
    console.log('event', event);
    event.preventDefault();
    event.stopPropagation();
  })
});
