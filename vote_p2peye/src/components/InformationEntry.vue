<template>
  <div class="InformationEntry">
    <h1>员工信息录入</h1>
    <div class="insert">
      <div class="top">
        <el-row :gutter="20">
          <el-col :span="8">
            <div class="grid-content bg-purple">
              <el-input v-model="nameValue" placeholder="请输入员工姓名"></el-input>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="grid-content bg-purple">
              <el-select v-model="jobValue" placeholder="请选择员工职位">
                <el-option
                  v-for="item in jobs"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="grid-content bg-purple">
              <el-select v-model="typeValue" placeholder="请选择新老员工">
                <el-option
                  v-for="item in type"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="bottom">
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="grid-content bg-purple">
              <el-input v-model="reasonValue" type="textarea" placeholder="请输入推荐理由"></el-input>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="sub">
        <el-button type="primary" @click="addPeople">新增人员</el-button>
      </div>
    </div>



    <div class="inseredPeople">
      <el-table
        :data="PeopleData"
        height="550"
        border
        style="width: 100%">
        <el-table-column
          prop="name"
          label="姓名"
          width="180">
        </el-table-column>
        <el-table-column
          prop="job"
          label="职位"
          width="180">
        </el-table-column>
        <el-table-column
          prop="reason"
          label="推荐理由">
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        nameValue:"",
        jobValue: '',
        reasonValue:'',
        typeValue:"",
        jobs:[
          {
            value:1,
            label:"前端"
          },
          {
            value:2,
            label:"PHP"
          },
          {
            value:3,
            label:"JAVA"
          },
          {
            value:4,
            label:"IOS"
          },
          {
            value:5,
            label:"Android"
          },
          {
            value:6,
            label:"测试"
          },
          {
            value:7,
            label:"运维"
          }
        ],
        type:[
          {
            value:1,
            label:"新员工"
          },
          {
            value:2,
            label:"老员工"
          }
        ],
        PeopleData: [],
        lock:false,
        openSocket:false,
        InsertData:{}
      }
    },
    created:function(){
      var _this = this;

      this.initWebSocket();
    },
    methods:{
      addPeople:function(){
        var _this = this;
        if(this.lock){
          _this.$message({
            message: "添加中请稍后..",
            type: 'success'
          });
          return;
        }
        if(this.nameValue == "" && this.jobValue =="" && this.reasonValue ==""){
          _this.$message({
            message: "请填写信息",
            type: 'error'
          });
          return;
        }

        if(this.nameValue == ""){
          _this.$message({
            message: "员工姓名不能为空",
            type: 'error'
          });
          return;
        }else if(this.jobValue ==""){
          _this.$message({
            message: "员工职位不能为空",
            type: 'error'
          });
          return;
        }else if(this.reasonValue ==""){
          _this.$message({
            message: "推荐理由不能为空",
            type: 'error'
          });
          return;
        }
        this.InsertData = {
          name : this.nameValue.trim(),
          job : this.jobValue,
          type:this.typeValue,
          uid:30,
          reason : this.reasonValue.trim()
        };

        var addPeople = {
          "interface":"insert",
          "data":this.InsertData
        }
        console.log(this.InsertData)
        if(this.openSocket){
          this.websocketsend(addPeople);
        }else{
          _this.$message({
            message: "socket未连接",
            type: 'error'
          });
        }


      },
      initWebSocket(){ //初始化weosocket
        console.log("insocket")
        const wsuri = "ws://192.168.5.156:9527";//ws地址
        this.websock = new WebSocket(wsuri);
        this.websock.onopen = this.websocketonopen;

        this.websock.onerror = this.websocketonerror;

        this.websock.onmessage = this.websocketonmessage;
        this.websock.onclose = this.websocketclose;
      },

      websocketonopen() {
        console.log("WebSocket连接成功");

        this.openSocket = true
        //获取列表

        var getPeoplesdata = {
          "interface" : 'getPeoples',
          data : ''
        }
        this.websocketsend(getPeoplesdata);


      },
      websocketonerror(e) { //错误
        console.log("WebSocket连接发生错误");
        this.openSocket = false
      },
      websocketonmessage(event){ //数据接收
        var _this = this;
        //注意：长连接我们是后台直接1秒推送一条数据，
        //但是点击某个列表时，会发送给后台一个标识，后台根据此标识返回相对应的数据，
        //这个时候数据就只能从一个出口出，所以让后台加了一个键，例如键为1时，是每隔1秒推送的数据，为2时是发送标识后再推送的数据，以作区分
        event = JSON.parse(event.data)
        console.log(event)

        switch (event.interface){
          case "getPeoples":
            if(event.code == 200){
              this.PeopleData = event.data;
            }else{
              _this.$message({
                message: event.message,
                type: 'error'
              });
            }

            break;
          case "insert":
            if(event.code == 200){
              this.PeopleData.push(this.InsertData);
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
      },
    }
  }
</script>

<style lang="scss" type="text/css" scoped>
  @import "../styles/InformationEntry.scss";
</style>
