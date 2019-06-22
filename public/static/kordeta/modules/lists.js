;layui.define(['table', 'form'], function(exports){
  var $               = layui.$
      ,table          = layui.table
      ,form           = layui.form
      ,privateFun     = {}
      ,lists          = {};

  //列表Table
  table.set({'isDev':CommonJsParame.isDev}).render({
    elem: '#' + CommonJsParame.TabelId,
    url: CommonJsParame.TabelUrl, //模拟接口
    cols: eval("(" + CommonJsParame.TabelHeadData + ")"),
    text: '对不起，加载出现异常！',
    done:function(res, curr, count){
      privateFun.registerEvent();
    }
  });

  //事件
  lists.setListTplFieldDetail = function(othis){
      console.log(othis.attr("lay-listid"));
  }

  lists.setListTplField = function(othis){
      layer.open({
        type:2,
        title: '列表模板设置',
        area:  ['80%', '80%'],
        content:othis.attr("lay-url"),
        maxmin :true,
      });
  }

  lists.buttonType1 = function(othis){
      var _url  = othis.attr("lay-url");
      if (!_url) { return ;}

      window.location.href = _url;
  }

  lists.buttonType2 = function(othis){
      var _url        = othis.attr("lay-url")
          ,_title     = othis.attr("lay-title")
          ,_width     = othis.attr("lay-width")
          ,_height    = othis.attr("lay-height");
      
      if (!_url) { return ;}

      layer.open({
        type:2,
        title:_title,
        area:[(!_width ? 800 : _width) + 'px',(!_height ? 550 : _height) + 'px'],
        content:othis.attr("lay-url"),
        maxmin:true,
      });
  }

  lists.buttonType3 = function(othis){
      var _url  = othis.attr("lay-url");
      console.log(_url);
  }

  //注册事件监听
  privateFun.registerEvent  = function(){
    $('*[lay-event]').on('click', function(othis){
      var othis   = $(this);
      var event   = othis.attr('lay-event');console.log(event);
      lists[event] ? lists[event].call(this,othis) : '';
    });
  }
  
  exports('lists', {})
});