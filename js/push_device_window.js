pushDeviceWindow = function(grid, delivery_user_id) {
  var wnd = this;

  var fset = null;
  var cnt = new Ext.Container({
    autoEl : 'div',
    items : []
  });

  function loadChkGrp() {
    Ext.Ajax.request({
      url : 'delivery_user_manager_back.php?func=delivery_list',
      params : {
        delivery_user_id : delivery_user_id
      },
      method : "GET",
      callback : function(options, success, response) {
        var rs = Ext.decode(response.responseText);
        var msg = rs.msg;
        var flag = rs.success;
        var data = rs.data;
        if (success) {
          if (flag == "false") {
            ShowMessage('Manager', decodeURI(msg), 'ERROR');
          } else if (flag == "true") {
            var config = {
              name : 'myCheckboxes',
              columns : 3,
              autoScroll : false,
              autoHeight : true,
              hideLabel : true,
              invalidClass : '',
              items : []
            };

            for (i = 0; i < data.length; i++) {
              item = data[i];
              config.items.push({
                name : 'devices[]',
                inputValue : item.id,
                boxLabel : item.device_name,
                checked : item.check
              });
            }

            if (config.items.length === 0) {
              config.items.push({
                boxLabel : 'No options',
                checked : false,
                disabled : true
              });
            }
            var chkGrp = null;
            chkGrp = new Ext.form.CheckboxGroup(config);

            fset = new Ext.form.FieldSet({
              title : '全部设备一览',
              width : 470,
              height : 150,
              autoScroll : true,
              items : [ chkGrp ]
            });
            cnt.removeAll();
            fset.render(cnt.getEl());
            cnt.doLayout();
          }
        } else {
          ShowMessage('Manager', decodeURI(msg), 'ERROR');
        }
      }
    });
  }
  ;

  var frm = new Ext.FormPanel({
    labelWidth : 100,
    width : "100%",
    defaults : {
      width : 250
    },
    defaultType : 'checkbox',
    border : false,
    frame : true,
    labelAlign : 'right',
    url : 'delivery_user_manager_back.php?func=push_device&delivery_user_id='
        + delivery_user_id,

    items : [ cnt ],

    buttons : [
        {
          text : '确定',
          handler : function() {
            frm.getForm().submit(
                {
                  params : {
                    delivery_user_id : delivery_user_id
                  },
                  timeout : 600,
                  success : function(form, action) {
                    ShowMessage('Manager', '操作完成 ', 'INFO');
                    wnd.destroy();
                    grid.store.load();
                  },
                  failure : function(form, action) {
                    if (action != null && action.result != null
                        && action.result.msg != null) {
                      ShowMessage('Manager', decodeURI(action.result.msg),
                          'ERROR');
                    }
                  }
                });
          }
        }, {
          text : '取消',
          handler : function() {
            wnd.destroy();
          }
        } ]
  });

  pushDeviceWindow.superclass.constructor.call(this, {
    closeAction : 'destroy',
    items : [ frm ],
    height : 250,
    width : 500,
    layout : 'fit',
    border : false,
    frame : true,
    title : '分配设备',
    modal : true,
    plain : true
  });

  loadChkGrp();
}

Ext.extend(pushDeviceWindow, Ext.Window, {});