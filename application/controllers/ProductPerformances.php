<?php
defined('BASEPATH') OR exit('');
require_once 'functions.php';
/**
 * Description of Transactions
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class ProductPerformances extends CI_Controller{
    private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->genlib->QAMgrOnly();

        $this->load->model(['company', 'customerProject', 'platform', 'product', 'productPerformance']);
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function index(){
        $transData['platforms'] = $this->platform->getActiveItems('Name', 'ASC');
        $transData['products'] = $this->product->getActiveItems('Name', 'ASC');
        $transData['devices'] = $this->productPerformance->getSortedItems('Device', 'ASC');
        $transData['resolutions'] = $this->productPerformance->getSortedItems('Resolution', 'ASC');
        $transData['customers'] = $this->company->getActiveItems('Name', 'Customer', 'ASC');
        $transData['customerProjects'] = $this->customerProject->getActiveItems('Name', 'ASC');

        $data['pageContent'] = $this->load->view('productPerformances/productPerformances', $transData, TRUE);
        $data['pageTitle'] = "Product Performance";

        $this->load->view('main', $data);
    }


    /**
     * [add new record]
     */
    public function add(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('itemProduct', 'ProductID', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemPlatform', 'PlatformID', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemDevice', 'Device', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemVersion', 'Version', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemResolution', 'Resolution', ['required', 'trim', 'max_length[255]'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemReportDate', 'ReportDate', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction

            $itemProduct = set_value('itemProduct');
            $itemPlatform = set_value('itemPlatform');
            $itemDevice = set_value('itemDevice');
            $itemPerformance = set_value('itemPerformance');
            $itemVersion = set_value('itemVersion');
            $itemResolution = set_value('itemResolution');
            $itemPower = set_value('itemPower');
            $itemReportDate = set_value('itemReportDate');
            $itemDesc = set_value('itemDesc');
            $itemCustomer = set_value('itemCustomer');
            $itemProject = set_value('itemProject');

            $insertedId = $this->productPerformance->add($itemProduct, $itemPlatform, $itemDevice, $itemPerformance,
            $itemVersion, $itemResolution, $itemPower, $itemReportDate, $itemCustomer, $itemProject, $itemDesc);

            $itemName = set_value('itemProduct');

            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "New addition performance of product:{$itemName}";

            $insertedId ? $this->genmod->addevent("Creation of new performance", $insertedId, $desc, "issue", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                    ['status'=>1, 'msg'=>"Issue successfully added"]
                    :
                    ['status'=>0, 'msg'=>"Oops! Unexpected server error! Please contact administrator for help. Sorry for the embarrassment"];
        }

        else{
            //return all error messages
            $json = $this->form_validation->error_array();//get an array of all errors

            $json['msg'] = "One or more required fields are empty or not correctly filled";
            $json['status'] = 0;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    /**
     * "lilt" = "load Items List Table"
     */
    public function lilt(){
        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "Name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";

        //count the total number of items in db
        $totalItems = $this->db->count_all('product_performance');

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "productPerformances/lilt", $limit, ['onclick'=>'return lilt(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //get all items from db
        $data['allItems'] = $this->productPerformance->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('productPerformances/productPerformanceslisttable', $data, TRUE);//get view with populated items table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

     public function edit(){
         $this->genlib->ajaxOnly();

         $this->load->library('form_validation');

         $this->form_validation->set_error_delimiters('', '');
        //itemProduct:itemProduct, itemPlatform:itemPlatform, itemDevice:itemDevice, itemPerformance:itemPerformance,
        //    itemPower:itemPower, itemResolution:itemResolution, itemVersion:itemVersion,
        //    itemReportDate:itemReportDate, itemDesc:itemDesc
         $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric']);
         $this->form_validation->set_rules('itemProduct', 'ProductID', ['required', 'trim', 'numeric'], ['required'=>"required"]);
         $this->form_validation->set_rules('itemPlatform', 'PlatformID', ['required', 'trim', 'numeric'], ['required'=>"required"]);
         $this->form_validation->set_rules('itemDevice', 'Device', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);
         $this->form_validation->set_rules('itemVersion', 'Version', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);
         $this->form_validation->set_rules('itemResolution', 'Resolution', ['required', 'trim', 'max_length[255]'], ['required'=>"required"]);
         $this->form_validation->set_rules('itemReportDate', 'ReportDate', ['required', 'trim', 'max_length[80]'], ['required'=>"required"]);

         if($this->form_validation->run() !== FALSE){
             $itemId = set_value('_iId');

             $itemProduct = set_value('itemProduct');
             $itemPlatform = set_value('itemPlatform');
             $itemDevice = set_value('itemDevice');
             $itemPerformance = set_value('itemPerformance');
             $itemVersion = set_value('itemVersion');
             $itemResolution = set_value('itemResolution');
             $itemPower = set_value('itemPower');
             $itemReportDate = set_value('itemReportDate');
             $itemDesc = set_value('itemDesc');
             $itemCustomer = set_value('itemCustomer');
             $itemProject = set_value('itemProject');

             //update item in db
             $updated = $this->productPerformance->edit($itemId, $itemProduct, $itemPlatform, $itemDevice, $itemPerformance, $itemVersion,
             $itemResolution, $itemPower, $itemReportDate, $itemCustomer, $itemProject, $itemDesc);

             $json['status'] = $updated ? 1 : 0;

             //add event to log
             //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
             $desc = "Details of item with code '$itemId' was updated";

             $this->genmod->addevent("Group Info Update", $itemId, $desc, 'product group', $this->session->admin_id);
         }

         else{
             $json['status'] = 0;
             $json = $this->form_validation->error_array();
         }

         $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

     public function crosscheckName($itemName, $itemId){
         //check db to ensure name was previously used for the item we are updating
         $itemWithName = $this->genmod->getTableCol('product', 'id', 'Name', $itemName);

         //if item name does not exist or it exist but it's the name of current item
         if(!$itemWithName || ($itemWithName == $itemId)){
             return TRUE;
         }

         else{//if it exist
             $this->form_validation->set_message('crosscheckName', 'There is an item with this name');

             return FALSE;
         }
     }

     /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */


     public function delete(){
         $this->genlib->ajaxOnly();

         $json['status'] = 0;
         $item_id = $this->input->post('i', TRUE);

         if($item_id){
             $this->db->where('id', $item_id)->delete('product_performance');

             $json['status'] = 1;
         }

         //set final output
         $this->output->set_content_type('application/json')->set_output(json_encode($json));
     }

     /**
      * [active issue or not]
      * @return [type] [description]
      */
    public function active() {
        $this->genlib->ajaxOnly();

        $item_id = $this->input->post('itemId');
        $new_status = $this->genmod->gettablecol('product_issue', 'Active', 'id', $item_id) == 1 ? 0 : 1;

        $json['status'] = 0;

        if($item_id){
            $this->db->where('id', $item_id)->set('Active', $new_status)->update('product_issue');

            $json['status'] = 1;
            $json['_ns'] = $new_status;
            $json['_aId'] = $item_id;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }


}
