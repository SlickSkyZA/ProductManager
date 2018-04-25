<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class Company extends CI_Model{
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
        $this->db->select('company.id, company.Name, IFNULL(customer_region.Name,"") RegionName, customer_type.Name TypeName,
        priority.Name PriorityName, priority.Value PriorityValue, company.AddedDate, company.UpdatedDate, company.Notes, company.RelationshipType');

        $this->db->join('customer_region', 'company.RegionID = customer_region.id', 'left');
        $this->db->join('customer_type', 'company.TypeID = customer_type.id');
        $this->db->join('priority', 'company.PriorityID = priority.id');

        $run_q = $this->db->get('company');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        }

        else{
            return FALSE;
        }
    }

    /**
     *
     * @param type $itemName
     * @param type $itemQuantity
     * @param type $itemPrice
     * @param type $itemDescription
     * @param type $itemCode
     * @return boolean
     */
    public function add($itemName, $itemRegion, $itemType, $itemRSType, $itemPriority, $itemDesc){
        $data = ['RegionID'=>$itemRegion, 'PriorityID'=>$itemPriority, 'Name'=>$itemName, 'TypeID'=>$itemType, 'RelationshipType'=>$itemRSType, 'Notes'=>$itemDesc];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('company', $data);

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
        $this->db->select('company.id, company.Name, IFNULL(customer_region.Name,"") RegionName, customer_type.Name TypeName,
        priority.Name PriorityName, priority.Value PriorityValue, company.AddedDate, company.UpdatedDate, company.Notes, company.RelationshipType');

        $this->db->join('customer_region', 'company.RegionID = customer_region.id', 'left');
        $this->db->join('customer_type', 'company.TypeID = customer_type.id');
        $this->db->join('priority', 'company.PriorityID = priority.id');
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->like('company.Name', $this->db->escape_like_str($value));

        $run_q = $this->db->get('company');

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
    public function edit($itemId, $itemName, $itemType, $itemRSType, $itemRegionID, $itemPriorityID, $itemDesc){
       $data = ['Name'=>$itemName, 'RegionID'=>$itemRegionID, 'TypeID'=>$itemType, 'RelationshipType'=>$itemRSType, 'PriorityID'=>$itemPriorityID, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('company', $data);

       return TRUE;
    }

    /**
     * get active times.
     * @param  [type] $orderBy     [description]
     * @param  [type] $orderFormat [description]
     * @return [type]              [description]
     */
    public function getActiveItems($orderBy, $type, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);

        $this->db->where('RelationshipType', $type);
        $run_q = $this->db->get('company');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
            return FALSE;
        }
    }

    /**
     * get active times.
     * @param  [type] $orderBy     [description]
     * @param  [type] $orderFormat [description]
     * @return [type]              [description]
     */
    public function getCompanyByType($orderBy, $type, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->select('company.id, company.Name Name, customer_type.Name TypeName, company.RelationshipType');
        $this->db->join('customer_type', 'company.TypeID = customer_type.id');
        $this->db->where('customer_type.Name', $type);
        $run_q = $this->db->get('company');

        if($run_q->num_rows() > 0){
            return $run_q->result();
        } else {
            return FALSE;
        }
    }
}
