<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class ProductTransaction extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    /**
     * [getAll description]
     * @param  [type]  $orderBy     [description]
     * @param  [type]  $orderFormat [description]
     * @param  integer $start       [description]
     * @param  string  $limit       [description]
     * @return [type]               [description]
     */
    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->select('xscp.id, product.Name ProductName, company.Name CustomerName, IFNULL(competittor.Name,"") CompetitorName,
        priority.Name PriorityName, product_status.Name StatusName, priority.Value PriorityValue, product_platform.Name PlatformName,
        IFNULL(customer_project.Name,"") ProjectName, xscp.MilestoneDate, xscp.AddedDate, xscp.UpdatedDate, xscp.Notes');

        $this->db->join('product', 'xscp.ProductID = product.id');
        $this->db->join('company', 'xscp.CustomerID = company.id');
        $this->db->join('company as competittor', 'xscp.VenderID = competittor.id', 'left');
        $this->db->join('customer_project', 'xscp.ProjectID = customer_project.id', 'left');
        $this->db->join('product_status', 'xscp.StatusID = product_status.id');
        $this->db->join('priority', 'xscp.PriorityID = priority.id');
        $this->db->join('product_platform', 'xscp.PlatformID = product_platform.id');

        $run_q = $this->db->get('xscp');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
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
    public function add($product, $customer, $priority, $platform, $status, $competitor, $projectName, $itemMilestone, $description){
        $data = ['ProductID'=>$product, 'CustomerID'=>$customer, 'PriorityID'=>$priority, 'PlatformID'=>$platform,
        'StatusID'=>$status, 'VenderID'=>$competitor, 'ProjectID'=>$projectName, 'Notes'=>$description];

        if ($itemMilestone !== "") {
            $this->db->set('MilestoneDate', $itemMilestone);
        } else {
            $this->db->set('MilestoneDate', NULL);
        }
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('xscp', $data);

        if($this->db->insert_id()){
            return $this->db->insert_id();
        }

        else{
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
     * @param type $value
     * @return boolean
     */
    public function itemsearch($value){
        $q = "SELECT xscp.id, product.Name ProductName, customer.Name CustomerName, IFNULL(customer_vender.Name,'') CompetitorName,
                    priority.Name PriorityName, product_status.Name StatusName, priority.Value PriorityValue, product_platform.Name PlatformName,
                    IFNULL(customer_project.Name,'') ProjectName, xscp.MilestoneDate, xscp.AddedDate, xscp.UpdatedDate, xscp.Notes
                FROM xscp
                join product ON xscp.ProductID = product.id
                join customer ON xscp.CustomerID = customer.id
                left join customer_vender ON xscp.VenderID = customer_vender.id
                left join customer_project ON xscp.ProjectID = customer_project.id
                join product_status ON xscp.StatusID = product_status.id
                join priority ON xscp.PriorityID = priority.id
                join product_platform ON xscp.PlatformID = product_platform.id
                WHERE
                product.Name LIKE '%".$this->db->escape_like_str($value)."%'";

        $run_q = $this->db->query($q, [$value, $value]);

        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
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
    * @param type $itemId
    * @param type $itemName
    * @param type $itemDesc
    * @param type $itemPrice
    */
   public function edit($itemId, $itemProductID, $itemCustomerID, $itemPriorityID, $itemPlatformID, $itemStatusID, $itemCompetitorID, $itemProjectName, $itemMilestone, $itemDesc){
       $data = ['ProductID'=>$itemProductID, 'CustomerID'=>$itemCustomerID, 'PriorityID'=>$itemPriorityID, 'PlatformID'=>$itemPlatformID,
       'StatusID'=>$itemStatusID, 'VenderID'=>$itemCompetitorID, 'ProjectID'=>$itemProjectName, 'Notes'=>$itemDesc];
       if ($itemMilestone !== "") {
           $this->db->set('MilestoneDate', $itemMilestone);
       } else {
           $this->db->set('MilestoneDate', NULL);
       }
       $this->db->where('id', $itemId);
       $this->db->update('xscp', $data);

       return TRUE;
   }

}
