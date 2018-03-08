'use strict';

$(document).ready(function() {
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //get earnings for current  year on page load
    getEarnings();

    //load payment method pie charts
    loadPaymentMethodChart();


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
});


/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/
/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/

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
