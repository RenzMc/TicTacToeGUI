<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\graphic\network;

use Renz\TicTacToe\libs\InvMenu\session\InvMenuInfo;
use Renz\TicTacToe\libs\InvMenu\session\PlayerSession;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, InvMenuInfo $new) : void;
}