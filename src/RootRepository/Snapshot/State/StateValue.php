<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\State;

use APPelit\LaraSauce\Types\ValueObject;

class StateValue implements \JsonSerializable
{
    /** @var string */
    private $valueType;

    /** @var mixed */
    private $value;

    /**
     * @param array $value
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateValue
     */
    public static function fromSnapshot(array $value): StateValue
    {
        $stateValue = new static();

        $stateValue->valueType = $value['value_type'];
        $stateValue->value = $value['value'];

        return $stateValue;
    }

    /**
     * StateValue constructor.
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if ($value instanceof ValueObject) {
            $this->valueType = get_class($value);
            $this->value = $value->toPayload();
        } else {
            $this->value = $value;
        }
    }

    /**
     * @return ValueObject|mixed|null
     */
    public function getValue()
    {
        if ($this->valueType) {
            /** @var ValueObject $valueClass */
            $valueClass = $this->valueType;

            return $valueClass::fromPayload($this->value);
        }

        return $this->value;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'value_type' => $this->valueType,
            'value' => $this->value,
        ];
    }
}
