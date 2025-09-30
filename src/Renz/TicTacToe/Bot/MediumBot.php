<?php

declare(strict_types=1);

namespace Renz\TicTacToe\Bot;

use Renz\TicTacToe\Game;

class MediumBot implements Bot {

    public function makeMove(Game $game): void {
        $move = $this->findWinningMove($game);
        if ($move !== null) {
            $game->handleMove(null, $move + 1);
            return;
        }

        $move = $this->findBlockingMove($game);
        if ($move !== null) {
            $game->handleMove(null, $move + 1);
            return;
        }

        for ($i = 0; $i < 9; $i++) {
            if ($game->getBoard()[$i] === " ") {
                $game->handleMove(null, $i + 1);
                return;
            }
        }
    }

    private function findWinningMove(Game $game): ?int {
        return $this->findBestMove($game, "O");
    }

    private function findBlockingMove(Game $game): ?int {
        return $this->findBestMove($game, "X");
    }

    private function findBestMove(Game $game, string $player): ?int {
        $board = $game->getBoard();
        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];

        foreach ($winningCombinations as $combination) {
            $values = [$board[$combination[0]], $board[$combination[1]], $board[$combination[2]]];
            $valueCounts = array_count_values($values);

            if (isset($valueCounts[$player]) && $valueCounts[$player] === 2 && in_array(" ", $values, true)) {
                foreach ($combination as $index) {
                    if ($board[$index] === " ") {
                        return $index;
                    }
                }
            }
        }

        return null;
    }
}