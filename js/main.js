/* ---------------------------------------------------
 *		Main Page Generator

 *
 *
 *-----------------------------------------------------*/
Ext.onReady(function() {
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

	Ext.Ajax.timeout = 600000;

	pageContents = new pageContents();

	view = new Ext.Viewport({
		id : 'main_frame',
		layout : 'border',
		margins : '0 0 0 0',
		cmargins : '0 0 0 0',

		items : [ {
			border : false,
			contentEl : 'banner',
			region : 'north',
			height : 30,
			layout : 'fit',
			margins : '0 0 0 0',
			cmargins : '0 0 0 0'
		}, pageContents, {
			contentEl : 'footer',
			border : false,
			region : 'south',
			height : 25,
			margins : '5 5 5 5',
			cmargins : '0 0 0 0'
		} ]
	});
	// password change
	var linkChangePassword = Ext.get('ch_password');

	linkChangePassword.on('click', function() {
		var wnd = new changePasswordWindow();
		wnd.show();
	});
});
