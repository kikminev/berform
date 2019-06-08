<?php

namespace App\Document\Payment;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Currency
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var string $systemCode
     * @MongoDB\Field(type="string")
     */
    protected $systemCode;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSystemCode(): string
    {
        return $this->systemCode;
    }

    /**
     * @param string $systemCode
     */
    public function setSystemCode(string $systemCode): void
    {
        $this->systemCode = $systemCode;
    }
}
