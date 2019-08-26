import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    cookie: 'xxxxxxxx',
    member: {
			user_id: 23,
		}
  },
  mutations: {
    login: (state, data) => {
      state.member = data
    },
    logout: (state) => {
      state.cookie = ''
      state.member = {}
    },
    setCookie: (state, value) => {
      state.cookie = value
    }
  }
})
