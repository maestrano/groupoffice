/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: CompanyDialog.js 15280 2013-07-23 11:48:26Z michaelhart86 $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.addressbook.CompanyDialog = function(config)
{
	Ext.apply(this, config);

	this.goDialogId = 'company';
	
	this.personalPanel = new GO.addressbook.CompanyProfilePanel();	    
		    
	this.commentPanel = new Ext.Panel({
		title: GO.addressbook.lang['cmdPanelComments'], 
		layout: 'fit',
		border:false,
		items: [
		new Ext.form.TextArea({
			name: 'comment',
			fieldLabel: '',
			hideLabel: true
		})
		]
	});

	this.commentPanel.on('show', function(){
		this.companyForm.form.findField('comment').focus();
	}, this);

	/* employees Grid */
	this.employeePanel = new GO.addressbook.EmployeesPanel();

  
	var items = [
	this.personalPanel,
	this.commentPanel];
	      	
	this.selectAddresslistsPanel = new GO.addressbook.SelectAddresslistsPanel();
					
	items.push(this.selectAddresslistsPanel);
	items.push(this.employeePanel);
  
	if(GO.customfields && GO.customfields.types["GO_Addressbook_Model_Company"])
	{
		for(var i=0;i<GO.customfields.types["GO_Addressbook_Model_Company"].panels.length;i++)
		{
			items.push(GO.customfields.types["GO_Addressbook_Model_Company"].panels[i]);
		}
	}	
	
	this.companyForm = new Ext.FormPanel({
		waitMsgTarget:true,		
		border: false,
		baseParams: {},
		items: [
		this.tabPanel = new Ext.TabPanel({
			border: false,
			activeTab: 0,
			enableTabScroll:true,
			deferredRender: false,
			hideLabel: true,
			anchor:'100% 100%',
			items: items
		})
		]
	});				
    


	this.id= 'addressbook-window-new-company';
	this.layout= 'fit';
	this.modal= false;
	this.shadow= false;
	this.border= false;
	this.height= 560;
	this.width= 820;
	this.plain= true;
	this.closeAction= 'hide';
	this.collapsible=true;
	this.title= GO.addressbook.lang['cmdCompanyDialog'];
	this.items= this.companyForm;
	this.buttons=  [
	{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.saveCompany(true);
		},
		scope: this
	},
	/*{
		text: GO.lang['cmdApply'],
		handler: function(){
			this.saveCompany();
		},
		scope: this
	},*/
	{
		text: GO.lang['cmdClose'],
		handler: function()
		{
			this.hide();
		},
		scope: this
	}
	];
		
	var focusFirstField = function(){
		this.companyForm.form.findField('name').focus(true);
	};
	this.focus= focusFirstField.createDelegate(this);



	this.tbar = [this.moveEmployeesButton = new Ext.Button({
		text:GO.addressbook.lang.moveEmployees,
		handler:function(){
			if(!this.moveEmpWin){

				this.moveEmpForm = new Ext.FormPanel({
					cls:'go-form-panel',
//					url:GO.settings.modules.addressbook.url+'action.php',
					url: GO.url('addressbook/company/moveEmployees'),
					baseParams:{
//						task:'move_employees',
						from_company_id:0
					},
					waitMsgTarget:true,
					items:new GO.addressbook.SelectCompany({
						allowBlank:false,
						anchor:'100%',
						hiddenName:'to_company_id'
					})
				});

				this.moveEmpWin = new GO.Window({
					title:GO.addressbook.lang.moveEmployees,
					closable:true,
					modal:true,
					width:400,
					autoHeight:true,
					items:this.moveEmpForm,
					buttons:[{
						text:GO.lang.cmdOk,
						handler:function(){
							this.moveEmpForm.form.submit({
								waitMsg:GO.lang['waitMsgSave'],
								success:function(form, action){
									this.moveEmpWin.hide();
								},
								failure: function(form, action) {

									if(action.failureType == 'client')
									{
										Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
									} else {
										Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
									}
								},
								scope: this
							})
						},
						scope:this
					}]
				});
			}
			this.moveEmpForm.baseParams.from_company_id=this.company_id;
			this.moveEmpWin.show();
		},
		scope:this
	})];

		this.personalPanel.formAddressBooks.on({
					scope:this,
					change:function(sc, newValue, oldValue){
						var record = sc.store.getById(newValue);
						GO.customfields.disableTabs(this.tabPanel, record.data,'companyCustomfields');	
					}
				});

	GO.addressbook.CompanyDialog.superclass.constructor.call(this);
	
	this.addEvents({
		'save':true
	});
	
//	if (GO.customfields) {
//		this.personalPanel.formAddressBooks.on('select',function(combo,record,index){
//			var allowed_cf_categories = record.data.allowed_cf_categories.split(',');
//			this.updateCfTabs(allowed_cf_categories);
//		},this);
//		this.companyForm.form.on('actioncomplete',function(form, action){
//			if(action.type=='load'){
//				
//			}
//		},this);
//	}
}
	
