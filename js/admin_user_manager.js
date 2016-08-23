pageContents = function() {
  Ext.QuickTips.init();
  // 管理员grid
  store_admin_user_list = new Ext.data.JsonStore({
    url : 'admin_user_manager_back.php?func=admin_user_list',
    root : 'data',
    fields : [ 'id', 'username', 'password', 'type', 'create_date' ],
    idProperty : 'id',
    totalProperty : 'totalCount'
  });

  var grid_admin_user_list = new Ext.grid.GridPanel({
    title : '当前管理员列表',
    region : 'center',
    store : store_admin_user_list,
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
      header : '类型',
      sortable : true,
      height : 20,
      dataIndex : 'type',
      renderer : function(value, metaData, record, rowIndex, colIndex, store) {
        if (value == 1)
          return '管理员';
        else if (value == 2)
          return '加盟商';
      }
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
      tooltip : 'add Admin User',
      handler : onAddAdminUser
    }, {
      text : '修改',
      icon : 'images/edit.png',
      tooltip : 'edit Admin User',
      handler : onEditAdminUser
    }, {
      text : '删除',
      icon : 'images/delete.png',
      tooltip : 'delete Admin User',
      handler : onRemoveAdminUser
    } ],
    bbar : new Ext.PagingToolbar({
      store : store_admin_user_list,
      displayInfo : true,
      displayMsg : '{0} - {1} of {2}'
    })
  });

  store_admin_user_list.load();

  function onAddAdminUser(btn, ev) {
    var addWnd = new addAdminUserWindow(grid_admin_user_list);
    addWnd.show();
  }

  function onEditAdminUser(btn, ev) {
    var gridRecs = grid_admin_user_list.getSelectionModel().getSelections();
    if (!gridRecs.length > 0) {
      return false;
    }
    var editAdminUserWnd = new editAdminUserWindow(grid_admin_user_list,
        gridRecs[0].get("id"));
    editAdminUserWnd.show();
  }

  function onRemoveAdminUser(btn, ev) {
    var recs = grid_admin_user_list.getSelectionModel().getSelections();
    if (recs.length > 0) {
      Ext.MessageBox.confirm("Manager", "确定要删除吗？", function(btn) {
        var id = recs[0].get("id")
        if (btn == "yes") {
          Ext.Ajax
              .request({
                url : 'admin_user_manager_back.php?func=admin_user_delete&id='
                    + id,
                callback : function(options, success, response) {
                  var rs = Ext.decode(response.responseText);
                  var msg = rs.msg;
                  var flag = rs.success;
                  if (success) {
                    if (flag == "false") {
                      ShowMessage('Manager', decodeURI(msg), 'ERROR');
                    } else if (flag == "true") {
                      grid_admin_user_list.store.load();
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

  pageContents.superclass.constructor.call(this, {
    title : '管理员管理',
    region : 'center',
    layout : 'border',
    flex : 1,
    items : grid_admin_user_list
  });
};

Ext.extend(pageContents, Ext.Panel, {

});