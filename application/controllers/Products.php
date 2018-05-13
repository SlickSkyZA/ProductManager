<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Transactions
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Products extends CI_Controller{
    private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->genlib->AdminOnly();

        $this->load->model(['priority', 'productGroup', 'product']);
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function index(){
        $transData['priorities'] = $this->priority->getActiveItems('Name', 'ASC');//get items with at least one qty left, to be used when doing a new transaction
        $transData['product_group'] = $this->productGroup->getActiveItems('Name', 'ASC');

        $data['pageContent'] = $this->load->view('products/products', $transData, TRUE);
        $data['pageTitle'] = "Products";

        $this->load->view('main', $data);
    }

    /**
     * [add description]
     */
    public function add(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('itemName', 'Product Name', ['required', 'trim', 'max_length[80]', 'is_unique[product.Name]'], //numeric
                ['required'=>"required"]);
        $this->form_validation->set_rules('itemGroup', 'Product Group', ['required', 'trim', 'numeric'], ['required'=>"required"]);
        $this->form_validation->set_rules('itemPriority', 'Priority', ['required', 'trim', 'numeric'], ['required'=>"required"]);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction

            $itemName = set_value('itemName');
            $itemGroupID = set_value('itemGroup');
            $itemPriorityID = set_value('itemPriority');
            $itemDPM = set_value('itemDPM');
            $itemQPM = set_value('itemQPM');
            $itemPM = set_value('itemPM');
            $itemDesc = set_value('itemDesc');
            /**
             * insert info into db
             * function header: add($itemName, $itemQuantity, $itemPrice, $itemDescription, $itemCode)
             */
            $insertedId = $this->product->add($itemName, $itemGroupID, $itemPriorityID, $itemDPM, $itemQPM, $itemPM, $itemDesc);

            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "New addition of product:{$itemName}";

            $insertedId ? $this->genmod->addevent("Creation of new product", $insertedId, $desc, "product", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                    ['status'=>1, 'msg'=>"Product successfully added"]
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
     * @return [type] [description]
     */
    public function lilt(){
        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "Name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";

        //count the total number of items in db
        $totalItems = $this->db->count_all('product');

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "products/lilt", $limit, ['onclick'=>'return lilt(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //get all items from db
        $data['allItems'] = $this->product->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('products/productslisttable', $data, TRUE);//get view with populated items table

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
        $this->form_validation->set_rules('itemName', 'Product Name', ['required', 'trim',
             'callback_crosscheckName['.$this->input->post('_iId', TRUE).']'], ['required'=>'required']);
        $this->form_validation->set_rules('itemDesc', 'Product Description', ['trim']);
        $this->form_validation->set_rules('itemVersion', 'Product Version', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $itemId = set_value('_iId');
            $itemDesc = set_value('itemDesc');
            $itemName = set_value('itemName');
            $itemGroupID = set_value('itemGroupID');
            $itemPriorityID = set_value('itemPriorityID');
            $itemDPM = set_value('itemDPM');
            $itemQPM = set_value('itemQPM');
            $itemPM = set_value('itemPM');

            //update item in db
            $updated = $this->product->edit($itemId, $itemName, $itemGroupID, $itemPriorityID, $itemDPM, $itemQPM, $itemPM, $itemDesc);

            $json['status'] = $updated ? 1 : 0;

            //add event to log
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "Details of item with code '$itemId' was updated";

            $this->genmod->addevent("Group Info Update", $itemId, $desc, 'product group', $this->session->admin_id);
        } else {
            $json['status'] = 0;
            $json = $this->form_validation->error_array();
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * [crosscheckName description]
     * @param  [type] $itemName [description]
     * @param  [type] $itemId   [description]
     * @return [type]           [description]
     */
    public function crosscheckName($itemName, $itemId){
        //check db to ensure name was previously used for the item we are updating
        $itemWithName = $this->genmod->getTableCol('product', 'id', 'Name', $itemName);

        //if item name does not exist or it exist but it's the name of current item
        if(!$itemWithName || ($itemWithName == $itemId)){
            return TRUE;
        } else {//if it exist
            $this->form_validation->set_message('crosscheckName', 'There is an item with this name');

            return FALSE;
        }
    }


    public function delete(){
        $this->genlib->ajaxOnly();

        $json['status'] = 0;
        $item_id = $this->input->post('i', TRUE);

        if($item_id){
            $this->db->where('id', $item_id)->delete('product');
            $json['status'] = 1;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
