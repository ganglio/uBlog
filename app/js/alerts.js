$(document).ready(function(){

	$(".alert.has-progress").each(function(i,e){
		$(e).append("<div class='tn-progress'></div>");
		var time=$(e).data("time");
		$(".tn-progress",e).animate({
			width:"100%"
		},time,'linear',function(){
			$(this).siblings(".close").click();
		});
	});
});