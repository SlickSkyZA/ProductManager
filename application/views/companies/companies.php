<?php
defined('BASEPATH') OR exit('');

$current_regions = [];
$current_priorities = [];
$current_types = [];

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

if(isset($customer_types) && !empty($customer_types)){
    foreach($customer_types as $get){
        $current_types[$get->id] = $get->Name;
    }
}

?>

<style href="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.css')?>" rel="stylesheet"></style>

<script>
    var currentRegions = <?=json_encode($current_regions)?>;
    var currentPriorities = <?=json_encode($current_priorities)?>;
    var currentTypes = <?=json_encode($current_types)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Company</button>
                    </div>

                    <div class="col-sm-2 form-inline form-group-sm">
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

                    <div class="col-sm-3 form-group-sm form-inline">
                        <label for="itemsListSortBy">Sort by</label>
                        <select id="itemsListSortBy" class="form-control">
                            <option value="Name-ASC">Company Name (A-Z)</option>
                            <option value="Name-DESC">Company Name (Z-A)</option>
                            <option value="RegionID-ASC">Region (Ascending)</option>
                            <option value="RegionID-DESC">Region (Descending)</option>
                            <option value="TypeID-ASC">Type (Ascending)</option>
                            <option value="TypeID-DESC">Type (Descending)</option>
                            <option value="PriorityValue-ASC">Priority (Ascending)</option>
                            <option value="PriorityValue-DESC">Priority (Descending)</option>
                        </select>
                    </div>
                    <div class="col-sm-3 form-group-sm form-inline">
                        <label for="itemsListFilterBy">Company</label>
                        <select class="form-control selectedTypeDefault" id="itemsListFilterBy" name="itemsListFilterBy">
                        </select>
                    </div>
                    <div class="col-sm-2 form-inline form-group-sm">
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
                                <label for="itemName">Company Name</label>
                                <input type="text" id="itemName" name="itemName" placeholder="Customer Name" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemNameErr')">
                                <span class="help-block errMsg" id="itemNameErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemRegion">Company Region(Optional)</label>
                                <select class="form-control selectedGroupDefault" id="itemRegion" name="itemRegion" maxlength="80"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemType">Company Type</label>
                                <select class="form-control selectedTypeDefault" id="itemType" name="itemType"
                                    onchange="checkField(this.value, 'itemTypeErr')"></select>
                                <span class="help-block errMsg" id="itemTypeErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemRSType">Company Relationship</label>
                                <select class="form-control selectedRSTypeDefault" id="itemRSType" name="itemRSType"
                                    onchange="checkField(this.value, 'itemRSTypeErr')">
                                    <option value="Customer">Customer</option>
                                    <option value="Competitor">Competitor</option>
                                </select>
                                <span class="help-block errMsg" id="itemRSTypeErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemPriority">Priority</label>
                                <select class="form-control selectedPriorityDefault" id="itemPriority" name="itemPriority" maxlength="80"
                                    onchange="checkField(this.value, 'itemPriorityErr')"></select>
                                <span class="help-block errMsg" id="itemPriorityErr"></span>
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
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Company</button>
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
                <form name="editItemForm" id="editItemForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Company Name</label>
                            <input type="text" id="itemNameEdit" placeholder="Priority Name" autofocus class="form-control  checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRegionEdit">Company Region(Optional)</label>
                            <select class="form-control selectedGroupDefault" id="itemRegionEdit" name="itemRegionEdit"></select>
                            <span class="help-block errMsg" id="itemRegionEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemTypeEdit">Company Type</label>
                            <select class="form-control selectedTypeDefault" id="itemTypeEdit" name="itemTypeEdit"
                                onchange="checkField(this.value, 'itemTypeEditErr')"></select>
                            <span class="help-block errMsg" id="itemTypeEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemRSTypeEdit">Company Relationship</label>
                            <select class="form-control selectedRSTypeDefault" id="itemRSTypeEdit" name="itemRSTypeEdit"
                                onchange="checkField(this.value, 'itemRSTypeEditErr')">
                                <option value="Customer">Customer</option>
                                <option value="Competitor">Competitor</option>
                            </select>
                            <span class="help-block errMsg" id="itemRSTypeEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPriorityEdit">Company Priority</label>
                            <select class="form-control selectedPriorityDefault checkField" id="itemPriorityEdit" name="itemPriorityEdit"></select>
                            <span class="help-block errMsg" id="itemPriorityEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescEdit" name="itemDescEdit" placeholder="Optional Item Description"></textarea>
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
<script src="<?=base_url()?>public/js/companies.js"></script>
