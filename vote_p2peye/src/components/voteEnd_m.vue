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
          <div class="name">{{peopleInfo.job}}-{{peopleInfo.name}}</div>
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
        lock:false,
        rangeValue:0,
        openSocket:false,
        userInfo:{},
        peopleInfo:{}
      }
    },
    created:function(){
      this.initWebSocket();
      if(localStorage.getItem("userInfo")){
        this.userInfo = JSON.parse(localStorage.getItem("userInfo"));
      }else{
        this.userInfo.id=0;
      }
    },
    methods:{
      subValue:function(){
        var _this = this;

        var Data = {
          interface : "vote",
          data:{
            from_id:this.userInfo.id,
            to_id:this.peopleInfo.id,
            count:this.rangeValue
          }
        };

        this.websocketsend(Data)
      },
      initWebSocket(){ //初始化weosocket
        var userInfo = this.userInfo;

        console.log("insocket")
        const wsuri = "ws://192.168.5.156:9527/?"+userInfo.id;//ws地址
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
              this.$router.push("/voted");
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
        console.log("connection closed (" + e.code + ")");
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
  @import "../styles/vote_m.scss";
</style>
