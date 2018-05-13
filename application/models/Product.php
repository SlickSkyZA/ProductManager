<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class Product extends CI_Model{
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
        $this->db->select('product.id, product.Name, product_group.Name GroupName, product.DPM, product.QPM, product.PM,
        priority.Name PriorityName, priority.Value PriorityValue, product.AddedDate, product.UpdatedDate, product.Notes');

        $this->db->join('product_group', 'product.GroupID = product_group.id');
        $this->db->join('priority', 'product.PriorityID = priority.id');

        $run_q = $this->db->get('product');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
            return FALSE;
        }
    }

    /**
     * [add description]
     * @param [type] $itemName       [description]
     * @param [type] $itemGroupID    [description]
     * @param [type] $itemPriorityID [description]
     * @param [type] $itemDPM        [description]
     * @param [type] $itemQPM        [description]
     * @param [type] $itemPM         [description]
     * @param [type] $itemDesc       [description]
     */
    public function add($itemName, $itemGroupID, $itemPriorityID, $itemDPM, $itemQPM, $itemPM, $itemDesc) {
        $data = ['Name'=>$itemName, 'GroupID'=>$itemGroupID, 'PriorityID'=>$itemPriorityID, 'DPM'=>$itemDPM, 'QPM'=>$itemQPM, 'PM'=>$itemPM, 'Notes'=>$itemDesc];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('product', $data);

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
    public function itemsearch($value){
        $q = "SELECT product.id, product.Name, product_group.Name GroupName, product.DPM, product.QPM, product.PM,
                priority.Name PriorityName, priority.Value PriorityValue, product.AddedDate, product.UpdatedDate, product.Notes
                FROM product
                JOIN product_group ON product.GroupID = product_group.id
                JOIN priority ON product.PriorityID = priority.id
                WHERE
                product.Name LIKE '%".$this->db->escape_like_str($value)."%'";

        $run_q = $this->db->query($q, [$value, $value]);

        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
            return FALSE;
        }
    }

    /**
     * [edit description]
     * @param  [type] $itemId         [description]
     * @param  [type] $itemName       [description]
     * @param  [type] $itemGroupID    [description]
     * @param  [type] $itemPriorityID [description]
     * @param  [type] $itemDPM        [description]
     * @param  [type] $itemQPM        [description]
     * @param  [type] $itemPM         [description]
     * @param  [type] $itemDesc       [description]
     * @return [type]                 [description]
     */
    public function edit($itemId, $itemName, $itemGroupID, $itemPriorityID, $itemDPM, $itemQPM, $itemPM, $itemDesc){
       $data = ['Name'=>$itemName, 'GroupID'=>$itemGroupID, 'PriorityID'=>$itemPriorityID, 'DPM'=>$itemDPM, 'QPM'=>$itemQPM, 'PM'=>$itemPM,'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('product', $data);

       return TRUE;
    }

    /**
     * [getActiveItems description]
     * @param  [type] $orderBy     [description]
     * @param  [type] $orderFormat [description]
     * @return [type]              [description]
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
}
