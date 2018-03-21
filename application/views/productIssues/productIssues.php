<?php
defined('BASEPATH') OR exit('');

$current_products = [];
$current_customers = [];
$current_priorities = [];
$current_issueTypes = [];
$current_customerProjects = [];

if(isset($priorities) && !empty($priorities)){
    foreach($priorities as $get){
        $current_priorities[$get->id] = $get->Name;
    }
}

if(isset($products) && !empty($products)){
    foreach($products as $get){
        $current_products[$get->id] = $get->Name;
    }
}

if(isset($customers) && !empty($customers)){
    foreach($customers as $get){
        $current_customers[$get->id] = $get->Name;
    }
}

if(isset($productIssues) && !empty($productIssues)){
    $id = 0;
    foreach($productIssues as $get){
        $current_issueTypes[$id] = $get->IssueType;
        $id = $id + 1;
    }
}

if(isset($customerProjects) && !empty($customerProjects)){
    foreach($customerProjects as $get){
        $current_customerProjects[$get->id] = $get->Name;
    }
}

?>

<script>
    var currentProducts = <?=json_encode($current_products)?>;
    var currentPriorities = <?=json_encode($current_priorities)?>;
    var currentCustomers = <?=json_encode($current_customers)?>;
    var currentIssueTypes = <?=json_encode($current_issueTypes)?>;
    var currentCustomerProjects = <?=json_encode($current_customerProjects)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Issue</button>
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
                                <label for="itemCustomer">Customer Name</label>
                                <select class="form-control selectedCustomerDefault" id="itemCustomer" name="itemCustomer"
                                    onchange="checkField(this.value, 'itemCustomerErr')"></select>
                                <span class="help-block errMsg" id="itemCustomerErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemProject">Customer Project</label>
                                <select class="form-control selectedProjectDefault" id="itemProject" name="itemProject"></select>
                                <span class="help-block errMsg" id="itemProjectErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemPriority">Priority</label>
                                <select class="form-control selectedPriorityDefault" id="itemPriority" name="itemPriority"
                                    onchange="checkField(this.value, 'itemPriorityErr')"></select>
                                <span class="help-block errMsg" id="itemPriorityErr"></span>
                            </div>
                            <div class="col-sm-4 form-group-sm">
                                <label for="itemIssueType">Issue Type</label>
                                <select class="form-control selectedIssueTypeDefault" id="itemIssueType" name="itemIssueType"
                                    onchange="checkField(this.value, 'itemIssueTypeErr')"></select>
                                <span class="help-block errMsg" id="itemIssueTypeErr"></span>
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
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Issue</button>
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
<script src="<?=base_url()?>public/js/productIssues.js"></script>
