<?php defined('BASEPATH') OR exit('') ?>

<div class="panel panel-hash">
    <!-- Default panel contents -->
    <div class="panel-heading"><i class="fa fa-cart-plus"></i> Product Transactions</div>
    <?php if($allItems): ?>
    <div class="table table-responsive">
        <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>ID</th>
                    <th>Value</th>
                    <th>Customer</th>
                    <th>Vender</th>
                    <th>StatusDate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allItems as $get): ?>
                <tr>
                    <input type="hidden" value="<?=$get->id?>" class="curItemId">
                    <th class="itemSN"><?=$sn?>.</th>
                    <td><span id="itemID-<?=$get->id?>"><?=$get->id?></span></td>
                    <td><span id="itemValue-<?=$get->id?>"><?=$get->Value?></td>
                    <td><span id="itemCustomer-<?=$get->id?>"><?=$get->Name?></td>
                    <td><span id="itemVender-<?=$get->id?>"><?=$get->Vender?></td>
                    <td><span id="itemDate-<?=$get->id?>"><?= $get->DateTime?> </td>
                </tr>
                <?php $sn++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<!-- table div end--->
    <?php else: ?>
    <ul><li>No items</li></ul>
    <?php endif; ?>
</div>
<!--- panel end-->
<div class="row text-right" style="margin-right:20px;margin-top:-10px">
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>
<div class="row" >
    <div class=" text-center" style="margin-top:-62px">
        <ul class="pagination">
            <?= isset($links) ? $links : "" ?>
        </ul>
    </div>
</div>
<!---Pagination div-->
