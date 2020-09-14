<?php

namespace App\Parser;

use App\Drop;
use App\Node;
use App\Submission;

class ParseWrapper
{
    private $data;
    private $type;

    public function __construct(string $type, string $parse)
    {
        $this->data = json_decode($parse, true);
        $this->type = $type;
    }

    public static function create(Submission $submission): ?self
    {
        return $submission->parse ? new self($submission->type, $submission->parse) : null;
    }

    public function append(self $append)
    {
        $lastLineNumber = $this->lastLine();
        $lastLine = $this->dropLine($lastLineNumber);
        $matchingLine = $append->getMatchingLine($lastLine);
        $matchingLine = $matchingLine === null ? 0 : ($matchingLine + 1);
        $lastLineOfAppend = $append->lastLine();

        for ($line = $matchingLine; $line <= $lastLineOfAppend; $line++) {
            foreach ($append->dropLine($line) as $drop) {
                $dropData = $drop->raw();
                $dropData['y'] += $lastLineNumber + 1;

                $this->data['drops'][] = $dropData;
            }
        }
    }

    public function dropCount(): int
    {
        return $this->data['drop_count'];
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

    public function getCountForDrop(Drop $drop): int
    {
        $code = $drop->uid;
        $stack = $drop->quantity;

        $drops = array_filter(
            $this->drops(),
            function (ParseDrop $drop) use ($code, $stack) {
                return $drop->code() === $code && $drop->stack() === $stack;
            }
        );

        return count($drops);
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
                if ($carry)
                    return true;

                if ($this->type === "simple" && !$drop->isDefaultDrop())
                    return false;

                return !$drop->isInNode($node);
            },
            false
        );
    }

    public function hasMissingDrops(): bool
    {
        $drops = $this->drops();
        if (!count($drops))
            return true;

        $firstDrop = $drops[0];
        if (!$firstDrop->isQuestQp())
            return true;

        if ($this->type === "full") {
            $dropCount = $this->data['drop_count'];

            $drops = array_filter(
                $drops,
                function (ParseDrop $drop): bool {
                    return !$drop->isQuestQp();
                }
            );

            return $dropCount !== count($drops);
        } else {
            $currencies = false;
            foreach ($drops as $drop) {
                if ($drop->isQuestQp())
                    continue;

                if ($currencies) {
                    // there should be no default drops after it starts receiving currency
                    if ($drop->isDefaultDrop()) {
                        return true;
                    } else {
                        continue;
                    }
                }

                if ($drop->isDefaultDrop()) {
                    continue;
                } else {
                    $currencies = true;
                    continue;
                }
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

    public function hash(): ?string
    {
        $clonedData = json_decode(json_encode($this->data), true);
        if (!$clonedData || !is_array($clonedData))
            return null;

        $clonedData = array_intersect_key(
            $clonedData,
            [
                'status' => null,
                'qp_total' => null,
                'qp_gained' => null,
                'drop_count' => null,
                'drops_found' => null,
                'drops' => null,
            ]
        );

        $json = json_encode($clonedData);

        return hash('sha256', $json);
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

    public function scrollPosition(): float
    {
        return $this->data['scroll_position'];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function totalQp(): int
    {
        return $this->data['qp_total'];
    }

}
