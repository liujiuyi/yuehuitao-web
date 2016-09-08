pageContents = function() {
  Ext.QuickTips.init();
  // 设备grid
  store_device_list = new Ext.data.JsonStore({
    url : 'device_manager_back.php?func=device_list',
    root : 'data',
    fields : [ 'id', 'device_code', 'device_name', 'device_address',
        'box_number', 'admin_user_name' ],
    idProperty : 'id',
    totalProperty : 'totalCount'
  });

  var grid_device_list = new Ext.grid.GridPanel({
    title : '当前设备列表',
    region : 'west',
    store : store_device_list,
    viewConfig : {
      forceFit : true
    },
    columns : [
        new Ext.grid.RowNumberer(),
        {
          header : '设备ID',
          sortable : true,
          hidden : true,
          dataIndex : 'id'
        },
        {
          header : '设备标识',
          sortable : true,
          height : 20,
          dataIndex : 'device_code'
        },
        {
          header : '设备名称',
          sortable : true,
          height : 20,
          dataIndex : 'device_name'
        },
        {
          header : '设备地址',
          sortable : true,
          height : 20,
          dataIndex : 'device_address'
        },
        {
          height : 20,
          header : '格子数量',
          sortable : true,
          height : 20,
          dataIndex : 'box_number'
        },
        {
          header : '加盟商',
          sortable : true,
          height : 20,
          dataIndex : 'admin_user_name'
        },
        {
          xtype : 'actioncolumn',
          header : '操作',
          align : 'center',
          width : 50,
          items : [ {
            icon : 'images/return.png',
            tooltip : 'URL',
            handler : function(grid, rowIndex, colIndex) {
              var gridRecs = grid.getStore().getAt(rowIndex);
              Ext.MessageBox.alert("二维码链接", CODE_URL + "?device_id="
                  + gridRecs.get('id'));
            }
          } ]
        } ],
    tbar : [ {
      text : '添加设备',
      icon : 'images/add.png',
      tooltip : 'add Device',
      handler : onAddDevice
    }, {
      text : '修改设备',
      icon : 'images/edit.png',
      tooltip : 'edit Device',
      handler : onEditDevice
    }, {
      text : '删除设备',
      icon : 'images/delete.png',
      tooltip : 'delete Device',
      handler : onRemoveDevice
    } ],
    bbar : new Ext.PagingToolbar({
      store : store_device_list,
      displayInfo : true,
      displayMsg : '{0} - {1} of {2}'
    }),
    listeners : {
      rowclick : function(grid, rowIndex, e) {
        record = store_device_list.getAt(rowIndex);
        var device_id = record.data.id;
        store_box_list.setBaseParam('device_id', device_id);
        grid_box_list.store.reload();
      }
    }
  });

  store_device_list.load();

  function onAddDevice(btn, ev) {
    var addWnd = new addDeviceWindow(grid_device_list);
    addWnd.show();
  }

  function onEditDevice(btn, ev) {
    var gridRecs = grid_device_list.getSelectionModel().getSelections();
    if (!gridRecs.length > 0) {
      return false;
    }
    var editDeviceWnd = new editDeviceWindow(grid_device_list, gridRecs[0]
        .get("id"));
    editDeviceWnd.show();
  }

  function onRemoveDevice(btn, ev) {
    var recs = grid_device_list.getSelectionModel().getSelections();
    if (recs.length > 0) {
      Ext.MessageBox.confirm("Manager", "确定要删除吗？", function(btn) {
        var id = recs[0].get("id")
        if (btn == "yes") {
          Ext.Ajax.request({
            url : 'device_manager_back.php?func=device_delete&id=' + id,
            callback : function(options, success, response) {
              var rs = Ext.decode(response.responseText);
              var msg = rs.msg;
              var flag = rs.success;
              if (success) {
                if (flag == "false") {
                  ShowMessage('Manager', decodeURI(msg), 'ERROR');
                } else if (flag == "true") {
                  grid_device_list.store.load();
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

  // 盒子grid
  store_box_list = new Ext.data.JsonStore({
    url : 'box_manager_back.php?func=box_list',
    root : 'data',
    fields : [ 'id', 'box_number', 'box_no', 'device_id', 'goods_name', 'goods_price',
        'status' ],
    idProperty : 'id',
    totalProperty : 'totalCount'
  });

  var grid_box_list = new Ext.grid.GridPanel({
    title : '当前设备格子列表',
    region : 'west',
    store : store_box_list,
    viewConfig : {
      getRowClass : function(rec, index) {
        if (rec.get('status') == 0) {
          return 'schedule-red';
        }
      },
      forceFit : true
    },
    columns : [ new Ext.grid.RowNumberer(), {
      header : '格子ID',
      sortable : true,
      hidden : true,
      dataIndex : 'id'
    }, {
      header : '格子号',
      sortable : true,
      height : 20,
      dataIndex : 'box_number'
    }, {
      header : '格子序号',
      sortable : true,
      height : 20,
      dataIndex : 'box_no'
    }, {
      header : '商品名称',
      sortable : true,
      height : 20,
      dataIndex : 'goods_name'
    }, {
      header : '商品价格',
      sortable : true,
      height : 20,
      dataIndex : 'goods_price'
    }, {
      height : 20,
      header : '状态',
      sortable : true,
      height : 20,
      dataIndex : 'status',
      renderer : function(value, metaData, record, rowIndex, colIndex, store) {
        if (value == 1)
          return '正常';
        else if (value == 0)
          return '无货';
      }
    }, {
      xtype : 'actioncolumn',
      header : '操作',
      align : 'center',
      width : 50,
      items : [ {
        icon : 'images/edit.png',
        tooltip : 'edit',
        handler : function(grid, rowIndex, colIndex) {
          var gridRecs = grid.getStore().getAt(rowIndex);
          onEditBox(gridRecs.get('id'));
        }
      }, {
        icon : 'images/open.png',
        tooltip : 'open',
        handler : function(grid, rowIndex, colIndex) {
          var gridRecs = grid.getStore().getAt(rowIndex);
          Ext.MessageBox.confirm("Manager", "确定要弹开吗？", function(btn) {
            var box_id = gridRecs.get('id');
            if (btn == "yes") {
              Ext.Ajax.request({
                url : 'box_manager_back.php?func=box_open&id=' + box_id,
                callback : function(options, success, response) {
                  var rs = Ext.decode(response.responseText);
                  var msg = rs.msg;
                  var flag = rs.success;
                  if (success) {
                    if (flag == "false") {
                      ShowMessage('Manager', decodeURI(msg), 'ERROR');
                    }
                  } else {
                    ShowMessage('Manager', decodeURI(msg), 'ERROR');
                  }
                }
              });
            }
          });

        }
      } ]
    } ],
    listeners : {
      itemdblclick : function(item, record) {
        var gridRecs = grid_box_list.getSelectionModel().selectRow();
        if (!gridRecs.length > 0) {
          return false;
        }
        onEditBox(gridRecs[0].get("id"));
      }
    }
  });

  function onEditBox(box_id) {
    var editBoxWnd = new editBoxWindow(grid_box_list, box_id);
    editBoxWnd.show();
  }

  pageContents.superclass.constructor.call(this, {
    title : '设备管理',
    region : 'center',
    layout : 'border',
    items : [ {
      region : 'west',
      border : true,
      layout : 'fit',
      split : true,
      width : 600,
      items : grid_device_list
    }, {
      region : 'center',
      border : true,
      layout : 'fit',
      split : true,
      items : grid_box_list
    } ]
  });
};

Ext.extend(pageContents, Ext.Panel, {

});