<?php

namespace Renz\TicTacToe\Bot;

use Renz\TicTacToe\Game;

interface Bot {
    public function makeMove(Game $game): void;
}