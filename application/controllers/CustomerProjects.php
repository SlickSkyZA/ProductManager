<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Items
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class CustomerProjects extends CI_Controller{

    public function __construct(){
        parent::__construct();

        $this->genlib->checkLogin();

        $this->genlib->AdminOnly();

        $this->load->model(['customerProject', 'company', 'RelCustomerAssembly', 'RelCustomerModule']);
    }

    /**
     * initial info
     * @return [type] [description]
     */
    public function index(){
        $transData['camera_vender'] = $this->company->getCompanyByType('Name', 'Camera Module Factory', 'ASC');
        $transData['camera_assembly'] = $this->company->getCompanyByType('Name', 'Assembly Factory', 'ASC');
        $transData['customers'] = $this->company->getActiveItems('Name', 'Customer', 'ASC');
        $transData['soc_companies'] = $this->customerProject->getSortedItems('SOCCompany', 'ASC');
        $transData['soc_names'] = $this->customerProject->getSortedItems('SOCName', 'ASC');
        $transData['hardware_DSP'] = $this->customerProject->getSortedItems('DSP', 'ASC');
        $transData['hardware_GPU'] = $this->customerProject->getSortedItems('GPU', 'ASC');
        $transData['hardware_RAM'] = $this->customerProject->getSortedItems('RAM', 'ASC');
        $transData['camera_type0'] = $this->customerProject->getSortedItems('FrontCameraType', 'ASC');
        $transData['camera_res0'] = $this->customerProject->getSortedItems('FrontCameraRes', 'ASC');
        $transData['camera_type1'] = $this->customerProject->getSortedItems('RearCameraType', 'ASC');
        $transData['camera_res1'] = $this->customerProject->getSortedItems('RearCameraRes','ASC');

        $data['pageContent'] = $this->load->view('customerProjects/customerProjects', $transData, TRUE);
        $data['pageTitle'] = "Customer Projects";

        $this->load->view('main', $data);
    }

    /**
     * "lilt" = "load Items List Table"
     */
    public function lilt(){
        $this->genlib->ajaxOnly();

        $this->load->helper('text');

        //set the sort order
        $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "Name";
        $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";
        $filter = $this->input->get('filter', TRUE) ? $this->input->get('filter', TRUE) : "";

        //count the total number of items in db
        $totalItems = $this->customerProject->countAll($filter);

        $this->load->library('pagination');

        $pageNumber = $this->uri->segment(3, 0);//set page number to zero if the page number is not set in the third segment of uri

        $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10;//show $limit per page
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;//start from 0 if pageNumber is 0, else start from the next iteration

        //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
        $config = $this->genlib->setPaginationConfig($totalItems, "customerProjects/lilt", $limit, ['onclick'=>'return lilt(this.href);']);

        $this->pagination->initialize($config);//initialize the library class

        //get all items from db
        if ($filter == "") {
            $data['allItems'] = $this->customerProject->getAll($orderBy, $orderFormat, $start, $limit);
        } else {
            $data['allItems'] = $this->customerProject->getAll($orderBy, $orderFormat, $start, $limit, $filter);
        }
        $data['range'] = $totalItems > 0 ? "Showing " . ($start+1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
        $data['links'] = $this->pagination->create_links();//page links
        $data['sn'] = $start+1;

        $json['itemsListTable'] = $this->load->view('customerProjects/customerProjectslisttable', $data, TRUE);//get view with populated items table

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * [add description]
     */
    public function add(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('itemName', 'Project Name', ['required', 'trim', 'max_length[80]', 'is_unique[customer_project.Name]'], //numeric
                ['required'=>"required"]);
        $this->form_validation->set_rules('itemSOCCompany', 'SOCCompany', ['trim']);
        $this->form_validation->set_rules('itemSOCName', 'SOCName', ['trim']);
        $this->form_validation->set_rules('itemGPU', 'GPU', ['trim']);
        $this->form_validation->set_rules('itemDSP', 'DSP', ['trim']);
        $this->form_validation->set_rules('itemRAM', 'RAM', ['trim']);
        $this->form_validation->set_rules('itemCamera0', 'Camera0', ['trim']);
        $this->form_validation->set_rules('itemCamera1', 'Camera1', ['trim']);
        $this->form_validation->set_rules('itemCamera0Res', 'Camera0Res', ['trim']);
        $this->form_validation->set_rules('itemCamera1Res', 'Camera1Res', ['trim']);
        $this->form_validation->set_rules('itemDesc', 'Item Description', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $this->db->trans_start();//start transaction
            $itemName = set_value('itemName');
            $itemCustomer = set_value('itemCustomer');
            $itemSOCCompany = set_value('itemSOCCompany');
            $itemSOCName = set_value('itemSOCName');
            $itemGPU = set_value('itemGPU');
            $itemDSP = set_value('itemDSP');
            $itemRAM = set_value('itemRAM');
            $itemCamera0 = set_value('itemCamera0');
            $itemCamera1 = set_value('itemCamera1');
            $itemCamera0Res = set_value('itemCamera0Res');
            $itemCamera1Res = set_value('itemCamera1Res');
            $itemStartDate = set_value('itemStartDate');
            $itemMPDate = set_value('itemMPDate');
            $itemShipDate = set_value('itemShipDate');
            $itemDesc = set_value('itemDesc');
            $itemCamAssemblies = set_value('itemCamAssemblies');
            $itemCamModules = set_value('itemCamModules');

            /**
             * insert info into db
             * function header: add($itemName, $itemQuantity, $itemPrice, $itemDescription, $itemCode)
             */
            $insertedId = $this->customerProject->add($itemName, $itemCustomer, $itemSOCCompany, $itemSOCName, $itemGPU,
            $itemCamera0Res, $itemCamera1Res, $itemStartDate, $itemMPDate, $itemShipDate, $itemDSP, $itemRAM, $itemCamera0, $itemCamera1, $itemDesc);

            $this->RelCustomerAssembly->add($insertedId, $itemCamAssemblies);
            $this->RelCustomerModule->add($insertedId, $itemCamModules);

            //insert into eventlog
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "New addition of type:{$itemName}";

            $insertedId ? $this->genmod->addevent("Creation of new vernder", $insertedId, $desc, "Customer Type", $this->session->admin_id) : "";

            $this->db->trans_complete();

            $json = $this->db->trans_status() !== FALSE ?
                    ['status'=>1, 'msg'=>"Type successfully added"]
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

    /**
     * [edit description]
     * @return [type] [description]
     */
    public function edit(){
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric']);
        $this->form_validation->set_rules('itemName', 'Vender Name', ['required', 'trim', 'max_length[80]'], ['required'=>'required']);
        $this->form_validation->set_rules('itemCustomer', 'Customer ID', ['required', 'trim', 'numeric']);
        $this->form_validation->set_rules('itemSOCCompany', 'SOCCompany', ['trim']);
        $this->form_validation->set_rules('itemSOCName', 'SOCName', ['trim']);
        //$this->form_validation->set_rules('itemGPU', 'GPU', ['trim']);
        //$this->form_validation->set_rules('itemDSP', 'DSP', ['trim']);
        //$this->form_validation->set_rules('itemRAM', 'RAM', ['trim']);
        //$this->form_validation->set_rules('itemCamera0', 'Camera0', ['trim']);
        //$this->form_validation->set_rules('itemCamera1', 'Camera1', ['trim']);
        //$this->form_validation->set_rules('itemCamera0Res', 'Camera0Res', ['trim']);
        //$this->form_validation->set_rules('itemCamera1Res', 'Camera1Res', ['trim']);
        //$this->form_validation->set_rules('itemDesc', 'Item Description', ['trim']);

        if($this->form_validation->run() !== FALSE){
            $itemId = set_value('_iId');
            $itemDesc = set_value('itemDesc');
            $itemCustomer = set_value('itemCustomer');
            $itemSOCCompany = set_value('itemSOCCompany');
            $itemSOCName = set_value('itemSOCName');
            $itemGPU = set_value('itemGPU');
            $itemDSP = set_value('itemDSP');
            $itemRAM = set_value('itemRAM');
            $itemCamera0 = set_value('itemCamera0');
            $itemCamera1 = set_value('itemCamera1');
            $itemCamera0Res = set_value('itemCamera0Res');
            $itemCamera1Res = set_value('itemCamera1Res');
            $itemStartDate = set_value('itemStartDate');
            $itemMPDate = set_value('itemMPDate');
            $itemShipDate = set_value('itemShipDate');
            $itemName = set_value('itemName');

            $itemCamModules = set_value('itemCamModules');
            $itemCamAssemblies = set_value('itemCamAssemblies');

            //update item in db
            $updated = $this->customerProject->edit($itemId, $itemName, $itemCustomer, $itemSOCCompany, $itemSOCName,
            $itemGPU, $itemDSP, $itemRAM, $itemCamera0, $itemCamera1, $itemCamera0Res, $itemCamera1Res, $itemStartDate,
            $itemMPDate, $itemShipDate, $itemDesc);

            $this->RelCustomerAssembly->edit($itemId, $itemCamAssemblies);
            $this->RelCustomerModule->edit($itemId, $itemCamModules);

            $json['status'] = $updated ? 1 : 0;

            //add event to log
            //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
            $desc = "Details of item with code '$itemId' was updated";

            $this->genmod->addevent("Customer Project Info Update", $itemId, $desc, 'Customer Project', $this->session->admin_id);
        }

        else{
            $json['status'] = 0;
            $json = $this->form_validation->error_array();
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }


    /**
     * remove customer's project
     * @return [type] [description]
     */
    public function delete(){
        $this->genlib->ajaxOnly();

        $json['status'] = 0;
        $item_id = $this->input->post('i', TRUE);

        if($item_id){
            $this->db->where('id', $item_id)->delete('customer_project');
            $this->db->where('CustomerProjectID', $item_id)->delete('rel_customer_project_module');
            $this->db->where('CustomerProjectID', $item_id)->delete('rel_customer_project_assembly');
            $json['status'] = 1;
        }

        //set final output
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
