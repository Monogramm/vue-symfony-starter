<?php


namespace App\Service\Paypal\Checkout\Request\Object;

class Context implements \JsonSerializable
{
    private const LOCALES_BCP_47 = [
        'en' => 'en-US',
        'fr' => 'fr-FR'
    ];

    private $returnUrl;

    private $cancelUrl;

    private $brandName;

    private $locale;

    private $landingPage;

    private $shippingPreference;

    private $userAction;


    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * @param string|null $returnUrl Return URL on payment success
     * @return static
     */
    public function setReturnUrl(?string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }

    /**
     * @param string|null $cancelUrl Return URL on payment cancel
     * @return static
     */
    public function setCancelUrl(?string $cancelUrl): self
    {
        $this->cancelUrl = $cancelUrl;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale Locale
     * @return static
     */
    public function setLocale(?string $locale): self
    {
        if ($locale && isset(self::LOCALES_BCP_47[$locale])) {
            $this->locale = $locale;
        }
        return $this;
    }

    public function getLandingPage(): ?string
    {
        return $this->landingPage;
    }

    /**
     * @param string|null $landingPage Landing pageURL
     * @return static
     */
    public function setLandingPage(?string $landingPage): self
    {
        $this->landingPage = $landingPage;
        return $this;
    }

    public function getShippingPreference(): ?string
    {
        return $this->shippingPreference;
    }

    public function setShippingPreference(?string $shippingPreference): self
    {
        $this->shippingPreference = $shippingPreference;
        return $this;
    }

    public function getUserAction(): ?string
    {
        return $this->userAction;
    }

    public function setUserAction(?string $userAction): self
    {
        $this->userAction = $userAction;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = [];

        $this->returnUrl ? $data['return_url'] = $this->returnUrl : null;
        $this->cancelUrl ? $data['cancel_url'] = $this->cancelUrl : null;
        $this->locale ? $data['locale'] = $this->locale : null;
        $this->landingPage ? $data['landing_page'] = $this->landingPage : null;
        $this->shippingPreference ? $data['shipping_preference'] = $this->shippingPreference : null;
        $this->userAction ? $data['shipping_preference'] = $this->userAction : null;
        $this->brandName ? $data['brand_name'] = $this->brandName : null;

        return $data;
    }

    /**
     * @return static
     */
    public static function create(
        string $returnUrl,
        string $cancelUrl
    ): self {
        return (new self())
            ->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);
    }
}
