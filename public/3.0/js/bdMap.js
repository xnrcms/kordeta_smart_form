function loadBdMap(longid,latid,addrid)
{
    //定位坐标
    var longitude   = $("#"+longid).val();
    var latitude    = $("#"+latid).val();
    var destPoint   = new BMap.Point(longitude,latitude);

    /**开始处理百度地图**/
    var bdMap       = new BMap.Map("map_"+addrid);
    var marker      = new BMap.Marker(destPoint);
    bdMap.centerAndZoom(new BMap.Point(destPoint.lng, destPoint.lat), 12);//初始化地图
    bdMap.enableScrollWheelZoom();
    bdMap.addControl(new BMap.NavigationControl());
    bdMap.addOverlay(marker);//添加标注
    bdMap.addEventListener("click", function(e){
        destPoint   = e.point;
        $("#"+longid).val(destPoint.lat);
        $("#"+latid).val(destPoint.lng);
        bdMap.clearOverlays();
        var marker1 = new BMap.Marker(destPoint);  // 创建标注
        bdMap.addOverlay(marker1);
    });

    locationAddrss(addrid,bdMap)
    searchAddressClick(addrid,bdMap);
}
    
function locationAddrss(addrid,mapobj)
{
    var local   = new BMap.LocalSearch(mapobj, {renderOptions:{ map: mapobj}});
    local.setMarkersSetCallback(function(posi){
    	$("#locate-btn"+addrid).removeAttr("disabled");
	    for(var i=0;i<posi.length;i++){
	        if(i==0){destPoint = posi[0].point;}
	        posi[i].marker.addEventListener("click", function(data){
	            destPoint = data.target.getPosition(0);
	        });
	    }
    });
    local.search($("#"+addrid).val());
    return false;
}

function searchAddressClick(addrid,mapobj)
{
	$("#locate-btn" + addrid).click(function(){
        if($("#"+addrid).val() == ""){ alert("请输入详细地址！"); return false;}
        $("#locate-btn" + addrid).attr("disabled","disabled");
        locationAddrss(addrid,mapobj);
        return false;
    });
}