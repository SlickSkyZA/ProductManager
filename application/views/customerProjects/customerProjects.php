<?php
defined('BASEPATH') OR exit('');

$current_customers = [];
if(isset($customers) && !empty($customers)){
    foreach($customers as $get){
        $current_customers[$get->id] = $get->Name;
    }
}

$current_CameraVender = [];
if(isset($camera_vender) && !empty($camera_vender)){
    foreach($camera_vender as $get){
        $current_CameraVender[$get->id] = $get->Name;
    }
}

$current_CameraAssembly = [];
if(isset($camera_assembly) && !empty($camera_assembly)){
    foreach($camera_assembly as $get){
        $current_CameraAssembly[$get->id] = $get->Name;
    }
}

$current_soccompanies = [];
if(isset($soc_companies) && !empty($soc_companies)){
    $id = 0;
    foreach($soc_companies as $get){
        $current_soccompanies[$id] = $get->SOCCompany;
        $id = $id + 1;
    }
}

$current_socnames = [];
if(isset($soc_names) && !empty($soc_names)){
    $id = 0;
    foreach($soc_names as $get){
        $current_socnames[$id] = $get->SOCName;
        $id = $id + 1;
    }
}

$current_DSP = [];
if(isset($hardware_DSP) && !empty($hardware_DSP)){
    $id = 0;
    foreach($hardware_DSP as $get){
        $current_DSP[$id] = $get->DSP;
        $id = $id + 1;
    }
}
$current_GPU = [];
if(isset($hardware_GPU) && !empty($hardware_GPU)){
    $id = 0;
    foreach($hardware_GPU as $get){
        $current_GPU[$id] = $get->GPU;
        $id = $id + 1;
    }
}
$current_RAM = [];
if(isset($hardware_RAM) && !empty($hardware_RAM)){
    $id = 0;
    foreach($hardware_RAM as $get){
        $current_RAM[$id] = $get->RAM;
        $id = $id + 1;
    }
}

$current_Camera0Types = [];
if(isset($camera_type0) && !empty($camera_type0)){
    $id = 0;
    foreach($camera_type0 as $get){
        $current_Camera0Types[$id] = $get->FrontCameraType;
        $id = $id + 1;
    }
}
$current_Camera1Types = [];
if(isset($camera_type1) && !empty($camera_type1)){
    $id = 0;
    foreach($camera_type1 as $get){
        $current_Camera1Types[$id] = $get->RearCameraType;
        $id = $id + 1;
    }
}
$current_Camera0Res = [];
if(isset($camera_res0) && !empty($camera_res0)){
    $id = 0;
    foreach($camera_res0 as $get){
        $current_Camera0Res[$id] = $get->FrontCameraRes;
        $id = $id + 1;
    }
}
$current_Camera1Res = [];
if(isset($camera_res1) && !empty($camera_res1)){
    $id = 0;
    foreach($camera_res1 as $get){
        $current_Camera1Res[$id] = $get->RearCameraRes;
        $id = $id + 1;
    }
}

?>

