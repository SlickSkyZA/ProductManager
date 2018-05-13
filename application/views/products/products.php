<?php
defined('BASEPATH') OR exit('');

$current_groups = [];
$current_priorities = [];

if(isset($priorities) && !empty($priorities)){
    foreach($priorities as $get){
        $current_priorities[$get->id] = $get->Name;
    }
}

if(isset($product_group) && !empty($product_group)){
    foreach($product_group as $get){
        $current_groups[$get->id] = $get->Name;
    }
}
?>

<script>
    var currentGroups = <?=json_encode($current_groups)?>;
    var currentPriorities = <?=json_encode($current_priorities)?>;
</script>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Product</button>
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
                            <option value="Name-ASC">Product Name (A-Z)</option>
                            <option value="GroupID-ASC">GroupID (Ascending)</option>
                            <option value="PriorityValue-ASC">Priority (Ascending)</option>
                            <option value="Name-DESC">Product Name (Z-A)</option>
                            <option value="GroupID-DESC">GroupID (Descending)</option>
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
            <div class="col-sm-4 hidden" id='createNewItemDiv'>
                <div class="well">
                    <button class="btn btn-info btn-xs pull-left" id="useBarcodeScanner">Use Scanner</button>
                    <button class="close cancelAddItem">&times;</button><br>
                    <form name="addNewItemForm" id="addNewItemForm" role="form">
                        <div class="text-center errMsg" id='addCustErrMsg'></div>

                        <br>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemName">Product Name</label>
                                <input type="text" id="itemName" name="itemName" placeholder="Product Name" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemNameErr')">
                                <span class="help-block errMsg" id="itemNameErr"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemGroup">Product Group</label>
                                <select class="form-control selectedGroupDefault" id="itemGroup" name="itemGroup" maxlength="80"
                                    onchange="checkField(this.value, 'itemGroupErr')"></select>
                                <span class="help-block errMsg" id="itemGroupErr"></span>
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
                                <label for="itemDPM">DPM (Optional)</label>
                                <input type="text" id="itemDPM" name="itemDPM" placeholder="DPM (Optional)" maxlength="80" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemQPM">QPM (Optional)</label>
                                <input type="text" id="itemQPM" name="itemQPM" placeholder="QPM (Optional)" maxlength="80" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemPM">PM (Optional)</label>
                                <input type="text" id="itemPM" name="itemPM" placeholder="PM (Optional)" maxlength="80" class="form-control">
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
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Product</button>
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
                <h4 class="text-center">Edit Product Info</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="addNewItemEditForm" id="addNewItemEditForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Product Name</label>
                            <input type="text" id="itemNameEdit" placeholder="Priority Name" autofocus class="form-control  checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemGroupEdit">Product Group</label>
                            <select class="form-control selectedGroupDefault checkField" id="itemGroupEdit" name="itemGroupEdit"></select>
                            <span class="help-block errMsg" id="itemGroupEditErr"></span>
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
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemDPMEdit">DPM (Optional)</label>
                            <input type="text" id="itemDPMEdit" name="itemDPMEdit" placeholder="DPM" class="form-control">
                            <span class="help-block errMsg" id="itemDPMEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemQPMEdit">QPM (Optional)</label>
                            <input type="text" id="itemQPMEdit" name="itemQPMEdit" placeholder="QPM" class="form-control">
                            <span class="help-block errMsg" id="itemQPMEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPMEdit">PM (Optional)</label>
                            <input type="text" id="itemPMEdit" name="itemPMEdit" placeholder="PM" class="form-control">
                            <span class="help-block errMsg" id="itemDPMEditErr"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescEdit" placeholder="Optional Item Description"></textarea>
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
<script src="<?=base_url()?>public/js/products.js"></script>
