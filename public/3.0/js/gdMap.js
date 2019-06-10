function loadGdMap(longid,latid,addrid)
{
    var map = new AMap.Map("map_"+addrid, {resizeEnable: true});
    //为地图注册click事件获取鼠标点击出的经纬度坐标
    var clickEventListener = map.on('click', function(e) {
        document.getElementById("lnglat_"+addrid).value = e.lnglat.getLng() + ',' + e.lnglat.getLat()
    });
    var auto = new AMap.Autocomplete({
        input: "tipinput_"+addrid
    });

    AMap.event.addListener(auto, "select", select);//注册监听，当选中某条记录时会触发
    function select(e) {
        if (e.poi && e.poi.location) {
            map.setZoom(15);
            map.setCenter(e.poi.location);
            marker = new AMap.Marker({
                icon: "http://webapi.amap.com/theme/v1.3/markers/n/mark_b.png",
                position: e.poi.location
            });
            marker.setMap(map);
        }
    }
}