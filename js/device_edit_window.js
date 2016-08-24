editDeviceWindow = function(grid, device_id) {
  var wnd = this;

  var frm = new Ext.FormPanel({
    xtype : 'form',
    width : 180,
    defaultType : 'textfield',
    frame : true,
    url : 'device_manager_back.php?func=device_update',
    labelAlign : 'right',
    labelPad : 10,
    labelWidth : 80,
    items : [ {
      xtype : 'textfield',
      name : 'device_id',
      hidden : true,
      value : device_id
    }, {
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
      hidden : true,
      fieldLabel : '格子数量',
      labelStyle : 'font-weight:bold;'
    }, {
      xtype : 'combo',
      name : 'admin_user_id',
      hiddenName : 'admin_user_id',
      width : 150,
      store : new Ext.data.JsonStore({
        url : 'admin_user_manager_back.php?func=admin_device_user_list',
        root : 'data',
        fields : [ 'id', 'username' ],
        idProperty : 'id',
        autoLoad : true
      }),
      triggerAction : 'all',
      mode : 'local',
      valueField : 'id',
      displayField : 'username',
      editable : false,
      fieldLabel : '加盟商',
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
                      ShowMessage('Manager', decodeURI(action.result.msg),
                          'ERROR');
                    }
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

  editDeviceWindow.superclass.constructor.call(this, {
    closeAction : 'destroy',
    items : [ frm ],
    height : 250,
    width : 500,
    layout : 'fit',
    border : false,
    frame : true,
    title : '修改设备',
    modal : true,
    plain : true
  });
  this.on("beforeshow", function() {
    frm.getForm().load({
      url : 'device_manager_back.php?func=device_info',
      params : {
        device_id : device_id
      },
      success : function(form, action) {
      }
    });
  });
}

Ext.extend(editDeviceWindow, Ext.Window, {});