<?php
/**
 * Model for table with files
 */
class FilesModel extends SimpleModel  {
	public $name = "files";

	function findByUser($id){
		return dibi::query("SELECT * FROM ".$this->name." WHERE user=%i ORDER BY uploaded ASC", $id);
	}
	function findByHash($hash){
		return dibi::query("SELECT * FROM ".$this->name." WHERE hash=%s LIMIT 1", $hash);
	}
	function findExpired(){
		return dibi::query("SELECT * FROM ".$this->name." WHERE expire < %t",new DateTime);
	}

}