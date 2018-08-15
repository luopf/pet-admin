/***
 * 夺宝币配置页面
 * @since 2015-11-15
 * @author jjhu
 */
$(function(){
    var host = getJSurl();
    function init(){
        bindEvent();
    }

    function bindEvent(){
        //重新选择门头照片
        $(".inner-section table .select-logo").click(function(){
            //当前门头照路径
            var path = $(this).parent().find("input").val();
            var logos = ['./themes/image/logo_bg_1.jpg','./themes/image/logo_bg_2.jpg','./themes/image/logo_bg_3.jpg','./themes/image/logo_bg_4.jpg','./themes/image/logo_bg_5.jpg'];
            //去除当前门头照片
            logos = $.map(logos,function(n){
                if(n == path){
                    return null;
                }else{
                    return n ;
                }
            });

            if($("#selectLogoModal .modal-body .logo-option").length == 0){
                //首次选择门头图片
                var html = "";
                for(var i = 0; i < logos.length; i++){
                    html += "<div class='logo-option'><label><input type='radio' name='fxlogo' class='fx-logo'><img src='"+logos[i]+"'></label></div>";
                }
                $("#selectLogoModal .modal-body").html(html);
            }
            $("#selectLogoModal").modal('show');
        });

        //选择门头照，确认按钮
        $("#selectLogoModal .btn-confirm").click(function(){
            var checkedOption = $("#selectLogoModal .logo-option .fx-logo:checked");
            if(checkedOption.length == 0){
                $("#myModal .modal-body").html("<p class='text-danger'>请选择一张门头照片</p>");
                $("#myModal").modal('show');
                //定时器，1.5秒后模态框自动关闭
                setTimeout(function(){
                    $("#myModal").modal('hide');
                },1500);
            }else{
                var selectedLogoPath = checkedOption.next().attr("src");
                $(".inner-section table .select-logo").parent().find("input").val(selectedLogoPath);
                $(".inner-section table .select-logo").parent().find("img").attr("src",selectedLogoPath);
                $("#selectLogoModal").modal('hide');
            }
        });
        //保存配置
        $(".item .submit").click(function(){
            $(".item form").ajaxSubmit($.extend(true,{},formOptions,options,{url:host+'/admin/config/setFeeRatioConfig',success:successResponse,error:errorResponse}));
            return false;
        });

    }
    function successResponse(json,statusText){
        if(json.errorCode == 0){
            $("#myModal .modal-body").html("<p class='text-success'><b>恭喜您，操作成功！</b></p>");
            $("#myModal").modal('show');
            //定时器，1.5秒后模态框自动关闭
            setTimeout(function(){
                $("#myModal").modal('hide');
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
    }

    /**
     *请求添加失败时（如网络不通畅、超时等）的回调方法
     */
    function errorResponse(XMLHttpRequest,textStatus,errorThrown){
        $("#myModal .modal-body").html("<p class='text-danger'>很抱歉，请求失败,网络异常！</p>");
        $("#myModal").modal('show');
        //定时器，1.5秒后模态框自动关闭
        setTimeout(function(){
            $("#myModal").modal('hide');
        },1500);
    }
    init();

});