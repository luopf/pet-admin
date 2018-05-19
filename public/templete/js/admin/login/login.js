$(function(){
	var host = getJSurl();
	$('#entry').click(function(){
		if($('#adminName').val()==''){
			$('.mask,.dialog').show();
			$('.dialog .dialog-bd p').html('请输入管理员账号');
		}else if($('#adminPwd').val()==''){
			$('.mask,.dialog').show();
			$('.dialog .dialog-bd p').html('请输入管理员密码');
		}else{
			$('.mask,.dialog').hide();
			var account = $('#adminName').val();
			var password = $('#adminPwd').val();
            $.ajax({
                url:host+'/index.php/admin/Login/adminLogin',
                type:"post",
                data:{"account":account,"password":password},
                dataType:"json",
                beforeSend:function(xhr){
                    //显示“加载中。。。”

                },
                complete:function(){
                    //隐藏“加载中。。。”

                },
                success:function(json,statusText){
                    if(json.errorCode == 0){

						window.location.href = host+"/index.php/admin/index/index.html";
                    }
                },
                error:errorResponse
            });


		}
	});



});
