<template>
  <div class="ui-waitvote">
    <!-- <mt-header title="评选人等待投票">
    </mt-header> -->
    <banner></banner>
    <!-- <div @click="jumpTo('/InformationEntry')">213</div> -->
    <div class="ui-Candidate ui-contentbg">
      <div class="ui-listtitle">候选人</div>
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
      <div class="enter-key" @click="startVote"></div>
    </div>
    <div class="ui-judges">
      <div class="ui-listtitle">评委席</div>
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
  import { Toast } from 'mint-ui';
  import banner from './common/banner';
  export default {
    components:{
      banner
    },
    name: 'waitVote',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false,
        openSocket:false,
        jugeList:[]
      }
    },
    created:function(){
      this.initWebSocket();
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
              this.$router.push("/tjInfo");
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
      startVote(){
        if(this.openSocket){
          this.websocketsend({
            "interface":"start",
            "data":""
          })
        }else{
          this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }

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
  @import "../styles/waitVote.scss";
</style>
