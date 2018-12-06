<template>
  <div class="register">
    <banner></banner>
    <div class="content">
      <div class="info">
        <mt-field label="姓名" placeholder="请输入姓名" v-model="username"></mt-field>
      </div>
      <mt-button @click="register" size="large" type="primary">进入评选</mt-button>
    </div>
    <div class="des">*请输入姓名作为评选人的标识，非评选人请勿输入！</div>

  </div>

</template>

<script>
  import { Toast } from 'mint-ui';
  import banner from './common/banner';
  export default {
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        username:"",
        password:'',
        lock:false,
        timers:null,
        websock:null
      }
    },
    components:{
      banner
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
          this.websocketsend(loginData);
        }else{
          _this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }
//        this.$http.post("http://192.168.9.215/server/register.php",postData,{
//          headers:{'Content-Type':'application/x-www-form-urlencoded'}
//        }).then(function(res){
//          if(res.data.code == 200){
//            Toast({
//              message: '录入成功！',
//              position: 'middle',
//              duration: 2000
//            });
//            localStorage.setItem("voteUser",Data.name);
//            _this.$router.push('/waitVote_m')
//          }else{
//            Toast({
//              message:  res.data.message,
//              position: 'middle',
//              duration: 2000
//            });
//          }
//          _this.lock = false;
//        }).catch(function(res){
//          _this.lock = false;
//          Toast({
//            message:  "服务异常，请稍后再试",
//            position: 'middle',
//            duration: 2000
//          });
//
//        });
      },
      initWebSocket(){ //初始化weosocket
        console.log("insocket")
        const wsuri = "ws://192.168.3.12:9527/?0";//ws地址
        this.websock = new WebSocket(wsuri);
        this.websock.onopen = this.websocketonopen;

        this.websock.onerror = this.websocketonerror;

        this.websock.onmessage = this.websocketonmessage;
        this.websock.onclose = this.websocketclose;
      },

      websocketonopen() {
        console.log("WebSocket连接成功");

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
          case "register":
            if(event.code == 200){
              var userInfo  = event.data;
              console.log(userInfo)
              localStorage.setItem("userInfo",JSON.stringify(userInfo));
              this.$store.commit("changelogin",{
                isLogin:true
              })
              localStorage.setItem("ingStatus",0)
              if(event.data.id ==30){
                this.$router.replace("/waitVote")
//                this.$router.push("/InformationEntry")
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
        this.openSocket = false
      },
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/index.scss";
</style>
