<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class Customer extends CI_Model{
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
        $this->db->select('customer.id, customer.Name, customer_region.Name RegionName,
        priority.Name PriorityName, priority.Value PriorityValue, customer.AddedDate, customer.UpdatedDate, customer.Notes');

        $this->db->join('customer_region', 'customer.RegionID = customer_region.id');
        $this->db->join('priority', 'customer.PriorityID = priority.id');

        $run_q = $this->db->get('customer');

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
    public function add($customerName, $customerRegion, $priority, $description){
        $data = ['RegionID'=>$customerRegion, 'PriorityID'=>$priority, 'Name'=>$customerName, 'Notes'=>$description];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('customer', $data);

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
        $q = "SELECT customer.id, customer.Name, customer_region.Name RegionName,
                priority.Name PriorityName, priority.Value PriorityValue, customer.AddedDate, customer.UpdatedDate, customer.Notes
                FROM customer
                JOIN customer_region ON customer.RegionID = customer_region.id
                JOIN priority ON customer.PriorityID = priority.id
                WHERE
                customer.Name LIKE '%".$this->db->escape_like_str($value)."%'";

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
   public function edit($itemId, $itemName, $itemRegionID, $itemPriorityID, $itemDesc){
       $data = ['Name'=>$itemName, 'RegionID'=>$itemRegionID, 'PriorityID'=>$itemPriorityID, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('customer', $data);

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

        $run_q = $this->db->get('customer');

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

        $run_q = $this->db->get('product_group');

        return $run_q->num_rows() ? $run_q->row() : FALSE;
    }
}