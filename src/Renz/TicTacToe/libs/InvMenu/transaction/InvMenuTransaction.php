<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface InvMenuTransaction{

	public function getPlayer() : Player;

	public function getOut() : Item;

	public function getIn() : Item;

	public function getItemClicked() : Item;

	public function getItemClickedWith() : Item;

	public function getAction() : SlotChangeAction;

	public function getTransaction() : InventoryTransaction;

	public function continue() : InvMenuTransactionResult;

	public function discard() : InvMenuTransactionResult;
}