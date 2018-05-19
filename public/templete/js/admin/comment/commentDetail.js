$(function(){
    $(".content .right-section #myForm .image-border").mouseover(function(){
    	$(this).addClass("image-border-color");
    }).mouseout(function(){
    	$(this).removeClass("image-border-color");
    }).click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass("active");
			$(".content .right-section #myForm .show-img").addClass("view");
			$(this).next().addClass('trianle-see');
		}else{
			$(this).parent().siblings().children().removeClass('active');
			$(this).parent().siblings().find('.trianle').addClass('trianle-see');
			$(this).next().removeClass('trianle-see');
			$(this).addClass("active");
			var src = $(this).attr("data");
			$(".content .right-section #myForm .show-img").removeClass("view");
			$(".content .right-section #myForm .show-img img").attr("src",src);
		}
	});
});