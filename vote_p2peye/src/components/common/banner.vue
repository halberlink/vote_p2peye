<template>
	<div>
		<div class="ui-logobg">
			<div class="ui-logo">
				<img src="../../assets/logo.png" />
			</div>
		</div>
		<div class="ui-bannerbg">
			<div class="ui-banner"></div>
		</div>
	</div>
</template>
<style type="text/css" scoped>
	.ui-logobg{
		height: 75px;
		background-color: #fff;
	}
	.ui-logo{
		margin: 0 auto;
		width: 1170px;
		padding: 12px 0 0 30px;
		text-align: left;
		height: 63px;
		background-color: #fff;
	}
	.ui-bannerbg{
		height: 230px;
		margin-bottom: 20px;
		background-color: #0099ee;
	}
	.ui-banner{
		height: 230px;
		width: 1200px;
		margin: 0 auto;
		background: url('../../assets/banner.png') no-repeat 0 -74px ;
	}
	.ui-content{

	}
</style>
<style type="text/css">
	.ui-contentbg{
		background: url('../../assets/banner.png') no-repeat 0 -325px ;
	}
</style>
<script type="text/javascript">
  export default {
    name: 'banner',
    data () {
      return {
      }
    },
    methods:{
    }
  }
</script>
