$(document).ready(function(){
	$("#modal").modal('hide');

	$("a[data-toggle=modal]").click(function(){
		var $target=$(this).data("target");
		var href=$(this).attr("href");
		$($target).load(href);
	});

	$("#modal").on("click",".submit",function(){
		var $form = $("#modal form");
		if ("ajax" !== $form.attr("method")) {
			$form.submit();
		} else {
			var values = $form.serializeArray();
			$.post($form.attr("action"),
				values,
				function(data){
					$("#modal").html(data);
				},'html')
		}
	});

	$(".sortable").sortable({
			handle: ".handle",
			stop: function(e,u) {
				$("tr",e.target).each(function(i,e){
					$("[name=weight]",e).val(i);
				});
				$(e.target).parent().addClass("modified");
			}
		});

	$(".sort-update").click(function(){
		var status = "";
		$(".sortable .handle").each(function(i,e){
			var id = $("[name=id]",e).val();
			var weight = $("[name=weight]",e).val();
			status += "weight["+id+"]="+weight+"&";
		});
		$.post("/widgets/latest/fixweight",
			status,
			function(data){
				$(".sortable").parent().removeClass("modified");
			},'text');
	});

	$("#platform-selector").change(function(){
		var platform=$(this).val();
		$(".platform-wrapper").hide();
		$(".platform-wrapper[data-platform="+platform+"]").show();
	}).change();
});