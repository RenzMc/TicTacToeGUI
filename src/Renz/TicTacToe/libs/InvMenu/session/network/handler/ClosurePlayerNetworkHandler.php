<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network\handler;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\NetworkStackLatencyEntry;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\player\Player;

final class ClosurePlayerNetworkHandler implements PlayerNetworkHandler{

	/** @var Closure */
	private $creator;

	/**
	 * @param Closure $creator
	 *
	 * @phpstan-param Closure(Player, NetworkStackLatencyPacket, Closure) : NetworkStackLatencyEntry $creator
	 */
	public function __construct(Closure $creator){
		$this->creator = $creator;
	}

	public function createNetworkStackLatencyEntry(Closure $then) : ?NetworkStackLatencyEntry{
		$packet = NetworkStackLatencyPacket::create(mt_rand(), true);
		return ($this->creator)(Player $player, $packet, $then);
	}
}