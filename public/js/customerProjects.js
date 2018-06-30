
'use strict';

$(document).ready(function(){
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status
    var LastSearchKey = '';
    //initial select
    selected2_tag_addnew_initial(".selectedFilterDefault", currentCustomers, "Select Customer");
    $('.selectedFilterDefault').select2({dropdownAutoWidth : true});

    //load all items once the page is ready
    lilt();

    /**
     * Toggle the form to add a new item
     */
    $("#createItem").click(function(){
        //$("#itemsListDiv").toggleClass("col-sm-8", "col-sm-12");
        $("#createNewItemDiv").toggleClass('hidden');
        $("#itemName").focus();

        return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
            selected2_tag_addnew_initial(".selectedCustomerDefault", currentCustomers, "Select Customer");
            selected2_tag_addnew_initial(".selectedSOCCompanyDefault", currentSOCCompanies, "Select SOC Company");
            selected2_tag_addnew_initial(".selectedSOCNameDefault", currentSOCNames, "Select SOC");
            selected2_tag_addnew_initial(".selectedDSPDefault", currentDSPs, "Select DSP");
            selected2_tag_addnew_initial(".selectedGPUDefault", currentGPUs, "Select GPU");
            selected2_tag_addnew_initial(".selectedRAMDefault", currentRAMs, "Select RAM");
            selected2_tag_addnew_initial(".selectedCamera0Default", currentCamera0Types, "Select Camera Type");
            selected2_tag_addnew_initial(".selectedCamera1Default", currentCamera1Types, "Select Camera Type");
            selected2_tag_addnew_initial(".selectedCamera0ResDefault", currentCamera0Res, "Select Resolution");
            selected2_tag_addnew_initial(".selectedCamera1ResDefault", currentCamera1Res, "Select Resolution");
            selected2_tag_addopt_initial(".selectedCamVenderDefault", currentCamVenders);
            selected2_tag_addopt_initial(".selectedCamAssemblyDefault", currentCamAssemblies);

			resolve(); //selectedGroupsArr, selectedPrioritysArr
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
			//add select2 to the 'select input'
			$('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedSOCCompanyDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedSOCNameDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedDSPDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedGPUDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedRAMDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera0Default').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera0ResDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera1Default').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera1ResDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamVenderDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCamAssemblyDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });


    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemStartDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });
    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemMPDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });
    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemShipDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
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

        changeInnerHTML(['itemNameErr', 'addCustErrMsg'], "");

        var itemName = $("#itemName").val();
        var itemCustomer = $("#itemCustomer").val();
        var itemSOCCompanyVal = $("#itemSOCCompany").val();
        var itemSOCNameVal = $("#itemSOCName").val();
        var itemGPUVal = $("#itemGPU").val();
        var itemDSPVal = $("#itemDSP").val();
        var itemRAMVal = $("#itemRAM").val();
        var itemCamera0Val = $("#itemCamera0").val();
        var itemCamera1Val = $("#itemCamera1").val();
        var itemCamera0ResVal = $("#itemCamera0Res").val();
        var itemCamera1ResVal = $("#itemCamera1Res").val();

        var itemStartDate = $("#itemStartDate").val();
        var itemMPDate = $("#itemMPDate").val();
        var itemShipDate = $("#itemShipDate").val();
        var itemDesc = $("#itemDesc").val();

        var itemCamModules = $("#itemCamModule").select2("val");
        var itemCamAssemblies = $("#itemCamAssembly").select2("val");
        //console.log(itemCamModules);
        //console.log(itemCamAssemblies);

        var itemSOCCompany = $("#itemSOCCompany").find("option:selected").text();
        var itemSOCName = $("#itemSOCName").find("option:selected").text();
        var itemGPU = itemGPUVal != '' ? $("#itemGPU").find("option:selected").text() : '';
        var itemDSP = itemDSPVal != '' ? $("#itemDSP").find("option:selected").text() : '';
        var itemRAM = itemRAMVal != '' ? $("#itemRAM").find("option:selected").text() : '';
        var itemCamera0 = itemCamera0Val != '' ? $("#itemCamera0").find("option:selected").text() : '';
        var itemCamera1 = itemCamera1Val != '' ? $("#itemCamera1").find("option:selected").text() : '';
        var itemCamera0Res = itemCamera0ResVal != '' ? $("#itemCamera0Res").find("option:selected").text() : '';
        var itemCamera1Res = itemCamera1ResVal != '' ? $("#itemCamera1Res").find("option:selected").text() : '';

        if(!itemName || !itemSOCCompanyVal || !itemSOCNameVal){
            !itemName ? $("#itemNameErr").text("required") : "";
            !itemCustomer ? $("#itemCustomerErr").text("required") : "";
            !itemSOCCompanyVal ? $("#itemSOCCompanyErr").text("required") : "";
            !itemSOCNameVal ? $("#itemSOCNameErr").text("required") : "";

            $("#addCustErrMsg").text("One or more required fields are empty");

            return;
        }

        displayFlashMsg("Adding type '"+itemName+"'", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"customerProjects/add",
            data:{itemName:itemName, itemCustomer:itemCustomer, itemSOCCompany:itemSOCCompany, itemCamAssemblies:itemCamAssemblies,
                itemSOCName:itemSOCName, itemGPU:itemGPU, itemDSP:itemDSP, itemRAM:itemRAM, itemCamModules:itemCamModules,
                itemCamera0:itemCamera0, itemCamera1:itemCamera1, itemDesc:itemDesc, itemCamera0Res:itemCamera0Res,
                itemCamera1Res:itemCamera1Res, itemStartDate:itemStartDate, itemMPDate:itemMPDate, itemShipDate:itemShipDate},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("addNewItemForm").reset();
                    $("#itemCustomer").val(itemCustomer);

                    selected2_tag_addnew_optional(".selectedSOCCompanyDefault", "#itemSOCCompany", currentSOCCompanies, itemSOCCompany, itemSOCCompanyVal, "Select SOC Company");
                    selected2_tag_addnew_optional(".selectedSOCNameDefault", "#itemSOCName", currentSOCNames, itemSOCName, itemSOCNameVal, "Select SOC");
                    selected2_tag_addnew_optional(".selectedDSPDefault", "#itemDSP", currentDSPs, itemDSP, itemDSPVal,"Select DSP");
                    selected2_tag_addnew_optional(".selectedGPUDefault", "#itemGPU", currentGPUs, itemGPU, itemGPUVal,"Select GPU");
                    selected2_tag_addnew_optional(".selectedRAMDefault", "#itemRAM", currentRAMs, itemRAM, itemRAMVal,"Select RAM");
                    selected2_tag_addnew_optional(".selectedCamera0Default", "#itemCamera0", currentCamera0Types, itemCamera0, itemCamera0Val, "Select Camera Type");
                    selected2_tag_addnew_optional(".selectedCamera1Default", "#itemCamera1", currentCamera1Types, itemCamera1, itemCamera1Val, "Select Camera Type");
                    selected2_tag_addnew_optional(".selectedCamera0ResDefault", "#itemCamera0Res", currentCamera0Res, itemCamera0Res, itemCamera0ResVal, "Select Resolution");
                    selected2_tag_addnew_optional(".selectedCamera1ResDefault", "#itemCamera1Res", currentCamera1Res, itemCamera1Res, itemCamera1ResVal, "Select Resolution");

                    //refresh the items list table
                    lilt();

                    //return focus to item code input to allow adding item with barcode scanner
                    $("#itemName").focus();
                } else {
                    hideFlashMsg();

                    //display all errors
                    $("#itemNameErr").text(returnedData.itemName);
                    $("#itemCustomerErr").text(returnedData.itemCustomer);
                    $("#itemSOCCompanyErr").text(returnedData.itemSOCCompany);
                    $("#itemSOCNameErr").text(returnedData.itemSOCName);
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
    $("#itemsListPerPage, #itemsListSortBy, #itemsListFilterBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
    });

    $("#itemSearch").keyup(function(){
        var value = $(this).val();
        //console.log("The Priority NAME value: %s", value);
        if(value){
            LastSearchKey = value;
            $.ajax({
                url: appRoot+"search/customerProjectSearch",
                type: "get",
                data: {v:value},
                success: function(returnedData){
                    $("#itemsListTable").html(returnedData.itemsListTable);
                }
            });
        } else {
            if (LastSearchKey != value) {
                LastSearchKey = value;
                //reload the table if all text in search box has been cleared
                displayFlashMsg("Loading page...", spinnerClass, "", "");
                lilt();
            }
        }
    });

    //triggers when an item's "edit" icon is clicked
    $("#itemsListTable").on('click', ".editItem", function(e){
        e.preventDefault();
        //get item info
        var itemId = $(this).attr('id').split("-")[1];
        var itemDesc = $("#itemDesc-"+itemId).attr('title');
        var itemName = $("#itemName-"+itemId).html();
        var itemCustomer = $("#itemCustomer-"+itemId).html();
        var itemSOCCompany = $("#itemSOCCompany-"+itemId).html();
        var itemSOCName = $("#itemSOCName-"+itemId).html();
        var itemGPU = $("#itemGPU-"+itemId).html();
        var itemDSP = $("#itemDSP-"+itemId).html();
        var itemRAM = $("#itemRAM-"+itemId).html();
        var itemCamera0 = $("#itemCamera0-"+itemId).html();
        var itemCamera1 = $("#itemCamera1-"+itemId).html();
        var itemCamera0Res = $("#itemCamera0Res-"+itemId).html();
        var itemCamera1Res = $("#itemCamera1Res-"+itemId).html();
        var itemStartDate = $("#itemStartDate-"+itemId).html();
        var itemMPDate = $("#itemMPDate-"+itemId).html();
        var itemShipDate = $("#itemShipDate-"+itemId).html();

        var itemCamModule = $("#itemCamModule-"+itemId).html();
        var itemCamAssembly = $("#itemCamAssembly-"+itemId).html();

        //prefill form with info
        $("#itemIdEdit").val(itemId);
        $("#itemNameEdit").val(itemName);
        $("#itemDescEdit").val(itemDesc);

        $("#itemStartDateEdit").val(itemStartDate);
        $("#itemMPDateEdit").val(itemMPDate);
        $("#itemShipDateEdit").val(itemShipDate);

        //remove all error messages that might exist
        $("#editItemFMsg").html("");
        $("#itemNameEditErr").html("");

        //launch modal
        $("#editItemModal").modal('show');

        return new Promise((resolve, reject)=>{
			//if an item has been selected (i.e. added to the current transaction), do not add it to the list. This way, an item will appear just once.
			//We start by forming an array of all selected items, then skip that item in the loop appending items to select dropdown
            selected2_tag_update_optional(".selectedSOCNameDefault", currentSOCNames, itemSOCName, "Select SOC");
            selected2_tag_update_optional(".selectedSOCCompanyDefault", currentSOCCompanies, itemSOCCompany, "Select SOC Company");
            selected2_tag_update_optional(".selectedCustomerDefault", currentCustomers, itemCustomer, "Select Customer");
            selected2_tag_update_optional(".selectedCamera1ResDefault", currentCamera1Res, itemCamera1Res, "Select Resolution");
            selected2_tag_update_optional(".selectedCamera0ResDefault", currentCamera0Res, itemCamera0Res, "Select Resolution");
            selected2_tag_update_optional(".selectedCamera1Default", currentCamera1Types, itemCamera1, "Select CameraType");
            selected2_tag_update_optional(".selectedCamera0Default", currentCamera0Types, itemCamera0, "Select CameraType");
            selected2_tag_update_optional(".selectedRAMDefault", currentRAMs, itemRAM, "Select RAM");
            selected2_tag_update_optional(".selectedGPUDefault", currentGPUs, itemGPU, "Select GPU");
            selected2_tag_update_optional(".selectedDSPDefault", currentDSPs, itemDSP, "Select DSP");
            selected2_tag_addopt_update(".selectedCamVenderDefault", currentCamVenders, itemCamModule);
            selected2_tag_addopt_update(".selectedCamAssemblyDefault", currentCamAssemblies, itemCamAssembly);
			resolve(); //selectedGroupsArr, selectedPrioritysArr
		}).then(()=>{ //selectedGroupsArray, selectedPrioritysArray
			//add select2 to the 'select input'
			$('.selectedCustomerDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedSOCCompanyDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedSOCNameDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedDSPDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedGPUDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedRAMDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera0Default').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera0ResDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera1Default').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamera1ResDefault').select2({dropdownAutoWidth : true, width : "100%", tags : true});
            $('.selectedCamVenderDefault').select2({dropdownAutoWidth : true, width : "100%"});
            $('.selectedCamAssemblyDefault').select2({dropdownAutoWidth : true, width : "100%"});
		}).catch(()=>{
			console.log('outer promise err');
		});

        return false;
    });

    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemStartDateEdit').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });
    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemMPDateEdit').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });
    //INITIALISE datepicker on the "From date" and "To date" fields
    $('#itemShipDateEdit').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoSize: true
    });

    //triggers when an Save icon is clicked
    $("#editItemSubmit").click(function(){
        var itemId = $("#itemIdEdit").val();
        var itemName = $("#itemNameEdit").val();
        var itemCustomer = $("#itemCustomerEdit").val();
        var itemSOCCompanyVal = $("#itemSOCCompanyEdit").val();
        var itemSOCNameVal = $("#itemSOCNameEdit").val();
        var itemGPUVal = $("#itemGPUEdit").val();
        var itemDSPVal = $("#itemDSPEdit").val();
        var itemRAMVal = $("#itemRAMEdit").val();
        var itemCamera0Val = $("#itemCamera0Edit").val();
        var itemCamera1Val = $("#itemCamera1Edit").val();
        var itemCamera0ResVal = $("#itemCamera0ResEdit").val();
        var itemCamera1ResVal = $("#itemCamera1ResEdit").val();

        var itemStartDate = $("#itemStartDateEdit").val();
        var itemMPDate = $("#itemMPDateEdit").val();
        var itemShipDate = $("#itemShipDateEdit").val();
        var itemDesc = $("#itemDescEdit").val();

        var itemCamModules = $("#itemCamModuleEdit").select2("val");
        var itemCamAssemblies = $("#itemCamAssemblyEdit").select2("val");
        console.log(itemCamModules);
        console.log(itemCamAssemblies);

        var itemSOCCompany = $("#itemSOCCompanyEdit").find("option:selected").text();
        var itemSOCName = $("#itemSOCNameEdit").find("option:selected").text();
        var itemGPU = itemGPUVal != '' ? $("#itemGPUEdit").find("option:selected").text() : '';
        var itemDSP = itemDSPVal != '' ? $("#itemDSPEdit").find("option:selected").text() : '';
        var itemRAM = itemRAMVal != '' ? $("#itemRAMEdit").find("option:selected").text() : '';
        var itemCamera0 = itemCamera0Val != '' ? $("#itemCamera0Edit").find("option:selected").text() : '';
        var itemCamera1 = itemCamera1Val != '' ? $("#itemCamera1Edit").find("option:selected").text() : '';
        var itemCamera0Res = itemCamera0ResVal != '' ? $("#itemCamera0ResEdit").find("option:selected").text() : '';
        var itemCamera1Res = itemCamera1ResVal != '' ? $("#itemCamera1ResEdit").find("option:selected").text() : '';

        if(!itemName || !itemId || !itemCustomer || !itemSOCCompanyVal || !itemSOCNameVal){
            !itemName ? $("#itemNameEditErr").html("Type name cannot be empty") : "";
            !itemCustomer ? $("#itemCustomerEditErr").html("required") : "";
            !itemSOCCompanyVal ? $("#itemSOCCompanyEditErr").html("required") : "";
            !itemSOCNameVal ? $("#itemSOCNameEditErr").html("required") : "";
            !itemId ? $("#editItemFMsg").html("Unknown Priority") : "";
            return;
        }

        $("#editItemFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> Processing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"customerProjects/edit",
            data: {itemName:itemName, itemCustomer:itemCustomer, itemSOCCompany:itemSOCCompany, itemCamModules:itemCamModules, itemCamAssemblies:itemCamAssemblies,
                itemSOCName:itemSOCName, itemGPU:itemGPU, itemDSP:itemDSP, itemRAM:itemRAM,
                itemCamera0:itemCamera0, itemCamera1:itemCamera1, itemDesc:itemDesc, itemCamera0Res:itemCamera0Res,
                itemCamera1Res:itemCamera1Res, itemStartDate:itemStartDate, itemMPDate:itemMPDate,
                itemShipDate:itemShipDate, _iId:itemId}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                selected2_tag_update_array(currentSOCCompanies, itemSOCCompany, itemSOCCompanyVal);
                selected2_tag_update_array(currentSOCNames, itemSOCName, itemSOCNameVal);
                selected2_tag_update_array(currentDSPs, itemDSP, itemDSPVal);
                selected2_tag_update_array(currentGPUs, itemGPU, itemGPUVal);
                selected2_tag_update_array(currentRAMs, itemRAM, itemRAMVal);
                selected2_tag_update_array(currentCamera0Types, itemCamera0, itemCamera0Val);
                selected2_tag_update_array(currentCamera1Types, itemCamera1, itemCamera1Val);
                selected2_tag_update_array(currentCamera0Res, itemCamera0Res, itemCamera0ResVal);
                selected2_tag_update_array(currentCamera1Res, itemCamera1Res, itemCamera1ResVal);

                $("#editItemFMsg").css('color', 'green').html("successfully updated");

                setTimeout(function(){
                    $("#editItemModal").modal('hide');
                }, 1000);

                lilt();
            }

            else{
                $("#editItemFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#itemNameEditErr").html(returnedData.itemName);
            }
        }).fail(function(){
            $("#editItemFMsg").css('color', 'red').html("Unable to process your request at this time. Please check your internet connection and try again");
        });
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
                    url: appRoot+"customerProjects/delete",
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
    var filter = $("#itemsListFilterBy").val();
    var limit = $("#itemsListPerPage").val();

    $.ajax({
        type:'get',
        url: url ? url : appRoot+"customerProjects/lilt/",
        data: {orderBy:orderBy, orderFormat:orderFormat, filter:filter, limit:limit},

        success: function(returnedData){
            hideFlashMsg();
            $("#itemsListTable").html(returnedData.itemsListTable);
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
