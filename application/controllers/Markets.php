<?php
defined('BASEPATH') OR exit('');
require_once 'functions.php';
/**
 * Description of Transactions
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Markets extends CI_Controller{
    private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->genlib->AdminOnly();

        $this->load->model(['Market', 'RelProductCompetitor', 'product', 'company', 'platform', 'productStatus','customerProject']);
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function index(){
        //get items with at least one qty left, to be used when doing a new transaction
        $transData['products'] = $this->product->getActiveItems('Name', 'ASC');
        $transData['customers'] = $this->company->getActiveItems('Name', 'Customer', 'ASC');
        $transData['platforms'] = $this->platform->getActiveItems('Name', 'ASC');
        $transData['venders'] = $this->company->getCompanyByType('Name', 'ASC', 'Algorithm');
        $transData['competitors'] = $this->company->getActiveItems('Name', 'Competitor', 'ASC');
        $transData['customer_projects'] = $this->customerProject->getActiveItems('Name', 'ASC');
        $transData['product_statuses'] = $this->productStatus->getActiveItems('Name', 'ASC');

        $data['pageContent'] = $this->load->view('markets/markets', $transData, TRUE);
        $data['pageTitle'] = "Market";

        $this->load->view('main', $data);
    }

    /**
     * [customer_project_filter description]
     * @return [type] [description]
     */
    public function customer_project_filter() {

        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $customer = $this->input->get('customer', TRUE) ? $this->input->get('customer', TRUE) : "";

        //get all items from db
        $data['allItems'] = $this->customerProject->getProjects('CustomerID', $customer);

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * 添加一条记录
     */
    public function add() {
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('itemDesc', 'Item Description', ['trim']);
        $this->form_validation->set_rules('itemCustomer', 'Customer ID', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction
            $itemProduct = set_value('itemProduct');
            $customer = set_value('itemCustomer');
            $platform = set_value('itemPlatform');
            $status = set_value('itemStatus');
            $itemCompetitor = set_value('itemCompetitor');
            $itemVender = set_value('itemVender');
            $itemProject = set_value('itemProjectName');
            $itemDate = set_value('itemMilestone');
            $itemDesc = set_value('itemDesc');
            log_message('debug', print_r("itemVender:".$itemVender, true));
            $insertedId = $this->Market->add($itemProduct, $customer, $platform, $status, $itemVender, $itemProject, $itemDate, $itemDesc);

            $this->RelProductCompetitor->add($insertedId, $itemCompetitor);
            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "New addition of product transaction:{$itemProduct}, {$itemDate}";

            $insertedId ? $this->genmod->addevent("Creation of new product", $insertedId, $desc, "Product Transaction", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                    ['status'=>1, 'msg'=>"Product transaction successfully added"]
                    :
                    ['status'=>0, 'msg'=>"Oops! Unexpected server error! Please contact administrator for help. Sorry for the embarrassment"];
        } else {
            //return all error messages
            $json = $this->form_validation->error_array();//get an array of all errors

            $json['msg'] = "One or more required fields are empty or not correctly filled";
            $json['status'] = 0;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * "lilt" = "load Items List Table"
     */
    public function lilt() {

        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "Name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";
        $filter = $this->input->get('filter', TRUE) ? $this->input->get('filter', TRUE) : "";
        $active = $this->input->get('active', TRUE) ? $this->input->get('active', TRUE) : "true";
        if ($active == 'true') { // == '1'
            $status = "1";
        } else {
            $status = "0";
        }
        //count the total number of items in db
        $totalItems = $this->Market->countAll($filter, $status);

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "markets/lilt", $limit, ['onclick'=>'return lilt(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //get all items from db
        $data['allItems'] = $this->Market->getAll($orderBy, $orderFormat, $start, $limit, $filter, $status);
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('markets/marketstable', $data, TRUE);//get view with populated items table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * [active issue or not]
     * @return [type] [description]
     */
    public function active() {
       $this->genlib->ajaxOnly();

       $item_id = $this->input->post('itemId');
       $new_status = $this->genmod->gettablecol('market', 'Active', 'id', $item_id) == 1 ? 0 : 1;

       $json['status'] = 0;

       if($item_id){
           $this->db->where('id', $item_id)->set('Active', $new_status)->update('market');

           $json['status'] = 1;
           $json['_ns'] = $new_status;
           $json['_aId'] = $item_id;
       }

       //set final output
       $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * 删除一条记录
     * @return [type] [description]
     */
    public function delete(){
        $this->genlib->ajaxOnly();

        $json['status'] = 0;
        $item_id = $this->input->post('i', TRUE);

        if($item_id){
            $this->db->where('id', $item_id)->delete('market');
            $this->db->where('MarketID', $item_id)->delete('rel_project_product_competitor');

            $json['status'] = 1;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * [edit description]
     * @return [type] [description]
     */
    public function edit(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');
        //itemName:itemName, itemGroupID:itemGroupID, itemPriorityID:itemPriorityID, itemVersion:itemVersion, itemDesc:itemDesc, _iId:itemId
        $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric']);
        $this->form_validation->set_rules('itemDesc', 'Product Description', ['trim']);
        $this->form_validation->set_rules('itemProject', 'Project', ['trim']);
        $this->form_validation->set_rules('itemStatusDate', 'Project Milestone', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $itemId = set_value('_iId');
            $itemProductID = set_value('itemProduct');
            $itemCustomerID = set_value('itemCustomer');
            $itemPlatformID = set_value('itemPlatform');
            $itemStatusID = set_value('itemStatus');
            $itemCompetitorID = set_value('itemCompetitor');
            $itemVenderID = set_value('itemVender');
            $itemProjectID = set_value('itemProject');
            $itemDesc = set_value('itemDesc');
            $itemStatusDate = set_value('itemStatusDate');
            $itemTransport = set_value('itemTransport');

            log_message('debug', print_r("itemTransport=".$itemTransport, true));

            //update item in db
            $updated = $this->Market->edit($itemId, $itemProductID, $itemCustomerID, $itemPlatformID, $itemVenderID, $itemStatusID, $itemProjectID, $itemStatusDate, $itemDesc, $itemTransport);

            $this->RelProductCompetitor->edit($itemId, $itemCompetitorID);

            $json['status'] = $updated ? 1 : 0;

            //add event to log
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "Details of item with code '$itemId' was updated, $itemStatusDate";

            $this->genmod->addevent("Product transaction Update", $itemId, $desc, 'Product transaction', $this->session->admin_id);
        } else {
            $json['status'] = 0;
            $json = $this->form_validation->error_array();
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

}
