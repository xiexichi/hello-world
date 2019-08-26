<template>
  <view :class="'price price__status--' + status + ' ' + customClass">
    <view :class="'price__icon price__icon--' + icon + ' price__icon--' + status">{{symbol}}</view>
    <view :class="'count price__count price__count--' + status">
      {{value && decimal !== 'small' ? value : ''}}
      <text v-if="value && decimal === 'small'">{{value}}</text>
      <text v-if="value && decimal === 'small'" class="count__decimal--small">.{{decimalNum}}</text>
    </view>
  </view>
</template>

<script>
  /**
   * 小数保留处理
   * @param priceNum 价格数字（单位元）
   * @param len 保留的小数长度
   * @param dir 取整方向，f (floor) 为向下取整，默认值；c（ceiling）为向上取整
   */
  function getDecimal(priceNum, len, dir) {
    if (!priceNum || !len) {
      return false;
    }

    dir = dir || 'f';
    priceNum = parseFloat(priceNum, 10);
    len = parseInt(len, 10);

    if (dir === 'f') {
      let intNumStr = priceNum.toString().split('.')[0];
      let decimalNumStr = priceNum.toString().split('.')[1] || '00';
      if (decimalNumStr.length < 2) {
          decimalNumStr += '0'
      }

      return intNumStr + '.' + decimalNumStr.substring(0, len);
    } else {
      return priceNum.toFixed(len);
    }
  }

  export default {
		name: 'j-price',
    props: {
      symbol: {
        type: String,
        default: '¥'
      },
      value: {
        type: [Number, String],
        default: ''
      },
      icon: {
        type: [String],
        default: ''
      },
      status: {
        type: String,
        default: ''
      },
      delColor: {
        type: String,
        default: '#999'
      },
      decimal: {
        type: String,
        default: '2'
      },
      decimalNum: {
        type: [Number, String],
        default: ''
      },
      customClass: {
        type: String,
        default: ''
      }
    },
    data: {},
    methods: {},
    onLoad () {
      if (this.value) {
        let value = this.value;
        switch (this.decimal) {
          // 保留一位小数
          case '1': {
            value = getDecimal(this.value, 1);
            break;
          }
          // 只显示整数
          case 'none': {
            value = parseInt(this.value);
            break;
          }
          // 小数部分缩小
          case 'small': {
            value = parseInt(this.value).toString().trim();
            this.decimalNum = (this.value.toString().split('.')[1] || '00').trim()
            break
          }
          default: {
              value = getDecimal(this.value, 2);
              break;
          }
        }
        this.value = value
      }
    }
  }
</script>

<style>
  .price,
  .price__icon,
  .price__count {
    display: inline-block;
    line-height: 1;
  }

  .price__icon--sup,
  .price__icon--sub {
    font-size: 52%;
  }

  .price__icon--sup {
    vertical-align: super;
    line-height: 1.1;
  }

  .price__icon--del,
  .price__count--del {
    font-weight: normal;
    text-decoration: line-through;
  }

  .count__decimal--small {
    display: inline;
    font-size: 62%;
  }
</style>