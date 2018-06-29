'use strict';

$(document).ready(function() {
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //initial select
    selected2_tag_default_initial(".selectedProductDefault", currentProducts, "", "LowLightShot");
    $('.selectedProductDefault').select2({dropdownAutoWidth : true});

    selected2_tag_default_initial(".selectedPriorityDefault", currentPriorities, "", "Middle");
    $('.selectedPriorityDefault').select2({dropdownAutoWidth : true});

    // year
    var date = new Date()
    var curYear = date.getFullYear();
    var currentYears = {};
    for(var key = 2017; key <= curYear; key++) {
        currentYears[key] = key;
    }

    selected2_tag_default_initial(".selectedYearDefault", currentYears, "", "2018");
    $('.selectedYearDefault').select2({dropdownAutoWidth : true});

    //get earnings for current  year on page load
    getEarnings();

    //load payment method pie charts
    loadPaymentMethodChart();

    loadPieChart('LowLightShot', 2018, 'Middle');
    flash_list();

    //WHEN "YEAR" IS CHANGED IN ORDER TO CHANGE THE YEAR OF ACCOUNT BEING SHOWN
    $("#earningAndExpenseYear").change(function(){
        var year = $(this).val();

        if(year){
            $("#yearAccountLoading").html("<i class='"+spinnerClass+"'></i> Loading...");

            //get earnings for current  year on page load
            getEarnings(year);

            //also get the payment menthods for that year
            loadPaymentMethodChart(year);
        }
    });

    //WHEN "YEAR" IS CHANGED IN ORDER TO CHANGE THE YEAR OF ACCOUNT BEING SHOWN
    $("#itemYear, #itemProduct, #itemPriority").change(function(){
        var year = $("#itemYear").val();
        var product =  $("#itemProduct").find("option:selected").text();
        var priority = $("#itemPriority").find("option:selected").text();

        console.debug(priority);
        console.debug(product);
        console.debug(year);

        if(year){
            $("#itemYearLoading").html("<i class='"+spinnerClass+"'></i> Loading...");
            $("#itemProductLoading").html("<i class='"+spinnerClass+"'></i> Loading...");
            $("#itemPriorityLoading").html("<i class='"+spinnerClass+"'></i> Loading...");

            //get earnings for current  year on page load
            //getEarnings(year);

            //also get the payment menthods for that year
            //loadPaymentMethodChart(year);
            //
            loadPieChart(product, year, priority);
            flash_list();
        }
    });
});

/**
 * 绘制饼图
 * @param  {[type]} product           [description]
 * @param  {[type]} year              [description]
 * @param  {[type]} customer_priority [description]
 * @return {[type]}                   [description]
 */
function loadPieChart(product, year, customer_priority) {
    var itemProductID = 0;
    for(var key in currentProducts) {
        if(currentProducts[key] == product){
            itemProductID = key;
            break;
        }
    }

    var itemPriorityValue = 0;
    for(var key in currentPriorities){
        if(currentPriorities[key] == customer_priority){
            itemPriorityValue = key;
            break;
        }
    }

    var itemYear = year;
    var dataString =[];

    console.debug(itemYear);
    console.debug(itemPriorityValue);
    console.debug(itemProductID);

    $.ajax({
        type: 'post',
        url: appRoot+"dashboard/getMarketInfo/",
        data:{itemProduct:itemProductID, itemPriority:itemPriorityValue, itemYear:itemYear},
        success: function(response) {
            //var response = jQuery.parseJSON(data);
            for(var j = 0,len=response.data.length; j < len; j++) {
                var temp = response.data[j];
                dataString.push({name:temp[0], y:temp[1]});
            }
            //console.debug(dataString);

            var earningsGraph = document.getElementById("marketChart");

            Highcharts.chart(earningsGraph, {
                chart: {
                    backgroundColor: 'rgba(0,0,0,0)',
                    plotBackgroundColor: 'rgba(0,0,0,0)',
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: product+' in '+year       //choose product and year
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
                },
                credits:{
                    enabled:false // remove logo
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Count',
                    colorByPoint: true,
                    data: dataString
                }]
            });
            //console.debug(dataString);

            if (response.status === 1) {

            } else {

            }

            //remove the loading info
            $("#itemYearLoading").html("");
            $("#itemProductLoading").html("");
            $("#itemPriorityLoading").html("");
            //append the year we are showing
            //$("#paymentMethodYear").html(" - "+response.year);
        },
        error: function(){
            if(!navigator.onLine){
                changeFlashMsgContent("You appear to be offline. Please reconnect to the internet and try again", "", "red", "");
            } else {
                changeFlashMsgContent("Unable to process your request at this time. Pls try again later!", "", "red", "");
            }
        }
    }).fail(function(){
        console.log('req failed');
    });
}

