<template>
  <div class="ui-tjinfo">
    <mt-header title="推荐人信息">
    </mt-header>
    <div class="ui-tj">
      <div class="ui-tit">候选人</div>
      <div class="ui-people">
        <div class="face">
          <img src="../assets/face.jpg" alt="">
          <div :class="peopleInfo.type == 1?'face-icon-new face-icon':'face-icon-old face-icon'"></div>
        </div>
        <div class="info">
          <div class="info-item">
            <div class="info-item-key">姓名：</div>
            <div class="info-item-value">{{peopleInfo.name}}</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">组：</div>
            <div class="info-item-value">{{peopleInfo.job}}</div>
          </div>

          <div class="info-item">
            <div class="info-item-key">类别 ：</div>
            <div class="info-item-value" v-if="peopleInfo.type == 1">新员工</div>
            <div class="info-item-value" v-else>老员工</div>
          </div>
        </div>
      </div>
    </div>
    <!--<div class="ui-des">-->
      <!--<div class="ui-tit">推荐语</div>-->
      <!--<div class="des">{{peopleInfo.reason}}</div>-->
    <!--</div>-->
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  export default {
    name: 'tjInfo_m',
    data () {
      return {
        peopleInfo:{
          name:'--',
          job:'--',
          type:0,
          reason:'--'
        },
        lock:false,
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
              this.peopleInfo = event.data.ing[0];
              //reset 投票 如果为最后一个的时候 结束
              if(this.peopleInfo){
                localStorage.setItem("ingStatus",this.peopleInfo.status)
                if(event.data.ing[0].status == 2){
                  this.$router.replace('/vote_m')
                }
              }else{
                _this.$message({
                  message: "全部都已投完！谢谢参与",
                  type: 'error'
                });
              }


            }else{

            }

            break;
          case "startVote":
            //获取进程状态
            if(event.code == 200){
              localStorage.setItem("status",event.data.status);
              this.$store.commit("changevote",{
                status:event.data.status
              })
              this.$router.replace("/vote_m");
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
    background: #cccccc;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/tjInfo_m.scss";
</style>
