<template>
  <div class="ui-waitvote">
    <!-- <mt-header title="评选人信息公示">
    </mt-header>
    <div @click="jumpTo('/InformationEntry')">213</div> -->
    <banner></banner>
    <div class="ui-Candidate ui-contentbg">
      <div class="ui-listtitle">候选人</div>
      <div class="ui-people">
        <div class="face">
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
          <div class="info-item">
            <div class="info-item-key">推荐语：</div>
            <div class="info-item-value">{{peopleInfo.reason}}</div>
          </div>

        </div>
      </div>
      <div class="enter-key" @click="nextStep"></div>
    </div>
    <div class="countList">
      <DataList :dataList="sortList"></DataList>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import banner from './common/banner';
  import DataList from './common/countList.vue';
  export default {
    components:{
      banner
    },
    name: 'tjInfo',
    data () {
      return {
        username:"",
        password:'',
        lock:false,
        openSocket:false,
        peopleInfo:{type:1},
        dataList:[]
      }
    },
    components:{
      DataList
    },
    computed:{
      sortList:function(){
        return this.dataList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
      }
    },
    created:function(){
      this.initWebSocket();
    },
    methods:{
      nextStep:function() {
        if(this.openSocket){
          this.websocketsend({
            "interface":"startVote",
            "data":""
          })
        }else{
          this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }
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
          case "startVote":
            //获取进程状态
            if(event.code == 200){
              localStorage.setItem("status",event.data.status);
              this.$store.commit("changevote",{
                status:event.data.status
              })
              this.$router.push("/vote");
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
  @import "../styles/vote.scss";
</style>
