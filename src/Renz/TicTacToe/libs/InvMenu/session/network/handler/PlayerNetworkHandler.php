<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network\handler;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\NetworkStackLatencyEntry;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\network\mcpe\protocol\types\NetworkStackLatencyPacketType;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : ?NetworkStackLatencyEntry;
}