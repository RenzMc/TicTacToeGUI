<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\graphic\network;

use Renz\TicTacToe\libs\InvMenu\session\InvMenuInfo;
use Renz\TicTacToe\libs\InvMenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class MultiInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	/**
	 * @param InvMenuGraphicNetworkTranslator[] $translators
	 */
	public function __construct(
		readonly private array $translators
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		foreach($this->translators as $translator){
			$translator->translate($session, $current, $packet);
		}
	}
}
