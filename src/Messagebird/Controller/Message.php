<?php

namespace Messagebird\Controller;

use Messagebird\App;
use Messagebird\Model\{
    ModelFactory,
    Message as MessageModel,
    ModelInterface
};
use \MessageBird\Client;

class Message extends Client
{
    /**
     * @var ModelInterface
     */
    protected $_message;

    /**
     * Error flag
     *
     * @var bool
     */
    protected $_sendError = false;

    /**
     * Processes initial user message
     *
     * @return $this
     */
    public function processMessage()
    {
        $inputData = $this->_getRequestBody();
        $message = filter_var($inputData['message'], FILTER_SANITIZE_STRING);
        if (!empty($message)) {
            $this->_message = ModelFactory::create('\Messagebird\Model\Message');
            $this->_message
                ->setOriginator($inputData['originator'])
                ->setRecipient($inputData['recipient'])
                ->setText($message);
            $this->_message->save();
        }
        return $this;
    }

    /**
     * Sends processed message
     *
     * @return \Messagebird\Controller\Message
     */
    public function sendMessage()
    {
        $result = null;
        if ($this->_message) {
            $messages = $this->_prepareMessage();
            foreach ($messages as $message) {
                $result = $this->_sendMessage($message);
                if ($this->_sendError) {
                    break;
                } else {
                    $this->_message
                        ->setIsSent()
                        ->save();
                }
            }
        }
        return $this->_render($result);
    }

    /**
     * Gets balance
     *
     * @return $this
     */
    public function getBalance()
    {
        return $this->_render($this->balance->read());
    }

    /**
     * Renders json response
     *
     * @param $result
     *
     * @return $this
     */
    protected function _render($result)
    {
        header('Content-Type: application/json');
        file_put_contents('php://output', json_encode($result));
        return $this;
    }

    /**
     * Gets request body
     *
     * @return mixed
     */
    protected function _getRequestBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Prepares message objects
     *
     * @return \MessageBird\Objects\Message[]
     */
    protected function _prepareMessage()
    {
        $messages = [];
        $message = $this->_message->getText();
        $maxLength = App::getApp()->getConfig()['messagebird-api']['max-length'];
        $length = App::getApp()->getConfig()['messagebird-api']['length'];
        if (strlen($message) > $maxLength) {
            $totalMsgPartsNo = ceil(strlen($message) / $length);
            $totalMessagePartsNoHex = dechex($totalMsgPartsNo);
            if (strlen($totalMessagePartsNoHex) == 1) {
                $totalMessagePartsNoHex = "0".$totalMessagePartsNoHex;
            }
            $identifyCode = rand(0, 255);
            $identifyCodeHex = dechex($identifyCode);
            $messageCharacterIndexStart = 0;
            for ($i = 1; $i <= $totalMsgPartsNo; $i++) {
                $messagePart = substr($message, $messageCharacterIndexStart, $length);
                $messageCharacterIndexStart += $length;
                $currentMessagePartsNoHex = dechex($i);
                if (strlen($currentMessagePartsNoHex) == 1) {
                    $currentMessagePartsNoHex = "0".$currentMessagePartsNoHex;
                }
                $userHeader = '050003'.$identifyCodeHex.$totalMessagePartsNoHex.$currentMessagePartsNoHex;
                $messages[] = $this->_createMessage($messagePart, $userHeader);
            }
        } else {
            $messages[] = $this->_createMessage();
        }
        return $messages;
    }

    /**
     * Creates message objects
     *
     * @param null $text
     * @param null $header
     *
     * @return \MessageBird\Objects\Message
     */
    protected function _createMessage($text = null, $header = null)
    {
        $Message = new \MessageBird\Objects\Message();
        $Message->originator = $this->_message->getOriginator();
        $Message->recipients = [$this->_message->getRecipient()];
        $smsText = ($text) ? $text : $this->_message->getText();
        $Message->datacoding = 'auto';
        if ($header) {
            $Message->setBinarySms($header, $smsText);
        } else {
            $Message->body = $smsText;
        }
        return $Message;
    }

    /**
     * Sends the message
     *
     * @param \MessageBird\Objects\Message $message
     *
     * @return mixed
     */
    protected function _sendMessage(\MessageBird\Objects\Message $message)
    {
        try {
            $result = $this->messages->create($message);
            sleep(1);
        } catch (\Exception $e) {
            $this->_sendError = true;
            $result = ['error' => $e->getMessage(), 'class' => get_class($e)];
        }
        return $result;
    }
}