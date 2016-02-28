<?php
namespace App\Model;
use DateTime;
use dibi;
use App\Model\Repository;
use Dibi\Connection;

/**
 * Model for table with stored-files
 */
class Files extends Repository
{
	public $name = "files";

	function findByUser($id)
	{
		return $this->connection->query("SELECT * FROM " . $this->name . " WHERE user=%i ORDER BY uploaded ASC", $id);
	}

	function findByHash($hash)
	{
		return $this->connection->query("SELECT * FROM " . $this->name . " WHERE hash=%s LIMIT 1", $hash);
	}

	function findExpired()
	{
		return $this->connection->query("SELECT * FROM " . $this->name . " WHERE expire < %t", new DateTime);
	}

}