import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
const state={
  status:0,
  uid:0,
  username:'',
  ingstatus:0,
  isLogin:false
}

export default new Vuex.Store({
  state,
  mutations:{
    changevote(state,payload){
      state.status = payload.status
    },
    changelogin(state,payload){

      state.isLogin = payload.isLogin
    },
    changeIngStatus(state,payload){
      state.ingstatus = payload.ingstatus
    }

  }
});
