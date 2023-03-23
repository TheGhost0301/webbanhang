"use strict";
const gspbInviewObserves = document.getElementsByClassName('gspb-inview');

let gspbinviewobserve = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            let item = entry.target;
            if(document.querySelector('.fr-page-preloader') !== null && window.scrollY < 150){
                setTimeout(() => {item.classList.add('gspb-inview-active');}, 300);
            }else{
                item.classList.add('gspb-inview-active');
            }
            //gspbinviewobserve.disconnect();
        }
    });
}, {threshold: 0.3});

for (let itemobserve of gspbInviewObserves) {
    gspbinviewobserve.observe(itemobserve);
}