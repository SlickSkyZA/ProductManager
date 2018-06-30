<?php
defined('BASEPATH') OR exit('');

$current_customers = [];
$current_products = [];
$current_platforms = [];
$current_statuses = [];
$current_competitors = [];
$current_venders = [];

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
if(isset($platforms) && !empty($platforms)){
    foreach($platforms as $get){
        $current_platforms[$get->id] = $get->Name;
    }
}
if(isset($product_statuses) && !empty($product_statuses)){
    foreach($product_statuses as $get){
        $current_statuses[$get->id] = $get->Name;
    }
}
if(isset($competitors) && !empty($competitors)){
    foreach($competitors as $get){
        $current_competitors[$get->id] = $get->Name;
    }
}
if(isset($venders) && !empty($venders)){
    foreach($venders as $get){
        $current_venders[$get->id] = $get->Name;
    }
}


?>
<!-- LOAD FILES
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 -->
<script>
    var currentCustomers = <?=json_encode($current_customers)?>;
    var currentProducts = <?=json_encode($current_products)?>;
    var currentPlatforms = <?=json_encode($current_platforms)?>;
    var currentStatuses = <?=json_encode($current_statuses)?>;
    var currentCompetitors = <?=json_encode($current_competitors)?>;
    var currentVenders = <?=json_encode($current_venders)?>;
    var currentStatusEdit = 0;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!--- Row to create new transaction-->
            <div class="row">
                <div class="col-sm-3">
                    <span class="pointer text-primary">
                        <button class='btn btn-primary btn-sm' id='createItem'><i class="fa fa-plus"></i> New Transaction </button>
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
                                            <label for="itemProject">Customer Project</label>
                                            <select class="form-control selectedProjectDefault" id="itemProject" name="itemProject"></select>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemPlatform">Platform</label>
                                            <select class="form-control selectedPlatformDefault checkField" id="itemPlatform" name="itemPlatform"></select>
                                            <span class="help-block errMsg" id="itemPlatformErr"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemStatus">Status</label>
                                            <select class="form-control selectedStatusDefault checkField" id="itemStatus" name="itemStatus"></select>
                                            <span class="help-block errMsg" id="itemStatusErr"></span>
                                        </div>
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemMilestone">Status Date</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <span><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <input type="text" class="form-control" value="" id="itemMilestone" name="itemMilestone" >
                                            </div>
                                            <span class="help-block errMsg" id='itemMilestoneErr'></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="itemCompetitor">Competitor</label>
                                            <select class="form-control selectedCompetitorDefault" id="itemCompetitor" name="itemCompetitor" multiple="true"></select>
                                        </div>
                                        <div class="col-sm-4 form-group-sm collapse" id="newVender">
                                            <label for="itemVender">Vender</label>
                                            <select class="form-control selectedVenderDefault" id="itemVender" name="itemVender"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8 form-group-sm">
                                            <label for="itemDesc" class="">Description (Optional)</label>
                                            <textarea class="form-control" id="itemDesc" name="itemDesc" rows='4'
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
                                    <button type="button" class="btn btn-primary btn-sm" id="addNewItem">Confirm</button>
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
                    <div class="col-sm-2 form-inline form-group-sm">
                        <label for="itemsListPerPage">Per Page</label>
                        <select id="itemsListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20" selected>20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-group-sm form-inline">
                        <label for="itemsListSortBy">Sort by</label>
                        <select id="itemsListSortBy" class="form-control">
                            <option value="AddedDate-DESC">AddedDate(Latest First)</option>
                            <option value="AddedDate-ASC">AddedDate(Oldest First)</option>
                            <option value="ProductName-DESC">Product (Highest first)</option>
                            <option value="ProductName-ASC">Product (Lowest first)</option>
                            <option value="CustomerName-DESC">Customer (Highest first)</option>
                            <option value="CustomerName-ASC">Customer (Lowest first)</option>
                            <option value="PlatformName-DESC">Platform (Highest first)</option>
                            <option value="PlatformName-ASC">Platform (Lowest first)</option>
                            <option value="StatusName-DESC">Status (Highest first)</option>
                            <option value="StatusName-ASC">Status (Lowest first)</option>
                        </select>
                    </div>

                    <div class="col-sm-4 form-group-sm form-inline">
                        <label for="itemsListFilterBy">Product</label>
                        <select id="itemsListFilterBy" name="itemsListFilterBy" class="form-control selectedFilterDefault">
                        </select>
                    </div>
                    <div class="col-sm-1 form-group-sm form-inline">
                        <label for="itemsActiveBy">Active</label>
                        <input type="checkbox" checked="checked" id="itemsActiveBy" name="itemsActiveBy" value="1">
                    </div>
                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control" placeholder="Search Transactions">
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
        <div class="col-sm-12" id="itemsListTable"></div>
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
                            <label for="itemProjectEdit">Project</label>
                            <select class="form-control selectedProjectDefault" id="itemProjectEdit" name="itemProjectEdit"></select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPlatformEdit">Platform</label>
                            <select class="form-control selectedPlatformDefault checkField" id="itemPlatformEdit" name="itemPlatformEdit"></select>
                            <span class="help-block errMsg" id="itemPlatformEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCompetitorEdit">Competitor</label>
                            <select class="form-control selectedCompetitorDefault" id="itemCompetitorEdit" name="itemCompetitorEdit" multiple="true"></select>
                        </div>
                        <div class="col-sm-4 form-group-sm" id="newVenderEdit">
                            <label for="itemVenderEdit">Vender</label>
                            <select class="form-control selectedVenderDefault" id="itemVenderEdit" name="itemVenderEdit"></select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemStatusEdit">Status</label>
                            <select class="form-control selectedStatusDefault checkField" id="itemStatusEdit" name="itemStatusEdit"></select>
                            <span class="help-block errMsg" id="itemStatusEditErr"></span>
                        </div>
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemMilestoneEdit">Status Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" value="" id="itemMilestoneEdit" name="itemMilestoneEdit" >
                            </div>
                            <span class="help-block errMsg" id='itemMilestoneEditErr'></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescEdit" class="">Description </label>
                            <textarea class="form-control" id="itemDescEdit" name="itemDescEdit" rows='4'
                                placeholder="Optional Product Description"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="itemIdEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary collapse" id="editNewSubmit">AddNewStatus</button>
                <button class="btn btn-primary" id="editItemSubmit">Update</button>
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

<script src="<?=base_url()?>public/js/markets.js"></script>
