<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>
<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
.ui-listtitle{
	line-height: 70px;
	border-bottom: 1px solid #eee;
	font-size: 30px;
	color: #333;
	text-align: left;
	padding-left: 30px;
}
.face{

}
.face img{
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.mint-header{
  background-color: #09e;
}

</style>
