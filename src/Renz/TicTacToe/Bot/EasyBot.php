<?php

declare(strict_types=1);

namespace Renz\TicTacToe\Bot;

use Renz\TicTacToe\Game;

class EasyBot implements Bot {

    public function makeMove(Game $game): void {
        for ($i = 0; $i < 9; $i++) {
            if ($game->getBoard()[$i] === " ") {
                
                $game->handleMove(null, $i + 1);
                return;
            }
        }
    }
}