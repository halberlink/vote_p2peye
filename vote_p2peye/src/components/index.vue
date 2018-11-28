<template>
  <div class="hello">
    <span @click="jumpTo('/InformationEntry')">PC展示开始投票</span>

  </div>
</template>

<script>
  export default {
    name: 'HelloWorld',
    data () {
      return {
        msg: 'Welcome to Your Vue.js App'
      }
    },
    methods:{
      jumpTo:function(url){
        this.$router.push(url)
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  span{
    cursor: pointer;
  }
</style>
