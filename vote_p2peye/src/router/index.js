import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/components/index'
import indexM from '@/components/index_m'
import waitVote from '@/components/waitVote'
import waitVote_m from '@/components/waitVote_m'
import tjInfo_m from '@/components/tjInfo_m'
import vote_m from '@/components/vote_m'
import vote from '@/components/vote'
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
      path: '/index_m',
      name: 'index_m',
      component: indexM
    },
    {
      path: '/InformationEntry',
      name: 'InformationEntry',
      component: InformationEntry,
      meta:{
        title: '信息录入后台'
      }
    },
    {
      path: '/waitVote',
      name: 'waitVote',
      component: waitVote,
      meta:{
        title: '等待投票'
      }
    },
    {
      path: '/waitVote_m',
      name: 'waitVote_m',
      component: waitVote_m,
      meta:{
        title: '等待投票'
      }
    },
    {
      path: '/tjInfo_m',
      name: 'tjInfo_m',
      component: tjInfo_m,
      meta:{
        title: '推荐人信息'
      }
    },
    {
      path: '/vote',
      name: 'vote',
      component: vote,
      meta:{
        title: '评选人投票'
      }
    },
    {
      path: '/vote_m',
      name: 'vote_m',
      component: vote_m,
      meta:{
        title: '评选人投票'
      }
    }
  ]
})
