editAdminUserWindow = function(grid, admin_user_id) {
  var wnd = this;

  var frm = new Ext.FormPanel({
    xtype : 'form',
    width : 180,
    defaultType : 'textfield',
    frame : true,
    url : 'admin_user_manager_back.php?func=admin_user_update',
    labelAlign : 'right',
    labelPad : 10,
    labelWidth : 80,
    items : [ {
      xtype : 'textfield',
      name : 'id',
      hidden : true
    }, {
      xtype : 'textfield',
      name : 'username',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '用户名'
    }, {
      xtype : 'textfield',
      name : 'password',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '密码',
      labelStyle : 'font-weight:bold;'
    }, {
      xtype : 'radiogroup',
      name : 'type',
      allowBlank : false,
      anchor : '55%',
      fieldLabel : '类型',
      labelStyle : 'font-weight:bold;',
      items : [ {
        boxLabel : '管理员',
        name : 'type',
        inputValue : '1'
      }, {
        boxLabel : '加盟商',
        name : 'type',
        inputValue : '2'
      } ]
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

  editAdminUserWindow.superclass.constructor.call(this, {
    closeAction : 'destroy',
    items : [ frm ],
    height : 250,
    width : 500,
    layout : 'fit',
    border : false,
    frame : true,
    title : '修改管理员',
    modal : true,
    plain : true
  });
  this.on("beforeshow", function() {
    frm.getForm().load({
      url : 'admin_user_manager_back.php?func=admin_user_info',
      params : {
        admin_user_id : admin_user_id
      },
      success : function(form, action) {
      }
    });
  });
}

Ext.extend(editAdminUserWindow, Ext.Window, {});