<template>
  <div class="ui-waitvote">
    <!-- <mt-header title="评选人等待投票">
    </mt-header> -->
    <banner></banner>
    <!-- <div @click="jumpTo('/InformationEntry')">213</div> -->
    <div class="ui-Candidate ui-contentbg">
      <div class="ui-listtitle">候选人</div>
      <div class="ui-people">
        <div class="face">
          <div class="face-icon-new face-icon"></div>
        </div>
        <div class="info">
          <div class="info-item">
            <div class="info-item-key">姓名：</div>
            <div class="info-item-value">张某某</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">组：</div>
            <div class="info-item-value">web</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">入职时间：</div>
            <div class="info-item-value">2018-12-01</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">推荐人：</div>
            <div class="info-item-value">张某某</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">类别 ：</div>
            <div class="info-item-value">新员工</div>
          </div>
        </div>
      </div>
      <div class="enter-key"></div>
    </div>
    <div class="ui-judges">
      <div class="ui-listtitle">评委席</div>
      <div class="ui-list">
        <div class="ui-list-item">
          <div class="ui-chair">
            <div class="ui-name">王五六</div>
          </div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>
        <div class="ui-list-item">
          <div class="ui-chair"></div>
          <div class="ui-voted"></div>
        </div>

      </div>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import banner from './common/banner';
  export default {
    components:{
      banner
    },
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false
      }
    },
    created:function(){
      console.log(this.$store.state.voteState)
      this.$store.commit("changevote",{
        state:1
      })
      console.log(this.$store.state.voteState)
    },
    methods:{
      jumpTo:function(url){
        this.$router.push(url)
      },
      register:function() {
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  html,body{
    background: #fafafa;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/vote.scss";
</style>
