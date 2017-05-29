<?php

namespace Messagebird\Model\Message;

use Messagebird\Model\{
    ModelFactory, Message, ModelInterface
};
use Messagebird\App;

class Collection
{
    protected $_tableName = 'message';

    /**
     * @var ModelInterface[]
     */
    protected $_items;

    /**
     * @var \PDO
     */
    protected $_db;

    protected $_model = '\Messagebird\Model\Message';

    public function __construct()
    {
        $this->db = App::getApp()->getDb();
    }

    public function load()
    {
        $query = "SELECT * FROM {$this->_tableName}";
        $result = $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $item) {
            /** @var Message $message */
            $message = ModelFactory::create($this->_model);
            $message->setData($item);
            $this->_items[$message->getId()] = $message;
        }
        return $this;
    }

    public function getAllMessages()
    {
        return $this->_items;
    }
}