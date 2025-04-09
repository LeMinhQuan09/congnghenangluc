/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./wp-content/themes/hd/resources/js/plugins-dev/passive-events-fix.js":
/*!*****************************************************************************!*\
  !*** ./wp-content/themes/hd/resources/js/plugins-dev/passive-events-fix.js ***!
  \*****************************************************************************/
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
!function (e) {
   true ? !(__WEBPACK_AMD_DEFINE_FACTORY__ = (e),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
		__WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : 0;
}(function () {
  var e,
    t = ["scroll", "wheel", "touchstart", "touchmove", "touchenter", "touchend", "touchleave", "mouseout", "mouseleave", "mouseup", "mousedown", "mousemove", "mouseenter", "mousewheel", "mouseover"];
  if (function () {
    var e = !1;
    try {
      var t = Object.defineProperty({}, "passive", {
        get: function get() {
          e = !0;
        }
      });
      window.addEventListener("test", null, t), window.removeEventListener("test", null, t);
    } catch (e) {}
    return e;
  }()) {
    var n = EventTarget.prototype.addEventListener;
    e = n, EventTarget.prototype.addEventListener = function (n, o, r) {
      var i,
        s = "object" == _typeof(r) && null !== r,
        u = s ? r.capture : r;
      (r = s ? function (e) {
        var t = Object.getOwnPropertyDescriptor(e, "passive");
        return t && !0 !== t.writable && void 0 === t.set ? Object.assign({}, e) : e;
      }(r) : {}).passive = void 0 !== (i = r.passive) ? i : -1 !== t.indexOf(n) && !0, r.capture = void 0 !== u && u, e.call(this, n, o, r);
    }, EventTarget.prototype.addEventListener._original = e;
  }
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module used 'module' so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__("./wp-content/themes/hd/resources/js/plugins-dev/passive-events-fix.js");
/******/ 	
/******/ })()
;
//# sourceMappingURL=passive-events-fix.js.map