function flash_list(url='') {

    var year = $("#itemYear").val();
    var product =  $("#itemProduct").find("option:selected").text();
    var priority = $("#itemPriority").find("option:selected").text();

    var itemProductID = 0;
    for(var key in currentProducts) {
        if(currentProducts[key] == product){
            itemProductID = key;
            break;
        }
    }

    var itemPriorityValue = 0;
    for(var key in currentPriorities){
        if(currentPriorities[key] == priority){
            itemPriorityValue = key;
            break;
        }
    }

    var itemYear = year;

    console.debug(itemYear);
    console.debug(itemPriorityValue);
    console.debug(itemProductID);

    //console.debug("filter=" + filter);

    $.ajax({
        type:'get',
        url: url ? url : appRoot+"dashboard/getMarketList/",
        data: {itemProduct:itemProductID, itemPriority:itemPriorityValue, itemYear:itemYear},

        success: function(returnedData){
            hideFlashMsg();
            $("#itemsListTable").html(returnedData.itemsListTable);
        },

        error: function(){

        }
    });

    return false;
}



/**
 *
 * @param {type} year
 * @returns {undefined}
 */
function getEarnings(year){
    var yearToFetch = year || '';

    $.ajax({
        type: 'GET',
        url: appRoot+"dashboard/earningsGraph/"+yearToFetch,
        dataType: "html"
    }).done(function(data){
        var response = jQuery.parseJSON(data);

        var barChartData = {
          labels : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"],
          datasets : [{
            fillColor : "rgba(255,255,255,1)", // bar color
            strokeColor : "rgba(151,187,205,0.8)", //hover color
            highlightFill : "rgba(242,245,233,1)", // highlight color
            highlightStroke : "rgba(151,187,205,1)", // highlight hover color
            data : response.total_earnings
          }]
        };

        //show the expense title
        document.getElementById('earningsTitle').innerHTML = "Earnings (" + response.earningsYear +")";

        var earningsGraph = document.getElementById("earningsGraph").getContext("2d");

        window.myBar = new Chart(earningsGraph, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Chart.js Bar Chart'
					}
				}
			});

        //remove the loading info
        $("#yearAccountLoading").html("");
    }).fail(function(){
        console.log('req failed');
    });
}

/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/


/**
 *
 * @returns {undefined}
 */
function loadPaymentMethodChart(year){
    var yearToGet = year ? year : "";
    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };
    $.ajax({
        type: 'GET',
        url: appRoot+"dashboard/paymentmethodchart/"+yearToGet,
        dataType: "html",
        success: function(data) {
            var response = jQuery.parseJSON(data);
            var cash = response.cash;
            var pos = response.pos;
            var cashAndPos = response.cashAndPos;

            if(response.status === 1) {
                if((cash === 0 && pos === 0 && cashAndPos === 0)) {
                  var paymentMethodData = {
                      type: 'pie',
                      data: {
                          datasets: [{
                              data: [
                                  cash,
                                  pos,
                                  cashAndPos,
                              ],
                              backgroundColor: [
                                  window.chartColors.red,
                                  window.chartColors.orange,
                                  window.chartColors.yellow,
                          //		window.chartColors.green,
                          //		window.chartColors.blue,
                              ],
                              label: 'Dataset 1'
                          }],
                          labels: [
                              'No Payment',
                              'POS Only',
                              'Cash and POS'
                          ]
                      },
                      options: {
                          responsive: true
                      }
                 };
                } else {
                    var per1 = cash / (cash + pos + cashAndPos) * 100;
                    var paymentMethodData = {
            			type: 'pie',
            			data: {
            				datasets: [{
            					data: [
            						cash,
            						pos,
            						cashAndPos,
            					],
            					backgroundColor: [
            						window.chartColors.red,
            						window.chartColors.orange,
            						window.chartColors.yellow,
            				//		window.chartColors.green,
            				//		window.chartColors.blue,
            					],
            					label: 'Dataset 1'
            				}],
            				labels: [
            					'Cash Only ' + per1 + '%',
            					'POS Only',
            					'Cash and POS'
            				]
            			},
            			options: {
            				responsive: true
            			}
            	   };
               }

           } else {//if status is 0
                var paymentMethodData = {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [
                                cash,
                                pos,
                                cashAndPos,
                            ],
                            backgroundColor: [
                                window.chartColors.red,
                                window.chartColors.orange,
                                window.chartColors.yellow,
                        //		window.chartColors.green,
                        //		window.chartColors.blue,
                            ],
                            label: 'Dataset 1'
                        }],
                        labels: [
                            'Cash Only',
                            'POS Only',
                            'Cash and POS'
                        ]
                    },
                    options: {
                        responsive: true
                    }
               };
            }

            var ctx = document.getElementById("paymentMethodChart").getContext("2d");
            window.myPie = new Chart(ctx, paymentMethodData);

            //remove the loading info
            $("#yearAccountLoading").html("");

            //append the year we are showing
            $("#paymentMethodYear").html(" - "+response.year);
        }
    });
}
