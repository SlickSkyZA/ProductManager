<?php
defined('BASEPATH') OR exit('');

$current_priorities = [];
$current_customers = [];
$current_products = [];
$current_platforms = [];
$current_statuses = [];
$current_competitors = [];

if(isset($priorities) && !empty($priorities)){
    foreach($priorities as $get){
        $current_priorities[$get->id] = $get->Name;
    }
}
if(isset($customers) && !empty($customers)){
    foreach($customers as $get){
        $current_customers[$get->id] = $get->Name;
    }
}
if(isset($products) && !empty($products)){
    foreach($products as $get){
        $current_products[$get->id] = $get->Name;
    }
}
if(isset($product_platforms) && !empty($product_platforms)){
    foreach($product_platforms as $get){
        $current_platforms[$get->id] = $get->Name;
    }
}
if(isset($product_statuses) && !empty($product_statuses)){
    foreach($product_statuses as $get){
        $current_statuses[$get->id] = $get->Name;
    }
}
if(isset($customer_venders) && !empty($customer_venders)){
    foreach($customer_venders as $get){
        $current_competitors[$get->id] = $get->Name;
    }
}
?>

<style href="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.css')?>" rel="stylesheet"></style>

<script>
    var currentPriorities = <?=json_encode($current_priorities)?>;
    var currentCustomers = <?=json_encode($current_customers)?>;
    var currentProducts = <?=json_encode($current_products)?>;
    var currentPlatforms = <?=json_encode($current_platforms)?>;
    var currentStatuses = <?=json_encode($current_statuses)?>;
    var currentCompetitors = <?=json_encode($current_competitors)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!--- Row to create new transaction-->
            <div class="row">
                <div class="col-sm-3">
                    <span class="pointer text-primary">
                        <button class='btn btn-primary btn-sm' id='showTransForm'><i class="fa fa-plus"></i> New Product Transaction </button>
                    </span>
                </div>
                <div class="col-sm-3">
                    <span class="pointer text-primary">
                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#reportModal'>
                            <i class="fa fa-newspaper-o"></i> Generate Report
                        </button>
                    </span>
                </div>
            </div>
            <br>
            <!--- End of row to create new transaction-->
            <!---form to create new transactions--->
            <div class="row collapse" id="newTransDiv">
                <!---div to display transaction form--->
                <div class="col-sm-12" id="salesTransFormDiv">
                    <div class="well">
                        <form name="salesTransForm" id="salesTransForm" role="form">
                            <div class="text-center errMsg" id='newTransErrMsg'></div>
                            <br>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemProduct">Product</label>
                                            <select class="form-control selectedProductDefault checkField" id="itemProduct" name="itemProduct"></select>
                                            <span class="help-block errMsg" id="itemProductErr"></span>
                                        </div>

                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemCustomer">Customer</label>
                                            <select class="form-control selectedCustomerDefault checkField" id="itemCustomer" name="itemCustomer"></select>
                                            <span class="help-block errMsg" id="itemCustomerErr"></span>
                                        </div>

                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemPlatform">Platform</label>
                                            <select class="form-control selectedPlatformDefault checkField" id="itemPlatform" name="itemPlatform"></select>
                                            <span class="help-block errMsg" id="itemPlatformErr"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemPriority">Priority</label>
                                            <select class="form-control selectedPriorityDefault checkField" id="itemPriority" name="itemPriority"></select>
                                            <span class="help-block errMsg" id="itemPriorityErr"></span>
                                        </div>

                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemStatus">Status</label>
                                            <select class="form-control selectedStatusDefault checkField" id="itemStatus" name="itemStatus"></select>
                                            <span class="help-block errMsg" id="itemStatusErr"></span>
                                        </div>

                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemCompetitor">Competitor (Optional)</label>
                                            <select class="form-control selectedCompetitorDefault" id="itemCompetitor" name="itemCompetitor"></select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemProjectName">Project Name (Optional)</label>
                                            <input type="text" id="itemProjectName" class="form-control" placeholder="Customer Project Name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="description" class="">Description (Optional)</label>
                                            <textarea class="form-control" id="description" name="description" rows='4'
                                                placeholder="Optional Product Description"></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <!-- <div class="col-sm-2 form-group-sm">
                                     <button class="btn btn-primary btn-sm" id='useScanner'>Use Barcode Scanner</button>
                                </div>
                                <br class="visible-xs">
                                <div class="col-sm-6"></div>
                                <br class="visible-xs">  -->
                                <div class="col-sm-4 form-group-sm">
                                    <button type="button" class="btn btn-primary btn-sm" id="confirmSaleOrder">Confirm</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="cancelSaleOrder">Clear</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="hideTransForm">Close</button>
                                </div>
                            </div>
                        </form><!-- end of form-->
                    </div>
                </div>
                <!--- end of div to display transaction form--->
            </div>
            <!--end of form--->

            <br><br>
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="transListPerPage">Per Page</label>
                        <select id="transListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <div class="col-sm-5 form-group-sm form-inline">
                        <label for="transListSortBy">Sort by</label>
                        <select id="transListSortBy" class="form-control">
                            <option value="AddedDate-DESC">AddedDate(Latest First)</option>
                            <option value="AddedDate-ASC">AddedDate(Oldest First)</option>
                            <option value="ProductID-DESC">Product (Highest first)</option>
                            <option value="ProductID-ASC">Product (Lowest first)</option>
                            <option value="CustomerID-DESC">Customer (Highest first)</option>
                            <option value="CustomerID-ASC">Customer (Lowest first)</option>
                            <option value="PlatformID-DESC">Platform (Highest first)</option>
                            <option value="PlatformID-ASC">Platform (Lowest first)</option>
                            <option value="StatusID-DESC">Status (Highest first)</option>
                            <option value="StatusID-ASC">Status (Lowest first)</option>
                        </select>
                    </div>

                    <div class="col-sm-4 form-inline form-group-sm">
                        <label for='transSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="transSearch" class="form-control" placeholder="Search Transactions">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>

    <hr>

    <!-- transaction list table-->
    <div class="row">
        <!-- Transaction list div-->
        <div class="col-sm-12" id="transListTable"></div>
        <!-- End of transactions div-->
    </div>
    <!-- End of transactions list table-->
