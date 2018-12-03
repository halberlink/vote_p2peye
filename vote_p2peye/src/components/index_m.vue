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
        timers:null
      }
    },
    created:function(){
//      this.initWebSocket();
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

        var postData = this.$qs.stringify(Data);

        this.lock = true;
        this.$http.post("http://192.168.9.215/server/register.php",postData,{
          headers:{'Content-Type':'application/x-www-form-urlencoded'}
        }).then(function(res){
          if(res.data.code == 200){
            Toast({
              message: '录入成功！',
              position: 'middle',
              duration: 2000
            });
            localStorage.setItem("voteUser",Data.name);
            _this.$router.push('/waitVote_m')
          }else{
            Toast({
              message:  res.data.message,
              position: 'middle',
              duration: 2000
            });
          }
          _this.lock = false;
        }).catch(function(res){
          _this.lock = false;
          Toast({
            message:  "服务异常，请稍后再试",
            position: 'middle',
            duration: 2000
          });

        });
      },
      initWebSocket(){ //初始化weosocket
        console.log("insocket")
        const wsuri = "ws://192.168.0.13:9527";//ws地址
        this.websock = new WebSocket(wsuri);
        this.websock.onopen = this.websocketonopen;

        this.websock.onerror = this.websocketonerror;

        this.websock.onmessage = this.websocketonmessage;
        this.websock.onclose = this.websocketclose;
      },

      websocketonopen() {
        console.log("WebSocket连接成功");
      },
      websocketonerror(e) { //错误
        console.log("WebSocket连接发生错误");
      },
      websocketonmessage(event){ //数据接收
//        const redata = JSON.parse(e.data);
        //注意：长连接我们是后台直接1秒推送一条数据，
        //但是点击某个列表时，会发送给后台一个标识，后台根据此标识返回相对应的数据，
        //这个时候数据就只能从一个出口出，所以让后台加了一个键，例如键为1时，是每隔1秒推送的数据，为2时是发送标识后再推送的数据，以作区分
        console.log(event.data);
      },

      websocketsend(agentData){//数据发送
        this.websock.send(this.username);
      },

      websocketclose(e){ //关闭
        console.log("connection closed (" + e.code + ")");
      },
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" type="text/css" scoped>

  @import "../styles/common/base.scss";
  @import "../styles/index_m.scss";
</style>
