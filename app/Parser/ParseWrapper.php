<?php

namespace App\Parser;

use App\Drop;
use App\Node;

class ParseWrapper
{
    private $data;

    public function __construct(string $parse)
    {
        $this->data = json_decode($parse, true);
    }

    public function append(self $append)
    {
        $lastLineNumber = $this->lastLine();
        $lastLine = $this->dropLine($lastLineNumber);
        $matchingLine = $append->getMatchingLine($lastLine) ?? 0;
        $lastLineOfAppend = $append->lastLine();

        for ($line = $matchingLine; $line <= $lastLineOfAppend; $line++) {
            foreach ($append->dropLine($line) as $drop) {
                $dropData = $drop->raw();
                $dropData['y'] += $lastLineNumber + 1;

                $this->data['drops'][] = $dropData;
            }
        }
    }

    /**
     * @param int $y
     * @return ParseDrop[]
     */
    public function dropLine(int $y): array
    {
        return array_values(
            array_filter($this->drops(), function (ParseDrop $drop) use ($y) {
                return $drop->y() === $y;
            })
        );
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

    /**
     * @param ParseDrop[] $drops
     * @return int|null
     */
    public function getMatchingLine(array $drops): ?int
    {
        $lastLine = $this->lastLine();

        for ($line = 0; $line <= $lastLine; $line++) {
            $dropLine = $this->dropLine($line);

            if (count($dropLine) !== count($drops)) {
                continue;
            }

            $count = count($dropLine);
            $matching = true;

            for ($x = 0; $x < $count; $x++) {
                if ($dropLine[$x]->code() !== $drops[$x]->code()) {
                    $matching = false;
                    break;
                }

                if ($dropLine[$x]->stack() !== $drops[$x]->stack()) {
                    $matching = false;
                    break;
                }
            }

            if ($matching) {
                return $line;
            }
        }

        return null;
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
        $drops = $this->drops();
        if (!count($drops))
            return true;

        $firstDrop = $drops[0];
        if (!$firstDrop->isQuestQp())
            return true;

        if ($full) {
            $dropCount = $this->data['drop_count'];

            $drops = array_filter(
                $drops,
                function (ParseDrop $drop): bool {
                    return !$drop->isQuestQp();
                }
            );

            return $dropCount === count($drops);
        } else {
            $currencies = false;
            foreach ($drops as $drop) {
                if ($drop->isQuestQp())
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

    public function lastLine(): int
    {
        $lines = array_map(function (ParseDrop $drop) {
            return $drop->y();
        }, $this->drops());

        return max($lines);
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

    public function toArray(): array
    {
        return $this->data;
    }

    public function getCountForDrop(Drop $drop): int
    {
        $code = $drop->uid;
        $stack = $drop->quantity;

        $drops = array_filter(
            $this->drops(),
            function (Drop $drop) use ($code, $stack) {
                return $drop->code() === $code && $drop->stack() === $stack;
            }
        );

        return count($drops);
    }

}
