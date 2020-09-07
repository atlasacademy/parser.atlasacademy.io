<?php

namespace App\Parser;

use App\Node;

class ParseDrop
{
    private const DEFAULT_MAPPING = [
        5 => "QUEST_QP",
    ];

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function code(): string
    {
        $id = $this->data['id'];
        if (array_key_exists($id, self::DEFAULT_MAPPING))
            return self::DEFAULT_MAPPING[$id];

        return $this->data['name'];
    }

    public function isCurrency(): bool
    {
        if ($this->isQuestQp() || $this->isDefaultDrop())
            return false;

        return true;
    }

    public function isDefaultDrop(): bool
    {
        $id = $this->data['id'];

        return array_key_exists($id, self::DEFAULT_MAPPING);
    }

    public function isInNode(Node $node)
    {
        if ($this->isQuestQp())
            return true;

        foreach ($node->drops as $nodeDrop) {
            if ($nodeDrop->uid === $this->code() && $nodeDrop->quantity === $this->stack())
                return true;
        }

        return false;
    }

    public function isQuestQp(): bool
    {
        return $this->data['id'] === 5;
    }

    public function isUnknown(): bool
    {
        return preg_match('/^item[0-9]{3}$/', $this->data['name']);
    }

    public function stack(): int
    {
        return $this->data['stack'];
    }

}
