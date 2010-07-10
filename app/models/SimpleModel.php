<?php
class SimpleModel {

	public $name;

	protected $primary = 'id';
	
	protected $autoIncrement = TRUE;

	public function __construct($name = NULL){
		if(!empty($name)){
			$this->name = $name;
		}
		if(empty($this->name)){
			throw new Exception("Empty table name.");
		}
	}
	
	public function findAll(){
		return dibi::dataSource("SELECT * FROM ".$this->name);
	}

	public function find($id){
		if(empty($id)){
			throw new Exception("Empty id");
		}
		return dibi::dataSource("SELECT * FROM ".$this->name." WHERE id=%i",$id)->fetch();
	}
	public function edit($id,$data){
		if(empty($id)){
			throw new Exception("Empty id");
		}
		return dibi::update($this->name, $data)->where("id = %i",$id)->execute();
	}
	public function add($data){
		return dibi::insert($this->name, $data)->execute($this->autoIncrement ? dibi::IDENTIFIER : NULL);
	}
	public function delete($id){
		if(empty($id)){
			throw new Exception("Empty id");
		}
		return dibi::delete($this->name)->where('id = %i', $id)->execute();
	}
	public function getTableName(){
		return $this->name;
	}

}