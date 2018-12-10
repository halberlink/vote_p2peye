<template>
  <div class="ui-waitvote">
    <div class="ui-Candidate">
      <div class="ui-tit">候选人</div>
      <div class="ui-list">
        <div class="ui-list-item" v-for="item in CandidateList ">
          <div class="ui-face"><img src="../assets/face.jpg" alt=""></div>
          <div class="ui-name">{{item.name}}</div>
        </div>
      </div>
    </div>
    <div class="ui-judges">
      <div class="ui-tit">评委席</div>
      <div class="ui-list">
        <div class="ui-list-item" v-for="item in jugeList">
          <div class="ui-chair">
            <div class="ui-name">{{item.uname}}</div>
          </div>
          <!--<div class="ui-voted"></div>-->
        </div>

      </div>
    </div>
  </div>

</template>

<script>

  import 'mint-ui/lib/style.css'
  import { Toast } from 'mint-ui';
  export default {
    name: 'waitVote_m',
    data () {
      return {
        username:"",
        password:'',
        lock:false,
        jugeList:[],
        CandidateList:[],
        openSocket:false,
        websock:null
      }
    },
    created:function(){
      this.initWebSocket();
    },
    beforeDestroy:function(){
      this.websock.close()
    },
    methods:{
      jumpTo:function(url){
        this.$router.push(url)
      },
      register:function() {
      },
      initWebSocket(){ //初始化weosocket
        var userInfo = {};
        if(localStorage.getItem("userInfo")){
          userInfo = localStorage.getItem("userInfo");
          userInfo = JSON.parse(userInfo)
        }else{
          userInfo.id = 0;
        }
        console.log("insocket")
        const wsuri = "ws://192.168.3.12:9527/?"+userInfo.id;//ws地址
        this.websock = new WebSocket(wsuri);
        this.websock.onopen = this.websocketonopen;

        this.websock.onerror = this.websocketonerror;

        this.websock.onmessage = this.websocketonmessage;
        this.websock.onclose = this.websocketclose;
      },

      websocketonopen() {
        console.log("WebSocket连接成功");
        //进入房间通知
        var getPeoplesdata = {
          "interface" : 'getPeoples',
          data : ''
        }
        this.websocketsend(getPeoplesdata);

        this.openSocket = true
      },
      websocketonerror(e) { //错误
        console.log("WebSocket连接发生错误");
        this.openSocket = false
      },
      websocketonmessage(event){ //数据接收
        var _this = this;

        event = JSON.parse(event.data)
        console.log(event)

        switch (event.interface){
          case "info":
            //获取进程状态
            if(event.code == 200){

              this.$store.commit("changevote",{
                status:event.data.status
              })
              this.jugeList = event.data.online;

            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "start":
            //获取进程状态
            if(event.code == 200){
              localStorage.setItem("status",event.data.status);
              this.$store.commit("changevote",{
                status:event.data.status
              })

              this.$router.replace("/tjInfo_m");
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "getPeoples":
            if(event.code == 200){
              this.CandidateList = event.data;
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
        }
      },

      websocketsend(agentData){//数据发送
        this.websock.send(JSON.stringify(agentData));

      },

      websocketclose(e){ //关闭
        this.openSocket = false
      },
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  html,body{
    width:100%;
    height: 100%;
    background: url("../assets/body_bg.jpg") no-repeat;
    -webkit-background-size: cover;
    background-size: cover;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/waitVote_m.scss";
  .mt-progress-progress {
    -webkit-border-radius: 0.333rem;
    -moz-border-radius: 0.333rem;
    border-radius: 0.333rem;
  }
  .mt-range-thumb {
    background-color: #dab567;
  }
  .mt-range-progress {
    background-color: #dab567;
    -webkit-border-radius: 0.333rem;
    -moz-border-radius: 0.333rem;
    border-radius: 0.333rem;
  }
  .mt-progress-runway{
    -webkit-border-radius: 0.333rem;
    -moz-border-radius: 0.333rem;
    border-radius: 0.333rem;
  }
  .mt-range-runway {
    border-color: #fff;
    -webkit-border-radius: 0.333rem;
    -moz-border-radius: 0.333rem;
    border-radius: 0.333rem;
  }
  .mint-button--primary {
    background-color: #dab567;
  }
</style>
