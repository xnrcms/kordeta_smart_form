/**

 @Name：layuiAdmin 公共业务
 @Author：贤心
 @Site：http://www.layui.com/admin/
 @License：LPPL
    
 */
 
layui.define(function(exports){
  var $ = layui.$
  ,layer = layui.layer
  ,laytpl = layui.laytpl
  ,setter = layui.setter
  ,view = layui.view
  ,admin = layui.admin
  
  //公共业务的逻辑处理可以写在此处，切换任何页面都会执行
  //……
  
  //退出
  admin.events.logout = function(othis){
    var _url    = othis.attr("lay-url");
    //执行退出接口
    admin.req({
      url: _url
      ,type: 'get'
      ,data: {}
      ,done: function(json){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
        layer.msg(json.msg,{ icon: 1,time:2500},function(){
          //清空本地记录的 token，并跳转到登入页
          admin.exit(function(){
            if(json.url != '') location.href = json.url;
          });
        });
      }
    });
  };

  //对外暴露的接口
  exports('common', {});
});