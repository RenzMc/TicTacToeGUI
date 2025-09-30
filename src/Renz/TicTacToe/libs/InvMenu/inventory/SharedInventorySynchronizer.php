<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\inventory;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Item;

final class SharedInventorySynchronizer implements InventoryListener{

	/** @var SharedInventoryNotifier */
	private $notifier;

	public function __construct(SharedInventoryNotifier $notifier){
		$this->notifier = $notifier;
	}

	public function onContentChange(Inventory $inventory, array $old_contents) : void{
		$viewers = $this->notifier->getInventory()->getViewers();
		if(count($viewers) <= 0){
			return;
		}

		$new_contents = $inventory->getContents();
		foreach($viewers as $viewer){
			$viewer->getNetworkSession()->getInvManager()?->syncContents($this->notifier->getInventory(), $new_contents);
		}
	}

	public function onSlotChange(Inventory $inventory, int $slot, Item $old_item) : void{
		$viewers = $this->notifier->getInventory()->getViewers();
		if(count($viewers) <= 0){
			return;
		}

		$new_item = $inventory->getItem($slot);
		foreach($viewers as $viewer){
			$viewer->getNetworkSession()->getInvManager()?->syncSlot($this->notifier->getInventory(), $slot, $new_item);
		}
	}
}