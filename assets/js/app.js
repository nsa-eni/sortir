/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
require('bootstrap/dist/css/bootstrap.min.css');
// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

const $ = require('jquery');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
require('bootstrap/dist/js/bootstrap.min');

global.$ = global.jQuery = $;
