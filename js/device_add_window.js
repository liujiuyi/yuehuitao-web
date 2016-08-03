addDeviceWindow = function(grid) {
  var wnd = this;

  var frm = new Ext.FormPanel({
    xtype : 'form',
    width : 180,
    defaultType : 'textfield',
    frame : true,
    url : 'device_manager_back.php?func=device_create',
    labelAlign : 'right',
    labelPad : 10,
    labelWidth : 80,
    items : [ {
      xtype : 'textfield',
      name : 'device_code',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '设备标识',
      labelStyle : 'font-weight:bold;',
      emptyText : '填写格式：yuehuotao0001'
    }, {
      xtype : 'textfield',
      name : 'device_name',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '设备名称',
      labelStyle : 'font-weight:bold;'
    }, {
      xtype : 'textfield',
      name : 'device_address',
      allowBlank : false,
      anchor : '75%',
      fieldLabel : '设备地址',
      labelStyle : 'font-weight:bold;'
    }, {
      xtype : 'numberfield',
      name : 'box_number',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '格子数量',
      labelStyle : 'font-weight:bold;'
    } ],
    buttons : [
        {
          text : '确定',
          handler : function() {
            var button = this;
            this.disable();
            frm.getForm().submit(
                {
                  timeout : 600,
                  waitTitle : 'Manager',
                  waitMsg : 'Doing...',
                  success : function(form, action) {
                    if (action.result.success == 'true') {
                      ShowMessage('Manager', '操作完成 ', 'INFO');
                      wnd.destroy();
                      grid.store.load();
                    } else if (action.result.success == 'false') {
                      ShowMessage('Manager',
                          decodeURI(action.result.msg), 'ERROR');
                    }
                  },
                  failure : function(form, action) {
                    if (action != null && action.result != null
                        && action.result.msg != null) {
                      ShowMessage('Manager',
                          decodeURI(action.result.msg), 'ERROR');
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

  addDeviceWindow.superclass.constructor.call(this, {
    closeAction : 'destroy',
    items : [ frm ],
    height : 250,
    width : 500,
    layout : 'fit',
    border : false,
    frame : true,
    title : '添加设备',
    modal : true,
    plain : true
  });
}

Ext.extend(addDeviceWindow, Ext.Window, {});