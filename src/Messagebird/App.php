<?php

namespace Messagebird;

use Messagebird\Model\Mode;
use Symfony\Component\Yaml\Yaml;

class App
{
    /**
     * @var App
     */
    private static $app;

    protected $db;

    protected $config;

    /**
     * @var Mode
     */
    protected $mode;

    private function __construct(\PDO $db, $configuration)
    {
        $this->config = $configuration;
        $this->db = $db;
    }

    public function initMode(Mode $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public static function getApp()
    {
        if (null === static::$app) {
            // parse config
            $configuration = Yaml::parse(file_get_contents(__DIR__ . '/etc/config.yml'));
            // getting db connection
            $dbConfig = $configuration['database'];
            $db = new \PDO("mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['dbname'],
                $dbConfig['username'], $dbConfig['password']);
            // inject db and configuration into the app
            static::$app = new static($db, $configuration);
        }
        return static::$app;
    }

    /**
     * @return \PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getMode()
    {
        return $this->mode->getMode();
    }

    public function setProdMode()
    {
        $this->mode->setMode(Mode::MODE_PROD);
        return $this;
    }

    public function setTestMode()
    {
        $this->mode->setMode(Mode::MODE_TEST);
        return $this;
    }

    public function getApiKey()
    {
        return $this->getConfig()['messagebird-api']["{$this->getMode()}-key"];
    }

    public function getMaxMessageLength()
    {
        return $this->getConfig()['messagebird-api']['max-length'];
    }

    private function __wakeup(){}
    private function __clone(){}
}