<?php

namespace Messagebird\Model;

class Mode extends Model
{
    const MODE_TEST = 'test';

    const MODE_PROD = 'prod';

    protected $mode;

    protected $tableName = 'mode';

    public function __construct()
    {
        parent::__construct();
        $this->_data = [];
    }

    public function getMode()
    {
        if (!$this->mode) {
            $query = "SELECT status FROM {$this->tableName} LIMIT 1";
            $stmt = $this->prepareSqlStatement($query);
            $stmt->execute();
            $this->mode = $stmt->fetchColumn();
        }
        return $this->mode;
    }

    public function setMode($mode = 'test')
    {
        $query = "UPDATE {$this->tableName} SET status = :mode";
        $stmt = $this->prepareSqlStatement($query, [':mode' => $mode]);
        $stmt->execute();
        $this->mode = $mode;
        return $this;
    }
}