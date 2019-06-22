;layui.define(['tree','form','table'], function(exports){
  var index = layer.load();
  var $       = layui.$,
      table   = layui.table,
      form    = layui.form,
      admin   = layui.admin,
      tree    = layui.tree,
      extend  = {
        thisMenuId: -1,
        getMenusForm:function( menuid ){
          var _url  = menuid > 0 ? '/manage/devmenu/edit/' : '/manage/devmenu/add';

          if (extend.thisMenuId == menuid) return;
          extend.thisMenuId   = menuid;

          admin.req({
            url:_url,
            data:{'id':menuid},
            success:function(json){
              if (json.data) {
                $('#editSystemMenus').html(json.data);
                form.render();
                extend.selectMenuIcon();
                layer.close(index);  
              }
            }
          });
        },

        selectMenuIcon:function()
        {
          $('button[data-type="selectMenuIcon"]').on('click', function(){

            var idTag     = 'selectMenuIcon';
            admin.popupRight({
              id: idTag,
              success: function(){
                $('#'+ this.id).html('<div style="padding: 20px;">' + CommonJsParame.MenuIconHtml + '</div>');
                $('#'+ this.id + ' ul li').on('click', function(){
                  var icon  = $(this).data('icon');
                  var itag  = '<i class="layui-icon '+icon+'"></i>';
                    $('input[name="icon"]').val(icon);
                    $('button[data-type="selectMenuIcon"] span').html(itag);
                    layer.close(admin.popup.index);
                });
              }
            });

            $('#' + idTag).html('<div style="padding: 20px;">'+iconHtml+'</div>');
            $('#' + idTag + ' ul li').on('click', function(){
              var icon  = $(this).data('icon');
              var itag  = '<i class="layui-icon '+icon+'"></i>';
                $('input[name="icon"]').val(icon);
                $('button[data-type="selectMenuIcon"] span').html(itag);
                layer.close(admin.popup.index);
            });

            return false;
          });
        },

        loadMenusList:function()
        {
          var spread  = CommonJsParame.spreadId ? (CommonJsParame.spreadId).split('|') : [];
          var spreads = new Array();
          $.each(spread,function(k,v){ spreads.push(parseInt(v)); });
          
          tree.render({
            elem: '#systemMenus',
            data: eval("(" + CommonJsParame.TabelTreeData + ")"),
            spread:spreads,
            click: function(item){
              index = layer.load();
              extend.getMenusForm(item.data.id);
            }
          });
        }
      };

  /* 自定义验证规则 */
  form.verify({
    title: function(value){
      if(value.length < 1 || value.length > 8){
        return '标题必须是1-8个字符内';
      }
    }
  });
    
  /* 监听指定开关 */
  form.on('switch(status)', function(data){
    /*layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
      offset: '6px'
    });
    layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)*/
  });
    
  /* 监听提交 */
  form.on('submit(form-submit)', function(data){
    var menuid      = parseInt($(this).attr("lay-menuid"))
        ,_url       = ''
        ,releaseUrl = '/manage/devmenu/release';

    if (menuid == 0) {
      _url        = '/manage/devmenu/add/id/0';
    }else if (menuid > 0) {
      _url        = '/manage/devmenu/edit/id/' + menuid;
    }else{
      return;
    }

    if (menuid == 0) {
      //二次确认
      layer.confirm('确定要新增菜单吗?', {icon: 3, title:'提示'}, function(index){ req(); });
    }else{
      req();
    }

    return;

    function req(){
      admin.req({
        url:_url,
        data:data.field,
        type:'post',
        success:function(json){
          if (json.code == 1) {
              admin.req({ url:releaseUrl, data:{},type:'get'});
              layer.msg(json.msg,{ icon: 1,time:2500},function(){
                  if(json.url != '') window.location.reload();
              });
          }
        }
      });
    }
  });

  exports('devmenu', extend)
});