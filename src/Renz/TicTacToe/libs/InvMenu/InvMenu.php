<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu;

use Closure;
use LogicException;
use Renz\TicTacToe\libs\InvMenu\inventory\SharedInvMenuSynchronizer;
use Renz\TicTacToe\libs\InvMenu\session\InvMenuInfo;
use Renz\TicTacToe\libs\InvMenu\session\PlayerWindowDispatcher;
use Renz\TicTacToe\libs\InvMenu\transaction\DeterministicInvMenuTransaction;
use Renz\TicTacToe\libs\InvMenu\transaction\InvMenuTransaction;
use Renz\TicTacToe\libs\InvMenu\transaction\InvMenuTransactionResult;
use Renz\TicTacToe\libs\InvMenu\transaction\SimpleInvMenuTransaction;
use Renz\TicTacToe\libs\InvMenu\type\InvMenuType;
use Renz\TicTacToe\libs\InvMenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

class InvMenu{

	public const TYPE_CHEST = InvMenuTypeIds::TYPE_CHEST;
	public const TYPE_DOUBLE_CHEST = InvMenuTypeIds::TYPE_DOUBLE_CHEST;
	public const TYPE_HOPPER = InvMenuTypeIds::TYPE_HOPPER;
	public const TYPE_DROPPER = InvMenuTypeIds::TYPE_DROPPER;

	/** @var InvMenuHandler */
	private static $handler;

	/** @var InvMenuType */
	private $type;

	/** @var string|null */
	private $name;

	/** @var callable|null */
	private $listener;

	/** @var callable|null */
	private $inventory_close_listener;

	/** @var bool */
	private $readonly = false;

	/** @var SharedInvMenuSynchronizer|null */
	private $synchronizer;

	public function __construct(InvMenuType $type, ?string $name = null){
		$this->type = $type;
		$this->name = $name;
	}

	public static function create(string $identifier) : InvMenu{
		return new InvMenu(InvMenuHandler::getTypeRegistry()->get($identifier));
	}

	public static function createSessionized(string $identifier) : SharedInvMenuSynchronizer{
		return new SharedInvMenuSynchronizer(InvMenu::create($identifier));
	}

	/**
	 * @param callable(DeterministicInvMenuTransaction) : void $callback
	 * @return self
	 */
	public function setListener(callable $callback) : self{
		$this->listener = $callback;
		return $this;
	}

	/**
	 * @param callable(Player, Inventory) : void $listener
	 * @return self
	 */
	public function setInventoryCloseListener(callable $listener) : self{
		$this->inventory_close_listener = $listener;
		return $this;
	}

	/**
	 * @return callable|null
	 */
	public function getInventoryCloseListener() : ?callable{
		return $this->inventory_close_listener;
	}

	public function setName(?string $name = null) : self{
		$this->name = $name;
		return $this;
	}

	public function getName() : ?string{
		return $this->name;
	}

	public function readonly(bool $value = true) : self{
		$this->readonly = $value;
		return $this;
	}

	public function isReadonly() : bool{
		return $this->readonly;
	}

	public function getType() : InvMenuType{
		return $this->type;
	}

	public function getInventory() : Inventory{
		return $this->type->createInventory();
	}

	/**
	 * @param Player $player
	 * @param string|null $name
	 * @param Closure|null $callback
	 * @return bool
	 */
	public function send(Player $player, ?string $name = null, ?Closure $callback = null) : bool{
		$session = InvMenuHandler::getPlayerManager()->get($player);
		if($session === null){
			return false;
		}

		$network = $session->getNetwork();
		if($network === null){
			return false;
		}

		$dispatcher = $network->getWindowDispatcher();
		if($dispatcher === null){
			return false;
		}

		$name = $name ?? $this->name;
		if($name !== null){
			$title = $name;
		}else{
			$title = "";
		}

		$inventory = $this->getInventory();
		$position = $this->type->getPosition();
		$position->y = max(-0x100, min(0x100 - 16, $position->y));

		$graphic = $this->type->createGraphic($inventory, $player, $title);
		if($graphic !== null){
			$dispatcher->dispatch($graphic, $callback);
			$session->setCurrentMenu(new InvMenuInfo($this, $inventory, $graphic), $callback);
			return true;
		}

		return false;
	}

	public function sendInventory(Player $player, ?Inventory $inventory = null) : bool{
		$session = InvMenuHandler::getPlayerManager()->get($player);
		if($session === null){
			return false;
		}

		$current = $session->getCurrentMenu();
		if($current === null){
			return false;
		}

		$inventory = $inventory ?? $current->getInventory();
		$graphic = $current->getGraphic();

		$graphic->send($inventory, $player);
		return true;
	}

	public function onInventoryClose(Player $player) : void{
		$session = InvMenuHandler::getPlayerManager()->get($player);
		if($session === null){
			return;
		}

		$current = $session->getCurrentMenu();
		if($current === null){
			return;
		}

		$current->getGraphic()->remove($player);
		$session->removeCurrentMenu();

		if($this->inventory_close_listener !== null){
			($this->inventory_close_listener)($player, $current->getInventory());
		}
	}

	public function onInventoryTransaction(Player $player, Item $out, Item $in, SlotChangeAction $action, InventoryTransaction $transaction) : InvMenuTransactionResult{
		if($this->readonly){
			return $this->listener === null ? InvMenuTransactionResult::cancel() : InvMenuTransactionResult::continue();
		}

		$inv_menu_txn = new SimpleInvMenuTransaction($player, $out, $in, $action, $transaction);
		if($this->listener !== null){
			$listener = $this->listener;
			$listener(new DeterministicInvMenuTransaction($inv_menu_txn));
		}
		return InvMenuTransactionResult::continue();
	}
}