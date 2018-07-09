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
    	//添加幻灯片
    	$('#save').click(function() {
			addAticle();
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

        });
    	
    }

    /**
     * 添加文章
     */
    function addAticle(){
        $("#myForm").ajaxSubmit($.extend(true,{},formOptions,myFormOptions));
    }

    /**
     * 提交添加商品信息的表单配置
     */
    var  myFormOptions={
        url:host+'/index.php/admin/Petaticle/insertAticle' ,
        success:successResponse,
        error:errorResponse
    };
    /**
     * 添加商品信息得到服务器响应的回调方法
     */
    function successResponse(json,statusText){
        if(json.errorCode == 0){
            responseTip(json.errorCode,"恭喜您，操作成功！",1500,function(){window.history.go(-1);});
        }else{
            responseTip(json.errorCode,json.errorInfo,1500);
        }
    }

    //表单验证信息
    var validateInfo ={
        rules:{
            imgurl:{
                required:true,
                accept:"jpg,jpeg,gif,png"
            }
        },
        messages:{

            imgurl:{
                required:"请选择图片",
                accept:"仅支持jpg、jpeg、gif、png格式"
            }
        },
        errorPlacement:function(error,element){

            element.parent().next().append(error);
        }
    };
   init();

});