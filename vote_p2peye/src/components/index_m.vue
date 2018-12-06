<template>
  <div class="register">
    <mt-header title="评选人登录">
    </mt-header>
   <div class="content">
     <div class="info">
       <mt-field label="姓名" placeholder="请输入姓名" v-model="username"></mt-field>
     </div>
     <mt-button @click="register" size="large" type="primary">进入评选</mt-button>
   </div>
   <div class="des">*请输入姓名作为评选人的标识，非评选人请勿输入！</div>
   <div class="des">*整个评分过程中请不要进行返回操作如果发生连接失败 刷新页面即可 谢谢配合！</div>

  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  export default {
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false,
        timers:null,
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
      register:function(){

        var _this = this;
        if(this.username == ""){
          Toast({
            message: '请输入姓名',
            position: 'middle',
            duration: 2000
          });
          return;
        }

        var Data = {
          name : this.username.trim(),
        };



        this.lock = true;


        var loginData = {
          "interface":"register",
          "data":Data
        }
        console.log(Data)
        if(this.openSocket){
          console.log(222222222)
          this.websocketsend(loginData);
        }else{
          _this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }
      },
      initWebSocket(){ //初始化weosocket

        const wsuri = "ws://192.168.3.12:9527/?0";//ws地址
        this.websock = new WebSocket(wsuri);
        this.websock.onopen = this.websocketonopen;

        this.websock.onerror = this.websocketonerror;

        this.websock.onmessage = this.websocketonmessage;
        this.websock.onclose = this.websocketclose;
      },

      websocketonopen() {
        console.log("WebSocket连接成功");
        this.openSocket = true;
      },
      websocketonerror(e) { //错误
        console.log("WebSocket连接发生错误");
      },
      websocketonmessage(event){ //数据接收
//        const redata = JSON.parse(e.data);
        //注意：长连接我们是后台直接1秒推送一条数据，
        //但是点击某个列表时，会发送给后台一个标识，后台根据此标识返回相对应的数据，
        //这个时候数据就只能从一个出口出，所以让后台加了一个键，例如键为1时，是每隔1秒推送的数据，为2时是发送标识后再推送的数据，以作区分
        var _this = this;
        event = JSON.parse(event.data)
        console.log(event)
        switch (event.interface){
          case "register":
            if(event.code == 200){
              var userInfo  = event.data;

              this.$store.commit("changelogin",{
                isLogin:true
              })
              if(userInfo.id != 30 && userInfo.uname == this.username.trim()){
                localStorage.setItem("userInfo",JSON.stringify(userInfo));
                this.$router.replace("/waitVote_m")
              }
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }
            this.lock = true;
            break;
        }
      },

      websocketsend(agentData){//数据发送
        this.websock.send(JSON.stringify(agentData));
      },

      websocketclose(e){ //关闭

      },
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/index_m.scss";
</style>
