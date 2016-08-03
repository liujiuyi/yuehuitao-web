changePasswordWindow = function() {
	var wnd = this;

	Ext.apply(Ext.form.VTypes, {
		password : function(val, field) {
			if (field.initialPassField) {
				var pwd = Ext.getCmp(field.initialPassField);
				return (val == pwd.getValue());
			}
			return true;
		},

		passwordText : '两次输入的密码不一致'
	});

	var frm = new Ext.FormPanel({
		frame : true,
		url : 'department_manager_back.php?func=change_password',
		defaultType : 'textfield',
		labelWidth : 130,
		labelAlign : 'right',
		labelPad : 10,

		items : [ {
			id : 'old_password',
			fieldLabel : '旧密码',
			name : 'old_password',
			inputType : 'password',
			allowBlank : false

		}, {
			id : 'new_password',
			fieldLabel : '新密码',
			name : 'new_password',
			inputType : 'password',
			allowBlank : false,
			minLength : 10,
			minLengthText : '请输入十位以上长度的密码',
			ref : 'new_password'

		}, {
			id : 'confirm_password',
			fieldLabel : '新密码(确认)',
			name : 'confirm_password',
			inputType : 'password',
			allowBlank : false,
			minLength : 10,
			minLengthText : '请输入十位以上长度的密码',
			vtype : 'password',
			initialPassField : 'new_password',

			listeners : {
				specialkey : function(field, e) {
					if (e.getKey() == e.ENTER) {
						frm.buttons[0].handler.call(frm.buttons[0].scope);
					}
				}
			}
		} ],
		buttons : [ {
			text : '确定',
			handler : function() {
				this.disable();
				frm.getForm().submit({
					timeout : 600,
					waitTitle : '更新密码',
					waitMsg : 'doing...',
					success : function(form, action) {
						if (action.result.success == 'true') {
							ShowMessage('Archive Manager', decodeURI(action.result.msg), 'INFO');
							wnd.destroy();
						} else if (action.result.success == 'false') {
							ShowMessage('Archive Manager', decodeURI(action.result.msg), 'ERROR');
						}
					},
					failure : function(form, action) {
						if (action != null && action.result != null && action.result.msg != null) {
							ShowMessage('Archive Manager', decodeURI(action.result.msg), 'ERROR');
						}
					}
				});
				wnd.show(); 
			}
		}, {
			text : '取消',
			handler : function() {
				wnd.destroy();
			}
		} ]
	});

	frm.on('afterrender', function() {
		frm.getComponent('old_password').focus(true, 1000);
		frm.getComponent('old_password').clearInvalid();
	}, this);

	changePasswordWindow.superclass.constructor.call(this, {
		title : '更新密码',
		width : 340,
		height : 160,
		layout : 'fit',
		closeAction : 'destroy',
		resizable : true,
		maximizable : true,
		minHeight : 160,
		minWidth : 340,
		items : [ frm ],
		plain : true,
		frame : true,
		border : false,
		modal : true
	});
}

Ext.extend(changePasswordWindow, Ext.Window, {});