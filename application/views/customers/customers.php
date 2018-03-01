<?php
defined('BASEPATH') OR exit('');

$current_regions = [];
$current_priorities = [];

if(isset($priorities) && !empty($priorities)){
    foreach($priorities as $get){
        $current_priorities[$get->id] = $get->Name;
    }
}

if(isset($product_regions) && !empty($product_regions)){
    foreach($product_regions as $get){
        $current_regions[$get->id] = $get->Name;
    }
}
?>

<style href="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.css')?>" rel="stylesheet"></style>

<script>
    var currentRegions = <?=json_encode($current_regions)?>;
    var currentPriorities = <?=json_encode($current_priorities)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Customers</button>
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
                            <option value="Name-ASC">Customer Name (A-Z)</option>
                            <option value="RegionID-ASC">Region (Ascending)</option>
                            <option value="PriorityValue-ASC">Priority (Ascending)</option>
                            <option value="Name-DESC">Customer Name (Z-A)</option>
                            <option value="RegionID-DESC">Region (Descending)</option>
                            <option value="PriorityValue-DESC">Priority (Descending)</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control" placeholder="Search Customer Name">
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
            <div class="col-sm-4 hidden" id='createNewItemDiv'>
                <div class="well">
                    <button class="btn btn-info btn-xs pull-left" id="useBarcodeScanner">Use Scanner</button>
                    <button class="close cancelAddItem">&times;</button><br>
                    <form name="addNewItemForm" id="addNewItemForm" role="form">
                        <div class="text-center errMsg" id='addCustErrMsg'></div>

                        <br>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="customerName">Customer Name</label>
                                <input type="text" id="customerName" name="customerName" placeholder="Customer Name" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'customerNameErr')">
                                <span class="help-block errMsg" id="customerNameErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="customerRegion">Customer Region</label>
                                <select class="form-control selectedGroupDefault" id="customerRegion" name="customerRegion" maxlength="80"
                                    onchange="checkField(this.value, 'customerRegionErr')"></select>
                                <span class="help-block errMsg" id="customerRegionErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="priority">Priority</label>
                                <select class="form-control selectedPriorityDefault" id="priority" name="priority" maxlength="80"
                                    onchange="checkField(this.value, 'priorityErr')"></select>
                                <span class="help-block errMsg" id="priorityErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="description" class="">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" rows='4'
                                    placeholder="Optional Product Description"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Customer</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddItem" class="btn btn-danger btn-sm cancelAddItem" form='addNewItemForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
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
    </div>
    <!-- End of row of adding new item form and items list table--->
</div>

<!---modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Customer Info</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="addNewItemForm" id="addNewItemForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Customer Name</label>
                            <input type="text" id="itemNameEdit" placeholder="Priority Name" autofocus class="form-control  checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRegionEdit">Customer Region</label>
                            <select class="form-control selectedGroupDefault checkField" id="itemRegionEdit" name="itemRegionEdit"></select>
                            <span class="help-block errMsg" id="itemRegionEditErr"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPriorityEdit">Product Priority</label>
                            <select class="form-control selectedPriorityDefault checkField" id="itemPriorityEdit" name="itemPriorityEdit"></select>
                            <span class="help-block errMsg" id="itemPriorityEditErr"></span>
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

<!---End of copy of div to clone when adding more items to sales transaction---->
<script src="<?=base_url()?>public/js/customers.js"></script>
<script src="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.js')?>"></script>
<script src="<?=base_url('public/ext/datetimepicker/jquery.timepicker.min.js')?>"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/datepair.min.js"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/jquery.datepair.min.js"></script>