<script>
    var currentCustomers = <?=json_encode($current_customers)?>;
    var currentSOCCompanies = <?=json_encode($current_soccompanies)?>;
    var currentSOCNames = <?=json_encode($current_socnames)?>;
    var currentDSPs = <?=json_encode($current_DSP)?>;
    var currentGPUs = <?=json_encode($current_GPU)?>;
    var currentRAMs = <?=json_encode($current_RAM)?>;
    var currentCamera0Types = <?=json_encode($current_Camera0Types)?>;
    var currentCamera1Types = <?=json_encode($current_Camera1Types)?>;
    var currentCamera0Res = <?=json_encode($current_Camera0Res)?>;
    var currentCamera1Res = <?=json_encode($current_Camera1Res)?>;
    var currentCamVenders = <?=json_encode($current_CameraVender)?>;
    var currentCamAssemblies = <?=json_encode($current_CameraAssembly)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Project</button>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="itemsListPerPage">Show</label>
                        <select id="itemsListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>per page</label>
                    </div>

                    <div class="col-sm-4 form-group-sm form-inline">
                        <label for="itemsListSortBy">Sort by</label>
                        <select id="itemsListSortBy" class="form-control">
                            <option value="Name-ASC">Project Name (A-Z)</option>
                            <option value="CustomerName-ASC">Customer Name (A-Z)</option>
                            <option value="Name-DESC">Project Name (Z-A)</option>
                            <option value="CustomerName-DESC">Customer Name (Z-A)</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control" placeholder="Search Project Name">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>

    <hr>

    <!-- row of adding new item form and items list table--->
    <div class="row col-sm-12">
        <div class="col-sm-12 hidden" id='createNewItemDiv'>
            <div class="well">
                <button class="btn btn-info btn-xs pull-left" id="useBarcodeScanner">Use Scanner</button>
                <button class="close cancelAddItem">&times;</button><br>
                <form name="addNewItemForm" id="addNewItemForm" role="form">
                    <div class="text-center errMsg" id='addCustErrMsg'></div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemName">Project Name</label>
                            <input type="text" id="itemName" name="itemName" placeholder="Project Name" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemNameErr')">
                            <span class="help-block errMsg" id="itemNameErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCustomer">Customer</label>
                            <select class="form-control selectedCustomerDefault checkField" id="itemCustomer" name="itemCustomer"> </select>
                            <span class="help-block errMsg" id="itemCustomerErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCCompany">SOC-Company</label>
                            <select class="form-control selectedSOCCompanyDefault checkField" id="itemSOCCompany" name="itemSOCCompany"> </select>
                            <span class="help-block errMsg" id="itemSOCCompanyErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCName">SOC-Name</label>
                            <select class="form-control selectedSOCNameDefault checkField" id="itemSOCName" name="itemSOCName"> </select>
                            <span class="help-block errMsg" id="itemSOCNameErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemDSP">DSP</label>
                            <select class="form-control selectedDSPDefault checkField" id="itemDSP" name="itemDSP"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemGPU">GPU</label>
                            <select class="form-control selectedGPUDefault checkField" id="itemGPU" name="itemGPU"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRAM">RAM</label>
                            <select class="form-control selectedRAMDefault checkField" id="itemRAM" name="itemRAM"> </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera0">Camera Front</label>
                            <select class="form-control selectedCamera0Default checkField" id="itemCamera0" name="itemCamera0"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera0Res">Camera Front Resolution</label>
                            <select class="form-control selectedCamera0ResDefault checkField" id="itemCamera0Res" name="itemCamera0Res"> </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera1">Camera Rear</label>
                            <select class="form-control selectedCamera1Default checkField" id="itemCamera1" name="itemCamera1"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera1Res">Camera Rear Resolution</label>
                            <select class="form-control selectedCamera1ResDefault checkField" id="itemCamera1Res" name="itemCamera1Res"> </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamModule">Camera Module</label>
                            <select class="form-control selectedCamVenderDefault checkField" id="itemCamModule" name="itemCamModule" multiple="true"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamAssembly">Camera Assembly</label>
                            <select class="form-control selectedCamAssemblyDefault checkField" id="itemCamAssembly" name="itemCamAssembly" multiple="true"> </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemStartDate">Code Freeze</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemStartDate" name="itemStartDate" >
                            </div>
                            <span class="help-block errMsg" id='itemStartDateErr'></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemMPDate">Mass Production</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemMPDate" name="itemMPDate" >
                            </div>
                            <span class="help-block errMsg" id='itemMPDateErr'></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemShipDate">Shipments</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemShipDate" name="itemShipDate" >
                            </div>
                            <span class="help-block errMsg" id='itemShipDateErr'></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDesc" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDesc" name="itemDesc" rows='4'
                                placeholder="Optional Product Description"></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row text-center">
                        <div class="col-sm-2 form-group-sm"></div>
                        <br class="visible-xs">
                        <div class="col-sm-6"></div>
                        <br class="visible-xs">
                        <div class="col-sm-4 form-group-sm">
                            <button class="btn btn-primary btn-sm" id="addNewItem">Add Project</button>
                            <button type="reset" id="cancelAddItem" class="btn btn-danger btn-sm cancelAddItem" form='addNewItemForm'>Cancel</button>
                        </div>
                    </div>
                </form><!-- end of form-->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <!---Form to add/update an item--->

            <!--- Item list div-->
            <div class="col-sm-12" id="itemsListDiv">
                <!-- Item list Table-->
                <div class="row">
                    <div class="col-sm-12" id="itemsListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of item list div-->

        </div>
    </div>
    <!-- End of row of adding new item form and items list table--->
