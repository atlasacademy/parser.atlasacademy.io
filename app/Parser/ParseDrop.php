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

        if ($this->isQp()) {
            [$code, $stack] = $this->getQpInfo();

            return $this->code = $code;
        }

        $code = $this->data['name'];
        $code = TemplateMap::getValue($code, $code);

        return $this->code = $code;
    }

    public function id(): int
    {
        return $this->data['id'];
    }

    public function isDefaultDrop(): bool
    {
        $code = $this->code();

        // if event ce
        if (preg_match('/^E[0-9]+[A-Z]$/i', $code))
            return true;

        // if event
        if ($code[0] === "E")
            return false;

        if ($this->isQp())
            return false;

        // if EXP card
        if (preg_match('/^B3/i', $code))
            return false;

        // everything else
        return true;
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

    public function isQp(): bool
    {
        $code = $this->data['name'];
        $code = TemplateMap::getValue($code, $code);

        if ($code === "QP"
            || preg_match('/^Q[0-9]+$/', $code))
            return true;

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

        if ($this->isQuestQp() || $this->isQp())
            return $this->unknown = false;

        $code = $this->data['name'];

        return $this->unknown = !TemplateMap::hasValue($code);
    }

    public function raw(): array
    {
        return $this->data;
    }

    public function rawName(): string
    {
        return $this->data['name'];
    }

    public function stack(): int
    {
        if ($this->isQp()) {
            [$code, $stack] = $this->getQpInfo();

            return $stack;
        }

        $code = $this->code();

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
            case 10000:
                return ['Q010', 1];
            case 15000:
                return ['Q015', 1];
            case 20000:
                return ['Q020', 1];
            case 30000:
                return ['Q030', 1];
            case 50000:
                return ['Q050', 1];
            default:
                return ['QP00', $stack];
        }
    }
}
