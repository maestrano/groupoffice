<?php

/**
 * Maestrano map table functions
 *
 * @author root
 */

class MnoSoaDB extends MnoSoaBaseDB {
    /**
    * Update identifier map table
    * @param  	string 	local_id                Local entity identifier
    * @param    string  local_entity_name       Local entity name
    * @param	string	mno_id                  Maestrano entity identifier
    * @param	string	mno_entity_name         Maestrano entity name
    *
    * @return 	boolean Record inserted
    */
    
    public static function addIdMapEntry($local_id, $local_entity_name, $mno_id, $mno_entity_name) {	
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
        $db = GO::getDbConnection();
	$query = "INSERT INTO mno_id_map (mno_entity_guid, mno_entity_name, app_entity_id, app_entity_name, db_timestamp) VALUES "
                            ."(". $db->quote($mno_id).", ".$db->quote(strtoupper($mno_entity_name)).", ".
                            $db->quote($local_id).", ".$db->quote(strtoupper($local_entity_name)).", UTC_TIMESTAMP)";
        
        
        $db->query($query);
        $id = $db->lastInsertId();
        
        MnoSoaLogger::debug("addIdMapEntry query = ".$query);
        
        if (empty($id)) {
            return false;
        } 
        
        return true;
    }
    
    /**
    * Get Maestrano GUID when provided with a local identifier
    * @param  	string 	local_id                Local entity identifier
    * @param    string  local_entity_name       Local entity name
    *
    * @return 	boolean Record found	
    */
    public static function getMnoIdByLocalId($local_id, $local_entity_name, $mno_entity_name)
    {
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
        $mno_entity = null;
        $db = GO::getDbConnection();
        
	// Fetch record
	$query = "SELECT mno_entity_guid, mno_entity_name, deleted_flag from mno_id_map WHERE "
                . "app_entity_id=".$db->quote($local_id)." and app_entity_name=".$db->quote(strtoupper($local_entity_name)).
                " and mno_entity_name=".$db->quote(strtoupper($mno_entity_name));
        
        $result = $db->query($query);
        
	// Return id value
	if ($row = $result->fetch()) {
            $mno_entity_guid = trim($row["mno_entity_guid"]);
            $mno_entity_name = trim($row["mno_entity_name"]);
            $deleted_flag = trim($row["deleted_flag"]);
            
            if (!empty($mno_entity_guid) && !empty($mno_entity_name)) {
                $mno_entity = (object) array (
                    "_id" => $mno_entity_guid,
                    "_entity" => $mno_entity_name,
                    "_deleted_flag" => $deleted_flag
                );
            }
	}
        
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . "returning mno_entity = ".json_encode($mno_entity));
	return $mno_entity;
    }
    
    public static function getLocalIdByMnoId($mno_id, $mno_entity_name, $local_entity_name)
    {
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
	$local_entity = null;
        $db = GO::getDbConnection();
        
	// Fetch record
	$query = "SELECT app_entity_id, app_entity_name, deleted_flag from mno_id_map where mno_entity_guid=".$db->quote($mno_id)
                ." and mno_entity_name=".$db->quote(strtoupper($mno_entity_name))
                ." and app_entity_name=".$db->quote(strtoupper($local_entity_name));

        $result = $db->query($query);
        
	// Return id value
	if ($row = $result->fetch()) {
            $app_entity_id = trim($row["app_entity_id"]);
            $app_entity_name = trim($row["app_entity_name"]);
            $deleted_flag = trim($row["deleted_flag"]);
            
            if (!empty($app_entity_id) && !empty($app_entity_name)) {
                $local_entity = (object) array (
                    "_id" => $app_entity_id,
                    "_entity" => $app_entity_name,
                    "_deleted_flag" => $deleted_flag
                );
            }
	}
	
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . "returning mno_entity = ".json_encode($local_entity));
	return $local_entity;
    }
    
    public static function deleteIdMapEntry($local_id, $local_entity_name) 
    {
        MnoSoaLogger::debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
        $db = GO::getDbConnection();
        // Logically delete record
        $query = "UPDATE mno_id_map SET deleted_flag=1 WHERE app_entity_id=".$db->quote($local_id)
                ." and app_entity_name=".$db->quote(strtoupper($local_entity_name));
        
        $db->query($query);
        
        MnoSoaLogger::debug("deleteIdMapEntry query = ".$query);
        
        return true;
    }
}

?>