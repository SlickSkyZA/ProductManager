
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
        $("#itemsListDiv").toggleClass("col-sm-8", "col-sm-12");
        $("#createNewItemDiv").toggleClass('hidden');
        $("#itemName").focus();

        //$(".selectedItemDefault").addClass("selectedItem").val("");

        //loop through the currentItems variable to add the items to the select input
		return new Promise((resolve, reject)=>{
            selected2_tag_addnew_initial(".selectedGroupDefault", currentRegions, "Select Region");
            selected2_tag_addnew_initial(".selectedPriorityDefault", currentPriorities, "Select Priority");
            selected2_tag_addnew_initial(".selectedTypeDefault", currentTypes, "Select Type");
			resolve();
		}).then(()=>{
			//add select2 to the 'select input'
		    $('.selectedGroupDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedTypeDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedRSTypeDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    $(".cancelAddItem").click(function(){
        //reset and hide the form
        document.getElementById("addNewItemForm").reset();//reset the form
        $("#createNewItemDiv").addClass('hidden');//hide the form
        $("#itemsListDiv").attr('class', "col-sm-12");//make the table span the whole div
    });


    //handles the submission of adding new item
    $("#addNewItem").click(function(e){
        e.preventDefault();

        changeInnerHTML(['itemNameErr', 'itemTypeErr', 'itemRSTypeErr', 'itemPriorityErr', 'addCustErrMsg'], "");

        var itemName = $("#itemName").val();
        var itemRegion = $("#itemRegion").val();
        var itemType = $("#itemType").val();
        var itemRSType = $("#itemRSType").val();
        var itemPriority = $("#itemPriority").val();
        var itemDesc = $("#itemDesc").val();

        if(!itemName || !itemType || !itemRSType || !itemPriority){
            !itemName ? $("#itemNameErr").text("required") : "";
            !itemType ? $("#itemTypeErr").text("required") : "";
            !itemRSType ? $("#itemRSTypeErr").text("required") : "";
            !itemPriority ? $("#itemPriorityErr").text("required") : "";

            $("#addCustErrMsg").text("One or more required fields are empty");

            return;
        }

        displayFlashMsg("Adding Company '"+itemName+"'", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"companies/add",
            data:{itemName:itemName, itemRegion:itemRegion, itemType:itemType, itemRSType:itemRSType, itemPriority:itemPriority, itemDesc:itemDesc},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("addNewItemForm").reset();
                    $("#itemRegion").val(itemRegion);
                    $("#itemType").val(itemType);
                    $("#itemType").val(itemRSType);
                    $("#itemPriority").val(itemPriority);
                    //refresh the items list table
                    lilt();

                    //return focus to item code input to allow adding item with barcode scanner
                    $("#itemName").focus();
                }

                else{
                    hideFlashMsg();

                    //display all errors
                    $("#itemNameErr").text(returnedData.itemName);
                    $("#itemRegionErr").text(returnedData.itemRegion);
                    $("#itemTypeErr").text(returnedData.itemType);
                    $("#itemPriorityErr").text(returnedData.itemPriority);
                    $("#addCustErrMsg").text(returnedData.msg);
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

    //reload items list table when events occur
    $("#itemsListPerPage, #itemsListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
    });

    /**
     * search by name.
     * @return {[type]} [description]
     */
    $("#itemSearch").keyup(function(){
        var orderBy = $("#itemsListSortBy").val().split("-")[0];
        var orderFormat = $("#itemsListSortBy").val().split("-")[1];
        var value = $(this).val();
        //console.log("The Priority NAME value: %s", value);
        if(value){
            $.ajax({
                url: appRoot+"search/companySearch",
                type: "get",
                data: {orderBy:orderBy, orderFormat:orderFormat, v:value},
                success: function(returnedData){
                    $("#itemsListTable").html(returnedData.itemsListTable);
                }
            });
        }

        else{
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
        var itemName = $("#itemName-"+itemId).html();
        var itemType = $("#itemType-"+itemId).html();
        var itemRSType = $("#itemRSType-"+itemId).html();
        var itemRegion = $("#itemRegion-"+itemId).html();
        var itemPriority = $("#itemPriority-"+itemId).html();

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemNameEdit").val(itemName);
        $("#itemTypeEdit").val(itemType);
        $("#itemRSTypeEdit").val(itemRSType);
        $("#itemDescEdit").val(itemDesc);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemNameEditErr").html("");
        $("#itemTypeEditErr").html("");
        $("#itemRSTypeEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        //loop through the currentItems variable to add the items to the select input
		return new Promise((resolve, reject)=>{
            selected2_tag_update_optional(".selectedGroupDefault", currentRegions, itemRegion, "Select Region");
            selected2_tag_update_optional(".selectedTypeDefault", currentTypes, itemType, "Select Type");
            selected2_tag_update_optional(".selectedPriorityDefault", currentPriorities, itemPriority, "Select Priority");
			resolve();
		}).then(()=>{
			//add select2 to the 'select input'
		    $('.selectedGroupDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedPriorityDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedTypeDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedRSTypeDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    $("#editItemSubmit").click(function(){
        var itemName = $("#itemNameEdit").val();
        var itemRegion = $("#itemRegionEdit").val();
        var itemType = $("#itemTypeEdit").val();
        var itemRSType = $("#itemRSTypeEdit").val();
        var itemPriority = $("#itemPriorityEdit").val();
        var itemDesc = $("#itemDescriptionEdit").val();
        var itemId = $("#itemIdEdit").val();

        if(!itemName || !itemId || !itemType || !itemPriority){
            !itemName ? $("#itemNameEditErr").html("required") : "";
            !itemType ? $("#itemTypeEditErr").html("required") : "";
            !itemPriority ? $("#itemPriorityEditErr").html("required") : "";
            !itemId ? $("#editItemFMsg").html("Unknown id") : "";
            return;
        }

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"companies/edit",
            data: {itemName:itemName, itemRegionID:itemRegion, itemType:itemType, itemRSType:itemRSType, itemPriorityID:itemPriority, itemDesc:itemDesc, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#editItemFMsg").css('color', 'green').html("successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            }

            else{
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemNameEditErr").html(returnedData.itemName);
                $("#iitemTypeEditErr").html(returnedData.itemType);
                $("#iitemRSTypeEditErr").html(returnedData.itemRSType);
                $("#itemPriorityEditErr").html(returnedData.itemPriority);
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
                    url: appRoot+"customers/delete",
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
        url: url ? url : appRoot+"companies/lilt/",
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
 * "vittrhist" = "View item's transaction history"
 * @param {type} itemId
 * @returns {Boolean}
 */
function vittrhist(itemId){
    if(itemId){

    }

    return false;
}



function resetItemSN(){
    $(".itemSN").each(function(i){
        $(this).html(parseInt(i)+1);
    });
}
