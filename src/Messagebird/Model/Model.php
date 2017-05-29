<?php

namespace Messagebird\Model;

use Messagebird\App;

abstract class Model implements ModelInterface
{
    /**
     * @var \PDO
     */
    protected $db;

    protected $tableName;

    protected $_data;

    protected $query;

    public function __construct()
    {
        $this->_data['id'] = null;
        $this->db = App::getApp()->getDb();
    }

    public function getId()
    {
        return $this->_data['id'];
    }

    public function load($id, $field = 'id')
    {
        $query = "SELECT * FROM {$this->tableName} WHERE $field = :field_id";
        $stmt = $this->prepareSqlStatement($query, [':field_id' => $id]);
        $data = $stmt->fetchAll();
        $this->_data = $data;
        return $this;
    }

    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    public function getData($key = null)
    {
        return ($key !== null && isset($this->_data['key'])) ? $this->_data['key'] : $this->_data;
    }

    protected function prepareSqlStatement($query, $params = [])
    {
        $stmt = $this->db->prepare($query);
        foreach ($params as $key=>$param) {
            $stmt->bindParam($key, $param);
        }
        $stmt->execute();
        return $stmt;
    }

    public function save()
    {
        $this->_data['id'] = $this->db->lastInsertId();
        return $this;
    }
}