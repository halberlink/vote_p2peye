<template>
  <div class="ui-vote">
    <mt-header title="评选人投票">
    </mt-header>
    <div class="vote-info">
      <div class="ui-tit">当前得分</div>
      <div class="vote-people">
        <div class="vote-people-info">
          <div class="face">
            <div class="face-icon-new face-icon"></div>
          </div>
          <div class="name">前端-张某某</div>
        </div>
        <div class="vote-num">
          <div class="key">评分：</div>
          <div class="value">
            <span>{{rangeValue}}</span>

          </div>
        </div>
        <mt-range v-model="rangeValue"
                  :min="0"
                  :max="10"
                  :step="1">
          <div slot="start">0</div>
          <div slot="end">10</div>
        </mt-range>
      </div>
      <mt-button @click="register" size="large" type="primary">提交评分</mt-button>
    </div>

    <div class="vote-history">
      <div class="ui-tit">历史得分</div>
      <div class="vote-history-item">
        <div class="face">
          <div class="face-icon-new face-icon"></div>
        </div>
        <div class="num">
          <div class="name">
            <span>前端-张某某</span>
            <span class="percent">9</span>
          </div>
          <div class="range">
            <mt-progress :value="60" :barHeight="10">
              <div slot="start"></div>
              <div slot="end"></div>
            </mt-progress>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  export default {
    name: 'vote_m',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false,
        rangeValue:0
      }
    },
    methods:{
      register:function(){
        var _this = this;
        if(this.username == ""){
          Toast({
            message: '请输入姓名',
            position: 'middle',
            duration: 2000
          });
          return;
        }

        var Data = {
          name : this.username.trim(),
        };

        var postData = this.$qs.stringify(Data);

        this.lock = true;
        this.$http.post("http://192.168.9.215/server/register.php",postData,{
          headers:{'Content-Type':'application/x-www-form-urlencoded'}
        }).then(function(res){
          if(res.data.code == 200){
            Toast({
              message: '录入成功！',
              position: 'middle',
              duration: 2000
            });
            _this.$router.push()
          }else{
            Toast({
              message:  res.data.message,
              position: 'middle',
              duration: 2000
            });
          }
          _this.lock = false;
        }).catch(function(res){
          _this.lock = false;
          Toast({
            message:  "服务异常，请稍后再试",
            position: 'middle',
            duration: 2000
          });

        });
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
  @import "../styles/vote_m.scss";
</style>
