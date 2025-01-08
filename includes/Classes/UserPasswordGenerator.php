<?php

namespace Classes;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class UserPasswordGenerator{
    /**
     *
     * @var ComputerPasswordGenerator
     */
    protected $Generator;

    /**
     *
     */
    public function __construct()
    {
        $this->Generator = new ComputerPasswordGenerator();
    }

    /**
     *
     * @return string
     */
    public function generate()
    {
        $this->Generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setLength(8)
        ;

        return $this->Generator->generatePassword();
    }


}
