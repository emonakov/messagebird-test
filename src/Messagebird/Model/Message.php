<?php

namespace Messagebird\Model;

class Message extends Model
{
    protected $tableName = 'message';

    public function __construct()
    {
        parent::__construct();
        $this->isSent();
    }

    public function setIsSent($isSent = true)
    {
        $this->_data['is_sent'] = $isSent;
        return $this;
    }

    public function setRecipient($recipient)
    {
        $this->_data['recipient'] = $recipient;
        return $this;
    }

    public function setOriginator($originator)
    {
        $this->_data['originator'] = $originator;
        return $this;
    }

    public function setText($message)
    {
        $this->_data['text'] = $message;
        return $this;
    }

    public function getText()
    {
        return $this->_data['text'];
    }

    public function getRecipient()
    {
        return $this->_data['recipient'];
    }

    public function getOriginator()
    {
        return $this->_data['originator'];
    }

    public function isSent()
    {
        if (!isset($this->_data['is_sent'])) {
            $this->setIsSent(false);
        }
        return $this->_data['is_sent'];
    }

    public function save()
    {
        if (!$this->getId()) {
            $query = "INSERT INTO {$this->tableName} VALUES (:id, :recipient, :originator, :text, :is_sent)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':recipient', $this->_data['recipient']);
            $stmt->bindParam(':originator', $this->_data['originator']);
            $stmt->bindParam(':text', $this->_data['text']);
        } else {
            $query = "UPDATE {$this->tableName} SET is_sent = :is_sent WHERE id = :id";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':id', $this->_data['id']);
        $stmt->bindParam(':is_sent', $this->_data['is_sent']);
        $stmt->execute();
        return parent::save();
    }
}