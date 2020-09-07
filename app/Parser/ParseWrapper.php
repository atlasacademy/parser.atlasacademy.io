<?php

namespace App\Parser;

use App\Node;

class ParseWrapper
{
    private $data;

    public function __construct(string $parse)
    {
        $this->data = json_decode($parse, true);
    }

    /**
     * @return ParseDrop[]
     */
    public function drops(): array
    {
        return array_map(function (array $dropData) {
            return new ParseDrop($dropData);
        }, $this->data['drops']);
    }

    public function hasInvalidDrops(Node $node)
    {
        return array_reduce(
            $this->drops(),
            function (bool $carry, ParseDrop $drop) use ($node): bool {
                return $carry ?: (!$drop->isInNode($node));
            },
            false
        );
    }

    public function hasMissingDrops(bool $full): bool
    {
        if ($full) {
            $dropCount = $this->data['drop_count'];

            $drops = array_filter(
                $this->drops(),
                function (ParseDrop $drop): bool {
                    return !$drop->isQuestQp();
                }
            );

            return $dropCount === count($drops);
        } else {
            $currencies = false;
            foreach ($this->drops() as $k => $drop) {
                if (!$k && $drop->isQuestQp())
                    continue;

                if (($currencies && $drop->isCurrency()) || (!$currencies && !$drop->isCurrency()))
                    continue;

                if (!$currencies && $drop->isCurrency()) {
                    $currencies = true;
                    continue;
                }

                return true;
            }

            return false;
        }
    }

    public function hasUnknownDrops(): bool
    {
        return array_reduce(
            $this->drops(),
            function (bool $carry, ParseDrop $drop): bool {
                return $carry ?: $drop->isUnknown();
            },
            false
        );
    }

    public function isValid(): bool
    {
        if (!$this->data || !is_array($this->data))
            return false;

        return ($this->data['status'] ?? null) === 'OK';
    }

    public function questQp(): ?int
    {
        $drops = $this->drops();
        $firstDrop = array_shift($drops);
        if (!$firstDrop)
            return null;

        if (!$firstDrop->isQuestQp())
            return null;

        return $firstDrop->stack();
    }

}
