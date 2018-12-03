import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
const state={
  status:0,
  uid:0,
  username:''
}

export default new Vuex.Store({
  state,
  mutations:{
    changevote(state,payload){
      state.status = payload.status
    },
    changelogin(state,payload){

      state.uid = payload.uid
      state.username = payload.username
    },

  }
});
