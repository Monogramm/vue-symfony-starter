<?php


namespace App\Service\Paypal\Checkout\Request\Object;

class Item implements \JsonSerializable
{
    private $unitAmount;

    private $name;

    private $quantity;

    public function getUnitAmount(): ?Amount
    {
        return $this->unitAmount;
    }

    public function setUnitAmount(?Amount $unitAmount)
    {
        $this->unitAmount = $unitAmount;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = [];

        $this->unitAmount ? $data['unit_amount'] = $this->unitAmount->jsonSerialize() : null;
        $this->quantity ? $data['quantity'] = (string) $this->quantity : null;
        $this->name ? $data['name'] = $this->name : null;

        return $data;
    }

    public static function create(
        string $name,
        int $quantity,
        string $price,
        string $currencyCode
    ) {
        $item = new self();

        $amount = (new Amount())
            ->setCurrencyCode($currencyCode)
            ->setValue($price);

        $item
            ->setName($name)
            ->setUnitAmount($amount)
            ->setQuantity($quantity);

        return $item;
    }
}
