<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class ProductPerformance extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->select('product_performance.id, product.Name ProductName, product_platform.Name PlatformName,
        customer.Name CustomerName, IFNULL(customer_project.Name,"") ProjectName,
        product_performance.Version, product_performance.Device, product_performance.Performance,
        product_performance.AddedDate, product_performance.UpdatedDate,
        product_performance.Power, product_performance.Resolution, product_performance.ReportDate, product_performance.Notes');

        $this->db->join('product', 'product_performance.ProductID = product.id');
        $this->db->join('product_platform', 'product_performance.PlatformID = product_platform.id');
        $this->db->join('customer', 'product_performance.CustomerID = customer.id');
        $this->db->join('customer_project', 'product_performance.ProjectID = customer_project.id', 'left');

        $run_q = $this->db->get('product_performance');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
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


    /**
     *
     * @param type $itemName
     * @param type $itemQuantity
     * @param type $itemPrice
     * @param type $itemDescription
     * @param type $itemCode
     * @return boolean
     */
    public function add($itemProduct, $itemPlatform, $itemDevice, $itemPerformance, $itemVersion, $itemResolution, $itemPower, $itemReportDate, $itemCustomer, $itemProject, $itemDesc) {
        $data = ['ProductID'=>$itemProduct, 'CustomerID'=>$itemCustomer, 'ProjectID'=>$itemProject, 'PlatformID'=>$itemPlatform, 'Device'=>$itemDevice, 'Performance'=>$itemPerformance,
        'Resolution'=>$itemResolution, 'Power'=>$itemPower, 'ReportDate'=>$itemReportDate, 'Version'=>$itemVersion, 'Notes'=>$itemDesc];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('product_performance', $data);

        if($this->db->insert_id()){
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function itemsearch($orderBy, $orderFormat, $value){
        $this->db->select('product_performance.id, product.Name ProductName, product_platform.Name PlatformName,
        product_performance.Version, product_performance.Device, product_performance.Performance,
        product_performance.AddedDate, product_performance.UpdatedDate,
        product_performance.Power, product_performance.Resolution, product_performance.ReportDate, product_performance.Notes');

        $this->db->join('product', 'product_performance.ProductID = product.id');
        $this->db->join('product_platform', 'product_performance.PlatformID = product_platform.id');
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->like('product.Name', $this->db->escape_like_str($value));
        $run_q = $this->db->get('product_performance');
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
            return FALSE;
        }
    }

   /**
    *
    * @param type $itemId
    * @param type $itemName
    * @param type $itemDesc
    * @param type $itemPrice
    */
   public function edit($itemId, $itemProduct, $itemPlatform, $itemDevice, $itemPerformance, $itemVersion, $itemResolution, $itemPower, $itemReportDate, $itemCustomer, $itemProject, $itemDesc) {
       $data = ['ProductID'=>$itemProduct, 'CustomerID'=>$itemCustomer, 'ProjectID'=>$itemProject, 'PlatformID'=>$itemPlatform, 'Device'=>$itemDevice, 'Performance'=>$itemPerformance,
       'Resolution'=>$itemResolution, 'Power'=>$itemPower, 'ReportDate'=>$itemReportDate, 'Version'=>$itemVersion, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('product_performance', $data);

       return TRUE;
   }


   public function getTagItems($orderBy, $orderFormat){
       $this->db->order_by($orderBy, $orderFormat);
       $this->db->select($orderBy);
       $this->db->group_by($orderBy);
       $this->db->where($orderBy.'!=', '');
       $run_q = $this->db->get('product_performance');

       if($run_q->num_rows() > 0){
           return $run_q->result();
       } else {
           return FALSE;
       }
   }

	public function getActiveItems($orderBy, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);
        $run_q = $this->db->get('product_performance');
        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
            return FALSE;
        }
    }
}
