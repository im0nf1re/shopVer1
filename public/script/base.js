"use strict"

let chooseCategory = document.getElementById('chooseCategory');
let categoryList = document.getElementById('categoryList');

categoryList.hidden = true;

let clicked = false;

let menuItems = document.querySelectorAll('.menu-item');

menuItems.forEach(function (el) {
    el.children[0].hidden = true;
    el.addEventListener('click', function () {
        if (!clicked) {
            clicked = true;
            el.children[0].hidden = false;
            el.children[0].style.top = el.offsetTop + el.offsetHeight + 'px';
            el.children[0].style.left = el.offsetLeft + 'px';
            el.children[0].style.width = el.offsetWidth + 'px';
        } else {
            clicked = false;
            el.children[0].hidden = true;
        }
    })
});

// chooseCategory.addEventListener('click', function () {
//     if (!clicked) {
//         clicked = true;
//         categoryList.hidden = false;
//         categoryList.style.top = chooseCategory.offsetTop + chooseCategory.offsetHeight + 'px';
//         categoryList.style.left = chooseCategory.offsetLeft + 'px';
//         categoryList.style.width = chooseCategory.offsetWidth + 'px';
//     } else {
//         clicked = false;
//         categoryList.hidden = true;
//     }
// });
