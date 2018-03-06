<?php
defined('BASEPATH') OR exit('');

$current_customers = [];
if(isset($customers) && !empty($customers)){
    foreach($customers as $get){
        $current_customers[$get->id] = $get->Name;
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
?>

<script>
    var currentCustomers = <?=json_encode($current_customers)?>;
    var currentSOCCompanies = <?=json_encode($current_soccompanies)?>;
    var currentSOCNames = <?=json_encode($current_socnames)?>;
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
                            <option value="Name-DESC">Project Name (Z-A)</option>
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

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCCompany">SOC-Company</label>
                            <select class="form-control selectedSOCCompanyDefault checkField" id="itemSOCCompany" name="itemSOCCompany"> </select>
                            <span class="help-block errMsg" id="itemSOCCompanyErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemSOCName">SOC-Name</label>
                            <select class="form-control selectedSOCNameDefault checkField" id="itemSOCName" name="itemSOCName"> </select>
                            <span class="help-block errMsg" id="itemSOCNameErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemGPU">GPU</label>
                            <input type="text" id="itemGPU" name="itemGPU" placeholder="GPU (Optional)" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemGPUErr')">
                            <span class="help-block errMsg" id="itemGPUErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemDSP">DSP</label>
                            <input type="text" id="itemDSP" name="itemDSP" placeholder="DSP (Optional)" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemDSPErr')">
                            <span class="help-block errMsg" id="itemDSPErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRAM">RAM</label>
                            <input type="text" id="itemRAM" name="itemRAM" placeholder="RAM (Optional)" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemRAMErr')">
                            <span class="help-block errMsg" id="itemRAMErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera0">Camera Front</label>
                            <input type="text" id="itemCamera0" name="itemCamera0" placeholder="Camera Type (Optional)" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemCamera0Err')">
                            <span class="help-block errMsg" id="itemCamera0Err"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCamera1">Camera Rear</label>
                            <input type="text" id="itemCamera1" name="itemCamera1" placeholder="Camera Type (Optional)" maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemCamera1Err')">
                            <span class="help-block errMsg" id="itemCamera1Err"></span>
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
                <h4 class="text-center">Edit Type Name</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="addNewItemForm" id="addNewItemForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Type Name</label>
                            <input type="text" id="itemNameEdit" placeholder="Platform Name" autofocus class="form-control checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescriptionEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescriptionEdit" placeholder="Optional Item Description"></textarea>
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
