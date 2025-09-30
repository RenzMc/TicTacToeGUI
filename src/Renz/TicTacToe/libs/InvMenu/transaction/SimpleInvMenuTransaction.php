<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class SimpleInvMenuTransaction implements InvMenuTransaction{

	/** @var Player */
	private $player;

	/** @var Item */
	private $out;

	/** @var Item */
	private $in;

	/** @var SlotChangeAction */
	private $action;

	/** @var InventoryTransaction */
	private $transaction;

	public function __construct(Player $player, Item $out, Item $in, SlotChangeAction $action, InventoryTransaction $transaction){
		$this->player = $player;
		$this->out = $out;
		$this->in = $in;
		$this->action = $action;
		$this->transaction = $transaction;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getOut() : Item{
		return $this->out;
	}

	public function getIn() : Item{
		return $this->in;
	}

	public function getItemClicked() : Item{
		return $this->out;
	}

	public function getItemClickedWith() : Item{
		return $this->in;
	}

	public function getAction() : SlotChangeAction{
		return $this->action;
	}

	public function getTransaction() : InventoryTransaction{
		return $this->transaction;
	}

	public function continue() : InvMenuTransactionResult{
		return InvMenuTransactionResult::continue();
	}

	public function discard() : InvMenuTransactionResult{
		return InvMenuTransactionResult::cancel();
	}
}