<template>
  <div class="ui-waitvote">
    <mt-header title="评选人等待投票">
    </mt-header>
    <div @click="jumpTo('/InformationEntry')">213</div>
    <div class="ui-Candidate">
      <div class="ui-tit">候选人</div>
      <div class="ui-people">
        <div class="face">
          <div class="face-icon-new face-icon"></div>
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
            <div class="info-item-key">入职时间：</div>
            <div class="info-item-value">2018-12-01</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">推荐人：</div>
            <div class="info-item-value">张某某</div>
          </div>
          <div class="info-item">
            <div class="info-item-key">类别 ：</div>
            <div class="info-item-value" v-if="peopleInfo.type == 1">新员工</div>
            <div class="info-item-value" v-else>老员工</div>
          </div>
        </div>
      </div>
      <div class="enter-key"></div>
    </div>
    <div class="ui-judges">
      <div class="ui-tit">评委席</div>
      <div class="ui-list">
        <div class="ui-list-item" v-for="item in jugeList">
          <div class="ui-chair">
            <div class="ui-name">{{item.uname}}</div>
          </div>
          <div class="ui-voted" v-if="item.vote_status == 1"></div>
        </div>
      </div>
    </div>
    <div class="countList">
      <DataList :dataList="sortList"></DataList>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import DataList from './countList.vue'
  export default {
    name: 'vote',
    data () {
      return {
        username:"",
        password:'',
        lock:false,
        openSocket:false,
        jugeList:[],
        peopleInfo:{},
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

              this.jugeList = event.data.online;
              this.peopleInfo = event.data.ing[0];

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
