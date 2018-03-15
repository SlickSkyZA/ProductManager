
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


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

            $(".selectedCustomerDefault").empty();
            for(let key in currentCustomers){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedCustomerDefault").append("<option value='"+key+"'>"+currentCustomers[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedCustomerDefault").prepend("<option value='' selected>Select Customer</option>");

            $(".selectedSOCCompanyDefault").empty();
            for(let key in currentSOCCompanies){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedSOCCompanyDefault").append("<option value='"+key+"'>"+currentSOCCompanies[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedSOCCompanyDefault").prepend("<option value='' selected>Select SOC Company</option>");

            $(".selectedSOCNameDefault").empty();
            for(let key in currentSOCNames){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedSOCNameDefault").append("<option value='"+key+"'>"+currentSOCNames[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedSOCNameDefault").prepend("<option value='' selected>Select SOC</option>");

            $(".selectedDSPDefault").empty();
            for(let key in currentDSPs){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedDSPDefault").append("<option value='"+key+"'>"+currentDSPs[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedDSPDefault").prepend("<option value='' selected>Select DSP</option>");

            $(".selectedGPUDefault").empty();
            for(let key in currentGPUs){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedGPUDefault").append("<option value='"+key+"'>"+currentGPUs[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedGPUDefault").prepend("<option value='' selected>Select GPU</option>");

            $(".selectedRAMDefault").empty();
            for(let key in currentRAMs){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedRAMDefault").append("<option value='"+key+"'>"+currentRAMs[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedRAMDefault").prepend("<option value='' selected>Select RAM</option>");

            $(".selectedCamera0Default").empty();
            for(let key in currentCamera0Types){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedCamera0Default").append("<option value='"+key+"'>"+currentCamera0Types[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedCamera0Default").prepend("<option value='' selected>Select CameraType</option>");

            $(".selectedCamera1Default").empty();
            for(let key in currentCamera1Types){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedCamera1Default").append("<option value='"+key+"'>"+currentCamera1Types[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedCamera1Default").prepend("<option value='' selected>Select CameraType</option>");

            $(".selectedCamera0ResDefault").empty();
            for(let key in currentCamera0Res){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedCamera0ResDefault").append("<option value='"+key+"'>"+currentCamera0Res[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedCamera0ResDefault").prepend("<option value='' selected>Select Resolution</option>");

            $(".selectedCamera1ResDefault").empty();
            for(let key in currentCamera1Res){
				//if the current key in the loop is in our 'selectedItemsArr' array
				$(".selectedCamera1ResDefault").append("<option value='"+key+"'>"+currentCamera1Res[key]+"</option>");
			}
			//prepend 'select item' to the select option
			$(".selectedCamera1ResDefault").prepend("<option value='' selected>Select Resolution</option>");

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

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    $(".cancelAddItem").click(function(){
        //reset and hide the form
        document.getElementById("addNewItemForm").reset();//reset the form
        $("#createNewItemDiv").addClass('hidden');//hide the form
        $("#itemsListDiv").attr('class', "col-sm-12");//make the table span the whole div
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

        var itemSOCCompany = $("#itemSOCCompany").find("option:selected").text();
        var itemSOCName = $("#itemSOCName").find("option:selected").text();
        var itemGPU = $("#itemGPU").find("option:selected").text();
        var itemDSP = $("#itemDSP").find("option:selected").text();
        var itemRAM = $("#itemRAM").find("option:selected").text();
        var itemCamera0 = $("#itemCamera0").find("option:selected").text();
        var itemCamera1 = $("#itemCamera1").find("option:selected").text();
        var itemCamera0Res = $("#itemCamera0Res").find("option:selected").text();
        var itemCamera1Res = $("#itemCamera1Res").find("option:selected").text();


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
            data:{itemName:itemName, itemCustomer:itemCustomer, itemSOCCompany:itemSOCCompany,
                itemSOCName:itemSOCName, itemGPU:itemGPU, itemDSP:itemDSP, itemRAM:itemRAM,
                itemCamera0:itemCamera0, itemCamera1:itemCamera1, itemDesc:itemDesc, itemCamera0Res:itemCamera0Res,
                itemCamera1Res:itemCamera1Res, itemStartDate:itemStartDate, itemMPDate:itemMPDate, itemShipDate:itemShipDate},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("addNewItemForm").reset();
                    $("#itemCustomer").val(itemCustomer);

                    if(!inArray(itemSOCCompany, currentSOCCompanies)){
                        currentSOCCompanies.push(itemSOCCompany);
                        $(".selectedSOCCompanyDefault").empty();
                        for(let key in currentSOCCompanies){
                            if (currentSOCCompanies[key] == itemSOCCompany) {
                            	$(".selectedSOCCompanyDefault").append("<option value='"+key+"' selected>"+currentSOCCompanies[key]+"</option>");
                            } else {
                            	$(".selectedSOCCompanyDefault").append("<option value='"+key+"'>"+currentSOCCompanies[key]+"</option>");
                            }
            			}
            			$(".selectedSOCCompanyDefault").prepend("<option value=''>Select SOC Company</option>");
                    } else {
                        $("#itemSOCCompany").val(itemSOCCompanyVal);
                    }

                    if(!inArray(itemSOCName, currentSOCNames)){
                        currentSOCNames.push(itemSOCName);
                        $(".selectedSOCNameDefault").empty();
                        for(let key in currentSOCNames){
                            if (currentSOCNames[key] == itemSOCName) {
    			                $(".selectedSOCNameDefault").append("<option value='"+key+"' selected>"+currentSOCNames[key]+"</option>");
                            } else {
                                $(".selectedSOCNameDefault").append("<option value='"+key+"'>"+currentSOCNames[key]+"</option>");
                            }
            			}
            			$(".selectedSOCNameDefault").prepend("<option value=''>Select SOC</option>");
                    } else {
                        $("#itemSOCName").val(itemSOCNameVal);
                    }
                    //refresh the items list table
                    lilt();

                    //return focus to item code input to allow adding item with barcode scanner
                    $("#itemName").focus();
                }

                else{
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


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //reload items list table when events occur
    $("#itemsListPerPage, #itemsListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lilt();
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#itemSearch").keyup(function(){
        var value = $(this).val();
        //console.log("The Priority NAME value: %s", value);
        if(value){
            $.ajax({
                url: appRoot+"search/customerProjectSearch",
                type: "get",
                data: {v:value},
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

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
            $(".selectedCustomerDefault").empty();
            for(let key in currentCustomers){
				if (currentCustomers[key] == itemCustomer) {
                    $(".selectedCustomerDefault").append("<option value='"+key+"' selected>"+currentCustomers[key]+"</option>");
                } else {
                    $(".selectedCustomerDefault").append("<option value='"+key+"'>"+currentCustomers[key]+"</option>");
                }

			}
			$(".selectedCustomerDefault").prepend("<option value=''>Select Customer" +  itemDSP + "</option>");

            $(".selectedSOCCompanyDefault").empty();
            for(let key in currentSOCCompanies){
				if (currentSOCCompanies[key] == itemSOCCompany) {
	                $(".selectedSOCCompanyDefault").append("<option value='"+key+"' selected>"+currentSOCCompanies[key]+"</option>");
                } else {
                    $(".selectedSOCCompanyDefault").append("<option value='"+key+"'>"+currentSOCCompanies[key]+"</option>");
                }
			}
			$(".selectedSOCCompanyDefault").prepend("<option value='' >Select SOC Company</option>");

            $(".selectedSOCNameDefault").empty();
            for(let key in currentSOCNames){
    			if (currentSOCNames[key] == itemSOCName) {
    				$(".selectedSOCNameDefault").append("<option value='"+key+"' selected>"+currentSOCNames[key]+"</option>");
                } else {
                    $(".selectedSOCNameDefault").append("<option value='"+key+"'>"+currentSOCNames[key]+"</option>");
                }
			}
			$(".selectedSOCNameDefault").prepend("<option value='' >Select SOC</option>");

            $(".selectedDSPDefault").empty();
            for(let key in currentDSPs){
				if (currentDSPs[key] == itemDSP) {
    				$(".selectedDSPDefault").append("<option value='"+key+"' selected>"+currentDSPs[key]+"</option>");
                } else {
                    $(".selectedDSPDefault").append("<option value='"+key+"'>"+currentDSPs[key]+"</option>");
                }
			}
			if (itemDSP == '') {
                $(".selectedDSPDefault").prepend("<option value='' selected>Select DSP</option>");
            } else {
                $(".selectedDSPDefault").prepend("<option value=''>Select DSP</option>");
            }

            $(".selectedGPUDefault").empty();
            for(let key in currentGPUs){
				if (currentGPUs[key] == itemGPU) {
				    $(".selectedGPUDefault").append("<option value='"+key+"' selected>"+currentGPUs[key]+"</option>");
                } else {
                    $(".selectedGPUDefault").append("<option value='"+key+"'>"+currentGPUs[key]+"</option>");
                }
			}
			if (itemGPU == '') {
                $(".selectedGPUDefault").prepend("<option value='' selected>Select GPU</option>");
            } else {
                $(".selectedGPUDefault").prepend("<option value=''>Select GPU</option>");
            }

            $(".selectedRAMDefault").empty();
            for(let key in currentRAMs){
				if (currentRAMs[key] == itemRAM) {
				    $(".selectedRAMDefault").append("<option value='"+key+"' selected>"+currentRAMs[key]+"</option>");
                } else {
                    $(".selectedRAMDefault").append("<option value='"+key+"'>"+currentRAMs[key]+"</option>");
                }
			}
			if (itemRAM == '') {
                $(".selectedRAMDefault").prepend("<option value='' selected>Select RAM</option>");
            } else {
                $(".selectedRAMDefault").prepend("<option value=''>Select RAM</option>");
            }

            $(".selectedCamera0Default").empty();
            for(let key in currentCamera0Types){
			    if (currentCamera0Types[key] == itemCamera0) {
    				$(".selectedCamera0Default").append("<option value='"+key+"' selected>"+currentCamera0Types[key]+"</option>");
                } else {
                	$(".selectedCamera0Default").append("<option value='"+key+"'>"+currentCamera0Types[key]+"</option>");
                }
			}
			if (itemCamera0 == '') {
                $(".selectedCamera0Default").prepend("<option value='' selected>Select CameraType</option>");
            } else {
                $(".selectedCamera0Default").prepend("<option value=''>Select CameraType</option>");
            }

            $(".selectedCamera1Default").empty();
            for(let key in currentCamera1Types){
				if (currentCamera1Types[key] == itemCamera1) {
                    $(".selectedCamera1Default").append("<option value='"+key+"' selected>"+currentCamera1Types[key]+"</option>");
                } else {
                    $(".selectedCamera1Default").append("<option value='"+key+"'>"+currentCamera1Types[key]+"</option>");
                }
			}
			if (itemCamera1 == '') {
                $(".selectedCamera1Default").prepend("<option value='' selected>Select CameraType</option>");
            } else {
                $(".selectedCamera1Default").prepend("<option value=''>Select CameraType</option>");
            }

            $(".selectedCamera0ResDefault").empty();
            for(let key in currentCamera0Res){
				if (currentCamera0Res[key] == itemCamera0Res) {
    				$(".selectedCamera0ResDefault").append("<option value='"+key+"' selected>"+currentCamera0Res[key]+"</option>");
                } else {
                    $(".selectedCamera0ResDefault").append("<option value='"+key+"'>"+currentCamera0Res[key]+"</option>");
                }
			}
			if (itemCamera0Res == '') {
                $(".selectedCamera0ResDefault").prepend("<option value='' selected>Select Resolution</option>");
            } else {
                $(".selectedCamera0ResDefault").prepend("<option value=''>Select Resolution</option>");
            }

            $(".selectedCamera1ResDefault").empty();
            for(let key in currentCamera1Res){
				if (currentCamera1Res[key] == itemCamera1Res) {
    				$(".selectedCamera1ResDefault").append("<option value='"+key+"' selected>"+currentCamera1Res[key]+"</option>");
                } else {
                	$(".selectedCamera1ResDefault").append("<option value='"+key+"'>"+currentCamera1Res[key]+"</option>");
                }
			}
			if (itemCamera1Res == '') {
                $(".selectedCamera1ResDefault").prepend("<option value='' selected>Select Resolution</option>");
            } else {
                $(".selectedCamera1ResDefault").prepend("<option value=''>Select Resolution</option>");
            }

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

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#editItemSubmit").click(function(){
        //var itemName = $("#itemNameEdit").val();
        //var itemDesc = $("#itemDescEdit").val();
        //
        //var itemCustomer = $("#itemCustomerEdit").val();
        //var itemSOCCompany = $("#itemSOCCompanyEdit").find("option:selected").text();
        //var itemSOCCompanyVal = $("#itemSOCCompanyEdit").val();
        //var itemSOCName = $("#itemSOCNameEdit").find("option:selected").text();
        //var itemSOCNameVal = $("#itemSOCNameEdit").val();
        //var itemGPU = $("#itemGPUEdit").val();
        //var itemDSP = $("#itemDSPEdit").val();
        //var itemRAM = $("#itemRAMEdit").val();
        //var itemCamera0 = $("#itemCamera0Edit").val();
        //var itemCamera1 = $("#itemCamera1Edit").val();
        //var itemCamera0 = $("#itemCamera0Edit").val();
        //var itemCamera1 = $("#itemCamera1Edit").val();

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
            data: {itemName:itemName, itemCustomer:itemCustomer, itemSOCCompany:itemSOCCompany,
                itemSOCName:itemSOCName, itemGPU:itemGPU, itemDSP:itemDSP, itemRAM:itemRAM,
                itemCamera0:itemCamera0, itemCamera1:itemCamera1, itemDesc:itemDesc, itemCamera0Res:itemCamera0Res,
                itemCamera1Res:itemCamera1Res, itemStartDate:itemStartDate, itemMPDate:itemMPDate,
                itemShipDate:itemShipDate, _iId:itemId}
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
    var limit = $("#itemsListPerPage").val();


    $.ajax({
        type:'get',
        url: url ? url : appRoot+"customerProjects/lilt/",
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
