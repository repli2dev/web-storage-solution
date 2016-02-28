<?php
namespace App\Model;
use dibi;
use Dibi\Connection;
use Exception;

class Repository
{

	public $name;

	protected $primary = 'id';
	protected $autoIncrement = TRUE;

	/** @var Connection */
	protected $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	public function findAll()
	{
		return $this->connection->dataSource("SELECT * FROM " . $this->name);
	}

	public function find($id)
	{
		if (empty($id)) {
			throw new Exception("Empty id");
		}
		return $this->connection->dataSource("SELECT * FROM " . $this->name . " WHERE id=%i", $id)->fetch();
	}

	public function edit($id, $data)
	{
		if (empty($id)) {
			throw new Exception("Empty id");
		}
		return $this->connection->update($this->name, $data)->where("id = %i", $id)->execute();
	}

	public function add($data)
	{
		return $this->connection->insert($this->name, $data)->execute($this->autoIncrement ? dibi::IDENTIFIER : NULL);
	}

	public function delete($id)
	{
		if (empty($id)) {
			throw new Exception("Empty id");
		}
		return $this->connection->delete($this->name)->where('id = %i', $id)->execute();
	}

	public function getTableName()
	{
		return $this->name;
	}

}