<?php

declare(strict_types=1);

namespace Renz\TicTacToe\Bot;

use Renz\TicTacToe\Game;

class HardBot implements Bot {

    public function makeMove(Game $game): void {
        $board = $game->getBoard();
        $bestMove = -1;
        $bestValue = -PHP_INT_MAX;

        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === " ") {
                $board[$i] = "O";
                $moveValue = $this->minimax($board, 0, false);
                $board[$i] = " ";
                if ($moveValue > $bestValue) {
                    $bestMove = $i;
                    $bestValue = $moveValue;
                }
            }
        }

        $game->handleMove(null, $bestMove + 1);
    }

    private function minimax(array $board, int $depth, bool $isMax): int {
        $score = $this->evaluate($board);

        if ($score === 10) {
            return $score - $depth;
        }

        if ($score === -10) {
            return $score + $depth;
        }

        if (!$this->isMovesLeft($board)) {
            return 0;
        }

        if ($isMax) {
            $best = -PHP_INT_MAX;

            for ($i = 0; $i < 9; $i++) {
                if ($board[$i] === " ") {
                    $board[$i] = "O";
                    $best = max($best, $this->minimax($board, $depth + 1, !$isMax));
                    $board[$i] = " ";
                }
            }

            return $best;
        } else {
            $best = PHP_INT_MAX;

            for ($i = 0; $i < 9; $i++) {
                if ($board[$i] === " ") {
                    $board[$i] = "X";
                    $best = min($best, $this->minimax($board, $depth + 1, !$isMax));
                    $board[$i] = " ";
                }
            }

            return $best;
        }
    }

    private function evaluate(array $board): int {
        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];

        foreach ($winningCombinations as $combination) {
            if ($board[$combination[0]] === $board[$combination[1]] && $board[$combination[1]] === $board[$combination[2]]) {
                if ($board[$combination[0]] === "O") {
                    return 10;
                } elseif ($board[$combination[0]] === "X") {
                    return -10;
                }
            }
        }

        return 0;
    }

    private function isMovesLeft(array $board): bool {
        return in_array(" ", $board, true);
    }
}