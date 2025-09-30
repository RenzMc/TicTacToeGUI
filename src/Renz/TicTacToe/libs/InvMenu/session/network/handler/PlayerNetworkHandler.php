<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network\handler;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
