<?php


namespace App\Service\Paypal\Checkout\Request\Object;

class PurchaseUnit implements \JsonSerializable
{
    /** @var Amount */
    private $amount;

    private $items;

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function setAmount(?Amount $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     * @return PurchaseUnit
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = [];

        $itemTotal = 0;
        /** @var Item $item */
        foreach ($this->items as $item) {
            $data['items'][] = $item->jsonSerialize();
            $itemTotal += $item->getQuantity() * $item->getUnitAmount()->getValue();
        }

        $this->amount ? $data['amount'] = $this->amount->jsonSerialize() : null;
        if ($itemTotal && $this->amount) {
            $itemTotalAmount = (new Amount())
                ->setCurrencyCode($this->amount->getCurrencyCode())
                ->setValue($itemTotal);

            $data['amount']['breakdown']['item_total'] = $itemTotalAmount->jsonSerialize();
        }

        return $data;
    }

    public static function create(
        string $totalPrice,
        string $currencyCode,
        array $items
    ) {
        $amount = (new Amount())
            ->setValue($totalPrice)
            ->setCurrencyCode($currencyCode);

        return (new self())
            ->setItems($items)
            ->setAmount($amount);
    }
}
