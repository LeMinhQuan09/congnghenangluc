/******/ (function() { // webpackBootstrap
/*!******************************************************************************!*\
  !*** ./wp-content/themes/hd/resources/js/plugins-dev/skip-link-focus-fix.js ***!
  \******************************************************************************/
/**
 * File skip-link-focus-fix.js
 *
 * Helps with accessibility for keyboard only users.
 * This is the source file for what is minified in the astra_skip_link_focus_fix() PHP function.
 *
 * Learn more: https://github.com/Automattic/_s/pull/136
 *
 * @package Astra
 */

(function () {
  var is_webkit = navigator.userAgent.toLowerCase().indexOf('webkit') > -1,
    is_opera = navigator.userAgent.toLowerCase().indexOf('opera') > -1,
    is_ie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
  if ((is_webkit || is_opera || is_ie) && document.getElementById && window.addEventListener) {
    window.addEventListener('hashchange', function () {
      var id = location.hash.substring(1),
        element;
      if (!/^[A-z0-9_-]+$/.test(id)) {
        return;
      }
      element = document.getElementById(id);
      if (element) {
        if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
          element.tabIndex = -1;
        }
        element.focus();
      }
    }, false);
  }
})();
/******/ })()
;
//# sourceMappingURL=skip-link-focus-fix.js.map