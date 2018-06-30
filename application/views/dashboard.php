<?php
defined('BASEPATH') OR exit('');

$current_products = [];
$current_priorities = [];
$current_competitors = [];

if(isset($products) && !empty($products)){
    foreach($products as $get){
        $current_products[$get->id] = $get->Name;
    }
}

if(isset($priorities) && !empty($priorities)){
    foreach($priorities as $get){
        $current_priorities[$get->Value] = $get->Name;
    }
}

if(isset($competitors) && !empty($competitors)){
    foreach($competitors as $get){
        $current_competitors[$get->id] = $get->Name;
    }
}

?>

<script>
    var currentProducts = <?=json_encode($current_products)?>;
    var currentPriorities = <?=json_encode($current_priorities)?>;
    var currentCompetitors = <?=json_encode($current_competitors)?>;
</script>

<div class="row latestStuffs">
    <div class="col-sm-4">
        <div class="panel panel-info">
            <div class="panel-body latestStuffsBody" style="background-color: #5cb85c">
                <div class="pull-left"><i class="fa fa-exchange"></i></div>
                <div class="pull-right">
                    <div><?=$totalSalesToday?></div>
                    <div class="latestStuffsText">Total Sales Quarter</div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#5cb85c">Number of Product Sold Quarter</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-info">
            <div class="panel-body latestStuffsBody" style="background-color: #f0ad4e">
                <div class="pull-left"><i class="fa fa-tasks"></i></div>
                <div class="pull-right">
                    <div><?=$totalTransactions?></div>
                    <div class="latestStuffsText pull-right">Total Transactions</div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#f0ad4e">All-time Total Transactions</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-info">
            <div class="panel-body latestStuffsBody" style="background-color: #337ab7">
                <div class="pull-left"><i class="fa fa-shopping-cart"></i></div>
                <div class="pull-right">
                    <div><?=$totalProducts?></div>
                    <div class="latestStuffsText pull-right">Products</div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#337ab7">Total Products</div>
        </div>
    </div>
</div>
<div class="row margin-top-5" >
    <div class="col-sm-6">
        <div class="panel panel-hash ">
            <div class="panel-heading">
                <i class="fa fa-cart-plus"></i> Product PIE
            </div>
            <div class="col-sm-4 margin-top-5">
                <select class="form-control selectedYearDefault" id="itemYear"></select>
                <span id="itemYearLoading"></span>
            </div>
            <div class="col-sm-4 margin-top-5">
                <select class="form-control selectedProductDefault " id="itemProduct"> </select>
                <span id="itemProductLoading"></span>
            </div>
            <div class="col-sm-4 margin-top-5">
                <select class="form-control selectedPriorityDefault" id="itemPriority"> </select>
                <span id="itemPriorityLoading"></span>
            </div>
            <section class="panel" style="margin-bottom:0px">
              <center>
                  <div id="marketChart"/>
              </center>
            </section>
        </div>
    </div>

    <div class="col-sm-6" id="itemsListTable">

    </div>
</div>
<!-- ROW OF GRAPH/CHART OF EARNINGS PER MONTH/YEAR-->
<div class="row margin-top-5">
    <div class="col-sm-9">
        <div class="box">
            <div class="box-header" style="background-color:/*#33c9dd*/#EEE;">
              <h3 class="box-title" id="earningsTitle"></h3>
            </div>

            <div class="box-body" style="background-color:/*#33c9dd*/#EEE;">
              <canvas style="padding-right:25px" id="earningsGraph" width="200" height="80"/></canvas>
            </div>
        </div>
    </div>
</div>
<!-- END OF ROW OF GRAPH/CHART OF EARNINGS PER MONTH/YEAR-->

<!-- ROW OF SUMMARY -->
<div class="row margin-top-5">
    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-cart-plus"></i> HIGH IN DEMAND</div>
            <?php if($topDemanded): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty Sold</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($topDemanded as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td><?=$get->totSold?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-cart-arrow-down"></i> LOW IN DEMAND</div>
            <?php if($leastDemanded): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty Sold</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($leastDemanded as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td><?=$get->totSold?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-money"></i> HIGHEST EARNING</div>
            <?php if($highestEarners): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Total Earned</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($highestEarners as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td>&#8358;<?=number_format($get->totEarned, 2)?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-money"></i> LOWEST EARNING</div>
            <?php if($lowestEarners): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Total Earned</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($lowestEarners as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td>&#8358;<?=number_format($get->totEarned, 2)?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END OF ROW OF SUMMARY -->

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Daily Transactions</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($dailyTransactions) && $dailyTransactions): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($dailyTransactions as $get): ?>
                        <tr>
                            <td><?=
                                    date('l jS M, Y', strtotime($get->transDate)) === date('l jS M, Y', time())
                                    ?
                                    "Today"
                                    :
                                    date('l jS M, Y', strtotime($get->transDate));
                                ?>
                            </td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>&#8358;<?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Days</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByDays) && $transByDays): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByDays as $get): ?>
                        <tr>
                            <td><?=$get->day?>s</td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>&#8358;<?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Months</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByMonths) && $transByMonths): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByMonths as $get): ?>
                        <tr>
                            <td><?=$get->month?></td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>&#8358;<?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Years</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByYears) && $transByYears): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByYears as $get): ?>
                        <tr>
                            <td><?=$get->year?></td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>&#8358;<?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url('public/chart/chart.min.js'); ?>"></script>
<script src="<?=base_url('public/highcharts-6.1.0/code/highcharts.src.js'); ?>"></script>
<script src="<?=base_url('public/highcharts-6.1.0/code/modules/exporting.src.js'); ?>"></script>
<script src="<?=base_url('public/highcharts-6.1.0/code/modules/export-data.src.js'); ?>"></script>
<script src="<?=base_url('public/js/dashboard.js')?>"></script>
