<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class CustomerProject extends CI_Model{
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

        $this->db->select('customer_project.id, customer_project.Name, customer.Name CustomerName, customer_project.SOCCompany,
        customer_project.SOCName, customer_project.GPU, customer_project.DSP, customer_project.RAM, customer_project.FrontCameraType,
        customer_project.RearCameraType, customer_project.AddedDate, customer_project.UpdatedDate, customer_project.Notes');

        $this->db->join('customer', 'customer_project.CustomerID = customer.id');

        $run_q = $this->db->get('customer_project');

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
    public function add($itemName, $itemCustomer, $itemSOCCompany, $itemSOCName, $itemGPU, $itemDSP, $itemRAM, $itemCamera0, $itemCamera1, $itemDesc){
        $data = ['Name'=>$itemName, 'CustomerID'=>$itemCustomer, 'SOCCompany'=>$itemSOCCompany, 'SOCName'=>$itemSOCName, 'GPU'=>$itemGPU, 'DSP'=>$itemDSP, 'RAM'=>$itemRAM, 'FrontCameraType'=>$itemCamera0, 'RearCameraType'=>$itemCamera1, 'Notes'=>$itemDesc];

        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3"
                ?
        $this->db->set('AddedDate', "datetime('now')", FALSE)
                :
        $this->db->set('AddedDate', "NOW()", FALSE);

        $this->db->insert('customer_project', $data);

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
        $q = "SELECT customer_project.id, customer_project.Name, customer.Name CustomerName, customer_project.SOCCompany,
        customer_project.SOCName, customer_project.GPU, customer_project.DSP, customer_project.RAM, customer_project.FrontCameraType,
        customer_project.RearCameraType, customer_project.AddedDate, customer_project.UpdatedDate, customer_project.Notes
            FROM customer_project
            JOIN customer ON customer_project.CustomerID = customer.id
            WHERE
            customer_project.Name LIKE '%".$this->db->escape_like_str($value)."%'";

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
   public function edit($itemId, $itemName, $itemCustomer, $itemSOCCompany, $itemSOCName, $itemGPU, $itemDSP, $itemRAM, $itemCamera0, $itemCamera1, $itemDesc) {
       $data = ['Name'=>$itemName, 'CustomerID'=>$itemCustomer, 'SOCCompany'=>$itemSOCCompany, 'SOCName'=>$itemSOCName, 'GPU'=>$itemGPU, 'DSP'=>$itemDSP, 'RAM'=>$itemRAM, 'FrontCameraType'=>$itemCamera0, 'RearCameraType'=>$itemCamera1, 'Notes'=>$itemDesc];

       $this->db->where('id', $itemId);
       $this->db->update('customer_project', $data);

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

        $run_q = $this->db->get('customer_project');

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

     public function getSOCCompanies($orderBy, $orderFormat){
         $this->db->order_by($orderBy, $orderFormat);
         $this->db->select("SOCCompany");
         $this->db->group_by('SOCCompany');
         $run_q = $this->db->get('customer_project');

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

      public function getSOCNames($orderBy, $orderFormat){
          $this->db->order_by($orderBy, $orderFormat);
          $this->db->select("SOCName");
          $this->db->group_by('SOCName');
          $run_q = $this->db->get('customer_project');

          if($run_q->num_rows() > 0){
              return $run_q->result();
          }

          else{
              return FALSE;
          }
      }
}
