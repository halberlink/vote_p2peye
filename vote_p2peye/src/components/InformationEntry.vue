<template>
  <div class="InformationEntry">
    <el-button>新增人员</el-button>
    <el-row :gutter="20">
      <el-col :span="6"><el-input v-model="input" placeholder="请输入内容"></el-input></el-col>
      <el-col :span="6"><div class="grid-content bg-purple"></div></el-col>
      <el-col :span="6"><div class="grid-content bg-purple"></div></el-col>
      <el-col :span="6"><div class="grid-content bg-purple"></div></el-col>
    </el-row>

  </div>
</template>

<script>
  export default {
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App',
        input:""
      }
    }
  }
</script>

<style lang="scss" type="text/css" scoped>
  @import "../styles/common/base.scss";
  @import "../styles/InformationEntry.scss";
</style>
