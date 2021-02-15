<?php


namespace App\Service\Paypal\Checkout\Request\Object;

class Amount implements \JsonSerializable
{
    private $currencyCode;

    private $value;

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(?string $currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function getValue() :?string
    {
        return $this->value;
    }

    public function setValue(?string $value)
    {
        $this->value = $value;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = [];

        $this->currencyCode ? $data['currency_code'] = $this->currencyCode : null;
        $this->value ? $data['value'] = $this->value : null;

        return $data;
    }
}
