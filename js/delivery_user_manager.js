pageContents = function() {
  Ext.QuickTips.init();
  // 管理员grid
  store_delivery_user_list = new Ext.data.JsonStore({
    url : 'delivery_user_manager_back.php?func=delivery_user_list',
    root : 'data',
    fields : [ 'id', 'username', 'password', 'create_date' ],
    idProperty : 'id',
    totalProperty : 'totalCount'
  });

  var grid_delivery_user_list = new Ext.grid.GridPanel({
    title : '当前配送员列表',
    region : 'center',
    store : store_delivery_user_list,
    viewConfig : {
      forceFit : true
    },
    columns : [ new Ext.grid.RowNumberer(), {
      header : '管理员ID',
      sortable : true,
      hidden : true,
      dataIndex : 'id'
    }, {
      header : '用户名',
      sortable : true,
      height : 20,
      dataIndex : 'username'
    }, {
      header : '密码',
      sortable : true,
      height : 20,
      dataIndex : 'password'
    }, {
      height : 20,
      header : '创建日期',
      sortable : true,
      height : 20,
      dataIndex : 'create_date'
    } ],
    tbar : [ {
      text : '添加',
      icon : 'images/add.png',
      tooltip : 'add Delivery User',
      handler : onAddDeliveryUser
    }, {
      text : '修改',
      icon : 'images/edit.png',
      tooltip : 'edit Delivery User',
      handler : onEditDeliveryUser
    }, {
      text : '删除',
      icon : 'images/delete.png',
      tooltip : 'delete Delivery User',
      handler : onRemoveDeliveryUser
    }, '->', {
      text : '为配货员指定设备',
      icon : 'images/push.png',
      tooltip : 'push Device',
      handler : onPushDevice
    } ],
    bbar : new Ext.PagingToolbar({
      store : store_delivery_user_list,
      displayInfo : true,
      displayMsg : '{0} - {1} of {2}'
    })
  });

  store_delivery_user_list.load();

  function onAddDeliveryUser(btn, ev) {
    var addWnd = new addDeliveryUserWindow(grid_delivery_user_list);
    addWnd.show();
  }

  function onEditDeliveryUser(btn, ev) {
    var gridRecs = grid_delivery_user_list.getSelectionModel().getSelections();
    if (!gridRecs.length > 0) {
      return false;
    }
    var editDeliveryUserWnd = new editDeliveryUserWindow(
        grid_delivery_user_list, gridRecs[0].get("id"));
    editDeliveryUserWnd.show();
  }

  function onRemoveDeliveryUser(btn, ev) {
    var recs = grid_delivery_user_list.getSelectionModel().getSelections();
    if (recs.length > 0) {
      Ext.MessageBox
          .confirm(
              "Manager",
              "确定要删除吗？",
              function(btn) {
                var id = recs[0].get("id")
                if (btn == "yes") {
                  Ext.Ajax
                      .request({
                        url : 'delivery_user_manager_back.php?func=delivery_user_delete&id='
                            + id,
                        callback : function(options, success, response) {
                          var rs = Ext.decode(response.responseText);
                          var msg = rs.msg;
                          var flag = rs.success;
                          if (success) {
                            if (flag == "false") {
                              ShowMessage('Manager', decodeURI(msg), 'ERROR');
                            } else if (flag == "true") {
                              grid_delivery_user_list.store.load();
                              grid_box_list.store.load();
                            }
                          } else {
                            ShowMessage('Manager', decodeURI(msg), 'ERROR');
                          }
                        }
                      });
                }
              });
    }
  }

  function onPushDevice() {
    var gridRecs = grid_delivery_user_list.getSelectionModel().getSelections();
    if (!gridRecs.length > 0) {
      return false;
    }
    var pushDeviceWnd = new pushDeviceWindow(grid_delivery_user_list,
        gridRecs[0].get("id"));
    pushDeviceWnd.show();
  }

  pageContents.superclass.constructor.call(this, {
    title : '配送员管理',
    region : 'center',
    layout : 'border',
    flex : 1,
    items : grid_delivery_user_list
  });
};

Ext.extend(pageContents, Ext.Panel, {

});