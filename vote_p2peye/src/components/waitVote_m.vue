<template>
  <div class="ui-waitvote">
    <mt-header title="评选人等待投票">
    </mt-header>
    <div @click="jumpTo('/InformationEntry')">213</div>
    <div class="ui-Candidate">
      <div class="ui-tit">候选人</div>
      <div class="ui-list">
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
        <div class="ui-list-item">
          <div class="ui-face"></div>
          <div class="ui-name">张三</div>
        </div>
      </div>
    </div>
    <div class="ui-judges">
      <div class="ui-tit">评委席</div>
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
  export default {
    name: 'waitVote_m',
    data () {
      return {
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
  @import "../styles/waitVote_m.scss";
</style>
