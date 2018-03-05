'use strict';


$(document).ready(function(){
    $("#selItemDefault").select2();


    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //load all items once the page is ready
    lilt();


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //when text/btn ("Add item") to clone the div to add an item is clicked
    $("#clickToClone").on('click', function(e){
        e.preventDefault();

        var cloned = $("#divToClone").clone();

        //remove the id 'divToClone' from the cloned div
        cloned.addClass('transItemList').removeClass('hidden').attr('id', '');

        //reset the form values (in the cloned div) to default
        cloned.find(".selectedItemDefault").addClass("selectedItem").val("");
        cloned.find(".itemAvailQty").html("0");
        cloned.find(".itemTransQty").val("0");
        cloned.find(".itemTotalPrice").html("0.00");

        //loop through the currentItems variable to add the items to the select input
		return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
			var selectedItemsArr = [];

			return new Promise((res, rej)=>{
				$(".selectedItem").each(function(){
					//push the selected value (which is the item code [a key in currentItems object]) to the array
					$(this).val() ? selectedItemsArr.push($(this).val()) : "";
				});

				res();
			}).then(()=>{
				for(let key in currentItems){
					//if the current key in the loop is in our 'selectedItemsArr' array
					if(!inArray(key, selectedItemsArr)){
						//if the item has not been selected, append it to the select list
						cloned.find(".selectedItemDefault").append("<option value='"+key+"'>"+currentItems[key]+"</option>");
					}
				}

				//prepend 'select item' to the select option
				cloned.find(".selectedItemDefault").prepend("<option value='' selected>Select Item</option>");

				resolve(selectedItemsArr);
			});
		}).then((selectedItemsArray)=>{
			//If the input is from the barcode scanner, we need to check if the item has already been added to the list and just increment the qty instead of
			//re-adding it to the list, thus duplicating the item.
			if($("#barcodeText").val()){
				//This means our clickToClone btn was triggered after an item was scanned by the barcode scanner
				//Check the gotten selected items array if the item scanned has already been selected
				if(inArray($("#barcodeText").val().trim(), selectedItemsArray)){
					//increment it
					$(".selectedItem").each(function(){
						if($(this).val() === $("#barcodeText").val()){
							var newVal = parseInt($(this).closest('div').siblings('.itemTransQtyDiv').find('.itemTransQty').val()) + 1;

							$(this).closest('div').siblings('.itemTransQtyDiv').find('.itemTransQty').val(newVal);

							//unset value in barcode input
							$("#barcodeText").val('');

							return false;
						}
					});
				}

				else{
					//if it has not been selected previously, append it to the list and set it as the selected item
					//then append our cloned div to div with id 'appendClonedDivHere'
					cloned.appendTo("#appendClonedDivHere");

					//add select2 to the 'select input'
					cloned.find('.selectedItemDefault').select2();

					//set it as the selected item
					changeSelectedItemWithBarcodeText($("#barcodeText"), $("#barcodeText").val());
				}
			}

			else{//i.e. clickToClone clicked manually by user
				//do not append if no item is selected in the last select list
				if($(".selectedItem").length > 0 && (!$(".selectedItem").last().val())){
					//do nothing
				}

				else{
					//then append our cloned div to div with id 'appendClonedDivHere'
					cloned.appendTo("#appendClonedDivHere");

					//add select2 to the 'select input'
					cloned.find('.selectedItemDefault').select2();
				}
			}
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //WHEN USER CLICKS BTN TO REMOVE AN ITEM FROM THE TRANSACTION LIST
    $("#appendClonedDivHere").on('click', '.retrit', function(e){
        e.preventDefault();

        $(this).closest(".transItemList").remove();

        ceipacp();//recalculate price
        calchadue();//also recalculate change due
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //reload transactions table when events occur
    $("#transListPerPage, #transListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        latr_();
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    $("#transSearch").keyup(function(){
        var value = $(this).val();

        if(value){
            $.ajax({
                url: appRoot+"search/transsearch",
                type: "get",
                data: {v:value},
                success: function(returnedData){
                    $("#transListTable").html(returnedData.transTable);
                }
            });
        }

        else{
            //reload the table if all text in search box has been cleared
            latr_();
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //enable/disable amount tendered input field based on the selected mode of payment
    $("#modeOfPayment").change(function(){
        var modeOfPayment = $(this).val();

        //remove any error message we might have
        $("#amountTenderedErr").html("");

        //unset the values of cashAmount and posAmount
        $("#cashAmount, #posAmount").val("");

        if(modeOfPayment === "POS"){
            /**
             * Change the Label
             * set the "cumulative amount" value field as the value of "amount tendered" and make the amountTendered field disabled
             * change "changeDue" to 0.00
             * hide "cash" an "pos" fields
             *
             */
            $("#amountTenderedLabel").html("Amount Tendered");
            $("#amountTendered").val($("#cumAmount").html()).prop('disabled', true);
            $("#changeDue").html('0.00');
            $(".cashAndPos").addClass('hidden');
        }

        else if(modeOfPayment === "Cash and POS"){
            /*
             * Change the label
             * make empty "amount tendered" field's value and also make it writable
             * unset any value that might be in "changeDue"
             * display "cash" an "pos" fields
             */
            $("#amountTenderedLabel").html("Total");
            $("#amountTendered").val('').prop('disabled', true);
            $("#changeDue").html('');
            $(".cashAndPos").removeClass('hidden');
        }

        else{//if cash. If something not recognise, we assume it is cash
            /*
             * change the label
             * empty and make amountTendered field writable
             * unset any value that might be in "changeDue"
             * hide "cash" an "pos" fields
             */
            $("#amountTenderedLabel").html("Amount Tendered");
            $("#amountTendered").val('').prop('disabled', false);
            $("#changeDue").html('');
            $(".cashAndPos").addClass('hidden');
        }
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //calculate the change due based on the amount tendered. Also ensure amount tendered is not less than the cumulative amount
    $("#amountTendered").on('change focusout keyup keydown keypress', calchadue);

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * unset mode of payment each time ".itemTransQty" changes
     * This will allow the user to be able to reselect the mode of payment,
     * thus enabling us to recalculate change due based on amount tendered
     */
    $("#appendClonedDivHere").on("change", ".itemTransQty", function(e){
        e.preventDefault();

		return new Promise((resolve, reject)=>{
			$("#modeOfPayment").val("");

			resolve();
		}).then(()=>{
			ceipacp();
		}).catch();

		//recalculate
	    ceipacp();

        $("#modeOfPayment").val("");
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * If mode of payment is "Cash and POS", both #cashAmount and #posAmount fields will be visible to user to add values
     * The addition of both will be set as the amount tendered
     */
    $("#cashAmount, #posAmount").on("change", function(e){
        e.preventDefault();

        var totalAmountTendered = parseFloat($("#posAmount").val()) + parseFloat($("#cashAmount").val());

        //set amount tendered as the value of "totalAmountTendered" and then trigger the change event on it
        $("#amountTendered").val(isNaN(totalAmountTendered) ? "" : totalAmountTendered).change();
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //calcuate cumulative amount if the percentage of VAT is changed
    $("#vat").change(ceipacp);

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //calcuate cumulative amount if the percentage of discount is changed
    $("#discount").change(ceipacp);

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //handles the submission of a new sales order
    $("#confirmSaleOrder").click(function(){
        //ensure all fields are properly filled
        changeInnerHTML(['itemProductErr', 'itemCustomerErr', 'itemPlatformErr', 'itemPriorityErr', 'itemStatusErr', 'newTransErrMsg'], "");

        var itemProduct = $("#itemProduct").val();
        var itemCustomer = $("#itemCustomer").val();
        var itemPlatform = $("#itemPlatform").val();
        var itemPriority = $("#itemPriority").val();
        var itemStatus = $("#itemStatus").val();
        var itemCompetitor = $("#itemCompetitor").val();
        var itemProjectName = $("#itemProjectName").val();
        var itemDesc = $("#description").val();

        if(!itemProduct || !itemCustomer || !itemPlatform || !itemPriority || !itemStatus){
            !itemProduct ? $("#itemProductErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerErr").text("required") : "";
            !itemPlatform ? $("#itemPlatformErr").text("required") : "";
            !itemPriority ? $("#itemPriorityErr").text("required") : "";
            !itemStatus ? $("#itemStatusErr").text("required") : "";

            $("#newTransErrMsg").text("One or more required fields are empty");

            return;
        }

        displayFlashMsg("Adding Product transaction", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"productTransactions/add",
            data:{itemProduct:itemProduct, itemCustomer:itemCustomer, itemPlatform:itemPlatform, itemPriority:itemPriority,
                itemStatus:itemStatus, itemCompetitor:itemCompetitor, itemProjectName:itemProjectName, itemDesc:itemDesc},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("salesTransForm").reset();
                    $("#itemProduct").val(itemProduct);
                    $("#itemCustomer").val(itemCustomer);
                    $("#itemPlatform").val(itemPlatform);
                    $("#itemPriority").val(itemPriority);
                    $("#itemStatus").val(itemStatus);
                    $("#itemCompetitor").val(itemCompetitor);

                    //refresh the items list table
                    lilt();

                    //return focus to item code input to allow adding item with barcode scanner
                    $("#itemProduct").focus();
                }

                else{
                    hideFlashMsg();

                    //display all errors
                    $("#itemProductErr").text(returnedData.itemProduct);
                    $("#itemCustomerErr").text(returnedData.itemCustomer);
                    $("#itemPlatformErr").text(returnedData.itemPlatform);
                    $("#itemPriorityErr").text(returnedData.itemPriority);
                    $("#itemStatusErr").text(returnedData.itemStatus);
                    $("#newTransErrMsg").text(returnedData.msg);
                }
            },

            error: function(){
                if(!navigator.onLine){
                    changeFlashMsgContent("You appear to be offline. Please reconnect to the internet and try again", "", "red", "");
                }

                else{
                    changeFlashMsgContent("Unable to process your request at this time. Pls try again later!", "", "red", "");
                }
            }
        });

    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //handles the submission of a new sales transaction
    $("#cancelSaleOrder").click(function(e){
        e.preventDefault();

        document.getElementById('salesTransForm').reset();

        return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
			return new Promise((res, rej)=>{
				//$(".selectedItem").each(function(){
				//	//push the selected value (which is the item code [a key in currentItems object]) to the array
				//	$(this).val() ? selectedItemsArr.push($(this).val()) : "";
				//});

				res();
			}).then(()=>{
                $(".selectedProductDefault").empty();
				for(let key in currentProducts){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedProductDefault").append("<option value='"+key+"'>"+currentProducts[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedProductDefault").prepend("<option value='' selected>Select Prodcut</option>");

                $(".selectedPriorityDefault").empty();
                for(let key in currentPriorities){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedPriorityDefault").append("<option value='"+key+"'>"+currentPriorities[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedPriorityDefault").prepend("<option value='' selected>Select Priority</option>");

                $(".selectedCustomerDefault").empty();
                for(let key in currentCustomers){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedCustomerDefault").append("<option value='"+key+"'>"+currentCustomers[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedCustomerDefault").prepend("<option value='' selected>Select Customer</option>");

                $(".selectedPlatformDefault").empty();
                for(let key in currentPlatforms){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedPlatformDefault").append("<option value='"+key+"'>"+currentPlatforms[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedPlatformDefault").prepend("<option value='' selected>Select Platforms</option>");

                $(".selectedStatusDefault").empty();
                for(let key in currentStatuses){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedStatusDefault").append("<option value='"+key+"'>"+currentStatuses[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedStatusDefault").prepend("<option value='' selected>Select Status</option>");

                $(".selectedCompetitorDefault").empty();
                for(let key in currentCompetitors){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedCompetitorDefault").append("<option value='"+key+"'>"+currentCompetitors[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedCompetitorDefault").prepend("<option value='' selected>Select Competitor</option>");


				resolve(); //selectedGroupsArr, selectedPrioritysArr
			});
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
				//add select2 to the 'select input'
			    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //triggers when an item's "edit" icon is clicked
    $("#transListTable").on('click', ".editItem", function(e){
        e.preventDefault();

        //get item info
        var itemId = $(this).attr('id').split("-")[1];
        var itemDesc = $("#itemDesc-"+itemId).attr('title');
        var itemName = $("#itemName-"+itemId).html();
        var itemGroup = $("#itemGroup-"+itemId).html();
        var itemPriority = $("#itemPriority-"+itemId).html();
        var itemVersion = $("#itemVersion-"+itemId).html();

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemNameEdit").val(itemName);
        $("#itemVersionEdit").val(itemVersion);
        $("#itemDescriptionEdit").val(itemDesc);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemNameEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        //$(".selectedItemDefault").addClass("selectedItem").val("");

        //loop through the currentItems variable to add the items to the select input
        return new Promise((resolve, reject)=>{
            //if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
            //We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
            var selectedGroupsArr = [];
            var selectedPrioritysArr = [];

            return new Promise((res, rej)=>{
                //$(".selectedItem").each(function(){
                //	//push the selected value (which is the item code [a key in currentItems object]) to the array
                //	$(this).val() ? selectedItemsArr.push($(this).val()) : "";
                //});

                res();
            }).then(()=>{
                $(".selectedGroupDefault").empty();
                for(let key in currentGroups){
                    //if the current key in the loop is in our 'selectedItemsArr' array
                    if(!inArray(key, selectedGroupsArr)){
                        //if the item has not been selected, append it to the select list
                        if (currentGroups[key] == itemGroup) {
                            $(".selectedGroupDefault").append("<option value='"+key+"' selected>"+currentGroups[key]+"</option>");
                        } else {
                            $(".selectedGroupDefault").append("<option value='"+key+"'>"+currentGroups[key]+"</option>");
                        }
                    }
                }

                //prepend 'select item' to the select option
                $(".selectedGroupDefault").prepend("<option value=''>Select Group</option>");

                $(".selectedPriorityDefault").empty();
                for(let key in currentPriorities){
                    //if the current key in the loop is in our 'selectedItemsArr' array
                    if(!inArray(key, selectedPrioritysArr)){
                        //if the item has not been selected, append it to the select list
                        if (currentPriorities[key] == itemPriority) {
                            $(".selectedPriorityDefault").append("<option value='"+key+"' selected>"+currentPriorities[key]+"</option>");
                        } else {
                            $(".selectedPriorityDefault").append("<option value='"+key+"'>"+currentPriorities[key]+"</option>");
                        }
                    }
                }

                //prepend 'select item' to the select option
                $(".selectedPriorityDefault").prepend("<option value=''>Select Priority</option>");

                resolve(selectedGroupsArr, selectedPrioritysArr);
            });
        }).then((selectedGroupsArray, selectedPrioritysArray)=>{
                //add select2 to the 'select input'
                $('.selectedGroupDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
        }).catch(()=>{
            console.log('outer promise err');
        });

        return false;
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#editItemSubmit").click(function(){
        var itemName = $("#itemNameEdit").val();
        var itemGroup = $("#itemGroupEdit").val();
        var itemPriority = $("#itemPriorityEdit").val();
        var itemVersion = $("#itemVersionEdit").val();
        var itemDesc = $("#itemDescriptionEdit").val();
        var itemId = $("#itemIdEdit").val();

        if(!itemName || !itemId || !itemGroup || !itemPriority){
            !itemName ? $("#itemNameEditErr").html("Product name cannot be empty") : "";
            !itemGroup ? $("#itemGroupEditErr").html("Product group cannot be empty") : "";
            !itemPriority ? $("#itemPriorityEditErr").html("Product priority cannot be empty") : "";
            !itemId ? $("#editItemFMsg").html("Unknown Priority") : "";
            return;
        }

        var itemGroupID = itemGroup;
        var itemPriorityID = itemPriority;

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"products/edit",
            data: {itemName:itemName, itemGroupID:itemGroupID, itemPriorityID:itemPriorityID, itemVersion:itemVersion, itemDesc:itemDesc, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#editItemFMsg").css('color', 'green').html("Product successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            }

            else{
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemNameEditErr").html(returnedData.itemName);
                $("#itemValueEditErr").html(returnedData.itemValue);
            }
        }).fail(function(){
            $("#editItemFMsg").css('color', 'red').html("Unable to process your request at this time. Please check your internet connection and try again");
        });
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //TO DELETE AN ITEM (The item will be marked as "deleted" instead of removing it totally from the db)
    $("#transListTable").on('click', '.delItem', function(e){
        e.preventDefault();

        //get the item id
        var itemId = $(this).parents('tr').find('.curItemId').val();
        var itemRow = $(this).closest('tr');//to be used in removing the currently deleted row

        if(itemId){
            var confirm = window.confirm("Are you sure you want to delete item? This cannot be undone.");

            if(confirm){
                displayFlashMsg('Please wait...', spinnerClass, 'black');

                $.ajax({
                    url: appRoot+"productTransactions/delete",
                    method: "POST",
                    data: {i:itemId}
                }).done(function(rd){
                    if(rd.status === 1){
                        //remove item from list, update items' SN, display success msg
                        $(itemRow).remove();

                        //update the SN
                        resetItemSN();

                        //display success message
                        changeFlashMsgContent('Item deleted', '', 'green', 1000);
                    }

                    else{

                    }
                }).fail(function(){
                    console.log('Req Failed');
                });
            }
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //WHEN THE "USE SCANNER" BTN IS CLICKED
    $("#useScanner").click(function(e){
        e.preventDefault();

        //focus on the barcode text input
        $("#barcodeText").focus();
    });



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //WHEN THE BARCODE TEXT INPUT VALUE CHANGED
    $("#barcodeText").keyup(function(e){
        e.preventDefault();

        var bText = $(this).val();
        var allItems = [];

		if(bText){
			for(let i in currentItems){
				if(bText === i){
					//remove any message that might have been previously displayed
					$("#itemCodeNotFoundMsg").html("");

					//if no select input has been added or the last select input has a value (i.e. an item has been selected)
					if(!$(".selectedItem").length || $(".selectedItem").last().val()){
						//add a new item by triggering the clickToClone btn. This will handle everything from 'appending a list of items' to 'auto-selecting
						//the corresponding item to the value detected by the scanner'
						$("#clickToClone").click();
					}

					//else if the last select input doesn't have a value
					else{
						//just change the selected item to the corresponding code in var bText
						changeSelectedItemWithBarcodeText($(this), bText);
					}

					break;
				}

				//if the value doesn't match the code of any item
				else{
					//display message telling user item not found
					$("#itemCodeNotFoundMsg").css('color', 'red').html("Item not found. Item may not be registered.");
				}
			}
		}
    });



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //TO SHOW/HIDE THE TRANSACTION FORM
    $("#showTransForm").click(function(){
        $("#newTransDiv").toggleClass('collapse');

        if($("#newTransDiv").hasClass('collapse')){
            $(this).html("<i class='fa fa-plus'></i> New Product Transaction");
        }

        else{
            $(this).html("<i class='fa fa-minus'></i> New Product Transaction");

            //remove error messages
            $("#itemCodeNotFoundMsg").html("");
        }

        return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
			return new Promise((res, rej)=>{
				//$(".selectedItem").each(function(){
				//	//push the selected value (which is the item code [a key in currentItems object]) to the array
				//	$(this).val() ? selectedItemsArr.push($(this).val()) : "";
				//});

				res();
			}).then(()=>{
                $(".selectedProductDefault").empty();
				for(let key in currentProducts){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedProductDefault").append("<option value='"+key+"'>"+currentProducts[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedProductDefault").prepend("<option value='' selected>Select Prodcut</option>");

                $(".selectedPriorityDefault").empty();
                for(let key in currentPriorities){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedPriorityDefault").append("<option value='"+key+"'>"+currentPriorities[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedPriorityDefault").prepend("<option value='' selected>Select Priority</option>");

                $(".selectedCustomerDefault").empty();
                for(let key in currentCustomers){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedCustomerDefault").append("<option value='"+key+"'>"+currentCustomers[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedCustomerDefault").prepend("<option value='' selected>Select Customer</option>");

                $(".selectedPlatformDefault").empty();
                for(let key in currentPlatforms){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedPlatformDefault").append("<option value='"+key+"'>"+currentPlatforms[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedPlatformDefault").prepend("<option value='' selected>Select Platforms</option>");

                $(".selectedStatusDefault").empty();
                for(let key in currentStatuses){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedStatusDefault").append("<option value='"+key+"'>"+currentStatuses[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedStatusDefault").prepend("<option value='' selected>Select Status</option>");

                $(".selectedCompetitorDefault").empty();
                for(let key in currentCompetitors){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedCompetitorDefault").append("<option value='"+key+"'>"+currentCompetitors[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedCompetitorDefault").prepend("<option value='' selected>Select Competitor</option>");


				resolve(); //selectedGroupsArr, selectedPrioritysArr
			});
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
				//add select2 to the 'select input'
			    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //TO HIDE THE TRANSACTION FORM FROM THE TRANSACTION FORM
    $("#hideTransForm").click(function(){
        $("#newTransDiv").toggleClass('collapse');

        //remove error messages
        $("#itemCodeNotFoundMsg").html("");

        //change main "new transaction" button back to default
        $("#showTransForm").html("<i class='fa fa-plus'></i> New Product Transaction");
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //PREVENT AUTO-SUBMISSION BY THE BARCODE SCANNER (this shouldn't matter but just to be on the safe side)
    $("#barcodeText").keypress(function(e){
        if(e.which === 13){
            e.preventDefault();
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#datePair .date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        assumeNearbyYear: true,
        todayBtn: 'linked',
        todayHighlight: true,
        endDate: 'today'
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //INITIALISE datepair on the "From date" and "To date" fields
    $("#datePair").datepair();

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //WHEN "GENERATE REPORT" BUTTON IS CLICKED FROM THE MODAL
    $("#clickToGen").click(function(e){
        e.preventDefault();

        var fromDate = $("#transFrom").val();
        var toDate = $("#transTo").val();

        if(!fromDate){
            $("#transFromErr").html("Select date to start from");

            return;
        }

        /*
         * remove any error msg, hide modal, launch window to display the report in
         */

        $("#transFromErr").html("");
        $("#reportModal").modal('hide');

        var strWindowFeatures = "width=1000,height=500,scrollbars=yes,resizable=yes";

        window.open(appRoot+"transactions/report/"+fromDate+"/"+toDate, 'Print', strWindowFeatures);
    });
});


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * gti_ = "Get Transaction Info"
 * @param {type} transId
 * @returns {Boolean}
 */
function gti_(transId){
    if(transId){
        $("#transReceipt").html("<i class='fa fa-spinner faa-spin animated'></i> Loading...");

        //make server request to get information about transaction
        $.ajax({
            type: "POST",
            url: appRoot+"transactions/transactionReceipt",
            data: {transId:transId},
            success: function(returnedData){
                if(returnedData.status === 1){
                    $("#transReceipt").html(returnedData.transReceipt);
                }

                else{

                }
            },
            error: function(){
                alert("ERROR!");
            }
        });
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//to update transaction
function uptr_(transId){
    //alert(transId);
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * ceipacp = "Calculate each item's price and cumulative price"
 * This calculates the total price of each item selected for sale and also their cumulative amount
 * @returns {undefined}
 */
function ceipacp(){
    var cumulativePrice = 0;

    //loop through the items selected to calculate the total of each item
    $(".transItemList").each(function(){
        //current item's available quantity
        var availQty = parseFloat($(this).find(".itemAvailQty").html());

        //current item's quantity to be sold
        var transQty = parseInt($(this).find(".itemTransQty").val());

        //if the qty needed is greater than the qty available
        if(transQty > availQty){
            //set the value back to the max available qty
            $(this).find(".itemTransQty").val(availQty);

            //display msg telling user the qty left
            $(this).find(".itemTransQtyErr").html("only "+ availQty + " left");

            ceipacp();//call itself in order to recalculate price
        }


        else{//if all is well
            //remove err msg if any
            $(this).find(".itemTransQtyErr").html("");

            //calculate the total price of current item
            var itemTotalPrice = parseFloat(($(this).find(".itemUnitPrice").html()) * transQty);

            //round to two decimal places
            itemTotalPrice = +(itemTotalPrice).toFixed(2);

            //display the total price
            $(this).find(".itemTotalPrice").html(itemTotalPrice);

            //add current item's total price to the cumulative amount
            cumulativePrice += itemTotalPrice;
        }

        //trigger the click event of "use barcode" btn to focus on the barcode input text
        $("#useScanner").click();
    });

    return new Promise(function(resolve, reject){
        //calculate discount amount using the discount percentage
        var discountAmount = getDiscountAmount(cumulativePrice);//get discount amount

        //now update verifyCumAmount by subtracting the discount amount from it
        cumulativePrice = +(cumulativePrice - discountAmount).toFixed(2);

        resolve();
    }).then(function(){
        //get vat amount
        var vatAmount = getVatAmount(cumulativePrice);

        //now update cumulativePrice by adding the amount of VAT to it
        cumulativePrice = +(cumulativePrice + vatAmount).toFixed(2);

        //display the cumulative amount
        $("#cumAmount").html(cumulativePrice);

        //update change due just in case amount tendered field is filled
        calchadue();
    }).catch(function(){
        console.log("Err");
    });
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Populates the available quantity and unit price of selected item to be sold
 * Auto set the quantity needed to 1
 * @param {type} selectedNode
 * @returns {undefined}
 */
function selectedItem(selectedNode){
    if(selectedNode){
        //get the elements of the selected item's avail qty and unit price
        var itemAvailQtyElem = selectedNode.parentNode.parentNode.children[1].children[1];
        var itemUnitPriceElem = selectedNode.parentNode.parentNode.children[2].children[1];
        var qtyNeededElem = selectedNode.parentNode.parentNode.children[3].children[1];

        var itemCode = selectedNode.value;

        //displayFlashMsg("Getting item info...", spinnerClass, "", "");

        //get item's available quantity and unitPrice
        $.ajax({
            url: appRoot+"items/gcoandqty",
            type: "get",
            data: {_iC:itemCode},
            success: function(returnedData){
                if(returnedData.status === 1){
                    itemAvailQtyElem.innerHTML = returnedData.availQty;
                    itemUnitPriceElem.innerHTML = parseFloat(returnedData.unitPrice);

                    qtyNeededElem.value = 1;

                    ceipacp();//recalculate since item has been changed/added
                    calchadue();//update change due as well in case amount tendered is not empty

                    //hideFlashMsg();

                    //return focus to the hidden barcode input
                    $("#useScanner").click();
                }

                else{
                    itemAvailQtyElem.innerHTML = "0";
                    itemUnitPriceElem.innerHTML = "0.00";

                    ceipacp();//recalculate since item has been changed/added
                    calchadue();//update change due as well in case amount tendered is not empty

                    //changeFlashMsgContent("Item not found", "", "red", "");
                }
            }
        });
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * calchadue = "Calculate change due"
 * @returns {undefined}
 */
function calchadue(){
    var cumAmount = parseFloat($("#cumAmount").html());
    var amountTendered = parseFloat($("#amountTendered").val());

    if(amountTendered && (amountTendered < cumAmount)){
        $("#amountTenderedErr").html("Amount cannot be less than &#8358;"+ cumAmount);

        //remove change due if any
        $("#changeDue").html("");
    }

    else if(amountTendered){
        $("#changeDue").html(+(amountTendered - cumAmount).toFixed(2));

        //remove error msg if any
        $("#amountTenderedErr").html("");
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * ctr_ = "Close Transaction receipt". This is for the receipt being displayed immediately the sales order is done
 * @deprecated v1.0.0
 * @returns {undefined}
 */
function ctr_(){
    //hide receipt and display form
    $("#salesTransFormDiv").removeClass("hidden");
    $("#showTransReceipt").addClass("hidden").html("");
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function resetTransList(){
    var tot = $(".transItemList").length;

    $(".transItemList").each(function(){
        if($(".transItemList").length > 1){
            $(this).remove();
        }

        else{
            return "";
        }
    });
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * latr_ = "Load all transactions"
 * @param {type} url
 * @returns {Boolean}
 */
function latr_(url){
    var orderBy = $("#transListSortBy").val().split("-")[0];
    var orderFormat = $("#transListSortBy").val().split("-")[1];
    var limit = $("#transListPerPage").val();

    $.ajax({
        type:'get',
        url: url ? url : appRoot+"transactions/latr_/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},

        success: function(returnedData){
            hideFlashMsg();

            $("#transListTable").html(returnedData.transTable);
        },

        error: function(){

        }
    });

    return false;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function changeSelectedItemWithBarcodeText(barcodeTextElem, selectedItem){
    $(".selectedItem").last().val(selectedItem).change();

    //then remove the value from the input
    $(barcodeTextElem).val("");
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getVatAmount(cumAmount){
    //update cumAmount by adding the amount VAT to it
    var vatPercentage = $("#vat").val();//get vat percentage

    //calculate the amount vat will be
    var vatAmount = parseFloat((vatPercentage/100) * cumAmount);

    return vatAmount;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getDiscountAmount(cumAmount){
    //update cumAmount by subtracting discount amount from it
    var discountPercentage = $("#discount").val();//get discount percentage

    //calculate the discount amount
    var discountAmount = parseFloat((discountPercentage/100) * cumAmount);

    return discountAmount;
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/**
 * "lilt" = "load Items List Table"
 * @param {type} url
 * @returns {undefined}
 */
function lilt(url){
    var orderBy = $("#transListSortBy").val().split("-")[0];
    var orderFormat = $("#transListSortBy").val().split("-")[1];
    var limit = $("#transListPerPage").val();


    $.ajax({
        type:'get',
        url: url ? url : appRoot+"productTransactions/lilt/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},

        success: function(returnedData){
            hideFlashMsg();
            $("#transListTable").html(returnedData.itemsListTable);
        },

        error: function(){

        }
    });

    return false;
}



function resetItemSN(){
    $(".itemSN").each(function(i){
        $(this).html(parseInt(i)+1);
    });
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//TO DELETE AN ITEM (The item will be marked as "deleted" instead of removing it totally from the db)
