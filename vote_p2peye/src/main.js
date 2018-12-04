// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Vuex from 'vuex'
import store from './store/store';
import App from './App'
import router from './router'
import axios from 'axios'
import qs from 'qs'
import Element from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import Mint from 'mint-ui';
import 'mint-ui/lib/style.css'
Vue.config.productionTip = false


Vue.use(Vuex)
Vue.use(Mint);
Vue.use(Element, { size: 'small', zIndex: 3000 });
Vue.prototype.$http = axios;
Vue.prototype.$qs = qs;


String.prototype.trim = function(){
  //从空格开始（至少一个空格），中间任意个字符，从空格结束（至少一个空格）
  return this.replace(/^\s+(.*?)\s+$/,'$1');
}
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //Android终端
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
var isMobile = (isiOS || isAndroid);
const ingStatus =  localStorage.getItem("ingStatus")

router.beforeEach((to, from, next) => {
  /* 路由发生变化修改页面title */
  if (to.meta.title) {
    document.title = to.meta.title
  }

  //是移动端 进入投票页面
  //PC端进入 开始投票页面
  if(!store.state.isLogin && to.path !== '/'){

    next({
      path:'/',
      replace:true
    })
  }
  if(isMobile){

    if(to.path === '/waitVote_m' && ingStatus == '2'){
      console.log(store.state.ingstatus);
      next({
        path:'/vote_m',
        replace:true
      })

    }else if(to.path === '/waitVote_m' && ingStatus == '1'){
      next({
        path:'/tjInfo_m',
        replace:true
      })

    }else{
      next()
    }

    // if(to.path === '/vote_m' && ingStatus != '1'){
    //   next({
    //     path:'/voteEnd_m',
    //     replace:true
    //   })
    //   return
    // }else if(to.path === '/waitVote_m' && ingStatus == '1'){
    //   console.log(store.state.ingstatus);
    //   next({
    //     path:'/vote_m',
    //     replace:true
    //   })
    //   return
    // }else if(to.path === '/waitVote_m' && ingStatus == '3'){
    //   next({
    //     path:'/tjInfo_m',
    //     replace:true
    //   })
    //   return
    // }else{
    //   next();
    // }


    if (to.path === '/') {

      next({
        path:"/index_m",
        replace:true
      })


    }else if(to.path === '/waitVote') {
      next({
        path:"/waitVote_m",
        replace:true
      })
    }else if(to.path === '/tjInfo') {
      next({
        path:"/tjInfo_m",
        replace:true
      })
    }else if(to.path === '/vote') {
      next({
        path:"/vote_m",
        replace:true
      })
    }
  }

  next();

});
/* eslint-disable no-new */

new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
