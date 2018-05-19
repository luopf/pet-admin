$(function(){
    var host = getJSurl();
    var total = 1;//分页总页面数
    var total_count = 0;//总记录数
    var currentPage = 1;//当前页
    var pageSize = pageOption.pageSize;//每页显示的记录数
    var idList = [];//被选中的商品主键集合
    //权限控制
    
    function init(){
        bindEvent();
    }

    function bindEvent(){
        myPagination();
        /**
         * 模糊查询事件
         *
         */
        $(".search-button").click(function(){
        	render(true,1,pageSize);
        });
        //enter事件
        $(".search-param-panel input").keydown(function(event){
            event = event ? event:window.event;
            if(event.keyCode == 13){
            	render(true,1,pageSize);
            }
        });
 
        //批量删除
        $(".delete-batch").click(function(){
        	var ids = idList.join(',');
            if(ids == ""){
        		$("#myModal .modal-body").html("<p class='text-danger'>您尚未选择要删除的选项，请先选择！</p>");
                $("#myModal").modal('show');
                //定时器，1.5秒后模态框自动关闭
                setTimeout(function(){
                    $("#myModal").modal('hide');
                },1500);
        	}else{
	            myConfirmModal("确定要批量删除评价信息吗？",function(){
		            $.ajax({
		                url:"./admin.php?c=dining_comment&a=deleteCommentBatch",
		                type:"post",
		                data:{"ids":ids},
		                dataType:"json",
		                beforeSend:function(xhr){
		                    //显示“加载中。。。”
		                    $("#loading").modal('show');
		                },
		                complete:function(){
		                    //隐藏“加载中。。。”
		                    $("#loading").modal('hide');
		                },
		                success:function(json,statusText){
		                    if(json.errorCode == 0){
		                    	if(currentPage != 1 && (total_count - idList.length) % pageSize == 0){
	                                currentPage = currentPage - 1;
	                            }
	                            idList = [];//初始化idList的值
	                            render(true,currentPage,pageSize);
		//                          window.location.reload();
		                            //window.location.href = "./admin.php?c=base_user&a=commentList";
		                    }else{
		                        //alert("添加失败，请稍后再试！");
		                        $("#myModal .modal-body").html("<p class='text-danger'>"+json.errorInfo+"</p>");
		                        $("#myModal").modal('show');
		                        //定时器，1.5秒后模态框自动关闭
		                        setTimeout(function(){
		                            $("#myModal").modal('hide');
		                        },1500);
		                    }
		                },
		                error:errorResponse
		            });
	            });
        	}
        });
        
        //统一回复
        $(".replybatch").click(function(){
        	$("#myReplyModal .modal-body .info").val('');//清空输入框的值
        	var ids = idList.join(',');
            if(ids == ""){
        		$("#myModal .modal-body").html("<p class='text-danger'>您尚未选择要回复的选项，请先选择！</p>");
                $("#myModal").modal('show');
                //定时器，1.5秒后模态框自动关闭
                setTimeout(function(){
                    $("#myModal").modal('hide');
                },1500);
        	}else{
	            myReplyModal("请填写回复内容",function(info){
		            $.ajax({
		                url:"./admin.php?c=dining_comment&a=replyCommentBatch",
		                type:"post",
		                data:{"ids":ids,"reply":info},
		                dataType:"json",
		                beforeSend:function(xhr){
		                    //显示“加载中。。。”
		                    $("#loading").modal('show');
		                },
		                complete:function(){
		                    //隐藏“加载中。。。”
		                    $("#loading").modal('hide');
		                },
		                success:function(json,statusText){
		                	 if(json.errorCode == 0){
		 	                    $("#myModal .modal-body").html("<p class='text-success'><b>恭喜您，评价成功！</b></p>");
		 	                    $("#myModal").modal('show');
		 	                    //定时器，1.5秒后模态框自动关闭
		 	                    setTimeout(function(){
		 	                        $("#myModal").modal('hide');
		 	                       if(currentPage != 1 && (total_count - idList.length) % pageSize == 0){
		                                currentPage = currentPage - 1;
		                            }
		                            idList = [];//初始化idList的值
		                            render(true,currentPage,pageSize);
		 	                    },1500);
		                	 }else{
		 	                    //alert("添加失败，请稍后再试！");
		 	                    $("#myModal .modal-body").html("<p class='text-danger'>"+json.errorInfo+"</p>");
		 	                    $("#myModal").modal('show');
		 	                    //定时器，1.5秒后模态框自动关闭
		 	                    setTimeout(function(){
		 	                        $("#myModal").modal('hide');
		 	                    },1500);
		 	                 }
		 	            },
		                error:errorResponse
		            });
	            });
        	}
        });
    }

    /**
     * 分页显示方法
     */
    function myPagination(){
        render(true,1,pageSize);
        //调用公共分页方法
        pagination("#page-selection",{total:total,pageSize:pageSize},render);

    }
    
    /**
     * 全选
     */
    function selectAll(){
        var boxs = $("input.select-single");//所有商品记录
        //被选中
        if($(this).prop("checked")){
            boxs.prop("checked",true);//复选框全部选中
            boxs.each(function(){
                if(idList.indexOf($(this).val()) < 0){//idList中不包含当前id值，则加入
                    idList.push($(this).val());
                }
            });
        }else{
            //全部取消
            boxs.prop("checked",false);//复选框全部取消选中
            //从idList数组中删除当前id
            boxs.each(function(){
                var index = idList.indexOf($(this).val());
                if(index >= 0){//idList中包含当前id值，则删除
                    idList.splice(index,1);
                }
            });
        }
        $("#ids").val(idList.join(","));//将当前选中的商品主键写入隐藏域gid中
    }
    
    /***
     * 单选事件
     */
    function selectSingle(){
        if($(this).prop("checked")){//单选选中时
            if(idList.indexOf($(this).val()) < 0){//idList中不包含当前id值，则加入
                idList.push($(this).val());
            }
            if($(this).parents("#list-table").find(".select-single").length == $(this).parents("#list-table").find(".select-single:checked").length){
                //所有复选框都选中时，将全选复选框置为选中状态
                $(this).parents("#list-table").find(".select-all").prop("checked",true);
            }
        }else{//单选复选框取消选中时
            //从idList数组中删除当前id
            var index = idList.indexOf($(this).val());
            if(index >= 0){//idList中包含当前id值，则删除
                idList.splice(index,1);
            }
            $(this).parents("#list-table").find(".select-all").prop("checked",false);
        }
        $("#ids").val(idList.join(","));//将当前选中的商品主键写入隐藏域id中
    }
    
    /**
     * 删除单条记录
     */
    function deleteOne(){
	     var id = $(this).attr("data_id");
	     var parent = $(this).parent();
	     var isCheck = parent.prev().attr("ischeck");
	     if(isCheck != 0){
	    	myConfirmModal("确定要删除该条评价信息吗？",function(){
	            $.ajax({
	                url:"./admin.php?c=dining_comment&a=deleteComment",
	                type:"post",
	                data:{"id":id},
	                dataType:"json",
	                beforeSend:function(xhr){
	                    //显示“加载中。。。”
	                    $("#loading").modal('show');
	                },
	                complete:function(){
	                    //隐藏“加载中。。。”
	                    $("#loading").modal('hide');
	                },
	                success:function(json,statusText){
	                    if(json.errorCode == 0){
	                    	if(currentPage != 1 && (total_count - idList.length) % pageSize == 0){
                                currentPage = currentPage - 1;
                            }
                            idList = [];//初始化idList的值
                            render(true,currentPage,pageSize);
	                    }else{
	                        //alert("添加失败，请稍后再试！");
	                        $("#myModal .modal-body").html("<p class='text-danger'>"+json.errorInfo+"</p>");
	                        $("#myModal").modal('show');
	                        //定时器，1.5秒后模态框自动关闭
	                        setTimeout(function(){
	                            $("#myModal").modal('hide');
	                        },1500);
	                    }
	                },
	                error:errorResponse
	            });
	    	});
	     }else{
	     	$("#myModal .modal-body").html("<p class='text-danger'>很抱歉，尚未审核，不能删除！</p>");
	         $("#myModal").modal('show');
	         //定时器，1.5秒后模态框自动关闭
	         setTimeout(function(){
	             $("#myModal").modal('hide');
	         },1500);
	     }
	 }
	
  //自定义回复对话框
    function myReplyModal(alertInfo,callback){//参数，当前操作提示文本
        $("#myReplyModal .modal-body .text-danger").html(alertInfo);
       
        $("#myReplyModal").modal('show');//对话框显现

        //提交回复--通过操作
        $("#myReplyModal .btn-success").one('click',function(){//一次点击
            var info = $("#myReplyModal .modal-body .info").focus().val();//获取输入框的值
            if(info != ""){
                $("#myReplyModal").modal('hide');//对话框显现
                callback(info);//调用回调函数
            }
        });
    }
    /**
     * 审核判断
     */
    function reply(){
    	$("#myReplyModal .modal-body .info").val('');//清空输入框的值
    	var id = $(this).attr("data_id");
//        var isCheck = $(this).attr("data");
        myReplyModal("请填写回复内容",function(info){
	        $.ajax({
	            url:host+'/index.php/admin/comment/replyComment',
	            type:"post",
	            data:{"id":id,"reply":info},
	            dataType:"json",
	            beforeSend:function(xhr){
	                //显示“加载中。。。”
	                $("#loading").modal('show');
	            },
	            complete:function(){
	                //隐藏“加载中。。。”
	                $("#loading").modal('hide');
	            },
	            success:function(json,statusText){
                    if(json.errorCode == 0){
                        $("#myModal .modal-body").html("<p class='text-success'><b>恭喜您，回复成功！</b></p>");
                        $("#myModal").modal('show');
                        //定时器，1.5秒后模态框自动关闭
                        setTimeout(function(){
                            $("#myModal").modal('hide');
                            if(currentPage != 1 && (total_count - idList.length) % pageSize == 0){
                                currentPage = currentPage - 1;
                            }
                            idList = [];//初始化idList的值
                            render(true,currentPage,pageSize);
//	                      window.location.reload();
                            //window.location.href = "./admin.php?c=base_user&a=commentList";
                        },1500);
                    }else{
                        //alert("添加失败，请稍后再试！");
                        $("#myModal .modal-body").html("<p class='text-danger'>"+json.errorInfo+"</p>");
                        $("#myModal").modal('show');
                        //定时器，1.5秒后模态框自动关闭
                        setTimeout(function(){
                            $("#myModal").modal('hide');
                        },1500);
                    }
	            },
	            error:errorResponse
	        });
        })
    }

    /**
     * 获取模糊参数
     */
    function getSelectInfo(){
        var nick_name = $.trim($("#nick_name").val());
        var goods_name = $.trim($("#goods_name").val());
        var level = $("#level").val();
        var is_reply = $("#is_reply").val();
        var has_image = $("#has_image").val();
        var is_anonym = $("#is_anonym").val();
        var from = $('#startTime').val();
        var to = $('#endTime').val();
        var selectInfo = {
            "nick_name":nick_name,
            "goods_name":goods_name,
            "is_reply":is_reply,
            "has_image":has_image,
            "level":level,
            "is_anonym":is_anonym,
            "from":from,
            "to":to
        };
        return selectInfo;
    }
    
    /**
     * 分页动态渲染数据
     * @param async ajax请求是否异步
     * @param pageIndex 当前显示页
     * @param pageSize 每页显示记录数
     */
    function render(async,pageIndex,pageSize){
        var selectInfo = getSelectInfo();
        selectInfo.pageIndex = pageIndex;
        selectInfo.pageSize = pageSize;
        $.ajax({
            async:async,
            type:'post',
            url:host+'/index.php/admin/comment/pagingComment',
            data:selectInfo,//从1开始计数
            dataType:'json',
            success:function(result){
                var html ='';
                if(result.errorCode == 0){
                    total_count = result.data.pageInfo.total_count;
                	total = result.data.pageInfo.total_page;
                    $("#page-selection").bootpag({total:total,total_count:total_count});//重新计算总页数,总记录数
                    currentPage = result.data.pageInfo.current_page;
                    var commentList = result.data.dataList;
                    
                    html+='<tr><th class="th1"><input type="checkbox" class="select-all my-icheckbox"></th><th class="th2">序号</th><th class="th3">昵称</th><th class="th8">发布消息编号</th><th class="th5">评分</th><th class="th6">等级</th><th class="th7">评价内容</th><th class="th8">评价时间</th><th class="th9">图片</th><th class="th9">匿名</th><th class="th10">是否回复</th><th class="th11">操作</th></tr>';
                    $(".inner-section #list-table tbody").html(html);
                    var thLength = $("#list-table tr th").length;
                    for(var i = 0; i < commentList.length;i++){
                        var obj = commentList[i];
                        var number = (pageIndex - 1)*pageSize + i + 1;//序号
                        var oid = obj.oid;
                        var mess_num = obj.mess_num;
                        var gid = obj.gid;
                        var goods_name = obj.goods_name;
                        var level = (obj.level == 1)?'<span class="text-success">好评</span>':(obj.level == 2)?'中评':(obj.level == 3)?'<span class="text-danger">差评</span>':'--';
                        var score = obj.score;
                        var user_id = obj.user_id;
                        var nick_name = obj.nick_name;
                        var add_time = obj.add_time;//评论时间
                        var is_reply = obj.is_reply;
                        var reply_content = obj.reply_content;
                        var replyText = (obj.is_reply == 0)?'<span class="text-danger">未回复</span>':'<span class="text-success" data-toggle="popover" data-trigger="hover" data-placement="top" title="'+reply_content+'">已回复</span>';
                        var comment_content = obj.content;
                        var reply_time = obj.reply_time;
                        var reply_account = obj.reply_account;
                        var has_image = (obj.has_image == 1)?'有':'无';
                        var is_anonym =(obj.is_anonym == 1)?'是':'否';
                        var disableText = (is_reply == 0)?' class="select-single my-icheckbox"':'class="my-icheckbox" disabled';
                       if(obj.is_anonym == 1){
                    	   nick_name = nick_name[0]+'***'+nick_name[nick_name.length-1];
                       }
                        var id = obj.id;
                        
                    	html+='<tr>'
                    			+'<td><input type="checkbox"  class="select-single my-icheckbox" value="'+id+'"  data_id="'+id+'"'+disableText+'></td>'
                    			+'<td>'+number+'</td>'
                    			+'<td><a href="'+host+'/index.php/admin/User/userDetail/uid/'+user_id+'" class="limitName" title="查看用户详情">'+nick_name+'</a></td>'
                    			+'<td><a href="'+host+'/index.php/admin/Message/messageDetail/mid/'+mess_num+'" title="查看消息详情">'+mess_num+'</a></td>'
                    			// +'<td><a href="./admin.php?c=dining_goods&amp;a=goodsDetail&amp;id='+gid+'" class="limitName" title="查看商品详情">'+goods_name+'</a></td>'
                    			+'<td>'+score+'</td>'
                    			+'<td>'+level+'</td>'
                    			+'<td><span class="limitName" title="'+comment_content+'">'+comment_content+'</span></td>'
                    			+'<td>'+add_time+'</td>'
                    			+'<td>'+has_image+'</td>'
                    			+'<td>'+is_anonym+'</td>'
                    			+'<td>'+replyText+'</td>'
                    			+'<td>'+commentOperation(id,is_reply)+'</td>'
                    		+'</tr>';
                    }
                    if(commentList.length == 0){
                        html += '<tr><td colspan="'+thLength+'"><p class="text-danger">查询结果为空。</p></td></tr>';
                    }
                    $(".inner-section #list-table tbody").html(html);
                    //全选事件
                    myCheck();
                    batchSelect(idList,".inner-section #list-table .select-all",".inner-section #list-table .select-single");
                    $(".inner-section [data-toggle='popover']").popover();
                  //全选事件
                    $(".inner-section .select-all").click(selectAll);
                    $(".inner-section .select-single").click(selectSingle);
                  //单条删除
                    $(".inner-section .delete").click(deleteOne);
                  //回复操作
                    $(".inner-section .reply").click(reply);
                }else{
                    responseTip(json.errorCode,json.errorInfo,1500);
                }
            },
            error:errorResponse
        });
       
    }
    
    /**
     * 获取订单操作
     * @param is_reply 回复状态
     * @param id 评论id
     */
    function commentOperation(id,is_reply){
        var html = "";
        switch (is_reply)
        {
            case 1://已回复
                html +='<a class="btn btn-xs btn-primary view" href="'+host+'/index.php/admin/comment/commentDetail/cid/'+id+'">查看</a>';
                html +='<a href="javascript:;" class="btn btn-default btn-xs delete" data_id="'+id+'">删除</a>';
                break;
            case 0://未回复
                html +='<a class="btn btn-xs btn-primary view" href="'+host+'/index.php/admin/comment/commentDetail/cid/'+id+'">查看</a>';
                html +='<a class="btn btn-xs btn-danger reply" href="javascript:;" data_id="'+id+'">回复</a>';
                html +='<a href="javascript:;" class="btn btn-default btn-xs delete" data_id="'+id+'">删除</a>';
                break;
            //默认状态是已删除  ——安全性要求
            default:html+="--"; //已删除

        }
        console.log(id);
        console.log(is_reply);
        console.log(html);
        return html ;
    }
    init();
});