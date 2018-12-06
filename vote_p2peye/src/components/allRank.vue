<template>
  <div class="ui-waitvote">
    <banner></banner>
    <div class="ui-content">

      <div class="countList static">
        <DataList :dataList="oldsortList" name="评分实时榜单老员工"></DataList>
      </div>
      <div class="countList static">
        <DataList :dataList="newsortList" name="评分实时榜单新员工"></DataList>
      </div>
      <div class="countList static">
        <DataList :dataList="allortList" name="评分实时总榜单"></DataList>
      </div>
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import DataList from './common/countList.vue'
  import banner from './common/banner';
  export default {
    name: 'allRank',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false,
        openSocket:false,
        jugeList:[],
        olddataList:[],
        newdataList:[],
        alldataList:[],
        websock:null,
        isFixd:false
      }
    },
    components:{
      DataList,
      banner
    },
    computed:{
      oldsortList:function(){
        return this.olddataList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
      },
      newsortList:function(){
        return this.newdataList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
      },
      allortList:function(){
        return this.alldataList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
      }
    },
    created:function(){
      var _this = this;
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
        switch (event.interface){
          case "info":
            //获取进程状态
            if(event.code == 200){

              this.newdataList = [];
              this.olddataList = [];
              this.alldataList = [];
              //榜单
              let RankCount = event.data.rank;

              for(let id in RankCount){
                let countItem = {};
                countItem.count = RankCount[id][0].count;
                countItem.job = RankCount[id][0].info.job;
                countItem.name = RankCount[id][0].info.name;
                this.alldataList.push(countItem);
                if(RankCount[id][0].info.type == 1){
                  this.newdataList.push(countItem);
                }else{
                  this.olddataList.push(countItem);
                }
              }
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

              this.$router.replace("/tjInfo");
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "rank":
            //获取进程状态
            if(event.code == 200){
              localStorage.setItem("status",event.data.status);


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
    background: #eee3ff;
  }
  .ui-content{
    display: flex;
    justify-content: space-between;
  }
  .countList{
    width: 340px;
    height:500px;
    overflow: hidden;
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
    -webkit-box-shadow: 0 2px 4px 0 rgba(7, 17, 27, 0.1);
    box-shadow: 0 2px 4px 0 rgba(7, 17, 27, 0.1);
  }
  .ui-content {
    font-size: 30px;
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
</style>
