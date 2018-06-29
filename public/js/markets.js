'use strict';

var currentProjects = [];

$(document).ready(function(){
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //initial select
    selected2_tag_default_initial(".selectedFilterDefault", currentProducts, "Select Product", "");
    $('.selectedFilterDefault').select2({dropdownAutoWidth : true});

    //load all items once the page is ready
    lilt();

    /**
     * 选择客户后执行
     * @return {[type]} [description]
     */
    $("#itemCustomer, #itemCustomerEdit").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        update_customer_project($(this).val());
    });

    /**
     * 选择过滤排序后执行
     * @return {[type]} [description]
     */
    $("#itemsListPerPage, #itemsListSortBy, #itemsActiveBy, #itemsListFilterBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
    });

    /**
     * 根据选择的交易状态，Signed Replaced NoNeed 触发选择供应商
     * @return {[type]} [description]
     */
    $("#itemStatus").change(function(){
        console.debug($(this).val());
        if ($(this).val() == 5 || $(this).val() == 6 || $(this).val() == 10) {
            $('#newVender').toggleClass('collapse', false);
        } else {
            $('#newVender').toggleClass('collapse', true);
            $(".selectedVenderDefault").val("").trigger('change');
        }
    });

    /**
     * 创建一个交易
     * @return {[type]} [description]
     */
    $("#createItem").click(function(){
        $("#newTransDiv").toggleClass('collapse');

        if($("#newTransDiv").hasClass('collapse')){
            $(this).html("<i class='fa fa-plus'></i> New Transaction");
        } else {
            $(this).html("<i class='fa fa-minus'></i> New Transaction");
            //remove error messages
            $("#itemCodeNotFoundMsg").html("");
        }

        return new Promise((resolve, reject)=>{
            selected2_tag_default_initial(".selectedProductDefault", currentProducts, "Select Prodcut", "");
            selected2_tag_default_initial(".selectedCustomerDefault", currentCustomers, "Select Customer", "");
            selected2_tag_default_initial(".selectedPlatformDefault", currentPlatforms, "Select Platforms", "");
            selected2_tag_default_initial(".selectedStatusDefault", currentStatuses, "Select Status", "");
            selected2_tag_default_initial(".selectedCompetitorDefault", currentCompetitors, "", "");
            selected2_tag_default_initial(".selectedVenderDefault", currentVenders, "Select Vender", "");
			resolve();

		}).then(()=>{
			//add select2 to the 'select input'
		    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedVenderDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    //handles the submission of a new sales order
    $("#addNewItem").click(function(){
        //ensure all fields are properly filled
        changeInnerHTML(['itemProductErr', 'itemCustomerErr', 'itemPlatformErr', 'itemStatusErr', 'newTransErrMsg'], "");

        var itemProduct = $("#itemProduct").val();
        var itemCustomer = $("#itemCustomer").val();
        var itemPlatform = $("#itemPlatform").val();
        var itemStatus = $("#itemStatus").val();
        var itemCompetitor = $("#itemCompetitor").val();
        var itemVender = $("#itemVender").val();
        var itemProjectName = $("#itemProject").val();
        var itemMilestone = $("#itemMilestone").val();
        var itemDesc = $("#itemDesc").val();

        if(!itemProduct || !itemCustomer || !itemPlatform || !itemStatus){
            !itemProduct ? $("#itemProductErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerErr").text("required") : "";
            !itemPlatform ? $("#itemPlatformErr").text("required") : "";
            !itemStatus ? $("#itemStatusErr").text("required") : "";

            $("#newTransErrMsg").text("One or more required fields are empty");
            return;
        }

        displayFlashMsg("Adding transaction", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"Markets/add",
            data:{itemProduct:itemProduct, itemCustomer:itemCustomer, itemPlatform:itemPlatform, itemVender:itemVender,
                itemStatus:itemStatus, itemCompetitor:itemCompetitor, itemProjectName:itemProjectName, itemMilestone:itemMilestone, itemDesc:itemDesc},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("salesTransForm").reset();
                    $("#itemProduct").val(itemProduct);
                    $("#itemCustomer").val(itemCustomer);
                    $("#itemPlatform").val(itemPlatform);
                    $("#itemStatus").val(itemStatus);
                    $("#itemVender").val(itemVender);
                    $("#itemCompetitor").val(itemCompetitor);
                    $("#itemProject").val(itemProjectName);
                    //refresh the items list table
                    lilt();
                    //return focus to item code input to allow adding item with barcode scanner
                    $("#itemProduct").focus();
                } else {
                    hideFlashMsg();
                    //display all errors
                    $("#itemProductErr").text(returnedData.itemProduct);
                    $("#itemCustomerErr").text(returnedData.itemCustomer);
                    $("#itemPlatformErr").text(returnedData.itemPlatform);
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

    /**
     * [description]
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#itemsListTable").on('click', ".editItem", function(e){
        e.preventDefault();

        //get item info
        var itemId = $(this).attr('id').split("-")[1];
        var itemProduct = $("#itemProduct-"+itemId).html();
        var itemCustomer = $("#itemCustomer-"+itemId).html();
        var itemPlatform = $("#itemPlatform-"+itemId).html();
        var itemStatus = $("#itemStatus-"+itemId).html();
        var itemCompetitor = $("#itemCompetitor-"+itemId).html();
        var itemVender = $("#itemVender-"+itemId).html();
        var itemProject = $("#itemProject-"+itemId).html();
        var itemMilestone = $("#itemMilestone-"+itemId).html();
        var itemDesc = $("#itemDesc-"+itemId).attr('title');

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemDescEdit").val(itemDesc);
        $("#itemMilestoneEdit").val(itemMilestone);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemProductEditErr").html("");
        $("#itemCustomerEditErr").html("");
        $("#itemPlatformEditErr").html("");
        $("#itemStatusEditErr").html("");
        $("#itemCompetitorEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        var CustomerID = 0;
        for(let key in currentCustomers){
            if (currentCustomers[key] == itemCustomer) {
                CustomerID = key;
            }
        }
        update_customer_project(CustomerID, itemProject);

        return new Promise((resolve, reject)=>{
            selected2_tag_update_optional(".selectedProductDefault", currentProducts, itemProduct, "Select Product");
            selected2_tag_update_optional(".selectedCustomerDefault", currentCustomers, itemCustomer, "Select Customer");
            selected2_tag_update_optional(".selectedPlatformDefault", currentPlatforms, itemPlatform, "Select Platform");
            selected2_tag_update_optional(".selectedStatusDefault", currentStatuses, itemStatus, "Select Status");
            selected2_tag_addopt_update(".selectedCompetitorDefault", currentCompetitors, itemCompetitor);
            selected2_tag_update_optional(".selectedVenderDefault", currentVenders, itemVender, "Select Vender");
			resolve(); //selectedGroupsArr, selectedPrioritysArr
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
			//add select2 to the 'select input'
			$('.selectedVenderDefault').select2({dropdownAutoWidth : true, width : "100%"});
		    $('.selectedCompetitorDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedStatusDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPlatformDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    /**
     * [description]
     * @return {[type]} [description]
     */
    $("#editItemSubmit").click(function(){
        var itemProduct = $("#itemProductEdit").val();
        var itemCustomer = $("#itemCustomerEdit").val();
        var itemPlatform = $("#itemPlatformEdit").val();
        var itemStatus = $("#itemStatusEdit").val();
        var itemCompetitor = $("#itemCompetitorEdit").val();
        var itemVender = $("#itemVenderEdit").val();
        var itemProject = $("#itemProjectEdit").val();
        var itemDesc = $("#itemDescEdit").val();
        var itemMilestone = $("#itemMilestoneEdit").val();
        var itemId = $("#itemIdEdit").val();

        if(!itemProduct || !itemCustomer || !itemPlatform || !itemStatus){
            !itemProduct ? $("#itemProductEditErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerEditErr").text("required") : "";
            !itemPlatform ? $("#itemPlatformEditErr").text("required") : "";
            !itemStatus ? $("#itemStatusEditErr").text("required") : "";

            $("#newTransErrMsg").text("One or more required fields are empty");
            return;
        }

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"Markets/edit",
            data: {itemProduct:itemProduct, itemCustomer:itemCustomer, itemPlatform:itemPlatform, itemStatus:itemStatus, itemCompetitor:itemCompetitor,
                itemVender:itemVender, itemProject:itemProject, itemDesc:itemDesc, itemStatusDate:itemMilestone, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#editItemFMsg").css('color', 'green').html("Product transaction successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            } else {
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemProductEditErr").html(returnedData.itemProduct);
                $("#itemCustomerEditErr").html(returnedData.itemCustomer);
                $("#itemPlatformEditErr").html(returnedData.itemPlatform);
                $("#itemStatusEditErr").html(returnedData.itemStatus);

            }
        }).fail(function(){
            $("#editItemFMsg").css('color', 'red').html("Unable to process your request at this time. Please check your internet connection and try again");
        });
    });

    //When the toggle on/off button is clicked to change
    $("#itemsListTable").on('click', '.issueActive', function(){
        var ElemId = $(this).attr('id');
        var itemId = $(this).attr('id').split("-")[1];
        //show spinner
        $("#"+ElemId).html("<i class='"+spinnerClass+"'</i>");

        if(itemId){
            $.ajax({
                url: appRoot+"Markets/active",
                method: "POST",
                data: {itemId:itemId}
            }).done(function(returnedData){
                if(returnedData.status === 1){
                    //change the icon to "on" if it's "off" before the change and vice-versa
                    var newIconClass = returnedData._ns === 1 ? "fa fa-toggle-on pointer" : "fa fa-toggle-off pointer";
                    //change the icon
                    $("#itemActive-"+returnedData._aId).html("<i class='"+ newIconClass +"'></i>");
                } else {
                    console.log('err');
                }
            });
        }
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

    //TO HIDE THE TRANSACTION FORM FROM THE TRANSACTION FORM
    $("#hideTransForm").click(function(){
        $("#newTransDiv").toggleClass('collapse');

        //remove error messages
        $("#itemCodeNotFoundMsg").html("");

        //change main "new transaction" button back to default
        $("#showTransForm").html("<i class='fa fa-plus'></i> New Product Transaction");
    });

    $('#itemMilestone').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });

    $('#itemMilestoneEdit').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });

});

/**
 * "lilt" = "load Items List Table"
 * @param {type} url
 * @returns {undefined}
 */
function lilt(url){
    var orderBy = $("#itemsListSortBy").val().split("-")[0];
    var orderFormat = $("#itemsListSortBy").val().split("-")[1];
    var filter = $("#itemsListFilterBy").val();
    var active = $("#itemsActiveBy").is(':checked');
    var limit = $("#itemsListPerPage").val();

    //console.debug("filter=" + filter);

    $.ajax({
        type:'get',
        url: url ? url : appRoot+"Markets/lilt/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit, filter:filter, active:active},

        success: function(returnedData){
            hideFlashMsg();
            $("#itemsListTable").html(returnedData.itemsListTable);
        },

        error: function(){

        }
    });

    return false;
}

/**
 * 选择客户之后自动过滤该客户的项目列表
 * @param  {[type]} customer         [客户ID]
 * @param  {String} [itemProject=''] [description]
 * @return {[type]}                  [description]
 */
function update_customer_project(customer, itemProject='') {
    console.debug(customer + "," + itemProject);
    $.ajax({
        type:'get',
        url: appRoot+"markets/customer_project_filter/",
        data: {customer:customer},

        success: function(returnedData){
            hideFlashMsg();
            //get data
            currentProjects = [];
            var list = returnedData.allItems;
            for(let key in list){
                currentProjects[list[key].id] = list[key].Name;
            }
            //console.debug(currentProjects);
            //selected2_tag_addnew_initial(".selectedProjectDefault", currentProjects, itemProject, "Select Project");
            selected2_tag_update_optional(".selectedProjectDefault", currentProjects, itemProject, "Select Project");
            $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
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
