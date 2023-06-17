function CheckFields(){
	var mapping = new Object();
	
	mapping.item_code = $('itemCode').value;
	mapping.CostCenterArea = $('CostCenterArea').value;
	mapping.account_transaction = $('accountTransaction').value;
	
	if(mapping.CostCenterArea == 'LD' || mapping.CostCenterArea == 'RD' || mapping.CostCenterArea == 'PH'|| mapping.CostCenterArea == 'OT'){
		mapping.account_income = $('IncomeAccount').value;
		mapping.account_cash = $('CashAccount').value;
		mapping.account_tax = $('TaxAccount').value;
		mapping.account_Inventory = $('InventoryAccount').value;
		mapping.account_COGS = $('COGSAccount').value;
		mapping.area = $('area').value;
		xajax_SaveMappingWithCostCenter(mapping);
	}else{
		if($('area').value !== ""){
			mapping.account_debit = $('DebitAccount').value;
			mapping.account_credit = $('CreditAccount').value;
			mapping.area = $('area').value;
			xajax_SaveMappingWithOutCostCenter(mapping);
		}else{
			mapping.account_debit = $('DebitAccount').value;
			mapping.account_credit = $('CreditAccount').value;

			if($('IncomeAccount')){
				mapping.income_account = $('IncomeAccount').value;	
			}
			
			if($('CashAccount')){
				mapping.cash_account = $('CashAccount').value;	
			}
			
			if($('TaxAccount')){
				mapping.tax_account = $('TaxAccount').value;
			}
			
			xajax_SaveMappingWithOutCostCenter(mapping);
		}		
	}

	
	
}
