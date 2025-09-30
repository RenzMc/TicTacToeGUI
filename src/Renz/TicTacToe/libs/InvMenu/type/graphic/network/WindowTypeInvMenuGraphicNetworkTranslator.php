<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\graphic\network;

use Renz\TicTacToe\libs\InvMenu\session\InvMenuInfo;
use Renz\TicTacToe\libs\InvMenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}