Ext.extend(GO.addressbook.CompanyDialog, GO.Window, {

	show : function(company_id)
	{
		if(!GO.addressbook.writableAddressbooksStore.loaded)
		{
			GO.addressbook.writableAddressbooksStore.load(
			{
				callback: function(){
					this.show(company_id);
				},
				scope:this
			});
		}else	if(!GO.addressbook.writableAddresslistsStore.loaded)
		{
			GO.addressbook.writableAddresslistsStore.load({
				callback:function(){
					this.show(company_id);
				},
				scope:this
			});
		}else
		{
			var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
			this.companyForm.form.reset();

			this.personalPanel.formAddressBooks.setValue(tempAddressbookID);
			
			if(!this.rendered)
			{
				this.render(Ext.getBody());
			}			
			
			if(company_id)
			{
				this.company_id = company_id;
			} else {
				this.company_id = 0;
			}	

			if(!GO.util.empty(GO.addressbook.defaultAddressbook)){
				var store = this.personalPanel.formAddressBooks.store;
				//add record to store if not loaded
				var r = store.getById(GO.addressbook.defaultAddressbook.id);
				if(!r)
				{
					store.add(GO.addressbook.defaultAddressbook);
				}

				this.personalPanel.setAddressbookID(GO.addressbook.defaultAddressbook.id);
				//this.personalPanel.formAddressBooks.setValue(GO.addressbook.defaultAddressbook);
			}else if(tempAddressbookID>0 && this.personalPanel.formAddressBooks.store.getById(tempAddressbookID))
			{
				this.personalPanel.setAddressbookID(tempAddressbookID);
			}else
			{
				this.personalPanel.formAddressBooks.selectFirst();
				this.personalPanel.setAddressbookID(this.personalPanel.formAddressBooks.getValue());
			}
			
			this.moveEmployeesButton.setDisabled(true);
		
			this.tabPanel.setActiveTab(0);
			
//			if(this.company_id > 0)
//			{
				this.loadCompany(company_id);				
//			} else {
//				this.employeePanel.setCompanyId(0);
//				var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
//				
//				this.companyForm.form.reset();
//
//				if(tempAddressbookID>0 && this.personalPanel.formAddressBooks.store.getById(tempAddressbookID))
//					this.personalPanel.formAddressBooks.setValue(tempAddressbookID);
//				else
//					this.personalPanel.formAddressBooks.selectFirst();
//				
//				this.personalPanel.setCompanyId(0);
//
//				var abRecord = this.personalPanel.formAddressBooks.store.getById(this.personalPanel.formAddressBooks.getValue());
//			
//				if (GO.customfields) {
//					var allowed_cf_categories = abRecord.data.allowed_cf_categories.split(',');
//					this.updateCfTabs(allowed_cf_categories);
//				}
//
//				GO.addressbook.CompanyDialog.superclass.show.call(this);
//			}		
		}
	},	

	updateCfTabs : function(allowed_cf_categories) {
//		for (var i=0; i<this.tabPanel.items.items.length; i++) {
//			if (typeof(this.tabPanel.items.items[i].category_id)!='undefined') {
//				this.tabPanel.hideTabStripItem(this.tabPanel.items.items[i]);
//				if(allowed_cf_categories.indexOf(this.tabPanel.items.items[i].category_id.toString())>=0)
//					this.tabPanel.unhideTabStripItem(this.tabPanel.items.items[i]);
//				else
//					this.tabPanel.hideTabStripItem(this.tabPanel.items.items[i]);
//			}
//		}
	},

	loadCompany : function(id)
	{
		this.beforeLoad();
		
		this.companyForm.form.load({
			url:GO.url('addressbook/company/load'),
			params: {
				id: id,
				addressbook_id:this.companyForm.form.findField('addressbook_id').getValue()
				
			},
			
			success: function(form, action) {
				

				this.employeePanel.setCompanyId(action.result.data['id']);
				this.personalPanel.setCompanyId(action.result.data['id']);
				this.moveEmployeesButton.setDisabled(false);
				
				if(GO.customfields)
					GO.customfields.disableTabs(this.tabPanel, action.result);	
				
				
				this.personalPanel.formAddressBooks.setRemoteText(action.result.remoteComboTexts.addressbook_id);
				
				
				this.afterLoad(action);

				GO.addressbook.CompanyDialog.superclass.show.call(this);
						
			},
			failure: function(form, action)
			{
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}			 		
			},
			scope: this
		});			
	},
	
	afterLoad  : function(action){
		
	},
	
	beforeLoad  : function(){
		
	},
	
	saveCompany : function(hide)
	{	
		this.companyForm.form.submit({
			url:GO.url('addressbook/company/submit'),
			params:
			{
				id : this.company_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.id)
				{
					this.company_id = action.result.id;				
					this.employeePanel.setCompanyId(action.result.id);
					this.moveEmployeesButton.setDisabled(false);
				}				
				this.fireEvent('save', this, this.company_id);
				
				GO.dialog.TabbedFormDialog.prototype.refreshActiveDisplayPanels.call(this);
				
				if (hide)
				{
					this.hide();
				}			
			},
			failure: function(form, action) {					

				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});			
	}
});
