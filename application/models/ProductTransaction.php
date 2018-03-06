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

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->select('xscp.id, product.Name ProductName, customer.Name CustomerName, IFNULL(customer_vender.Name,"") CompetitorName,
        priority.Name PriorityName, product_status.Name StatusName, priority.Value PriorityValue, product_platform.Name PlatformName,
        xscp.ProjectName, xscp.AddedDate, xscp.UpdatedDate, xscp.Notes');

        $this->db->join('product', 'xscp.ProductID = product.id');
        $this->db->join('customer', 'xscp.CustomerID = customer.id');
        $this->db->join('customer_vender', 'xscp.VenderID = customer_vender.id', 'left');
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
    public function add($product, $customer, $priority, $platform, $status, $competitor, $projectName, $description){
        $data = ['ProductID'=>$product, 'CustomerID'=>$customer, 'PriorityID'=>$priority, 'PlatformID'=>$platform,
        'StatusID'=>$status, 'VenderID'=>$competitor, 'ProjectName'=>$projectName, 'Notes'=>$description];

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
                    xscp.ProjectName, xscp.AddedDate, xscp.UpdatedDate, xscp.Notes
                FROM xscp
                join product ON xscp.ProductID = product.id
                join customer ON xscp.CustomerID = customer.id
                left join customer_vender ON xscp.VenderID = customer_vender.id
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
     * To add to the number of an item in stock
     * @param type $itemId
     * @param type $numberToadd
     * @return boolean
     */
    public function incrementItem($itemId, $numberToadd){
        $q = "UPDATE items SET quantity = quantity + ? WHERE id = ?";

        $this->db->query($q, [$numberToadd, $itemId]);

        if($this->db->affected_rows() > 0){
            return TRUE;
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

    public function decrementItem($itemCode, $numberToRemove){
        $q = "UPDATE items SET quantity = quantity - ? WHERE code = ?";

        $this->db->query($q, [$numberToRemove, $itemCode]);

        if($this->db->affected_rows() > 0){
            return TRUE;
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


   public function newstock($itemId, $qty){
       $q = "UPDATE items SET quantity = quantity + $qty WHERE id = ?";

       $this->db->query($q, [$itemId]);

       if($this->db->affected_rows()){
           return TRUE;
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

   public function deficit($itemId, $qty){
       $q = "UPDATE items SET quantity = quantity - $qty WHERE id = ?";

       $this->db->query($q, [$itemId]);

       if($this->db->affected_rows()){
           return TRUE;
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
   public function edit($itemId, $itemProductID, $itemCustomerID, $itemPriorityID, $itemPlatformID, $itemStatusID, $itemCompetitorID, $itemProjectName, $itemDesc){
       $data = ['ProductID'=>$itemProductID, 'CustomerID'=>$itemCustomerID, 'PriorityID'=>$itemPriorityID, 'PlatformID'=>$itemPlatformID,
       'StatusID'=>$itemStatusID, 'VenderID'=>$itemCompetitorID, 'ProjectName'=>$itemProjectName, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('xscp', $data);

       return TRUE;
   }

   /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

	public function getActiveItems($orderBy, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);

        $run_q = $this->db->get('product');

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
