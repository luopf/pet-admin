$(function(){
    var host = getJSurl();
    /**
     * 分页初始条件
     */
    var total_count = 1;//分页总记录数
    var total = 1;//分页总页面数
    var currentPage = 1;//当前页
    var pageSize = pageOption.pageSize;//每页显示的记录数
    var idList = [];//批量选择id所存的数组

    /**
     * 页面初始化
     */
    function init(){
        myPagination();
        bindEvent();
    }

    /**
     * 事件绑定
     */
    function bindEvent(){
        //根据条件查询商品信息
        $('.inner-section #search_btn').click(function() {
            render(true,1,pageSize);
            return false;
        });
        //enter键盘事件
        $(".inner-section .search-param-form input").keydown(function(event){
            event = event ? event: window.event;
            if(event.keyCode == 13){
                render(true,1,pageSize);
                return false;
            }
        });
        
        $('.row #import_btn').click(function() {
            var selectInfo = getSelectInfo();
            var data = $.param(selectInfo);
            window.location.href = "./admin.php?c=store_order&a=importExcel&"+data;
        });
          
      //批量删除
        $(".content .right-section .delete-batch").click(function(){
        	var ids = idList.join(',');
            if(ids == ""){
        		$("#myModal .modal-body").html("<p class='text-danger'>您尚未选择要删除的选项，请先选择！</p>");
                $("#myModal").modal('show');
                //定时器，1.5秒后模态框自动关闭
                setTimeout(function(){
                    $("#myModal").modal('hide');
                },1500);
        	}else{
	            myConfirmModal("确定要批量删除订单吗？",function(){
		            $.ajax({
		                url:"./admin.php?c=store_order&a=batchDeleteOrder",
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
		                    }else{
		                        responseTip(1,json.errorInfo);
		                    }
		                },
		                error:errorResponse
		            });
	            });
        	}
        });
    }

    /**
     * 获取模糊参数
     */
    function getSelectInfo(){

        var selectInfo = {
            user_name : $.trim($("#user_name").val()),//发布者姓名
            phone : $.trim($("#phone").val()),// 联系电话
            cate_name : $.trim($("#cate_name").val()),//宠物品种
            from : $('#startTime').val(),
            to : $('#endTime').val(),
            isdelete : 0
        };
        return selectInfo;
    }
    /**
     * 分页显示方法
     */
    function myPagination(){
        render(true,1,pageSize);
        //调用公共分页方法
        pagination("#page-selection",{pageSize:pageSize,total:total},render);

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
            url:host+'/index.php/admin/doctor/pagingDisease',
            data:selectInfo,//从1开始计数
            dataType:'json',
            success:function(result){

                var html ='';
                if(result.errorCode == 0){
                    total = result.data.pageInfo.total_page;
                    total_count = result.data.pageInfo.total_count;
                    $("#page-selection").bootpag({total:total,total_count:total_count});//重新计算总页数,总记录数
                    currentPage = result.data.pageInfo.current_page;
                    var myList = result.data.dataList;
                    html+='<tr><th class="th1"><input type="checkbox" class="select-all my-icheckbox"></th><th class="th1">序号</th><th class="th2">用户名</th><th class="th3">宠物品种</th><th class="th4">联系方式</th><th class="th8">病情情况</th><th class="th9">发布时间</th><th class="th10">操作</th></tr>';
                    var colspan = $(html).find("th").length;
                    for(var i = 0; i < myList.length;i++){
                        var obj = myList[i];
                        var num = (pageIndex-1)*pageSize + i+1;// 序号
                        var user_name = obj.user_name || "--";// 用户名
                        var cate_name = obj.cate_name || "--";// 宠物品种
                        var content = obj.content || "--";// 病情情况
                        var phone = obj.phone || "__";// 用户的联系方式
                        var add_time = obj.add_time || "--";// 发布的时间

                        var account = obj.account;//用户的account



                        var order_type_text = "--";




                         var addressInfo = obj.address_text ? obj.address_text:"--";
                        var totalprice = obj.total_price;

                        var end_time = obj.end_time;
                        var contact_time = obj.contact_time;
                        var express_code = obj.express_code;
                        var express_number = obj.express_number;
                        var oid = obj.id;
                        var paymethod = obj.pay_method;
                        var paymethod_text = "";


                        var operation = orderOperatoin('1',oid);

                        var message = obj.message ? obj.message : "--";
                        var checked = (idList.indexOf(oid) >= 0) ? "checked":"";//判断当前记录先前有没有被选中
                        html+='<tr>'
                        	+'<td><input type="checkbox" class="select-single my-icheckbox" value="'+oid+'" '+checked+'></td>'
                            +'<td class="th1">'+num+'</td>'
                            +'<td class="th2">'+user_name+'</td>'
                            +'<td class="th3">'+cate_name+'</td>'
                            +'<td class="th4">'+phone+'</td>'
                            +'<td class="th8">'+content+'</td>'
                            +'<td class="th9">'+add_time+'</td>'
                            +'<td>'+operation+'</td>'
                            +'</tr>';
                    }
                    if(myList.length == 0){
                        html += '<tr><td colspan="'+colspan+'"><p class="text-danger">暂无数据。</p></td></tr>';
                        $("#list-table tbody").html(html);
                    }else{
                        $("#list-table tbody").html(html);
                        myCheck();
                        batchSelect(idList,".inner-section #list-table .select-all",".inner-section #list-table .select-single");
                        //关闭订单
                        $(".close-message").click(function(){
                            var oid = $(this).attr('oid');
                            myConfirmModal("确认关闭订单吗？",function(){
                                setMessageState(oid,2);
                            });
                        });
                        //发货
                        $(".send-goods").click(function(){
                            var oid = $(this).attr('oid');
                            setOrderState(oid,3);
                        });
                        $("[data-toggle='popover']").popover();
                        //确认收货
                        $(".accept-goods").click(function(){
                            var oid = $(this).attr('oid');
                            setOrderState(oid,3);
                        });
                        // 审核通过
                        $(".set-state-pass").click(function(){
                            var oid = $(this).attr('oid');

                            setMessageState(oid,1);
                        });

                        //完成交易
                        $(".complete-order").click(function(){
                            var oid = $(this).attr('oid');
                            setOrderState(oid,5);
                        });
                        
                        //订单退款
                        $(".refund-order").click(function(){
                        	var oid = $(this).attr('oid');
                        	myConfirmModal("确定要全额退款吗？",function(){
	                        	setOrderState(oid,7);
                        	});
                        });

                        //订单详情
                        $(".order-detail").click(function(){
                            var oid = $(this).attr("oid");
                            window.location.href = "./admin.php?c=store_order&a=orderDetailList&oid="+oid;
                        });
                        //用户详情
                        $(".user-detail").click(function(){
                            var account = $(this).attr("account");
                            window.location.href = "./admin.php?c=base_user&a=orderUserDetail&account="+account;
                        });
                        //修改消息
                        $(".modify-message").click(function(){
                        	var oid = $(this).attr("oid");
                        	window.location.href = getJSurl()+"/index.php/admin/doctor/diseaseDetail/id/"+oid;
                        });
                        //删除订单
                        $(".delete-message").click(deleteMessage);
                        
                        //查看物流信息
                        $(".check-express").click(function(){
                        	var code = $(this).attr('data-code');
                        	var number =  $(this).attr('data-number');
//                        	var httpHost = $('#myBottomNav').attr('data-http');
//                        	var callBackUrl = "http://192.168.1.124/cws/admin.php?c=store_order_a_orderList_mid_29";
                        	window.location.href = "https://m.kuaidi100.com/index_all.html?type="+code+"&postid="+number;
                        });
                    }

                }else{
                    responseTip(result.errorCode,result.errorInfo,1500);
                }
            },
            error:errorResponse
        });
    }

    /**
     * 删除订单
     * @param oid
     * @param state
     */
    function deleteMessage(){
        var oid = $(this).attr('oid');
        myConfirmModal("确定删除当前订单吗？",function(){
            $.ajax(
                {
                    type:"post",
                    url:host+'/index.php/admin/doctor/deleteDisease',
                    data:{"id":oid},
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
                            if(currentPage !=1 && total_count % pageSize == 1){//非首页且末页记录数为1时
                                currentPage = currentPage - 1;
                            }
                            render(true,currentPage,pageSize);
                        }else{
                            responseTip(json.errorCode,json.errorInfo,1500);
                        }
                    },
                    error:errorResponse
                }
            );
        });
    }
    
    /**
     * 设置消息状态
     */
    function setMessageState(oid,state){
    	$.ajax(
            {
                type:"post",
                url:host+'/index.php/admin/message/setMessageState',
                data:{'oid':oid,'state':state},
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
                        render(true,currentPage,pageSize);
                    }else{
                    	responseTip(json.errorCode,json.errorInfo,1500);
                    }
                },
                error:errorResponse
            }
        );
        
    }

    /**
     * 设置订单的中文状态
     * //0待付款  1待发货 2已发货 3已收货 4交易关闭 5交易完成
     */
    function setState(state,express_code,express_number){
        var html = "";
        switch (state)
        {
            case '0':
                html ="<span class='text-info'>待付款</span>";
                break;
            case '1':
                html ="<span style='color:green'>已付款</span>";// 已支付
                break;
            case '2':
                html ="<span class='text-primary check-express' data-code='"+express_code+"' data-number = '"+express_number+"'>已发货(物流)</span>";
        	    html ="<a href='javascript:;'  data-code='"+express_code+"' data-number = '"+express_number+"' class='check-express' title='查看物流'><span class='text-primary'>待审核</span></a>";
            	break;
            case '3':
                html ="<a href='javascript:;'  data-code='"+express_code+"' data-number = '"+express_number+"' class='check-express' title='查看物流'><span class='text-primary'>已审核</span></a>";
                break;
            case '4':
                html ="<span style='color:gray'>已过期</span>";
                break;
            case '5':
                html ="<a href='javascript:;'  data-code='"+express_code+"' data-number = '"+express_number+"' class='check-express' title='查看物流'><span class='text-success'>交易完成</span></a>";
                break;
            case '6':
            	html ="<span class='text-danger'>申请退款</span>";
            	break;
            case '7':
            	html ="<span class='text-info'>退款成功</span>";
            	break;
            //默认状态是已删除  ——安全性要求
            default :html="--";
        }
        return html;
    }

    /**
     * 获取订单操作
     * @param state 订单状态
     * @param oid 订单id
     */
    function orderOperatoin(state,oid){
        var html = "";
        switch (state)
        {
            case '0'://未审核
                html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-message'title='设置'>查看</a>";
                html +="<a  href='javascript:;'  oid ='"+oid+"'  class='btn btn-primary btn-xs set-state-pass' state='1'>审核通过</a>";
                html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-danger btn-xs close-message'title='关闭消息'>关闭</a>";
                break;
            case '1'://已审核
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-message'title='设置'>查看</a>";
                html +="<a  href='javascript:;'  oid ='"+oid+"'  class='btn btn-danger btn-xs delete-message title='关闭消息'>删除</a>";
                break;
            case '2'://审核失败
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-message'title='设置'>查看</a>";
                html+="<a  href='javascript:;'  oid ='"+oid+"' class='btn btn-secondary btn-xs delete-message' title='删除消息'>删除</a>";
                break;
            case '3'://已下架
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-message'title='设置'>查看</a>";
                html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-message'title='设置'>上架</a>";
                html+="<a  href='javascript:;'  oid ='"+oid+"'  class='btn btn-danger btn-xs close-message' title='关闭订单'>关闭</a>";
                break;
            case '4'://已过期
                html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs order-detail' title='设置'>查看</a>";
                html+="<a  href='javascript:;'  oid ='"+oid+"' class='btn btn-secondary btn-xs delete-order'>删除</a>";
                break;
            case '5'://交易成功
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-order'title='设置'>查看</a>";
                html+="<a  href='javascript:;'  oid ='"+oid+"' class='btn btn-secondary btn-xs delete-order'>删除</a>";
                break;
            case '6'://申请退款
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-order'title='设置'>查看</a>";
            	html+="<a  href='javascript:;'  oid ='"+oid+"'  class='btn btn-danger btn-xs refund-order' title='同意退款'>退款</a>";
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-order'title='设置'>设置</a>";
            	html +="<a  href='javascript:;'  oid ='"+oid+"'  class='btn btn-primary btn-xs send-goods'>发货</a>";
            	break;
            case '7'://退款成功
            	html +="<a  href='javascript:;' oid ='"+ oid+"' class='btn btn-primary btn-xs modify-order'title='设置'>查看</a>";
            	html+="<a  href='javascript:;'  oid ='"+oid+"' class='btn btn-secondary btn-xs delete-order'>删除</a>";
            	break;
            //默认状态是已删除  ——安全性要求
            default:html+="--"; //已删除

        }
        return html ;
    }
    init();
});