;layui.define(['form','table'], function(exports){
  var $           = layui.$,
      form        = layui.form,
      admin       = layui.admin,
      privateFun  = {},
      devlist     = {
        loading:false

        ,refreshPage: function(){
          if(devlist.loading){ layer.msg('有操作在进行，请稍等...',{icon: 0,time:2000});return false; }
          devlist.loading    = true;

          layer.msg('页面刷新中...',{icon: 16,time:1000000});
          window.location.reload();
        }

        ,delfun:function(that){
          if(devlist.Loading){ layer.msg('有操作在进行，请稍等...',{icon: 0,time:2000});return false; }
          devlist.Loading    = true;
          
          // 删除按钮
          layer.confirm('确认删除？', { btn: ['确定', '取消']}, function () {

            layer.msg('数据删除中，请稍等...',{icon: 16,time:1000000});

            $.ajax({
              type: 'post',
              url: that.attr('data-url'),
              data: {},
              dataType: 'json',
              success: function (data) {
                
                var ic  = data.code == 1 ? 6 : 2;

                  layer.msg(data.msg,{icon: ic,time:2000},function(){

                      if(data.code == 1){

                          if(data.url != '') {
                              window.location.href = data.url;
                          }else{
                              window.location.reload();
                          }
                      }

                      devlist.Loading    = false;
                  });
              }
            })
          }, function () {
              devlist.Loading    = false;
              layer.closeAll();
          });
        }

        ,addDevList:function(othis){
          othis.parent().find("tbody").prepend(privateFun.getHtmlStr());
          privateFun.registerEvent();
        }

        ,submitListData:function(that){
          if(devlist.Loading){ layer.msg('有操作在进行，请稍等...',{icon: 0,time:2000});return false; }
          devlist.Loading    = true;
          var id              = parseInt(that.attr("lay-fieldid"));
          var obj             = $("#devlist_id_"+id);
          var parame          = {};
              parame.id       = id;
              parame.pid      = parseInt(CommonJsParame.pid);
              parame.hash     = obj.attr("data-hash");
              parame.title    = obj.find("input[name='title_"+id+"']").val();
              parame.tag      = obj.find("input[name='tag_"+id+"']").val();
              parame.width    = obj.find("input[name='width_"+id+"']").val();
              parame.type     = obj.find("select[name='type_"+id+"']").val();
              parame.edit     = (obj.find("input[name='edit_"+id+"']").is(':checked')) ? 1 : 0;
              parame.search   = (obj.find("input[name='search_"+id+"']").is(':checked')) ? 2 : 0;
              parame.sort     = obj.find("input[name='sort_"+id+"']").val();
              parame.status   = obj.find("input[name='status_"+id+"']:checked").val();

              layer.msg('请求处理中，请稍等...',{icon: 16,time:1000000});

              $.ajax({
                type: 'post',
                url: CommonJsParame.url,
                data: parame,
                dataType: 'json',
                success: function (data) {
                  var ic  = data.code == 1 ? 6 : 2;

                  layer.msg(data.msg,{icon: ic,time:2000},function(){

                      if(data.code == 1){

                          if(data.url != '') {
                              window.location.href = data.url;
                          }else{
                              window.location.reload();
                          }
                      }

                      devlist.Loading    = false;
                  });
                }
              });
        }
        
        ,cbEnable:function(othis){
          var parent = othis.parents('.onoff');
            $('.cb-disable',parent).removeClass('selected');
            othis.addClass('selected');
            $('.checkbox',parent).attr('checked', true);
        }
        ,cbDisable:function(othis){
          var parent = othis.parents('.onoff');
            $('.cb-enable',parent).removeClass('selected');
            othis.addClass('selected');
            $('.checkbox',parent).attr('checked', false);
        }
  };

  privateFun.getHtmlStr = function(){
    var htmls = '';
        htmls += '<tr id="devlist_id_0">';

        htmls += '<td width="20%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<input type="text" size="30" class="qsbox" name="title_0" placeholder="请输入表头名称" value="" style="width: 90%">';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<input type="text" size="30" class="qsbox" name="tag_0" placeholder="请输入数据标识" value="" style="width: 90%">';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<input type="text" size="30" class="qsbox" name="width_0" placeholder="请输入表头宽度" value="" style="width: 90%">';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<select name="type_0" id="type_id" class="form-control">';
        htmls += '<option value="">选择类型</option>';
        htmls += '<option value="string">字符串</option>';
        htmls += '<option value="number">数字</option>';
        htmls += '<option value="price">价格</option>';
        htmls += '<option value="select">枚举</option>';
        htmls += '<option value="bool">布尔</option>';
        htmls += '<option value="datetime">时间区间</option>';
        htmls += '<option value="image">图片</option>';
        htmls += '<option value="done">操作</option>';
        htmls += '<option value="id">ID</option>';
        htmls += '</select>';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<label><input type="checkbox" class="checkbox_item" name="edit_0" value="1"> 可编辑</label>';
        htmls += '<label><input type="checkbox" class="checkbox_item" name="search_0" value="1"> 可搜索</label>';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<input type="text" size="30" class="qsbox" name="sort_0" placeholder="请输入表头排序" value="" style="width: 90%">';
        htmls += '</div></td>';

        htmls += '<td width="12%">';
        htmls += '<div class="onoff" style="text-align: center;">';
        htmls += '<input id="menu_status1_0" name="status_0" value="1" type="radio" checked="checked" >';
        htmls += '<input id="menu_status0_0" name="status_0" value="2" type="radio" >';
        htmls += '<label for="menu_status1_0" lay-event="cbEnable" class="cb-enable  selected">启用</label>';
        htmls += '<label for="menu_status0_0" lay-event="cbDisable" class="cb-disable">禁用</label>';
        htmls += '</div></td>';

        htmls += '<td width="8%">';
        htmls += '<div style="text-align: center;">';
        htmls += '<a class="btn blue" href="javascript:;" lay-event="submitListData" lay-fieldid="0"><i class="fa"></i>保存</a>';
        htmls += '</div></td>';
        htmls += '</tr>';

        return htmls;
  }

  //注册事件监听
  privateFun.registerEvent  = function(){
    $('*[lay-event]').on('click', function(othis){
      var othis   = $(this);
      var event   = othis.attr('lay-event');
      devlist[event] ? devlist[event].call(this,othis) : '';
    });
  }
  
  privateFun.registerEvent();

  exports('devlist', {})
});