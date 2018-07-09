$(function(){
    var host = getJSurl();
    /**
     * 页面初始化
     */
    function init(){

        setMap(); //初始化地图


    	bindEvent();
        //表单的JQueryValidater配置验证---jquery.validate插件验证法
        $("#myForm").validate(validateInfo);
    }

    /**
     * 设置地图
     */
    function setMap(keyword){
        var longitude = $("#longitude").val();
        var latitude = $("#latitude").val();
        console.log(longitude);
        console.log(latitude);
        // 百度地图API功能
        var map = new BMap.Map("allmap");
        if(longitude == '' || latitude == ''){
            if(longitude == '' || latitude == ''){
                var city = '合肥';
            }
            map.centerAndZoom(city, 11);
        }else{
            map.centerAndZoom(new BMap.Point(longitude, latitude), 11);
        }
        map.enableScrollWheelZoom();//启用地图滚轮放大缩小
        var geoc = new BMap.Geocoder();
        if(keyword){
            var local = new BMap.LocalSearch(map, {
                renderOptions:{map: map}
            });
            local.search(keyword);
        }
        var content_text = $.trim($("#address_text").val());

        if(longitude != '' && latitude != ''){
            //标注点数组
            markerArr = [{title:"地址",content:content_text,point:longitude + "|" + latitude,isOpen:1,icon:{w:21,h:21,l:0,t:0,x:6,lb:5}}];
            addMarker();//向地图中添加marker
        }

        //显示信息
        function showInfo(e){
            $("#allmap .BMap_Marker").remove();

            geoc.getLocation(e.point, function(rs){
                var addComp = rs.addressComponents;
                //alert(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
                var content_text =  addComp.city + '·' +addComp.district + addComp.street + addComp.streetNumber;
                $("#address_text").val(content_text);
                console.log(rs);
                $("#longitude").val(e.point.lng);
                $("#latitude").val(e.point.lat);
                console.log(e.point.lng);
                console.log(e.point.lat);
                if($('#longitude').val() != '' || $('#latitude').val() != ''){
                    $('#allmap').parent().next().html('');
                }
                //标注点数组
                markerArr = [{title:"地址",content:content_text,point:e.point.lng+"|" + e.point.lat,isOpen:1,icon:{w:21,h:21,l:0,t:0,x:6,lb:5}}];
                addMarker(e);//向地图中添加marker
            });

            //alert(e.point.lng + ", " + e.point.lat);
        }
        map.addEventListener("click", showInfo);

        //创建marker
        function addMarker(e){
            for(var i=0;i<markerArr.length;i++){
                var json = markerArr[i];
                var p0 = json.point.split("|")[0];
                var p1 = json.point.split("|")[1];
                var point = new BMap.Point(p0,p1);
                var iconImg = createIcon(json.icon);
                var marker = new BMap.Marker(point,{icon:iconImg});
                var iw = createInfoWindow(i);
                var label = new BMap.Label(json.title,{"offset":new BMap.Size(json.icon.lb-json.icon.x+10,-20)});
                marker.setLabel(label);
                map.addOverlay(marker);
                label.setStyle({
                    borderColor:"#808080",
                    color:"#333",
                    cursor:"pointer"
                });

                (function(){
                    var index = i;
                    var _iw = createInfoWindow(i);
                    var _marker = marker;
                    _marker.addEventListener("click",function(){
                        this.openInfoWindow(_iw);
                    });
                    _iw.addEventListener("open",function(){
                        _marker.getLabel().hide();
                    })
                    _iw.addEventListener("close",function(){
                        _marker.getLabel().show();
                    })
                    label.addEventListener("click",function(){
                        _marker.openInfoWindow(_iw);
                    })
                    if(!!json.isOpen){
                        label.hide();
                        _marker.openInfoWindow(_iw);
                    }
                })()
            }
        }
    }


    /**
     * 事件绑定
     */
    function bindEvent(){

        //搜索
        $('.map-search-row .search-keyword').click(function(){
            var keyword = $('.map-search-row .keyword').val();
            if(keyword != ''){
                setMap(keyword);
                return false;
            }else{
                responseTip(1,"请先输入关键字",1500);
                return false;
            }
        });

    	//修改商品信息
    	$('.save').click(function() {
    		modifyMessage();
    	});
    	//返回
    	$('.back').click(function() {			 
            window.history.go(-1);
    	});

        //添加图片事件
        $("#imgurl_1").change(function(){
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
            $("#imgFlag_1").val(1);//图片更新的标识
        });
        //添加图片事件
        $("#imgurl_2").change(function(){
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
            $("#imgFlag_2").val(1);//图片更新的标识
        });
        //添加图片事件
        $("#imgurl_3").change(function(){
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
            $("#imgFlag_3").val(1);//图片更新的标识
        });

    }

    /**
     * 修改订单
     */
    function modifyMessage(){
        $("#myForm").ajaxSubmit($.extend(true,{},formOptions,orderFormOptions));
    }
    /**
     * 提交添加商品信息的表单配置
     */
    var  orderFormOptions={
        url:host+'/index.php/admin/message/modifyMessage',
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
            total_price:{//订单总价
                required:true,
                number: true
            },
            imgurl_1:{
                accept:"jpg,jpeg,gif,png"
            },
            imgurl_2:{
                accept:"jpg,jpeg,gif,png"
            },
            imgurl_3:{
                accept:"jpg,jpeg,gif,png"
            }

        },
        messages:{
        	 total_price:{//订单总价
                 required:"必填",
                 number:"必须输入正常的数组"
             },
            imgurl_1:{
                accept:"仅支持jpg、jpeg、gif、png格式"
            },
            imgurl_2:{
                accept:"仅支持jpg、jpeg、gif、png格式"
            },
            imgurl_3:{
                accept:"仅支持jpg、jpeg、gif、png格式"
            },
        },
        errorPlacement:function(error,element){
            //var name = element.attr("name");
            element.parent().next().append(error);
        }
    };
//创建InfoWindow
    function createInfoWindow(i){
        var json = markerArr[i];
        var iw = new BMap.InfoWindow("<b class='iw_poi_title' title='" + json.title + "'>" + json.title + "</b><div class='iw_poi_content'>"+json.content+"</div>");
        return iw;
    }
    //创建一个Icon
    function createIcon(json){
        var icon = new BMap.Icon("http://api.map.baidu.com/img/markers.png", new BMap.Size(json.w,json.h),{imageOffset: new BMap.Size(-json.l,-json.t),infoWindowOffset:new BMap.Size(json.lb+5,1),offset:new BMap.Size(json.x,json.h)})
        return icon;
    }
    init();
});