$(document).ready(function(){
	var removed = [];
	$(".sortable").sortable({
			handle: ".handle",
			stop: function(e,u) {
				$("tr",e.target).each(function(i,e){
					$("[id$=weight]",e).val(i);
				});
				$(e.target).parent().addClass("modified");
			}
		});

	$("#add_media").click(function(e){
		e.preventDefault();

		var used_types = [];
		$(".sortable select").each(function(i,e){
			var type = $(e).val();
			if ("screenshot" != type)
				used_types.push(type);
		});

		var media_prototype = $("#media_prototype").html().replace(/__name__/gi,$(".sortable tr").length);
		$(".sortable").append(media_prototype);
		$(".sortable tr:last-child option").each(function(i,e){
			if (-1 != used_types.indexOf($(e).val()) && $(e).val())
				$(e).remove();
		});
		if (2 == $(".sortable tr:last-child option").size())
			$(".sortable tr:last-child option[value='']").remove();
	});

	$(".sortable select, .sortable [type=file]").change(function(){
		$(".sortable").parent().addClass("modified");
	});

	$(".sortable").on("click",".delete i",function(e){
		var $row = $(e.target).parents("tr");
		removed.push($row.find("[id$=id]").val());
		$row.remove();
		$(".sortable").parent().addClass("modified");
		$("#node_removed").val(removed.join(","));
	});

	$("#back").click(function(){
		window.location.href="/admin";
	});
});