<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class ProductIssue extends CI_Model{
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
        $this->db->select('product_issue.id, product.Name ProductName, customer.Name CustomerName, IFNULL(customer_project.Name,"") ProjectName,
        product_issue.Version, priority.Name PriorityName, priority.Value PriorityValue, product_issue.AddedDate, product_issue.UpdatedDate,
        product_issue.IssueType, product_issue.Active, product_issue.ReportDate, product_issue.Notes');

        $this->db->join('product', 'product_issue.ProductID = product.id');
        $this->db->join('customer', 'product_issue.CustomerID = customer.id');
        $this->db->join('customer_project', 'product_issue.ProjectID = customer_project.id', 'left');
        $this->db->join('priority', 'product_issue.PriorityID = priority.id');

        $run_q = $this->db->get('product_issue');

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
    public function add($itemProduct, $itemCustomer, $itemPriority, $itemProject, $itemVersion, $itemIssueType, $itemReportDate, $itemDesc){
        $data = ['CustomerID'=>$itemCustomer, 'PriorityID'=>$itemPriority, 'ProductID'=>$itemProduct, 'ProjectID'=>$itemProject,
        'IssueType'=>$itemIssueType, 'ReportDate'=>$itemReportDate, 'Version'=>$itemVersion, 'Notes'=>$itemDesc];

        $this->db->set('Active', TRUE);
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('product_issue', $data);

        if($this->db->insert_id()){
            return $this->db->insert_id();
        }

        else{
            return FALSE;
        }
    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function itemsearch($orderBy, $orderFormat, $value){
        $this->db->select('product_issue.id, product.Name ProductName, customer.Name CustomerName, IFNULL(customer_project.Name,"") ProjectName,
        product_issue.Version, priority.Name PriorityName, priority.Value PriorityValue, product_issue.AddedDate, product_issue.UpdatedDate,
        product_issue.IssueType, product_issue.Active, product_issue.ReportDate, product_issue.Notes');

        $this->db->join('product', 'product_issue.ProductID = product.id');
        $this->db->join('customer', 'product_issue.CustomerID = customer.id');
        $this->db->join('customer_project', 'product_issue.ProjectID = customer_project.id', 'left');
        $this->db->join('priority', 'product_issue.PriorityID = priority.id');

        $this->db->order_by($orderBy, $orderFormat);
        $this->db->like('product.Name', $this->db->escape_like_str($value));
        $run_q = $this->db->get('product_issue');
        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
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
   public function edit($itemId, $itemProduct, $itemCustomer, $itemPriority, $itemProject, $itemVersion, $itemIssueType, $itemReportDate, $itemDesc){
       $data = ['CustomerID'=>$itemCustomer, 'PriorityID'=>$itemPriority, 'ProductID'=>$itemProduct, 'ProjectID'=>$itemProject,
       'IssueType'=>$itemIssueType, 'ReportDate'=>$itemReportDate, 'Version'=>$itemVersion, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('product_issue', $data);

       return TRUE;
   }


   public function getTagItems($orderBy, $orderFormat){
       $this->db->order_by($orderBy, $orderFormat);
       $this->db->select($orderBy);
       $this->db->group_by($orderBy);
       $this->db->where($orderBy.'!=', '');
       $run_q = $this->db->get('product_issue');

       if($run_q->num_rows() > 0){
           return $run_q->result();
       } else {
           return FALSE;
       }
   }

	public function getActiveItems($orderBy, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);
        $run_q = $this->db->get('product_issue');
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
     * array $where_clause
     * array $fields_to_fetch
     *
     * return array | FALSE
     */
    public function getItemInfo($where_clause, $fields_to_fetch){
        $this->db->select($fields_to_fetch);

        $this->db->where($where_clause);

        $run_q = $this->db->get('product group');

        return $run_q->num_rows() ? $run_q->row() : FALSE;
    }
}
