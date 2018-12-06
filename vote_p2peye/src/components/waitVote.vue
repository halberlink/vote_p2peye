<template>
  <div class="ui-waitvote">
    <!-- <mt-header title="评选人等待投票">
    </mt-header> -->
    <banner></banner>
    <!-- <div @click="jumpTo('/InformationEntry')">213</div> -->
    <div class="ui-main">
      <div class="ui-Candidate ui-contentbg">
        <div class="ui-listtitle">候选人</div>
        <div class="ui-list">
          <div class="ui-list-item" v-for="item in CandidateList">
            <div class="ui-face"><img src="../assets/face.jpg" alt=""></div>
            <div class="ui-name">{{item.name}}</div>
          </div>
        </div>
        <div class="enter-key" @click="startVote">下一环节</div>
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
    <div class="ui-content">

      <!--<div :class="isFixd?'countList fixd':'countList static'">-->
        <!--<DataList :dataList="sortList"></DataList>-->
      <!--</div>-->
    </div>
  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import DataList from './common/countList.vue'
  import banner from './common/banner';
  export default {
    name: 'waitVote',
    data () {
      return {
        lock:false,
        openSocket:false,
        CandidateList:[],
        jugeList:[],
        dataList:[],
        websock:null,
        isFixd:false
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
      }
    },
    created:function(){
      var _this = this;
      this.initWebSocket();


      window.addEventListener('scroll',function(){
        var st = document.documentElement.scrollTop || document.body.scrollTop;
        if(st <= 225){
          _this.isFixd = false
        }else{
          _this.isFixd = true
        }
      },false);
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
      startVote(){
        var _this = this;
        this.$confirm('此操作将使整体流程开始并且此流程不可逆，\n请确认全员都已进入并已准备好开始, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            if(_this.openSocket){
              _this.websocketsend({
                "interface":"start",
                "data":""
              })
            }else{
              _this.$message({
                message: "socket未连接",
                type: 'error'
              });
            }
        }).catch(() => {
            this.$message({
            type: 'info',
            message: '已取消开始'
          });
        });



      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/waitVote.scss";
</style>
