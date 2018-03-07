<?php
defined('BASEPATH') OR exit('');
require_once 'functions.php';
/**
 * Description of Transactions
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class ProductTransactions extends CI_Controller{
    private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->load->model(['productTransaction', 'priority', 'product', 'customer', 'platform', 'customerVender','productStatus','customerProject']);
    }

    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    public function index(){
        $transData['priorities'] = $this->priority->getActiveItems('Name', 'ASC');//get items with at least one qty left, to be used when doing a new transaction
        $transData['products'] = $this->product->getActiveItems('Name', 'ASC');
        $transData['customers'] = $this->customer->getActiveItems('Name', 'ASC');
        $transData['product_platforms'] = $this->platform->getActiveItems('Name', 'ASC');
        $transData['customer_venders'] = $this->customerVender->getActiveItems('Name', 'ASC');
        $transData['customer_projects'] = $this->customerProject->getActiveItems('Name', 'ASC');
        $transData['product_statuses'] = $this->productStatus->getActiveItems('Name', 'ASC');

        $data['pageContent'] = $this->load->view('productTransactions/productTransactions', $transData, TRUE);
        $data['pageTitle'] = "Product Transactions";

        $this->load->view('main', $data);
    }

    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    public function add(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('itemDesc', 'Item Description', ['trim']);
        $this->form_validation->set_rules('itemCustomer', 'Customer ID', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction
            $product = set_value('itemProduct');
            $customer = set_value('itemCustomer');
            $priority = set_value('itemPriority');
            $platform = set_value('itemPlatform');
            $status = set_value('itemStatus');
            $competitor = set_value('itemCompetitor');
            $projectName = set_value('itemProjectName');
            $itemMilestone = set_value('itemMilestone');
            $description = set_value('itemDesc');
            /**
             * insert info into db
             * function header: add($itemName, $itemQuantity, $itemPrice, $itemDescription, $itemCode)
             */
            $insertedId = $this->productTransaction->add($product, $customer, $priority, $platform, $status, $competitor, $projectName, $itemMilestone, $description);

            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "New addition of product transaction:{$product}, {$itemMilestone}";

            $insertedId ? $this->genmod->addevent("Creation of new product", $insertedId, $desc, "Product Transaction", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                    ['status'=>1, 'msg'=>"Product transaction successfully added"]
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
        $totalItems = $this->db->count_all('xscp');

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "productTransactions/lilt", $limit, ['onclick'=>'return lilt(this.href);']);

        $this->pagination->initialize($config);//initialize the library class


        //get all items from db
        $data['allItems'] = $this->productTransaction->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('productTransactions/productTranstable', $data, TRUE);//get view with populated items table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
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
            $this->db->where('id', $item_id)->delete('xscp');

            $json['status'] = 1;
        }

        //set final output
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
        //itemName:itemName, itemGroupID:itemGroupID, itemPriorityID:itemPriorityID, itemVersion:itemVersion, itemDesc:itemDesc, _iId:itemId
         $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric']);
         $this->form_validation->set_rules('itemDesc', 'Product Description', ['trim']);
         $this->form_validation->set_rules('itemProject', 'Project', ['trim']);

         if($this->form_validation->run() !== FALSE){
             $itemId = set_value('_iId');
             $itemProductID = set_value('itemProduct');
             $itemCustomerID = set_value('itemCustomer');
             $itemPriorityID = set_value('itemPriority');
             $itemPlatformID = set_value('itemPlatform');
             $itemStatusID = set_value('itemStatus');
             $itemCompetitorID = set_value('itemCompetitor');
             $itemProjectName = set_value('itemProjectName');
             $itemDesc = set_value('itemDesc');
             $itemMilestone = set_value('itemMilestone');

             //update item in db
             $updated = $this->productTransaction->edit($itemId, $itemProductID, $itemCustomerID,
             $itemPriorityID, $itemPlatformID, $itemStatusID, $itemCompetitorID, $itemProjectName, $itemMilestone, $itemDesc);

             $json['status'] = $updated ? 1 : 0;

             //add event to log
             //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
             $desc = "Details of item with code '$itemId' was updated";

             $this->genmod->addevent("Product transaction Update", $itemId, $desc, 'Product transaction', $this->session->admin_id);
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


    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

}