</div>


<!---modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Product Transaction Info</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="addNewItemForm" id="addNewItemForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemProductEdit">Product</label>
                            <select class="form-control selectedProductDefault checkField" id="itemProductEdit" name="itemProductEdit"></select>
                            <span class="help-block errMsg" id="itemProductEditErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCustomerEdit">Customer</label>
                            <select class="form-control selectedCustomerDefault checkField" id="itemCustomerEdit" name="itemCustomerEdit"></select>
                            <span class="help-block errMsg" id="itemCustomerEditErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPlatformEdit">Platform</label>
                            <select class="form-control selectedPlatformDefault checkField" id="itemPlatformEdit" name="itemPlatformEdit"></select>
                            <span class="help-block errMsg" id="itemPlatformEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPriorityEdit">Priority</label>
                            <select class="form-control selectedPriorityDefault checkField" id="itemPriorityEdit" name="itemPriorityEdit"></select>
                            <span class="help-block errMsg" id="itemPriorityEditErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemStatusEdit">Status</label>
                            <select class="form-control selectedStatusDefault checkField" id="itemStatusEdit" name="itemStatusEdit"></select>
                            <span class="help-block errMsg" id="itemStatusEditErr"></span>
                        </div>

                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCompetitorEdit">Competitor (Optional)</label>
                            <select class="form-control selectedCompetitorDefault" id="itemCompetitorEdit" name="itemCompetitorEdit"></select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemProjectNameEdit">Project Name (Optional)</label>
                            <input type="text" id="itemProjectNameEdit" class="form-control" placeholder="Customer Project Name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
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


<div class="modal fade" id='reportModal' data-backdrop='static' role='dialog'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss='modal'>&times;</div>
                <h4 class="text-center">Generate Report</h4>
            </div>

            <div class="modal-body">
                <div class="row" id="datePair">
                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id='transFrom' class="form-control date start" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id='transFromErr'></span>
                    </div>

                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id='transTo' class="form-control date end" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id='transToErr'></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id='clickToGen'>Generate</button>
                <button class="btn btn-danger" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<!---End of copy of div to clone when adding more items to sales transaction---->
<script src="<?=base_url()?>public/js/producttransactions.js"></script>
<script src="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.js')?>"></script>
<script src="<?=base_url('public/ext/datetimepicker/jquery.timepicker.min.js')?>"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/datepair.min.js"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/jquery.datepair.min.js"></script>
