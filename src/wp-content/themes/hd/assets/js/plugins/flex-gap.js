/******/ (function() { // webpackBootstrap
/*!*******************************************************************!*\
  !*** ./wp-content/themes/hd/resources/js/plugins-dev/flex-gap.js ***!
  \*******************************************************************/
/**
 * https://github.com/Modernizr/Modernizr/blob/master/feature-detects/css/flexgap.js
 *
 */

/*create flex container with row-gap set*/
var flex = document.createElement("div");
flex.style.display = "flex";
flex.style.flexDirection = "column";
flex.style.rowGap = "1px";

/*create two, elements inside it*/
flex.appendChild(document.createElement("div"));
flex.appendChild(document.createElement("div"));

/*append to the DOM (needed to obtain scrollHeight)*/
document.body.appendChild(flex);

/*flex container should be 1px high from the row-gap*/
var isSupported = flex.scrollHeight === 1;
flex.parentNode.removeChild(flex);
if (isSupported) {
  document.documentElement.classList.add("flex-gap");
}
/******/ })()
;
//# sourceMappingURL=flex-gap.js.map