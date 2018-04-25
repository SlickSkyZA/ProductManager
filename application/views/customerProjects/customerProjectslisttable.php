<?php defined('BASEPATH') OR exit('') ?>

<?= isset($range) && !empty($range) ? $range : ""; ?>

<style type="text/css">
.table th, td{
    text-align:center;/** 设置水平方向居中 */
    vertical-align:middle!important;/** 设置垂直方向居中 */
}
</style>

<div class="panel panel-primary">
    <!-- Default panel contents -->
    <div class="panel-heading">Types</div>
    <?php if($allItems): ?>
    <div class="table table-responsive">
        <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
            <thead>
                <tr align="center">
                    <th rowspan="2">SN</th>
                    <th rowspan="2">ID</th>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Customer</th>
                    <th colspan="2">SOC</th>
                    <th colspan="3">Hardware</th>
                    <th colspan="6">Camera</th>
                    <th colspan="3">Date</th>
                    <th rowspan="2">DESCRIPTION</th>
                    <th rowspan="2">EDIT</th>
                    <th rowspan="2">DELETE</th>
                </tr>
                <tr align="center">
                    <th>Company</th>
                    <th>Name</th>
                    <th>GPU</th>
                    <th>DSP</th>
                    <th>RAM</th>
                    <th>Front</th>
                    <th>FrontRes</th>
                    <th>Rear</th>
                    <th>RearRes</th>
                    <th>ModuleVender</th>
                    <th>Assembly</th>
                    <th>CF</th>
                    <th>MP</th>
                    <th>Ship</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allItems as $get): ?>
                <tr>
                    <input type="hidden" value="<?=$get->id?>" class="curItemId">
                    <th class="itemSN"><?=$sn?>.</th>
                    <td><span id="itemID-<?=$get->id?>"><?=$get->id?></span></td>
                    <td><span id="itemName-<?=$get->id?>"><?=$get->Name?></td>
                    <td><span id="itemCustomer-<?=$get->id?>"><?=$get->CustomerName?></td>
                    <td><span id="itemSOCCompany-<?=$get->id?>"><?=$get->SOCCompany?></td>
                    <td><span id="itemSOCName-<?=$get->id?>"><?=$get->SOCName?></td>
                    <td><span id="itemGPU-<?=$get->id?>"><?=$get->GPU?></td>
                    <td><span id="itemDSP-<?=$get->id?>"><?=$get->DSP?></td>
                    <td><span id="itemRAM-<?=$get->id?>"><?=$get->RAM?></td>
                    <td><span id="itemCamera0-<?=$get->id?>"><?=$get->FrontCameraType?></td>
                    <td><span id="itemCamera0Res-<?=$get->id?>"><?=$get->FrontCameraRes?></td>
                    <td><span id="itemCamera1-<?=$get->id?>"><?=$get->RearCameraType?></td>
                    <td><span id="itemCamera1Res-<?=$get->id?>"><?=$get->RearCameraRes?></td>
                    <td><span id="itemCamModule-<?=$get->id?>"><?=$get->ModuleName?></td>
                    <td><span id="itemCamAssembly-<?=$get->id?>"><?=$get->AssemblyName?></td>
                    <td><span id="itemStartDate-<?=$get->id?>"><?=$get->CodeFreeze?></td>
                    <td><span id="itemMPDate-<?=$get->id?>"><?=$get->MP?></td>
                    <td><span id="itemShipDate-<?=$get->id?>"><?=$get->Ship?></td>
                    <td>
                        <span id="itemDesc-<?=$get->id?>" data-toggle="tooltip" title="<?=$get->Notes?>" data-placement="auto">
                            <?=ellipsize_text($get->Notes, 10)?>
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
