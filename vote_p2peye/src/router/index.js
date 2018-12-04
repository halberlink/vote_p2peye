import Vue from 'vue'
import Router from 'vue-router'
import countList from '@/components/common/countList'
import Index from '@/components/index'
import indexM from '@/components/index_m'
import waitVote from '@/components/waitVote'
import waitVote_m from '@/components/waitVote_m'
import tjInfo_m from '@/components/tjInfo_m'
import tjInfo from '@/components/tjInfo'
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
      },
      children: [
        {
          // 当 /user/:id/profile 匹配成功，
          // UserProfile 会被渲染在 User 的 <router-view> 中
          path: 'countList',
          component: countList
        }
      ]
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
      path: '/tjInfo',
      name: 'tjInfo',
      component: tjInfo,
      meta:{
        title: '推荐人信息'
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
