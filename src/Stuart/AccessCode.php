<?php

namespace Stuart;

class AccessCode
{
    private $code;
    private $type;
    private $title;
    private $instructions;

    public function __construct($code, $type, $title, $instructions)
    {
        $this->code = $code;
        $this->type = $type;
        $this->title = $title;
        $this->instructions = $instructions;
    }
    
    public function getCode() {
        return $this->code;
    }

    public function getType() {
        return $this->type;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getInstructions() {
        return $this->instructions;
    }

}