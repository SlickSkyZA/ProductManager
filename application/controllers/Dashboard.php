<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Dashboard
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Dashboard extends CI_Controller{

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->load->model(['item', 'transaction', 'analytic', 'product', 'priority', 'company', 'market']);
    }

    /**
     *
     */
    public function index(){
        $data['products'] = $this->product->getActiveItems('Name', 'ASC');
        $data['priorities'] = $this->priority->getActiveItems('Name', 'ASC');

        $data['topDemanded'] = $this->analytic->topDemanded();
        $data['leastDemanded'] = $this->analytic->leastDemanded();
        $data['highestEarners'] = $this->analytic->highestEarners();
        $data['lowestEarners'] = $this->analytic->lowestEarners();
        $data['totalItems'] = $this->db->count_all('items');
        $data['totalSalesToday'] = (int)$this->analytic->totalSalesToday();
        $data['totalTransactions'] = $this->transaction->totalTransactions();
        $data['dailyTransactions'] = $this->analytic->getDailyTrans();
        $data['transByDays'] = $this->analytic->getTransByDays();
        $data['transByMonths'] = $this->analytic->getTransByMonths();
        $data['transByYears'] = $this->analytic->getTransByYears();

        $values['pageContent'] = $this->load->view('dashboard', $data, TRUE);

        $values['pageTitle'] = "Dashboard";

        $this->load->view('main', $values);
    }

    /**
     * 统计市场占有率
     * @param  string  $itemProduct  [description]
     * @param  string  $itemPriority [description]
     * @param  string  $itemYear     [description]
     * @param  boolean $not_ajax     [description]
     * @return [type]                [description]
     */
    public function getMarketInfo() {
        $this->genlib->ajaxOnly();
        $this->load->library('form_validation');

        //itemProduct:itemProductID, itemPriority:,
        $itemProductID = set_value('itemProduct');
        $itemPriorityID = set_value('itemPriority');
        $itemYear = set_value('itemYear');
        //log_message('debug', print_r($itemProductID.",".$itemPriorityID.",".$itemYear, TRUE));
        $totalCustomers = $this->company->getCompanyByPriority('Name', 'ASC', 'Mobile', 'Customer', $itemPriorityID);
        //log_message('debug', print_r($totalCustomers, TRUE));
        $list = array('N/A' => 0);
        foreach($totalCustomers as $get) {
            //log_message('debug', print_r($get->id, TRUE));
            $vender = $this->market->getLastVender($itemYear, $itemProductID, $get->id);
            if (!empty($vender)) {
                if ($vender[0]->VenderName == '') {
                    $vender[0]->VenderName = 'N/A';
                }
                $new = true;
                foreach($list as $key => $value) {
                    //log_message('debug', print_r($key."-".$vender[0]->VenderName, TRUE));
                    if ($key == $vender[0]->VenderName) {
                        $list[$key] = $list[$key] + 1;
                        $new = false;
                        break;
                    }
                }
                if ($new) {
                    $temp = array($vender[0]->VenderName => 1);
                    $list = array_merge($list, $temp);
                    //log_message('debug', print_r($list[$vender[0]->VenderName], TRUE));
                }
            }
            //log_message('debug', print_r($vender, TRUE));
        }
        //log_message('debug', print_r($list, TRUE));

        $data = array();
        foreach($list as $key => $value) {
            if ($value > 0) {
                array_push($data, array($key, $value));
            }
        }
        //log_message('debug', print_r($data, TRUE));

        $json['data'] = $data;
        $json['status'] = 0;
        //set final output based on where the request is coming from
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * [getMarketList description]
     * @return [type] [description]
     */
    public function getMarketList() {
        $this->genlib->ajaxOnly();
        $this->load->library('form_validation');

        //itemProduct:itemProductID, itemPriority:,
        $itemProductID = $this->input->get('itemProduct', TRUE) ? $this->input->get('itemProduct', TRUE) : 10; //set_value('itemProduct');
        $itemPriorityID = $this->input->get('itemPriority', TRUE) ? $this->input->get('itemPriority', TRUE) : 100; //set_value('itemPriority');
        $itemYear = $this->input->get('itemYear', TRUE) ? $this->input->get('itemYear', TRUE) : 2017;//set_value('itemYear');
        //log_message('debug', print_r($itemProductID.",".$itemPriorityID.",".$itemYear, TRUE));
        $totalCustomers = $this->company->getCompanyByPriority('Value', 'ASC', 'Mobile', 'Customer', $itemPriorityID);

        //count the total number of items in db
        $totalItems = count($totalCustomers);

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration
        //log_message('debug', print_r($start."--limit--".$limit, TRUE));
        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "dashboard/getMarketList", $limit, ['onclick'=>'return flash_list(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //log_message('debug', print_r($totalCustomers, TRUE));
        $totalCustomers = json_decode(json_encode($totalCustomers), true);
        $limit = $totalItems <  $start+$limit ? $totalItems : $start+$limit;
        $length = $limit - $start;
        for ($i = $start; $i < $limit; $i++) {
            $get = $totalCustomers[$i];
            //log_message('debug', print_r($get, TRUE));
            $vender = $this->market->getLastVender($itemYear, $itemProductID, $get['id']);
            //log_message('debug', print_r("====".$get['id'], TRUE));
            //log_message('debug', print_r($vender, TRUE));
            if (!empty($vender)) {
                $totalCustomers[$i] = array_merge($totalCustomers[$i], array('Vender'=>$vender[0]->VenderName, 'DateTime'=>$vender[0]->StatusDate));
            } else {
                $totalCustomers[$i] = array_merge($totalCustomers[$i], array('Vender'=>'', 'DateTime'=>date("Y-m-d")));
            }
            //log_message('debug', print_r($totalCustomers[$i], TRUE));
            //log_message('debug', print_r($totalCustomers[$i], TRUE));
        }

        //log_message('debug', print_r($start.">>>>>".$limit, TRUE));
        $allItems = array_slice($totalCustomers, $start, $length);
        //log_message('debug', print_r(json_decode(json_encode($allItems)), TRUE));

        //get all items from db
        $data['allItems'] = json_decode(json_encode($allItems));
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('dashboardstable', $data, TRUE);//get view with populated items table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     *
     * @param type $year year of earnings to fetch
     * @param boolean $not_ajax if request if ajax request or not
     * @return int
     */
    public function earningsGraph($year="", $not_ajax = false) {
        //set the year of expenses to show
        $year_to_fetch = $year ? $year : date('Y');

        $earnings = $this->genmod->getYearEarnings($year_to_fetch);
        $lastEarnings = 0;
        $monthEarnings = array();
        $hightEarn['highestEarning'] = 0;
        $dataarr = [];
        $allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        if ($earnings) {
            foreach ($allMonths as $allMonth) {
                foreach ($earnings as $get) {
                    $earningMonth = date("M", strtotime($get->transDate));

                    if ($allMonth == $earningMonth) {
                        $lastEarnings += $get->totalPrice;

                        $monthEarnings[$allMonth] = $lastEarnings;
                    }

                    else {
                        if (!array_key_exists($allMonth, $monthEarnings)) {
                            $monthEarnings[$allMonth] = 0;
                        }
                    }
                }

                if ($lastEarnings > $hightEarn['highestEarning']) {
                    $hightEarn['highestEarning'] = $lastEarnings;
                }

                $lastEarnings = 0;
            }

            foreach ($monthEarnings as $me) {
                $dataarr[] = $me;
            }
        }

        else {//if no earning, set earning to 0
            foreach ($allMonths as $allMonth) {
                $dataarr[] = 0;
            }
        }

        //add info into array
        $json = array("total_earnings" => $dataarr, 'earningsYear'=>$year_to_fetch);

        //set final output based on where the request is coming from
        if($not_ajax){
            return $json;
        }

        else{
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
        }
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
     */
    function paymentMethodChart($year=''){
        $year_to_fetch = $year ? $year : date('Y');

        $payment_methods = $this->genmod->getPaymentMethods($year_to_fetch);

        $json['status'] = 0;
        $cash = 0;
        $pos = 0;
        $cash_and_pos = 0;
        $json['year'] = $year_to_fetch;

        if($payment_methods) {
            foreach ($payment_methods as $get) {
                if ($get->modeOfPayment == "Cash") {
                    $cash++;
                }

                else if ($get->modeOfPayment == "POS") {
                    $pos++;
                }

                else if($get->modeOfPayment === "Cash and POS"){
                    $cash_and_pos++;
                }
            }

            //calculate the percentage of each
            $total = $cash + $pos + $cash_and_pos;

            $cash_percentage = round(($cash/$total) * 100, 2);
            $pos_percentage =  round(($pos/$total) * 100, 2);
            $cash_and_pos_percentage = round(($cash_and_pos/$total) * 100, 2);

            $json['status'] = 1;
            $json['cash'] = $cash_percentage;
            $json['pos'] = $pos_percentage;
            $json['cashAndPos'] = $cash_and_pos_percentage;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
