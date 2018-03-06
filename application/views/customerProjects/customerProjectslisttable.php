<?php defined('BASEPATH') OR exit('') ?>

<?= isset($range) && !empty($range) ? $range : ""; ?>
<div class="panel panel-primary">
    <!-- Default panel contents -->
    <div class="panel-heading">Types</div>
    <?php if($allItems): ?>
    <div class="table table-responsive">
        <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Customer</th>
                    <th>SOC Company</th>
                    <th>SOC</th>
                    <th>GPU</th>
                    <th>DSP</th>
                    <th>RAM</th>
                    <th>Front Camera</th>
                    <th>Rear Camera</th>
                    <th>Create DATE</th>
                    <th>Update DATE</th>
                    <th>DESCRIPTION</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allItems as $get): ?>
                <tr>
                    <input type="hidden" value="<?=$get->id?>" class="curItemId">
                    <th class="itemSN"><?=$sn?>.</th>
                    <td><span id="itemID-<?=$get->id?>"><?=$get->id?></span></td>
                    <td><span id="itemName-<?=$get->id?>"><?=$get->Name?></td>
                    <td><span id="itemCustomerName-<?=$get->id?>"><?=$get->CustomerName?></td>
                    <td><span id="itemSOCCompany-<?=$get->id?>"><?=$get->SOCCompany?></td>
                    <td><span id="itemSOCName-<?=$get->id?>"><?=$get->SOCName?></td>
                    <td><span id="itemGPU-<?=$get->id?>"><?=$get->GPU?></td>
                    <td><span id="itemDSP-<?=$get->id?>"><?=$get->DSP?></td>
                    <td><span id="itemRAM-<?=$get->id?>"><?=$get->RAM?></td>
                    <td><span id="itemCamera0-<?=$get->id?>"><?=$get->FrontCameraType?></td>
                    <td><span id="itemCamera1-<?=$get->id?>"><?=$get->RearCameraType?></td>
                    <td><span id="itemAddDate-<?=$get->id?>"><?=$get->AddedDate?></td>
                    <td><span id="itemUpdateDate-<?=$get->id?>"><?=$get->UpdatedDate?></td>
                    <td>
                        <span id="itemDesc-<?=$get->id?>" data-toggle="tooltip" title="<?=$get->Notes?>" data-placement="auto">
                            <?=character_limiter($get->Notes, 15)?>
                        </span>
                    </td>
                    <td class="text-center text-primary">
                        <span class="editItem" id="edit-<?=$get->id?>"><i class="fa fa-pencil pointer"></i> </span>
                    </td>
                    <td class="text-center"><i class="fa fa-trash text-danger delItem pointer"></i></td>
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

<!---Pagination div-->
<div class="col-sm-12 text-center">
    <ul class="pagination">
        <?= isset($links) ? $links : "" ?>
    </ul>
</div>
