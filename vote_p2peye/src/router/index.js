import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/components/index'
import indexM from '@/components/indexm'
import InformationEntry from '@/components/InformationEntry'

Vue.use(Router)

export default new Router({
  mode:'history',
  base:__dirname,
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index
    },
    {
      path: '/indexM',
      name: 'indexM',
      component: indexM
    },
    {
      path: '/InformationEntry',
      name: 'InformationEntry',
      component: InformationEntry
    }
  ]
})
