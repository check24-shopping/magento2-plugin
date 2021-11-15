<?php

namespace Check24Shopping\OrderImport\Helper\Config;

class ApiConfiguration
{
    /** @var int */
    private $partnerId;
    /** @var string */
    private $user;
    /** @var string */
    private $password;
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $id;

    public function __construct(
        int    $partnerId,
        string $user,
        string $password,
        string $host,
        int    $port
    )
    {
        $this->partnerId = $partnerId;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->id = md5($partnerId . $user . $password . $host . $port);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPartnerId(): int
    {
        return $this->partnerId;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
