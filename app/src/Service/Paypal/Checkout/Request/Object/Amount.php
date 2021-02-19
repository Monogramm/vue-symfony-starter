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

    /**
     * @return static
     */
    public function setCurrencyCode(?string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function getValue() :?string
    {
        return $this->value;
    }

    /**
     * @return static
     */
    public function setValue(?string $value): self
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
