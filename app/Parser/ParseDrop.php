<?php

namespace App\Parser;

use App\Node;
use App\TemplateMap;

class ParseDrop
{
    private const QP = 1;
    private const QUEST_QP = 5;

    private $code = null;
    private $data;
    private $unknown = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function code(): string
    {
        if ($this->code !== null)
            return $this->code;

        if ($this->isQuestQp())
            return $this->code = "QUEST_QP";

        if ($this->data['id'] === self::QP) {
            [$code, $stack] = $this->getQpInfo();

            return $this->code = $code;
        }

        return $this->code = TemplateMap::getValue($this->data['id'], $this->data['name']);
    }

    public function id(): int
    {
        return $this->data['id'];
    }

    public function isCurrency(): bool
    {
        if ($this->isQuestQp() || $this->isDefaultDrop())
            return false;

        return true;
    }

    public function isDefaultDrop(): bool
    {
        $code = $this->code();

        return $code[0] !== "E";
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
        return $this->data['id'] === self::QUEST_QP;
    }

    public function isUnknown(): bool
    {
        if ($this->unknown !== null)
            return $this->unknown;

        if ($this->isQuestQp())
            return $this->unknown = false;

        return $this->unknown = !TemplateMap::hasValue($this->data['id']);
    }

    public function raw(): array
    {
        return $this->data;
    }

    public function stack(): int
    {
        if ($this->data['id'] === self::QP) {
            [$code, $stack] = $this->getQpInfo();

            return $stack;
        }

        return $this->data['stack'];
    }

    public function x(): int
    {
        return $this->data['x'];
    }

    public function y(): int
    {
        return $this->data['y'];
    }

    private function getQpInfo(): array
    {
        $stack = $this->data['stack'];

        switch ($stack) {
            case 1000:
                return ['Q010', 1];
            case 1500:
                return ['Q015', 1];
            case 2000:
                return ['Q020', 1];
            case 3000:
                return ['Q030', 1];
            case 5000:
                return ['Q050', 1];
            default:
                return ['QP00', $stack];
        }
    }
}
