<template>
  <div class="ui-vote">
    <mt-header title="评选人投票">
    </mt-header>
    <div class="vote-info">
      <div class="ui-tit">当前得分</div>
      <div class="vote-people">
        <div class="vote-people-info">
          <div class="face">
            <img src="../assets/face.jpg" alt="">
            <div :class="peopleInfo.type==1?'face-icon-new face-icon':'face-icon-old face-icon'"></div>
          </div>
          <div class="name">{{peopleInfo.job}}-{{peopleInfo.name}}</div>
        </div>
        <div class="vote-num">
          <div class="key">当前评分：</div>
          <div class="value">
            <span>{{peopleInfo.count}}</span>

          </div>
        </div>
      </div>
    </div>
    <div class="vote-history">
      <div class="ui-tit">历史得分</div>
      <div class="vote-history-item" v-for="item in historyList">
        <div class="face">
          <div :class="item.to_info.type==1?'face-icon-new face-icon':'face-icon-old face-icon'"></div>
        </div>
        <div class="num">
          <div class="name">
            <span>{{item.to_info.job}}-{{item.to_info.name}}</span>
            <span class="percent">{{item.count}}</span>
          </div>
          <div class="range">
            <mt-progress :value="item.count | toNumber" :barHeight="10">
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
        peopleInfo:{},
        historyList:[],
        websock:null
      }
    },
    filters:{
      toNumber:function(value){
        return value?Number(value)*10:0;
      }
    },
    created:function(){

      if(localStorage.getItem("userInfo")){
        this.userInfo = JSON.parse(localStorage.getItem("userInfo"));
      }else{
        this.userInfo.id=0;
      }
      this.initWebSocket();
    },
    beforeDestroy:function(){
      this.websock.close()
    },
    methods:{
      resetVote:function(){
        this.$router.replace('/tjInfo_m')
      },
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
        console.log(this.userInfo)
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

              //被投票人
              this.peopleInfo = event.data.ing[0];

              localStorage.setItem("ingStatus",this.peopleInfo.status)
              //历史投票
              console.log("endvote")
              this.historyList = [];
              for(var name in event.data.history){
                //只拿当前评选人的历史
                if( event.data.history[name].from_id == this.userInfo.id){
                  this.historyList.push(event.data.history[name]);
                  //在当前评选人的历史中 拿到当前候选人的得分
                  if( event.data.history[name].to_id == this.peopleInfo.id){
                    this.peopleInfo.count = event.data.history[name].count
                  }
                }

              }
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "next":
            //获取进程状态
            if(event.code == 200){
              this.websock.close()
              this.$router.replace("/tjInfo_m")
            }else if(event.code == 4300){
              _this.$message({
                message: "全部都已投完！谢谢参与",
                type: 'error'
              });
//              this.$router.replace("/allRank_m")


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
  @import "../styles/vote_m.scss";
</style>
