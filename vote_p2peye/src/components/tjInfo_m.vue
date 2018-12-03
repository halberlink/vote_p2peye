<template>
  <div class="ui-tjinfo">
    <mt-header title="推荐人信息">
    </mt-header>
    <div @click="jumpTo('/InformationEntry')">213</div>
    <div class="ui-tj">
      <div class="ui-tit">推荐人</div>
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
    </div>
    <div class="ui-des">
      <div class="ui-tit">推荐语</div>
      <div class="des">优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀优秀</div>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  export default {
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
    background: #cccccc;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/tjInfo_m.scss";
</style>
