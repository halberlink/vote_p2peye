<template>
  <div class="InformationEntry">
    <div class="insert">
      <div class="top">
        <el-row :gutter="20">
          <el-col :span="12">
            <div class="grid-content bg-purple">
              <el-input v-model="input" placeholder="请输入内容"></el-input>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="grid-content bg-purple">
              <el-select v-model="jovValue" placeholder="请选择">
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
              <el-input v-model="input" type="textarea" placeholder="请输入内容"></el-input>
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
          prop="date"
          label="日期"
          width="180">
        </el-table-column>
        <el-table-column
          prop="name"
          label="姓名"
          width="180">
        </el-table-column>
        <el-table-column
          prop="address"
          label="地址">
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
        input:"",
        jovValue: '',
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
        PeopleData: [{
          date: '2016-05-03',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-08',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-06',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-07',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }]
      }
    },
    methods:{
      addPeople:function(){

        this.$http.post("/server/insert.php",postData,{
          headers:{'Content-Type':'application/x-www-form-urlencoded'}
        }).then(function(res){
          res = res.data;
          _this.$loading.hide();
          if(res.code && res.code == 200){
            _this.$toast(res.message);
            _this.$router.back(-1)
          }else{
            _this.$toast(res.message);
          }

        }).catch(function(res){
          _this.$loading.hide();
          _this.$toast("网络错误请稍后再试",2000)
        });
      }
    }
  }
</script>

<style lang="scss" type="text/css" scoped>
  @import "../styles/InformationEntry.scss";
</style>
