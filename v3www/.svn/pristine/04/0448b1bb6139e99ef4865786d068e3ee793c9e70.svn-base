import Vue from 'vue'
import App from './App'
import http from '@/utils/request'
import store from '@/store/index'
import helper from '@/utils/helper'

Vue.config.productionTip = false

App.mpType = 'app'
Vue.prototype.$http = http
Vue.prototype.$store = store
Vue.prototype.$helper = helper

// #ifdef H5
import 'vant/lib/index.css'
import Vant from 'vant'
Vue.use(Vant)
// #endif

const app = new Vue({
    ...App
})
app.$mount()
