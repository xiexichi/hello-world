'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = Component({
  behaviors: [],
  properties: {
    icon: {
      type: String,
      value: ''
    },
    title: {
      type: String,
      value: ''
    },
    tip: {
      type: String,
      value: ''
    },
    button: {
      type: String,
      value: ''
    }
  },
  data: {},
  methods: {
    emitEmptyTap: function emitEmptyTap(event) {
      var detail = event.detail;
      var option = {};
      this.triggerEvent('emptytap', detail, option);
    }
  }
});
