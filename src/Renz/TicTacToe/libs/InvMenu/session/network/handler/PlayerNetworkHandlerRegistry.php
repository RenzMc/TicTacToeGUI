<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network\handler;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\NetworkStackLatencyEntry;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\network\mcpe\protocol\types\NetworkStackLatencyPacketType;
use pocketmine\player\Player;

final class PlayerNetworkHandlerRegistry{

	/** @var PlayerNetworkHandler */
	private $default;

	/** @var PlayerNetworkHandler[] */
	private $handlers = [];

	public function __construct(){
		$this->registerDefault(new ClosurePlayerNetworkHandler(
			static function(Player $player, NetworkStackLatencyPacket $packet, Closure $then) : NetworkStackLatencyEntry{
				$timestamp = mt_rand();
				$packet->timestamp = $timestamp;
				$packet->needResponse = true;
				return new NetworkStackLatencyEntry($timestamp, $packet, $then);
			}
		));
	}

	public function registerDefault(PlayerNetworkHandler $handler) : void{
		$this->default = $handler;
	}

	public function register(string $identifier, PlayerNetworkHandler $handler) : void{
		$this->handlers[$identifier] = $handler;
	}

	public function get(Player $player) : PlayerNetworkHandler{
		return $this->handlers[$player->getName()] ?? $this->default;
	}
}