<template>
  <div>
    <h1>{{name}}</h1>
    <div class="inseredPeople">
      <el-table
        :data="dataList"
        height="550"
        border
        style="width: 100%">
        <el-table-column
          type="index"
          label="排名">
        </el-table-column>
        <el-table-column
          prop="name"
          label="姓名"
          width="100">
        </el-table-column>
        <el-table-column
          prop="job"
          label="职位"
          width="100">
        </el-table-column>
        <el-table-column
          prop="count"
          label="平均分"
        >
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'countList',
    data () {
      return {
      }
    },
    props:["dataList","name"],
    created:function(){
      var _this = this;

    },
    methods:{
    }
  }
</script>


