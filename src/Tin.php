<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Enums\TokenType;
use Felix\Tin\Themes\Theme;
use SplQueue;

class Tin
{
    public function __construct(protected Theme $theme)
    {
    }

    /** @param Theme|class-string<Theme> $theme */
    public static function from(Theme|string $theme, bool $supportsAnsi = true): self
    {
        if (is_string($theme)) {
            return new self(new $theme($supportsAnsi));
        }

        return new self($theme->ansi($supportsAnsi));
    }

    public function highlight(string $code): string
    {
        return $this->process($code, function (?Line $line): ?string {
            if (!$line) {
                return null;
            }

            $lineNumber = $line->theme->apply(
                $line->theme->comment,
                str_pad((string) $line->number, strlen((string) $line->totalCount), ' ', STR_PAD_LEFT) .
                ' | '
            );

            return $lineNumber . $line->toString() . PHP_EOL;
        });
    }

    public function process(string $code, callable $transformer): string
    {
        $tokens      = Tokenizer::tokenize($code);
        $highlighted = $this->groupTokensByLine($tokens);
        $totalLines  = $highlighted->count();
        $buffer      = '';

        foreach ($highlighted as $n => $lineTokens) {
            $line = $transformer(new Line($n + 1, $lineTokens, $totalLines, $this->theme));

            if ($line) {
                $buffer .= $line;
            }
        }

        return $buffer;
    }

    /**
     * @param iterable<Token> $tokens
     *
     * @return SplQueue<SplQueue<Token>> $tokens
     */
    private function groupTokensByLine(iterable $tokens): SplQueue
    {
        $line    = 0;
        /** @var SplQueue<SplQueue<Token>> $grouped */
        $grouped = new SplQueue();

        foreach ($tokens as $token) {
            $splits   = explode("\n", $token->text);
            $newLines = count($splits) - 1;

            for ($i = $line; $i <= $line + $newLines; $i++) {
                if (!isset($grouped[$i])) {
                    /** @var SplQueue<Token> $queue */
                    $queue = new SplQueue();
                    $grouped->add($i, $queue);
                }
            }

            foreach ($splits as $split) {
                if ($split === '' && $newLines > 0) {
                    $line++;
                    $newLines--;
                    continue;
                }

                $grouped[$line]?->push(new Token(
                    TokenType::fromPhpId($token->id),
                    $token->id,
                    $split,
                    $line,
                    $token->position
                ));
            }
        }

        return $grouped;
    }
}
