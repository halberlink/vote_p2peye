<template>
  <div class="ui-waitvote">
    <banner></banner>
    <div class="countList fl">
      <DataList :dataList="newsortList" name="评分实时榜单新员工"></DataList>
    </div>
    <div class="countList fr">
      <DataList :dataList="oldsortList" name="评分实时榜单老员工"></DataList>
    </div>
    <div class="ui-Candidate ui-contentbg">
      <div class="ui-listtitle">候选人</div>
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
          <!--<div class="info-item">-->
            <!--<div class="info-item-key">推荐语：</div>-->
            <!--<div class="info-item-value">{{peopleInfo.reason}}</div>-->
          <!--</div>-->

        </div>
      </div>
      <div class="enter-key" @click="nextStep">开始评分</div>
    </div>
  </div>

</template>

<script>

  import 'element-ui/lib/theme-chalk/index.css'
  import { Toast } from 'mint-ui';
  import banner from './common/banner';
  import DataList from './common/countList.vue';
  export default {
    name: 'tjInfo',
    data () {
      return {
        username:"",
        password:'',
        lock:false,
        openSocket:false,
        peopleInfo:{
          name:'--',
          job:'--',
          type:0,
          reason:'--'
        },
        olddataList:[],
        newdataList:[],
        alldataList:[],
        dataList:[],
        websock:null
      }
    },
    components:{
      DataList,
      banner
    },
    computed:{
      sortList:function(){
        return this.dataList.sort(function(a,b){
          var x = a["count"];
          var y = b["count"];
          return y-x;
        });
      },
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
      this.initWebSocket();
    },
    beforeDestroy:function(){
      console.log("开始投票")
      this.websock.close()
    },
    methods:{
      nextStep:function() {

        var _this = this;
        this.$confirm('是否开始当前候选人的评分', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
        if(_this.openSocket){
          _this.websocketsend({
            "interface":"startVote",
            "data":""
          })
        }else{
          _this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }
      }).catch(() => {
          _this.$message({
          type: 'info',
          message: '已取消继续'
        });
      });




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
              this.$router.replace("/vote");
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
  .fl{
    float: left;
  }
  .fr{
    float: right;
  }
  .countList{
    width: 340px;
    height:358px;
    overflow: hidden;
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
    -webkit-box-shadow: 0 2px 4px 0 rgba(7, 17, 27, 0.1);
    box-shadow: 0 2px 4px 0 rgba(7, 17, 27, 0.1);
  }
</style>
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/vote.scss";
</style>
