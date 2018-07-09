$(function(){
    var host = getJSurl();
    /**
     * 页面初始化
     */
    function init(){
        bindEvent();
    }

    function bindEvent(){
        //跳转到添加页面
        $(".addSlide").click(function(){
            if($("#list-table tr").length > 10){

                responseTip(1,"最多只能有10个幻灯片!",1500);
                return false;
            }
        });
        $(".deleteSlide").click(function(){
			var _this = $(this);
            var id = _this.attr('sid');
            myConfirmModal("确认删除吗？",function(){
                $.ajax({
                    url:host+'/index.php/admin/Petaticle/deleteSlide' ,
                    type:'post',
                    data:{id:id},
                    dataType:'json',
                    beforeSend:function(xhr){
                        //显示“加载中。。。”
                        $("#loading").modal('show');
                    },
                    complete:function(){
                        //隐藏“加载中。。。”
                        $("#loading").modal('hide');
                    },
                    success:function(json){
                        if(json.errorCode == 0){
                        	if(_this.parents(".list-table").find("tr").length == 2){
                        		window.location.reload();	
                        	}else{
                        		_this.parents("tr").remove();
                        	}
                        }else{
                            responseTip(json.errorCode,json.errorInfo,1500);
                        }
                    },
                    error:errorResponse
                });
            });

        });
    }

    init();
});