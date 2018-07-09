$(function(){
        var host = getJSurl();
    /**
     * 页面初始化
     */
    function init(){
		bindEvent();
        //表单的JQueryValidater配置验证---jquery.validate插件验证法
        $("#myForm").validate(validateInfo);

    }
    /**
     * 事件绑定
     */
    function bindEvent(){
    	//添加标签
    	$('#save').click(function() {
			editLabel();
		});

        //添加图片事件
        $("#imgurl").change(function(){
            var filepath=$(this).val();
            if(filepath == ""){
                return false;
            }
            var extStart=filepath.lastIndexOf(".");
            var ext=filepath.substring(extStart,filepath.length).toUpperCase();
            if(ext.toLowerCase()!=".jpg" && ext.toLowerCase()!=".jpeg"
                && ext.toLowerCase()!=".png" && ext.toLowerCase()!=".gif"){
                $(this).val("");
                responseTip(1,"文件格式不正确，仅支持jpg、jpeg、gif、png格式，文件小于5M！",2000);
                return false;
            }
            $("#imgFlag").val(1);//图片更新的标识
        });

        //添加广告图片事件
        $("#ad_image").change(function(){
            var filepath=$(this).val();
            if(filepath == ""){
                return false;
            }
            var extStart=filepath.lastIndexOf(".");
            var ext=filepath.substring(extStart,filepath.length).toUpperCase();
            if(ext.toLowerCase()!=".jpg" && ext.toLowerCase()!=".jpeg"
                && ext.toLowerCase()!=".png" && ext.toLowerCase()!=".gif"){
                $(this).val("");
                responseTip(1,"文件格式不正确，仅支持jpg、jpeg、gif、png格式，文件小于5M！",2000);
                return false;
            }
            $("#imgFlag2").val(1);//图片更新的标识
        });
    	
    }

    /**
     * 添加标签
     */
    function editLabel(){
        $("#myForm").ajaxSubmit($.extend(true,{},formOptions,myFormOptions));
    }

    /**
     * 提交添加商品信息的表单配置
     */
    var  myFormOptions={
        url:host+'/index.php/admin/Petaticle/updateLabel' ,
        success:successResponse,
        error:errorResponse
    };
    /**
     * 添加商品信息得到服务器响应的回调方法
     */
    function successResponse(json,statusText){
        if(json.errorCode == 0){
            responseTip(json.errorCode,"恭喜您，操作成功！",1500,function(){
                window.location.href = host+"/index.php/admin/Petaticle/labellist.html";

            });
        }else{
            responseTip(json.errorCode,json.errorInfo,1500);
        }
    }

    //表单验证信息
    var validateInfo ={
        rules:{
            name:{//标签名称
                required:true
            },
            imgurl:{
                accept:"jpg,jpeg,gif,png"
            },
            ad_image:{
                accept:"jpg,jpeg,gif,png"
            },
            link:{
            	url:true
            },
            goods_num:{
            	required:true
            }
        },
        messages:{
            name:{//商品名称
                required:"请输入名称"
            },
            imgurl:{
                accept:"仅支持jpg、jpeg、gif、png格式"
            },
            ad_image:{
                accept:"仅支持jpg、jpeg、gif、png格式"
            },
            link:{
            	url:"请输入正确的url格式"
            },
            goods_num:{
            	required:"请输入数量"
            }
        },
        errorPlacement:function(error,element){

            element.parent().next().append(error);
        }
    };
   init();

});