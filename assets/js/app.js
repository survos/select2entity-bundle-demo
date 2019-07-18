/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
require('bootstrap');
require('select2');

require('../css/app.css');

console.log("Init select2");

$('.js-select2').select2({});


var tahsilatData = [{
    id: "A",
    text: 'AÇIK TAHSİLAT'
}, {
    id: "B",
    text: 'SADECE BLOKE KREDİ KARTI İLE'
}];

$('#test').select2({
    placeholder: "Try this",
    data: tahsilatData,
});