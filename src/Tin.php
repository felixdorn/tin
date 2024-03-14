<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Outputs\AnsiOutput;

class Tin
{
    public function __construct(protected OutputInterface $output)
    {
    }

    /**
     * @param class-string<Theme|OutputInterface>|Theme|OutputInterface $theme       You may pass a theme/output class name
     *                                                                               or an instance of either, by default the AnsiOutput is used
     * @param bool                                                      $ansiEnabled Whether to enable ANSI output, by default it is enabled,
     *                                                                               this setting is ignored if you do not pass a theme class or instance
     */
    public static function from(string|Theme|OutputInterface $theme, bool $ansiEnabled = true): self
    {
        //  The logic here is somewhat convoluted to avoid breaking changes, keep it this way for now
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

    /** Highlights a piece of code with line numbers */
    public function highlight(string $code): string
    {
        return $this->process($code, function (?Line $line): ?string {
            if (!$line) {
                return null;
            }

            $lineNumber = $line->output->transform(
                TokenType::LineNumber,
                str_pad(
                    (string) $line->number,
                    strlen((string) $line->totalCount), ' ',
                    STR_PAD_LEFT
                ) . ' | ',
            );

            return $lineNumber . $line->toString() . $line->output->newLine();
        });
    }

    /**
     * Converts a piece of code to lines made of tokens and passes each line to a transformer.
     *
     * @param callable(Line): ?string $transformer
     */
    public function process(string $code, callable $transformer): string
    {
        $buffer     = '';
        $tokens     = $this->groupTokensByLine(
            Tokenizer::tokenize($code)
        );
        $totalLines = $tokens->count();

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
     * @return \SplQueue<\SplQueue<Token>> $tokens
     */
    private function groupTokensByLine(iterable $tokens): \SplQueue
    {
        $lineIndex = 0;
        /** @var \SplQueue<\SplQueue<Token>> $grouped */
        $grouped = new \SplQueue();

        foreach ($tokens as $token) {
            $lines        = explode(PHP_EOL, $token->text);
            $newLineCount = count($lines) - 1;

            // add empty queues for new lines
            for ($i = 0; $i <= $newLineCount; $i++) {
                if (isset($grouped[$lineIndex + $i])) {
                    continue;
                }

                /** @var \SplQueue<Token> $queue */
                $queue = new \SplQueue();
                $grouped->add($lineIndex + $i, $queue);
            }

            foreach ($lines as $line) {
                if ($token->id === T_INLINE_HTML || $token->id === T_DOC_COMMENT) {
                    $grouped[$lineIndex]->push($token->withText($line));
                    $lineIndex++;
                    continue;
                }

                if ($line === '' && $newLineCount > 0) {
                    $lineIndex++;
                    $newLineCount--;
                    continue;
                }

                $grouped[$lineIndex]->push($token->withText($line));
            }
        }

        return $grouped;
    }
}
