pageContents = function() {
  Ext.QuickTips.init();
  var x_pos = 0;
  // 订单grid
  store_order_list = new Ext.data.JsonStore({
    url : 'order_manager_back.php?func=order_list',
    root : 'data',
    fields : [ 'id', 'order_id', 'device_name', 'goods_name', 'order_price',
        'create_date' ],
    idProperty : 'id',
    totalProperty : 'totalCount',
    listeners : {
      load : function() {
        grid_order_list.current
            .setText(store_order_list.reader.jsonData.current);
      }
    }
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
      header : '设备名称',
      sortable : true,
      height : 20,
      flex : .3,
      dataIndex : 'device_name'
    }, {
      header : '商品名称',
      sortable : true,
      height : 20,
      flex : 1,
      dataIndex : 'goods_name'
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