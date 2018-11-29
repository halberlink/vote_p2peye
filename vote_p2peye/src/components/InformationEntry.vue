<template>
  <div class="InformationEntry">
    <h1>员工信息录入</h1>
    <div class="insert">
      <div class="top">
        <el-row :gutter="20">
          <el-col :span="12">
            <div class="grid-content bg-purple">
              <el-input v-model="nameValue" placeholder="请输入员工姓名"></el-input>
            </div>
          </el-col>
          <el-col :span="12">
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
        PeopleData: [],
        lock:false
      }
    },
    created:function(){
      var _this = this;
      this.$http.post("http://192.168.9.215/server/getAllpeoples.php",{},{
        headers:{'Content-Type':'application/x-www-form-urlencoded'}
      }).then(function(res){
        _this.PeopleData = res.data.data

      }).catch(function(res){

      });
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
        var Data = {
          name : this.nameValue.trim(),
          job : this.jobValue,
          reason : this.reasonValue.trim()
        };

        var postData = this.$qs.stringify(Data);

        this.lock = true;
        this.$http.post("http://192.168.9.215/server/insert.php",postData,{
          headers:{'Content-Type':'application/x-www-form-urlencoded'}
        }).then(function(res){
          if(res.data.code == 200){
            _this.PeopleData.push(Data);
            _this.$message({
              message: '添加成功',
              type: 'success'
            });
          }else{
            _this.$message({
              message: res.data.message,
              type: 'error'
            });
          }
          _this.lock = false;
        }).catch(function(res){
          _this.lock = false;
          _this.$message({
            message: "服务异常，请稍后再试",
            type: 'warning'
          });

        });
      }
    }
  }
</script>

<style lang="scss" type="text/css" scoped>
  @import "../styles/InformationEntry.scss";
</style>
