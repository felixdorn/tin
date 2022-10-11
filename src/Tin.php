<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Outputs\AnsiOutput;
use SplQueue;

class Tin
{
    public function __construct(protected OutputInterface $output)
    {
    }

    /**
     * @param class-string<Theme|OutputInterface>|Theme|OutputInterface $theme
     */
    public static function from(string|Theme|OutputInterface $theme, bool $ansiEnabled = true): self
    {
        if ($theme instanceof OutputInterface) {
            return new self($theme);
        }

        if (is_string($theme)) {
            $theme = new $theme();
        }

        if ($theme instanceof OutputInterface) {
            return new self($theme);
        }

        return new self(new AnsiOutput($theme, $ansiEnabled));
    }

    public function highlight(string $code): string
    {
        return $this->process($code, function (?Line $line): ?string {
            if (!$line) {
                return null;
            }

            $lineNumber = $line->output->transform(
                TokenType::Comment,
                str_pad(
                    (string) $line->number,
                    strlen((string) $line->totalCount), ' ',
                    STR_PAD_LEFT
                ) . ' | ',
            );

            return $lineNumber . $line->toString() . PHP_EOL;
        });
    }

    public function process(string $code, callable $transformer): string
    {
        $tokens = $this->groupTokensByLine(
            Tokenizer::tokenize($code)
        );
        $totalLines = $tokens->count();
        $buffer     = '';

        foreach ($tokens as $n => $lineTokens) {
            if ($line = $transformer(new Line($n + 1, $lineTokens, $totalLines, $this->output))) {
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
        $line = 0;
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
                if ($token->id === T_INLINE_HTML) {
                    $grouped[$line]?->push($token->withText($split));
                    $line++;
                    continue;
                }

                if ($split === '' && $newLines > 0) {
                    $line++;
                    $newLines--;
                    continue;
                }

                $grouped[$line]?->push($token->withText($split));
            }
        }

        return $grouped;
    }
}
