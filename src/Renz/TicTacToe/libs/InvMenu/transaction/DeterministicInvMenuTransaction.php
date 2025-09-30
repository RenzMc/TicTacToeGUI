<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class DeterministicInvMenuTransaction implements InvMenuTransaction{

	/** @var InvMenuTransaction */
	private $inner;

	/** @var InvMenuTransactionResult|null */
	private $result = null;

	public function __construct(InvMenuTransaction $transaction){
		$this->inner = $transaction;
	}

	public function continue() : InvMenuTransactionResult{
		return $this->result = InvMenuTransactionResult::continue();
	}

	public function discard() : InvMenuTransactionResult{
		return $this->result = InvMenuTransactionResult::cancel();
	}

	public function getPlayer() : Player{
		return $this->inner->getPlayer();
	}

	public function getOut() : Item{
		return $this->inner->getOut();
	}

	public function getIn() : Item{
		return $this->inner->getIn();
	}

	public function getItemClicked() : Item{
		return $this->inner->getItemClicked();
	}

	public function getItemClickedWith() : Item{
		return $this->inner->getItemClickedWith();
	}

	public function getAction() : SlotChangeAction{
		return $this->inner->getAction();
	}

	public function getTransaction() : InventoryTransaction{
		return $this->inner->getTransaction();
	}

	public function then(?callable $callback) : void{
		if($this->result === null){
			throw new \InvalidStateException("Cannot call " . __METHOD__ . "() before attempting transaction");
		}

		if($callback !== null){
			$callback($this->getPlayer(), $this->getOut(), $this->getIn(), $this->getAction(), $this->getTransaction());
		}
	}
}