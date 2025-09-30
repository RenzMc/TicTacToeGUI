<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\graphic;

use Closure;
use Renz\TicTacToe\libs\InvMenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuGraphic{

	public function send(Inventory $inventory, Player $player) : void;

	public function remove(Player $player) : void;

	public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator;
}