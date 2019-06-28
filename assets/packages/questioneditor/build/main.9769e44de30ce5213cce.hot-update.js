webpackHotUpdate("main",{

/***/ "./node_modules/cache-loader/dist/cjs.js?!./node_modules/babel-loader/lib/index.js!./node_modules/cache-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./src/helperComponents/Autocomplete.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/cache-loader/dist/cjs.js??ref--12-0!./node_modules/babel-loader/lib!./node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/vue-loader/lib??vue-loader-options!./src/helperComponents/Autocomplete.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es6_regexp_constructor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es6.regexp.constructor */ "./node_modules/core-js/modules/es6.regexp.constructor.js");
/* harmony import */ var core_js_modules_es6_regexp_constructor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es6_regexp_constructor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash_filter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash/filter */ "./node_modules/lodash/filter.js");
/* harmony import */ var lodash_filter__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash_filter__WEBPACK_IMPORTED_MODULE_1__);


/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'lsautocomplete',
  props: {
    dataList: {
      type: Array,
      required: true
    },
    searchableKeys: {
      type: Array,
      default: ['name', 'title']
    },
    showKey: {
      type: String,
      default: 'name'
    },
    valueKey: {
      type: String | Boolean,
      default: false
    },
    matchType: {
      type: String,
      default: 'fuzzy'
    },
    itemClass: {
      type: String,
      default: ''
    },
    inputClass: {
      type: String,
      default: ''
    },
    value: {
      default: ''
    }
  },
  data: function data() {
    return {
      input: '',
      filteredList: []
    };
  },
  computed: {
    showDropdown: function showDropdown() {
      return this.input != '';
    }
  },
  methods: {
    itemSelected: function itemSelected(item) {
      var result = this.valueKey === false ? item : item[valueKey];
      this.$emit('change', result);
    },
    search: function search() {
      var _this = this;

      this.$log.log('AUTOCOMPLETE search triggered');

      if (this.input != '') {
        this.filteredList = lodash_filter__WEBPACK_IMPORTED_MODULE_1___default()(this.dataList, function (listItem) {
          return searchableKeys.reduce(function (coll, key) {
            if (listItem[key] == undefined) {
              return coll;
            }

            return coll || _this[_this.matchType](listItem[key]);
          }, false);
        });
        this.showDropdown = true;
        return;
      }

      this.showDropdown = false;
    },
    fuzzy: function fuzzy(comparable) {
      var regExp = new RegExp("%" + this.input + "%");
      return regExp.test(comparable);
    },
    exact: function exact(comparable) {
      var regExp = new RegExp(this.input);
      return regExp.test(comparable);
    },
    start: function start(comparable) {
      var regExp = new RegExp(this.input + "%");
      return regExp.test(comparable);
    },
    lazy: function lazy(comparable) {
      return comparable.toLowerCase().indexOf(this.input.toLowerCase()) > -1;
    }
  },
  mounted: function mounted() {
    if (this.value != '') {
      this.input = this.value;
    }
  }
});

/***/ })

})
//# sourceMappingURL=main.9769e44de30ce5213cce.hot-update.js.map