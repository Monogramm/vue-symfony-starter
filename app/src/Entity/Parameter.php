<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParameterRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Parameter
{
    use Metadata;

    public const STRING_TYPE = 'string';

    public const SECRET_TYPE = 'secret';

    public const NUMBER_TYPE = 'number';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    private $value;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public static function types()
    {
        return [
            self::STRING_TYPE,
            self::SECRET_TYPE,
            self::NUMBER_TYPE
        ];
    }

    public function isSecret(): bool
    {
        return $this->type === self::SECRET_TYPE;
    }
}
