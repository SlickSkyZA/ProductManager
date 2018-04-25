<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class RelCustomerModule extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    /**
     * [add description]
     * @param [type] $ProjectID [description]
     * @param [type] $VenderID  [description]
     */
    public function add($ProjectID, $VenderID) {
        if (!empty($VenderID)) {
            foreach($VenderID as $get){
                $data = ['CustomerProjectID'=>$ProjectID, 'VenderID'=>$get];
                //set the datetime based on the db driver in use
                $this->db->platform() == "sqlite3"
                        ?
                $this->db->set('AddedDate', "datetime('now')", FALSE)
                        :
                $this->db->set('AddedDate', "NOW()", FALSE);

                $this->db->insert('rel_customer_project_module', $data);

            }
        }
    }

    /**
     * [add description]
     * @param [type] $ProjectID [description]
     * @param [type] $VenderID  [description]
     */
    public function edit($ProjectID, $VenderID) {
        $this->db->where('CustomerProjectID', $ProjectID)->delete('rel_customer_project_module');
        if (!empty($VenderID)) {    
            foreach($VenderID as $get){
                $data = ['CustomerProjectID'=>$ProjectID, 'VenderID'=>$get];
                //set the datetime based on the db driver in use
                $this->db->platform() == "sqlite3"
                        ?
                $this->db->set('AddedDate', "datetime('now')", FALSE)
                        :
                $this->db->set('AddedDate', "NOW()", FALSE);

                $this->db->insert('rel_customer_project_module', $data);

            }
        }
    }
}
