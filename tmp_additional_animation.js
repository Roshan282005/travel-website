// helper file (not loaded automatically) — kept for reference
// Contains GSAP + Lottie examples used in destination.php

/*
gsap.from('#map', {duration:1, scale:0.98, opacity:0, ease:'power3.out'});
gsap.from('.chat-container',{duration:0.8, y:30, opacity:0, delay:0.15, ease:'power3.out'});

const origAddMessage = window.addMessage;
window.addMessage = function(text, sender){ origAddMessage(text,sender); const msgs=document.querySelectorAll('.chat-messages .message'); const last=msgs[msgs.length-1]; gsap.from(last,{y:12, opacity:0, duration:0.36, ease:'back.out(1.7)'}); };
*/