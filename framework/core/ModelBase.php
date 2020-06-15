<?php

require_once 'Validator.php';

class ModelBase
{
    private $validator;

    function __construct()
    {
        $this->validator = new Validator();
        $this->fillValues();
    }


    private function validate($props, $rule)
    {
        if (method_exists($this->validator, $rule)) {
            $this->validator->{$rule}($props);
        } 
        else {
            Response::BadRequest('Incorrect validation rule');
        }
    }

    private function fillValues()
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {

            if (empty($this->{$prop->name})) {
                $this->{$prop->name} = $this->validator->get("$prop->name");
            } 
            
            else if (!empty($this->{$prop->name}) && is_array($this->{$prop->name})) {
                foreach ($this->{$prop->name} as $rule) {
                    $this->validate([$prop->name], $rule);
                }
                $this->{$prop->name} = $this->validator->get("$prop->name");
            }
        }
    }

    protected function getValue($prop)
    {
        return $this->validator->get($prop);
    }
}
