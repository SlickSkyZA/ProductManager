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


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //reload transactions table when events occur
    $("#transListPerPage, #transListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
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
                url: appRoot+"search/productTransactionsearch",
                type: "get",
                data: {v:value},
                success: function(returnedData){
                    $("#transListTable").html(returnedData.itemsListTable);
                }
            });
        }

        else{
            //reload the table if all text in search box has been cleared
            displayFlashMsg("Loading page...", spinnerClass, "", "");
            lilt();
        }
    });

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
        var itemProjectName = $("#itemProject").val();
        var itemMilestone = $("#itemMilestone").val();
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
                itemStatus:itemStatus, itemCompetitor:itemCompetitor, itemProjectName:itemProjectName, itemMilestone:itemMilestone, itemDesc:itemDesc},

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
                    $("#itemProject").val(itemProjectName);
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

                $(".selectedProjectDefault").empty();
                for(let key in currentProjects){
					//if the current key in the loop is in our 'selectedItemsArr' array
					$(".selectedProjectDefault").append("<option value='"+key+"'>"+currentProjects[key]+"</option>");
				}
				//prepend 'select item' to the select option
				$(".selectedProjectDefault").prepend("<option value='' selected>Select Project</option>");


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
                $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
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
        var itemProduct = $("#itemProduct-"+itemId).html();
        var itemCustomer = $("#itemCustomer-"+itemId).html();
        var itemPlatform = $("#itemPlatform-"+itemId).html();
        var itemPriority = $("#itemPriority-"+itemId).html();
        var itemStatus = $("#itemStatus-"+itemId).html();
        var itemCompetitor = $("#itemCompetitor-"+itemId).html();
        var itemProject = $("#itemProject-"+itemId).html();
        var itemDesc = $("#itemDesc-"+itemId).attr('title');

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemDescEdit").val(itemDesc);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemProductEditErr").html("");
        $("#itemCustomerEditErr").html("");
        $("#itemPlatformEditErr").html("");
        $("#itemPriorityEditErr").html("");
        $("#itemStatusEditErr").html("");
        $("#itemCompetitorEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown

            $(".selectedProductDefault").empty();
			for(let key in currentProducts){
				//if the current key in the loop is in our 'selectedItemsArr' array
                if (currentProducts[key] == itemProduct) {
                    $(".selectedProductDefault").append("<option value='"+key+"' selected>"+itemProduct+"</option>");
                } else {
                    $(".selectedProductDefault").append("<option value='"+key+"'>"+currentProducts[key]+"</option>");
                }
			}
			//prepend 'select item' to the select option
			$(".selectedProductDefault").prepend("<option value=''>Select Product</option>");

            $(".selectedCustomerDefault").empty();
            for(let key in currentCustomers){
				//if the current key in the loop is in our 'selectedItemsArr' array
				if (currentCustomers[key] == itemCustomer) {
                    $(".selectedCustomerDefault").append("<option value='"+key+"' selected>"+currentCustomers[key]+"</option>");
                } else {
                    $(".selectedCustomerDefault").append("<option value='"+key+"'>"+currentCustomers[key]+"</option>");
                }
			}
			//prepend 'select item' to the select option
			$(".selectedCustomerDefault").prepend("<option value=''>Select Customer</option>");

            $(".selectedPlatformDefault").empty();
            for(let key in currentPlatforms){
				//if the current key in the loop is in our 'selectedItemsArr' array
                if (currentPlatforms[key] == itemPlatform) {
                    $(".selectedPlatformDefault").append("<option value='"+key+"' selected>"+currentPlatforms[key]+"</option>");
                } else {
                    $(".selectedPlatformDefault").append("<option value='"+key+"'>"+currentPlatforms[key]+"</option>");
                }
			}
			//prepend 'select item' to the select option
			$(".selectedPlatformDefault").prepend("<option value=''>Select Platform</option>");

            $(".selectedPriorityDefault").empty();
            for(let key in currentPriorities){
				//if the current key in the loop is in our 'selectedItemsArr' array
                if (currentPriorities[key] == itemPriority) {
                    $(".selectedPriorityDefault").append("<option value='"+key+"' selected>"+currentPriorities[key]+"</option>");
                } else {
                    $(".selectedPriorityDefault").append("<option value='"+key+"'>"+currentPriorities[key]+"</option>");
                }
			}
			//prepend 'select item' to the select option
			$(".selectedPriorityDefault").prepend("<option value=''>Select Priority</option>");

            $(".selectedStatusDefault").empty();
            for(let key in currentStatuses){
                if (currentStatuses[key] == itemStatus) {
                    $(".selectedStatusDefault").append("<option value='"+key+"' selected>"+currentStatuses[key]+"</option>");
                } else {
                    $(".selectedStatusDefault").append("<option value='"+key+"'>"+currentStatuses[key]+"</option>");
                }
			}
			//prepend 'select item' to the select option
			$(".selectedStatusDefault").prepend("<option value=''>Select Status</option>");

            $(".selectedCompetitorDefault").empty();
            for(let key in currentCompetitors){
				//if the current key in the loop is in our 'selectedItemsArr' array
                if (currentCompetitors[key] == itemCompetitor) {
                    $(".selectedCompetitorDefault").append("<option value='"+key+"' selected>"+currentCompetitors[key]+"</option>");
                } else {
                    $(".selectedCompetitorDefault").append("<option value='"+key+"'>"+currentCompetitors[key]+"</option>");
                }
			}
            if (itemCompetitor == "") {
                itemCompetitor = "selected";
            }
			//prepend 'select item' to the select option
			$(".selectedCompetitorDefault").prepend("<option value='' "+itemCompetitor+">Select Competitor</option>");

            $(".selectedProjectDefault").empty();
            for(let key in currentProjects){
                if (currentProjects[key] == itemProject) {
                    //if the current key in the loop is in our 'selectedItemsArr' array
                    $(".selectedProjectDefault").append("<option value='"+key+"' selected>"+currentProjects[key]+"</option>");
                } else {
                    //if the current key in the loop is in our 'selectedItemsArr' array
                    $(".selectedProjectDefault").append("<option value='"+key+"'>"+currentProjects[key]+"</option>");
                }

            }
            if (itemProject == "") {
                itemProject = "selected";
            }
            //prepend 'select item' to the select option
            $(".selectedProjectDefault").prepend("<option value='' "+ itemProject+">Select Project</option>");

			resolve(); //selectedGroupsArr, selectedPrioritysArr
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
				//add select2 to the 'select input'
			    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
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
        var itemProduct = $("#itemProductEdit").val();
        var itemCustomer = $("#itemCustomerEdit").val();
        var itemPlatform = $("#itemPlatformEdit").val();
        var itemPriority = $("#itemPriorityEdit").val();
        var itemStatus = $("#itemStatusEdit").val();
        var itemCompetitor = $("#itemCompetitorEdit").val();
        var itemProject = $("#itemProjectEdit").val();
        var itemDesc = $("#itemDescEdit").val();
        var itemId = $("#itemIdEdit").val();

        if(!itemProduct || !itemCustomer || !itemPlatform || !itemPriority || !itemStatus){
            !itemProduct ? $("#itemProductEditErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerEditErr").text("required") : "";
            !itemPlatform ? $("#itemPlatformEditErr").text("required") : "";
            !itemPriority ? $("#itemPriorityEditErr").text("required") : "";
            !itemStatus ? $("#itemStatusEditErr").text("required") : "";

            $("#newTransErrMsg").text("One or more required fields are empty");

            return;
        }

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"productTransactions/edit",
            data: {itemProduct:itemProduct, itemCustomer:itemCustomer, itemPlatform:itemPlatform,
                itemPriority:itemPriority, itemStatus:itemStatus, itemCompetitor:itemCompetitor,
                itemProjectName:itemProject, itemDesc:itemDesc, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#editItemFMsg").css('color', 'green').html("Product transaction successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            }

            else{
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemProductEditErr").html(returnedData.itemProduct);
                $("#itemCustomerEditErr").html(returnedData.itemCustomer);
                $("#itemPlatformEditErr").html(returnedData.itemPlatform);
                $("#itemPriorityEditErr").html(returnedData.itemPriority);
                $("#itemStatusEditErr").html(returnedData.itemStatus);

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

            $(".selectedProjectDefault").empty();
            for(let key in currentProjects){
                //if the current key in the loop is in our 'selectedItemsArr' array
                $(".selectedProjectDefault").append("<option value='"+key+"'>"+currentProjects[key]+"</option>");
            }
            //prepend 'select item' to the select option
            $(".selectedProjectDefault").prepend("<option value='' selected>Select Project</option>");

			resolve(); //selectedGroupsArr, selectedPrioritysArr

		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
				//add select2 to the 'select input'
			    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
                $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
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


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemMilestone').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });

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
