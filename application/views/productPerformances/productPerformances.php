<?php
defined('BASEPATH') OR exit('');

$current_products = [];
$current_devices = [];
$current_resolutions = [];
$current_platforms = [];

if(isset($products) && !empty($products)){
    foreach($products as $get){
        $current_products[$get->id] = $get->Name;
    }
}

if(isset($platforms) && !empty($platforms)){
    foreach($platforms as $get){
        $current_platforms[$get->id] = $get->Name;
    }
}

if(isset($devices) && !empty($devices)){
    $id = 0;
    foreach($devices as $get){
        $current_devices[$id] = $get->Device;
        $id = $id + 1;
    }
}

if(isset($resolutions) && !empty($resolutions)){
    $id = 0;
    foreach($resolutions as $get){
        $current_resolutions[$id] = $get->Resolution;
        $id = $id + 1;
    }
}

?>

<script>
    var currentProducts = <?=json_encode($current_products)?>;
    var currentPlatforms = <?=json_encode($current_platforms)?>;
    var currentDevices = <?=json_encode($current_devices)?>;
    var currentResolutions = <?=json_encode($current_resolutions)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Performance</button>
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
                            <option value="ProductName-ASC">Product Name (A-Z)</option>
                            <option value="CustomerName-ASC">Customer (Ascending)</option>
                            <option value="PriorityValue-ASC">Priority (Ascending)</option>
                            <option value="ProductName-DESC">Product Name (Z-A)</option>
                            <option value="CustomerName-DESC">Customer (Descending)</option>
                            <option value="PriorityValue-DESC">Priority (Descending)</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control" placeholder="Search Product Name">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>

    <hr>

    <!-- row of adding new item form and items list table--->
    <div class="row">
        <div class="col-sm-12">
            <!---Form to add/update an item--->
            <div class="col-sm-12 hidden" id='createNewItemDiv'>
                <div class="well">
                    <button class="btn btn-info btn-xs pull-left" id="useBarcodeScanner">Use Scanner</button>
                    <button class="close cancelAddItem">&times;</button><br>
                    <form name="addNewItemForm" id="addNewItemForm" role="form">
                        <div class="text-center errMsg" id='addCustErrMsg'></div>

                        <br>

                        <div class="row">
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemProduct">Product Name</label>
                                <select class="form-control selectedProductDefault" id="itemProduct" name="itemProduct"
                                    onchange="checkField(this.value, 'itemProductErr')"></select>
                                <span class="help-block errMsg" id="itemProductErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemPlatform">Platform</label>
                                <select class="form-control selectedPlatformDefault" id="itemPlatform" name="itemPlatform"
                                    onchange="checkField(this.value, 'itemPlatformErr')"></select>
                                <span class="help-block errMsg" id="itemPlatformErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemDevice">Device</label>
                                <select class="form-control selectedDeviceDefault" id="itemDevice" name="itemDevice"></select>
                                <span class="help-block errMsg" id="itemDeviceErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemPerformance">Performance(ms)</label>
                                <input type="text" id="itemPerformance" name="itemPerformance" placeholder="Performance(ms)" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemPerformanceErr')">
                                <span class="help-block errMsg" id="itemPerformanceErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemPower">Power(ma)</label>
                                <input type="text" id="itemPower" name="itemPower" placeholder="Power(ma)" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemPowerErr')">
                                <span class="help-block errMsg" id="itemPowerErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemResolution">Image Resolution</label>
                                <select class="form-control selectedResolutionDefault" id="itemResolution" name="itemResolution"
                                    onchange="checkField(this.value, 'itemResolutionErr')"></select>
                                <span class="help-block errMsg" id="itemResolutionErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemVersion">Version</label>
                                <input type="text" id="itemVersion" name="itemVersion" placeholder="Version " maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemVersionErr')">
                                <span class="help-block errMsg" id="itemVersionErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemReportDate">Report Date</label>
                                <input type="text" id="itemReportDate" name="itemReportDate" placeholder="Report Date " maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemReportDateErr')">
                                <span class="help-block errMsg" id="itemReportDateErr"></span>
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
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Performance</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddItem" class="btn btn-danger btn-sm cancelAddItem" form='addNewItemForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
        </div>
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
    <!-- End of row of adding new item form and items list table--->

</div>

<!---modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Issue Info</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="editItemForm" id="editItemForm" role="form">
                    <div class="text-center errMsg" id='addCustErrMsg'></div>

                    <br>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemProductEdit">Product Name</label>
                            <select class="form-control selectedProductDefault" id="itemProductEdit" name="itemProductEdit"
                                onchange="checkField(this.value, 'itemProductEditErr')"></select>
                            <span class="help-block errMsg" id="itemProductEditErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCustomer">Customer Name</label>
                            <select class="form-control selectedCustomerDefault" id="itemCustomerEdit" name="itemCustomerEdit"
                                onchange="checkField(this.value, 'itemCustomerEditErr')"></select>
                            <span class="help-block errMsg" id="itemCustomerEditErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemProjectEdit">Customer Project</label>
                            <select class="form-control selectedProjectDefault" id="itemProjectEdit" name="itemProjectEdit"></select>
                            <span class="help-block errMsg" id="itemProjectEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPriorityEdit">Priority</label>
                            <select class="form-control selectedPriorityDefault" id="itemPriorityEdit" name="itemPriorityEdit"
                                onchange="checkField(this.value, 'itemPriorityEditErr')"></select>
                            <span class="help-block errMsg" id="itemPriorityEditErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemIssueTypeEdit">Issue Type</label>
                            <select class="form-control selectedIssueTypeDefault" id="itemIssueTypeEdit" name="itemIssueTypeEdit"
                                onchange="checkField(this.value, 'itemIssueTypeEditErr')"></select>
                            <span class="help-block errMsg" id="itemIssueTypeEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemVersionEdit">Version</label>
                            <input type="text" id="itemVersionEdit" name="itemVersionEdit" placeholder="Version " maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemVersionEditErr')">
                            <span class="help-block errMsg" id="itemVersionEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemReportDateEdit">Report Date</label>
                            <input type="text" id="itemReportDateEdit" name="itemReportDateEdit" placeholder="Report Date " maxlength="80"
                                class="form-control" onchange="checkField(this.value, 'itemReportDateEditErr')">
                            <span class="help-block errMsg" id="itemReportDateEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescEdit" name="itemDescEdit" rows='4'
                                placeholder="Optional Product Description"></textarea>
                        </div>
                    </div>
                    <br>
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

<!---End of copy of div to clone when adding more items to sales transaction---->
<script src="<?=base_url()?>public/js/productPerformances.js"></script>
