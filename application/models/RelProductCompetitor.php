<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class RelProductCompetitor extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    /**
     * add
     * @param [type] $ProjectID    [description]
     * @param [type] $ProductID    [description]
     * @param [type] $CompetitorID [description]
     */
    public function add($MarketID, $CompetitorID) {
        if (!empty($CompetitorID)) {
            foreach($CompetitorID as $get){
                $data = ['MarketID'=>$MarketID, 'CompetitorID'=>$get];
                //set the datetime based on the db driver in use
                $this->db->platform() == "sqlite3"
                        ?
                $this->db->set('AddedDate', "datetime('now')", FALSE)
                        :
                $this->db->set('AddedDate', "NOW()", FALSE);

                $this->db->insert('rel_project_product_competitor', $data);

            }
        }
    }

    /**
     * update
     * @param [type] $ProjectID    [description]
     * @param [type] $ProductID    [description]
     * @param [type] $CompetitorID [description]
     */
    public function edit($MarketID, $CompetitorID) {
        $this->db->where('MarketID', $MarketID);
        $this->db->delete('rel_project_product_competitor');
        if (!empty($CompetitorID)) {
            foreach($CompetitorID as $get){
                $data = ['MarketID'=>$MarketID, 'CompetitorID'=>$get];
                //set the datetime based on the db driver in use
                $this->db->platform() == "sqlite3"
                        ?
                $this->db->set('AddedDate', "datetime('now')", FALSE)
                        :
                $this->db->set('AddedDate', "NOW()", FALSE);

                $this->db->insert('rel_project_product_competitor', $data);

            }
        }
    }
}