</div>

<!---modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Customer Project</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="editItemForm" id="editItemForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Project Name</label>
                            <input type="text" id="itemNameEdit" name="itemNameEdit" placeholder="Project Name" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemNameEditErr')">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCustomerEdit">Customer</label>
                            <select class="form-control selectedCustomerDefault checkField" id="itemCustomerEdit" name="itemCustomerEdit"> </select>
                            <span class="help-block errMsg" id="itemCustomerEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCCompanyEdit">SOC-Company</label>
                            <select class="form-control selectedSOCCompanyDefault checkField" id="itemSOCCompanyEdit" name="itemSOCCompanyEdit"> </select>
                            <span class="help-block errMsg" id="itemSOCCompanyEditErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCNameEdit">SOC-Name</label>
                            <select class="form-control selectedSOCNameDefault checkField" id="itemSOCNameEdit" name="itemSOCNameEdit"> </select>
                            <span class="help-block errMsg" id="itemSOCNameEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemDSPEdit">DSP</label>
                            <select class="form-control selectedDSPDefault checkField" id="itemDSPEdit" name="itemDSPEdit"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemGPUEdit">GPU</label>
                            <select class="form-control selectedGPUDefault checkField" id="itemGPUEdit" name="itemGPUEdit"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRAMEdit">RAM</label>
                            <select class="form-control selectedRAMDefault checkField" id="itemRAMEdit" name="itemRAMEdit"> </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera0Edit">Camera Front</label>
                            <select class="form-control selectedCamera0Default checkField" id="itemCamera0Edit" name="itemCamera0Edit"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera0ResEdit">Camera Front Resolution</label>
                            <select class="form-control selectedCamera0ResDefault checkField" id="itemCamera0ResEdit" name="itemCamera0ResEdit"> </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera1Edit">Camera Rear</label>
                            <select class="form-control selectedCamera1Default checkField" id="itemCamera1Edit" name="itemCamera1Edit"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera1ResEdit">Camera Rear Resolution</label>
                            <select class="form-control selectedCamera1ResDefault checkField" id="itemCamera1ResEdit" name="itemCamera1ResEdit"> </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamModuleEdit">Camera Module</label>
                            <select class="form-control selectedCamVenderDefault checkField" id="itemCamModuleEdit" name="itemCamModuleEdit" multiple="true"> </select>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamAssemblyEdit">Camera Assembly</label>
                            <select class="form-control selectedCamAssemblyDefault checkField" id="itemCamAssemblyEdit" name="itemCamAssemblyEdit" multiple="true"> </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemStartDateEdit">Code Freeze</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemStartDateEdit" name="itemStartDateEdit" >
                            </div>
                            <span class="help-block errMsg" id='itemStartDateEditErr'></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemMPDateEdit">Mass Production</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemMPDateEdit" name="itemMPDateEdit" >
                            </div>
                            <span class="help-block errMsg" id='itemMPDateEditErr'></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemShipDateEdit">Shipments</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemShipDateEdit" name="itemShipDateEdit" >
                            </div>
                            <span class="help-block errMsg" id='itemShipDateEditErr'></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescEdit" name="itemDescEdit" rows='4'
                                placeholder="Optional Product Description"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="itemIdEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="editItemSubmit">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal--->
<script src="<?=base_url()?>public/js/customerProjects.js"></script>
