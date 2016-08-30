pageContents = function() {
  Ext.QuickTips.init();
  var x_pos = 0;
  // 订单grid
  store_order_list = new Ext.data.JsonStore({
    url : 'order_manager_back.php?func=order_list',
    root : 'data',
    fields : [ 'id', 'order_id', 'username', 'device_name', 'order_price', 'create_date' ],
    idProperty : 'id',
    totalProperty : 'totalCount',
    listeners : {
      load : function() {
        grid_order_list.current
            .setText(store_order_list.reader.jsonData.current);
      }
    }
  });

  var searchWord = new Ext.form.TextField({
    itemId : 'searchinfo',
    name : 'searchinfo',
    width : 180,
    ref : '../searchinfo',
    emptyText : '请输入订单号或者设备名称'
  });

  var grid_order_list = new Ext.grid.GridPanel({
    region : 'center',
    store : store_order_list,
    viewConfig : {
      forceFit : true
    },
    columns : [ new Ext.grid.RowNumberer(), {
      header : 'ID',
      sortable : true,
      hidden : true,
      dataIndex : 'id'
    }, {
      header : '订单ID',
      sortable : true,
      flex : .3,
      height : 20,
      dataIndex : 'order_id'
    }, {
      header : '加盟商',
      sortable : true,
      height : 20,
      flex : .3,
      dataIndex : 'username'
    }, {
      header : '设备名称',
      sortable : true,
      height : 20,
      flex : .3,
      dataIndex : 'device_name'
    }, {
      height : 20,
      header : '金额',
      sortable : true,
      flex : .3,
      height : 20,
      dataIndex : 'order_price'
    }, {
      height : 20,
      header : '日期',
      sortable : true,
      flex : .3,
      height : 20,
      dataIndex : 'create_date'
    } ],
    tbar : [ '->', {
      xtype : 'combo',
      allowBlank : false,
      width : 120,
      mode : 'local',
      fieldLabel : '加盟商',
      triggerAction : "all",
      editable : false,
      store : new Ext.data.JsonStore({
        url : 'admin_user_manager_back.php?func=admin_device_user_list',
        root : 'data',
        fields : [ 'id', 'username' ],
        idProperty : 'id',
        autoLoad : true
      }),
      valueField : 'id',
      displayField : 'username',
      hiddenName : 'admin_user_id',
      listeners : {
        select : function(combo) {
          var searchAdminUserId = combo.getValue();
          store_order_list.setBaseParam('admin_user_id', searchAdminUserId);
          store_order_list.load();
        }
      }
    }, '-', searchWord, {
      handler : searchObject,
      icon : 'images/search.png',
      tooltip : 'press ENTER to search'
    }, '-', {
      icon : 'images/prev.png',
      tooltip : 'previous',
      handler : function() {
        onHandler('previous');
      }
    }, '-', {
      xtype : 'tbtext',
      ref : '../current',
      text : Ext.util.Format.date(new Date(), 'Y-m-d')
    }, '-', {
      icon : 'images/next.png',
      tooltip : 'next',
      handler : function() {
        onHandler('next');
      }
    } ],
    bbar : new Ext.PagingToolbar({
      store : store_order_list,
      displayInfo : true,
      displayMsg : '{0} - {1} of {2}'
    })
  });

  searchWord.on("specialkey", function(field, ev) {
    if (ev.getKey() == ev.ENTER) {
      ev.preventDefault();
      searchObject();
    }
  });

  function searchObject() {
    store_order_list.setBaseParam('searchinfo', grid_order_list.searchinfo
        .getValue());
    grid_order_list.store.load();
  }

  function onHandler(type) {
    switch (type) {
    case 'previous':
      showData(x_pos - 1);
      break;
    case 'next':
      showData(x_pos + 1);
      break;
    }
  }

  function showData(xpos) {
    x_pos = xpos;
    if (x_pos == null)
      x_pos = 0;
    store_order_list.setBaseParam('pos', x_pos);
    store_order_list.load({
      params : {
        start : 0,
        limit : PAGE_COUNT
      }
    });
  }

  store_order_list.load();

  pageContents.superclass.constructor.call(this, {
    title : '订单一览',
    region : 'center',
    layout : 'border',
    flex : 1,
    items : grid_order_list
  });
};

Ext.extend(pageContents, Ext.Panel, {

});