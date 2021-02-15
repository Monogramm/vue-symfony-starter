<?php


namespace App\Service;

class CodeGenerator extends PasswordGenerator
{
    private const ALL_CODE_CHARS = 'QWERTYUPADFHJKLZXCVNM' . PasswordGenerator::NUM;

    private $defaultChars;

    public function __construct()
    {
        parent::__construct(self::ALL_CODE_CHARS);
    }
}
