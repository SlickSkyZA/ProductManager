
'use strict';

$(document).ready(function(){
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //load all items once the page is ready
    lilt();

    //WHEN USE BARCODE SCANNER IS CLICKED
    $("#useBarcodeScanner").click(function(e){
        e.preventDefault();

        $("#itemCode").focus();
    });

    /**
     * Toggle the form to add a new item
     */
    $("#createItem").click(function(){
        //$("#itemsListDiv").toggleClass("col-sm-8", "col-sm-12");
        $("#createNewItemDiv").toggleClass('hidden');
        $("#itemProduct").focus();

        //loop through the currentItems variable to add the items to the select input
		return new Promise((resolve, reject)=>{
            selected2_tag_addnew_initial(".selectedProductDefault", currentProducts, "Select Product");
            selected2_tag_addnew_initial(".selectedPriorityDefault", currentPriorities, "Select Priority");
            selected2_tag_addnew_initial(".selectedCustomerDefault", currentCustomers, "Select Customer");
            selected2_tag_addnew_initial(".selectedProjectDefault", currentCustomerProjects, "Select Project");
            selected2_tag_addnew_initial(".selectedIssueTypeDefault", currentIssueTypes, "Select Issue Type");
			resolve();
		}).then(()=>{
		    $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedIssueTypeDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemReportDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });

    /**
     * [triger cancel button]
     * @return
     */
    $(".cancelAddItem").click(function(){
        //reset and hide the form
        document.getElementById("addNewItemForm").reset();//reset the form
        $("#createNewItemDiv").addClass('hidden');//hide the form
        $("#itemsListDiv").attr('class', "col-sm-12");//make the table span the whole div
    });

    //handles the submission of adding new item
    $("#addNewItem").click(function(e){
        e.preventDefault();

        changeInnerHTML(['itemProductErr', 'itemCustomerErr', 'itemPriorityErr', 'itemProjectErr',
        'itemVersionErr', 'itemIssueTypeErr', 'itemReportDateErr','addCustErrMsg'], "");

        var itemProduct = $("#itemProduct").val();
        var itemCustomer = $("#itemCustomer").val();
        var itemPriority = $("#itemPriority").val();
        var itemProject = $("#itemProject").val();
        var itemVersion = $("#itemVersion").val();
        var itemReportDate = $("#itemReportDate").val();
        var itemDesc = $("#itemDesc").val();

        var itemIssueTypeValue = $("#itemIssueType").val();

        var itemIssueType = itemIssueTypeValue != '' ? $("#itemIssueType").find("option:selected").text() : '';

        if(!itemProduct || !itemCustomer || !itemPriority || !itemVersion || !itemIssueTypeValue || !itemReportDate){
            !itemProduct ? $("#itemProductErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerErr").text("required") : "";
            !itemPriority ? $("#itemPriorityErr").text("required") : "";
            !itemVersion ? $("#itemVersionErr").text("required") : "";
            !itemIssueTypeValue ? $("#itemIssueTypeErr").text("required") : "";
            !itemReportDate ? $("#itemReportDateErr").text("required") : "";

            $("#addCustErrMsg").text("One or more required fields are empty");

            return;
        }

        displayFlashMsg("Adding Issue in '"+itemProduct+"'", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"productIssues/add",
            data:{itemProduct:itemProduct, itemCustomer:itemCustomer, itemPriority:itemPriority, itemProject:itemProject,
                itemVersion:itemVersion, itemIssueType:itemIssueType, itemReportDate:itemReportDate, itemDesc:itemDesc},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("addNewItemForm").reset();

                    selected2_tag_addnew_optional(".selectedIssueTypeDefault", "#itemIssueType", currentIssueTypes, itemIssueType, itemIssueTypeValue, "Select Issue Type");
                    $("#itemProduct").val(itemProduct);
                    $("#itemPriority").val(itemPriority);
                    $("#itemCustomer").val(itemCustomer);
                    $("#itemProject").val(itemProject);
                    $("#itemVersion").val(itemVersion);
                    $("#itemReportDate").val(itemReportDate);

                    //refresh the items list table
                    lilt();

                    //return focus to item code input to allow adding item with barcode scanner
                    $("#productName").focus();
                }

                else{
                    hideFlashMsg();
                    //display all errors
                    $("#itemProductErr").text(returnedData.itemProduct);
                    $("#itemCustomerErr").text(returnedData.itemCustomer);
                    $("#itemPriorityErr").text(returnedData.itemPriority);
                    $("#itemVersionErr").text(returnedData.itemVersion);
                    $("#itemIssueTypeErr").text(returnedData.itemIssueType);
                    $("#itemReportDateErr").text(returnedData.itemReportDate);
                    $("#addCustErrMsg").text(returnedData.msg);
                }
            },

            error: function(){
                if(!navigator.onLine){
                    changeFlashMsgContent("You appear to be offline. Please reconnect to the internet and try again", "", "red", "");
                } else {
                    changeFlashMsgContent("Unable to process your request at this time. Pls try again later!", "", "red", "");
                }
            }
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
                url: appRoot+"productIssues/active",
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

    //reload items list table when events occur
    $("#itemsListPerPage, #itemsListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
    });

    /**
     * [search by product name]
     * @return {[type]} [description]
     */
    $("#itemSearch").keyup(function(){
        var orderBy = $("#itemsListSortBy").val().split("-")[0];
        var orderFormat = $("#itemsListSortBy").val().split("-")[1];
        var value = $(this).val();
        //console.log("The Priority NAME value: %s", value);
        if(value){
            $.ajax({
                url: appRoot+"search/productIssueSearch",
                type: "get",
                data: {orderBy:orderBy, orderFormat:orderFormat, v:value},
                success: function(returnedData){
                    $("#itemsListTable").html(returnedData.itemsListTable);
                }
            });
        } else {
            //reload the table if all text in search box has been cleared
            displayFlashMsg("Loading page...", spinnerClass, "", "");
            lilt();
        }
    });

    //triggers when an item's "edit" icon is clicked
    $("#itemsListTable").on('click', ".editItem", function(e){
        e.preventDefault();

        //get item info
        var itemId = $(this).attr('id').split("-")[1];
        var itemDesc = $("#itemDesc-"+itemId).attr('title');
        var itemProduct = $("#itemProduct-"+itemId).html();
        var itemCustomer = $("#itemCustomer-"+itemId).html();
        var itemPriority = $("#itemPriority-"+itemId).html();
        var itemVersion = $("#itemVersion-"+itemId).html();
        var itemReportDate = $("#itemReportDate-"+itemId).html();
        var itemProject = $("#itemProject-"+itemId).html();
        var itemIssueType = $("#itemIssueType-"+itemId).html();

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemVersionEdit").val(itemVersion);
        $("#itemDescEdit").val(itemDesc);
        $("#itemVersionEdit").val(itemVersion);
        $("#itemReportDateEdit").val(itemReportDate);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemProductEditErr").html("");
        $("#itemCustomerEditErr").html("");
        $("#itemPriorityEditErr").html("");
        $("#itemVersionEditErr").html("");
        $("#itemReportDateEditErr").html("");
        $("#itemProjectEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        //$(".selectedItemDefault").addClass("selectedItem").val("");

        //loop through the currentItems variable to add the items to the select input
		return new Promise((resolve, reject)=>{
            selected2_tag_update_optional(".selectedProductDefault", currentProducts, itemProduct, "Select Product");
            selected2_tag_update_optional(".selectedPriorityDefault", currentPriorities, itemPriority, "Select Priority");
            selected2_tag_update_optional(".selectedCustomerDefault", currentCustomers, itemCustomer, "Select Customer");
            selected2_tag_update_optional(".selectedProjectDefault", currentCustomerProjects, itemProject, "Select Project");
            selected2_tag_update_optional(".selectedIssueTypeDefault", currentIssueTypes, itemIssueType, "Select Issue Type");
            resolve();
		}).then(()=>{
			//add select2 to the 'select input'
            $('.selectedProductDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedProjectDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedIssueTypeDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
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
        changeInnerHTML(['itemProductEditErr', 'itemCustomerEditErr', 'itemPriorityEditErr', 'itemProjectEditErr',
        'itemVersionEditErr', 'itemIssueTypeEditErr', 'itemReportDateEditErr','addCustErrMsg'], "");
        var itemId = $("#itemIdEdit").val();
        var itemProduct = $("#itemProductEdit").val();
        var itemCustomer = $("#itemCustomerEdit").val();
        var itemPriority = $("#itemPriorityEdit").val();
        var itemProject = $("#itemProjectEdit").val();
        var itemVersion = $("#itemVersionEdit").val();
        var itemReportDate = $("#itemReportDateEdit").val();
        var itemDesc = $("#itemDescEdit").val();

        var itemIssueTypeValue = $("#itemIssueTypeEdit").val();

        var itemIssueType = itemIssueTypeValue != '' ? $("#itemIssueTypeEdit").find("option:selected").text() : '';

        if(!itemProduct || !itemCustomer || !itemPriority || !itemVersion || !itemIssueTypeValue || !itemReportDate){
            !itemProduct ? $("#itemProductEditErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerEditErr").text("required") : "";
            !itemPriority ? $("#itemPriorityEditErr").text("required") : "";
            !itemVersion ? $("#itemVersionEditErr").text("required") : "";
            !itemIssueTypeValue ? $("#itemIssueTypeEditErr").text("required") : "";
            !itemReportDate ? $("#itemReportDateEditErr").text("required") : "";

            $("#addCustErrMsg").text("One or more required fields are empty");

            return;
        }

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"productIssues/edit",
            data: {itemProduct:itemProduct, itemCustomer:itemCustomer, itemPriority:itemPriority, itemProject:itemProject,
                itemVersion:itemVersion, itemIssueType:itemIssueType, itemReportDate:itemReportDate, itemDesc:itemDesc, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                selected2_tag_update_array(currentIssueTypes, itemIssueType, itemIssueTypeValue);
                $("#editItemFMsg").css('color', 'green').html("Product issue successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            }

            else{
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemProductEditErr").html(returnedData.itemProduct);
                $("#itemCustomerEditErr").html(returnedData.itemCustomer);
                $("#itemPriorityEditErr").html(returnedData.itemPriority);
                $("#itemVersionEditErr").html(returnedData.itemVersion);
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

    //trigers the modal to update stock
    $("#itemsListTable").on('click', '.updateStock', function(){
        //get item info and fill the form with them
        var itemId = $(this).attr('id').split("-")[1];
        var itemName = $("#itemName-"+itemId).html();
        var itemCurQuantity = $("#itemQuantity-"+itemId).html();
        var itemCode = $("#itemCode-"+itemId).html();

        $("#stockUpdateItemId").val(itemId);
        $("#stockUpdateItemName").val(itemName);
        $("#stockUpdateItemCode").val(itemCode);
        $("#stockUpdateItemQInStock").val(itemCurQuantity);

        $("#updateStockModal").modal('show');
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //PREVENT AUTO-SUBMISSION BY THE BARCODE SCANNER
    $("#itemCode").keypress(function(e){
        if(e.which === 13){
            e.preventDefault();

            //change to next input by triggering the tab keyboard
            $("#itemName").focus();
        }
    });



    //TO DELETE AN ITEM (The item will be marked as "deleted" instead of removing it totally from the db)
    $("#itemsListTable").on('click', '.delItem', function(e){
        e.preventDefault();

        //get the item id
        var itemId = $(this).parents('tr').find('.curItemId').val();
        var itemRow = $(this).closest('tr');//to be used in removing the currently deleted row

        if(itemId){
            var confirm = window.confirm("Are you sure you want to delete item? This cannot be undone.");

            if(confirm){
                displayFlashMsg('Please wait...', spinnerClass, 'black');

                $.ajax({
                    url: appRoot+"productIssues/delete",
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
});



/**
 * "lilt" = "load Items List Table"
 * @param {type} url
 * @returns {undefined}
 */
function lilt(url){
    var orderBy = $("#itemsListSortBy").val().split("-")[0];
    var orderFormat = $("#itemsListSortBy").val().split("-")[1];
    var limit = $("#itemsListPerPage").val();


    $.ajax({
        type:'get',
        url: url ? url : appRoot+"productIssues/lilt/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},

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
 *
 */
function resetItemSN(){
    $(".itemSN").each(function(i){
        $(this).html(parseInt(i)+1);
    });
}

/**
* 新增页面所有中项
*
*/
function selected2_tag_addnew_initial(str, list, str2) {
    $(str).empty();
    for(let key in list){
        $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
    }
    $(str).prepend("<option value='' selected>"+str2+"</option>");
}

/**
* 编辑页面指定对应选中项
*
*/
function selected2_tag_update_optional(str, list, item, str2) {
    $(str).empty();
    for(let key in list){
    	if (list[key] == item) {
    		$(str).append("<option value='"+key+"' selected>"+list[key]+"</option>");
        } else {
        	$(str).append("<option value='"+key+"'>"+list[key]+"</option>");
        }
    }
    if (item == '') {
        $(str).prepend("<option value='' selected>"+ str2 +"</option>");
    } else {
        $(str).prepend("<option value=''>"+ str2 +"</option>");
    }
}

/**
* 新增页面新加项目添加至列表
*
*/
function selected2_tag_addnew_optional(str, strVal, list, item, itemVal, str2) {
    if(!inArray(item, list)){
        if (itemVal != '') {
            list.push(item);
            $(str).empty();
            for(let key in list){
                if (list[key] == item) {
                    $(str).append("<option value='"+key+"' selected>"+list[key]+"</option>");
                } else {
                    $(str).append("<option value='"+key+"'>"+list[key]+"</option>");
                }
            }
            $(str).prepend("<option value=''>"+str2+"</option>");
        }
    } else {
        $(strVal).val(itemVal);
    }
}

/**
* 更新tag数据到list
*
*/
function selected2_tag_update_array(list, item, itemVal) {
    if(!inArray(item, list)){
        if (itemVal != '') {
            list.push(item);
        }
    }
}
