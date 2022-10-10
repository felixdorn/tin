<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Enums\TokenType;
use Felix\Tin\Themes\Theme;
use Generator;
use SplStack;

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
        return $this->process($code, [$this, 'highlightLine']);
    }

    public function process(string $code, callable $transformer): string
    {
        $tokens      = Tokenizer::tokenize($code);
        $highlighted = iterator_to_array($this->groupTokensByLine($tokens));
        $totalLines  = count($highlighted);

        ob_start();

        foreach ($highlighted as $n => $lineTokens) {
            $line = $transformer(new Line($n, $lineTokens, $totalLines, $this->theme));

            if ($line) {
                echo $line;
            }
        }

        return ob_get_clean();
    }

    /** @param Generator<Token> $tokens */
    private function groupTokensByLine(Generator $tokens): Generator
    {
        $line       = 1;
        $lineTokens = new SplStack();

        foreach ($tokens as $token) {
            $splits   = explode("\n", $token->text);
            $newLines = count($splits) - 1;

            foreach ($splits as $split) {
                if ($split === '' && $newLines > 0) {
                    if (!$lineTokens->isEmpty()) {
                        yield $line => $lineTokens;
                        $lineTokens = new SplStack();
                    }

                    $line++;
                    $newLines--;

                    yield $line => new SplStack();
                    continue;
                }

                $lineTokens->push(new Token(
                    TokenType::fromPhpId($token->id),
                    $token->id,
                    $split,
                    $line,
                    $token->position
                ));
            }
        }

        if (!$lineTokens->isEmpty()) {
            yield $line => $lineTokens;
        }
    }

    public function highlightLine(?Line $line): ?string
    {
        if (!$line) {
            return null;
        }

        $lineNumber = $line->theme->apply(
            $line->theme->comment,
            str_pad((string) $line->number, strlen((string) $line->lineCount), ' ', STR_PAD_LEFT) .
            ' | '
        );

        return $lineNumber . $line->toString() . PHP_EOL;
    }
}
