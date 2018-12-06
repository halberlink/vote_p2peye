<template>
  <div class="ui-vote">
    <div class="vote-history">
      <div class="ui-tit">历史评分</div>

      <div class="history-scroll" refs="scrollbox">
        <div class="vote-history-item" v-for="item in sortList">
          <div class="face">
            <img src="../assets/face.jpg" alt="">
            <div :class="item.to_info.type==1?'face-icon-new face-icon':'face-icon-old face-icon'"></div>
          </div>
          <div class="num">
            <div class="name">
              <span>{{item.to_info.job}}-{{item.to_info.name}}</span>
              <span class="percent">{{item.count}}</span>
            </div>
            <div class="range">
              <mt-progress :value="item.count | toNumber" :barHeight="10" :class="item.curPeople?'curPeople':''">
                <div slot="start"></div>
                <div slot="end"></div>
              </mt-progress>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="vote-info">
      <div class="vote-people">
        <div class="vote-people-info">
          <div class="face">
            <img src="../assets/face.jpg" alt="">
            <div :class="peopleInfo.type==1?'face-icon-new face-icon':'face-icon-old face-icon'"></div>
          </div>
          <div class="name">{{peopleInfo.job}}-{{peopleInfo.name}}</div>
        </div>
        <!--<div class="vote-num">-->
          <!--<div class="key">评分：</div>-->
          <!--<div class="value">-->
            <!--<span>{{rangeValue/10}}</span>-->

          <!--</div>-->
        <!--</div>-->
        <span class="currange">{{rangeValue/10}}</span>

      </div>

    </div>

    <div class="float-index">
      <div class="ranglimit">
        <span>0</span>
        <span>10</span>
      </div>
      <div class="vote-range">
        <mt-range v-model="rangeValue"
                  :min="0"
                  :max="100"
                  :step="1"
                  :bar-height="4"
        >
        </mt-range>
      </div>
      <div class="vote-range">
        <mt-button @click="subValue" size="large" type="primary">提交评分</mt-button>
      </div>
    </div>

  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import { MessageBox } from 'mint-ui';
  export default {
    name: 'vote_m',
    data () {
      return {
        lock:false,
        rangeValue:0,
        openSocket:false,
        userInfo:{},
        curCountJuge:{},
        peopleInfo:{
          name:'--',
          job:'--',
          type:0,
          reason:'--'
        },
        historyList:[
        ],
        websock:null
      }
    },
    filters:{
      toNumber:function(value){
        return value?Number(value)*10:0;
      }
    },
    watch: {
      rangeValue(newName, oldName) {

        var curCountJuge = {
          count:newName/10,
          curPeople:true,
          to_info:{
            type:this.peopleInfo.type,
            job:this.peopleInfo.job,
            name:this.peopleInfo.name
          }
        }
        for(let i in this.sortList){
          if(this.sortList[i].to_info.name == curCountJuge.to_info.name){
            document.querySelector(".history-scroll").scrollTop = parseInt(i/6)*320;
            this.sortList.splice(i,1,curCountJuge);
            break;
          }
        }
      }
    },
    computed:{
      sortList:function(){
        return this.historyList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
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
      console.log("投票中")
      this.websock.close()
    },
    methods:{
      subValue:function(){
        var _this = this;

        MessageBox.confirm('确定提交当前评分？').then(action => {
          var Data = {
            interface : "vote",
            data:{
              from_id:_this.userInfo.id,
              to_id:_this.peopleInfo.id,
              count:_this.rangeValue/10
            }
          };
        _this.websocketsend(Data)
      }).catch(function(){
          _this.$message({
            message: "已取消",
            type: 'info'
          });
        });



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

              this.peopleInfo = event.data.ing[0];

              localStorage.setItem("ingStatus",this.peopleInfo.status)
              this.historyList = [];
              for(var name in event.data.history){
                 if( event.data.history[name].from_id == this.userInfo.id){
                  this.historyList.push(event.data.history[name]);
                }
              }

              var curCountJuge = {
                count:this.rangeValue,
                to_info:{
                  type:this.peopleInfo.type,
                  job:this.peopleInfo.job,
                  name:this.peopleInfo.name
                }
              }
              this.historyList.push(curCountJuge);
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "vote":
            //获取进程状态
            if(event.code == 200){

              console.log(event.data.from_id,this.userInfo.id)
              if(event.data.from_id == this.userInfo.id){
                this.websock.close()
                this.$router.replace("/voteEnd_m");
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
              this.$router.replace("/tjInfo_m");

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
  .curPeople .mt-progress-progress{
    background: #dab567;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/vote_m.scss";
</style>
