<?php

namespace Slashplus\IdentityGermany\Exceptions;

class ClassNotFoundException extends \Exception
{
    private $classname;

    /**
     * @param string $message
     * @param string $classname
     */
    public function __construct($message, $classname)
    {
        parent::__construct($message);

        $this->classname = $classname;
    }

    public function getClassname()
    {
        return $this->classname;
    }
}
