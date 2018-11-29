// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import qs from 'qs'
import Element from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.config.productionTip = false
Vue.use(Element, { size: 'small', zIndex: 3000 });
Vue.prototype.$http = axios;
Vue.prototype.$qs = qs;


String.prototype.trim = function(){
  //从空格开始（至少一个空格），中间任意个字符，从空格结束（至少一个空格）
  return this.replace(/^\s+(.*?)\s+$/,'$1');
}
router.beforeEach((to, from, next) => {
  /* 路由发生变化修改页面title */
  if (to.meta.title) {
    document.title = to.meta.title
  }
  var u = navigator.userAgent;
  var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //Android终端
  var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
  //是移动端 进入投票页面
  //PC端进入 开始投票页面
  if (to.path === '/' && (isiOS || isAndroid)) {

    next({
      path:"/indexM"
    })
  } else {
    next()
  }

});
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
