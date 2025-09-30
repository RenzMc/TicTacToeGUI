<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu;

use Renz\TicTacToe\libs\InvMenu\session\PlayerManager;
use Renz\TicTacToe\libs\InvMenu\session\PlayerSession;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\player\Player;

class InvMenuEventHandler implements Listener{

	/** @var PlayerManager */
	private $player_manager;

	public function __construct(PlayerManager $player_manager){
		$this->player_manager = $player_manager;
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 * @priority NORMAL
	 */
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof ContainerClosePacket){
			$player = $event->getOrigin()->getPlayer();
			if($player !== null){
				$session = $this->player_manager->getNullable($player);
				if($session !== null){
					$current = $session->getCurrentMenu();
					if($current !== null && $current->getInventory()->getContainerId() === $packet->id){
						$current->getMenu()->onInventoryClose($player);
						$session->removeCurrentMenu();
					}
				}
			}
		}
	}

	/**
	 * @param DataPacketSendEvent $event
	 * @priority NORMAL
	 */
	public function onDataPacketSend(DataPacketSendEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof NetworkStackLatencyPacket){
			$player = $event->getTarget();
			if($player instanceof Player){
				$session = $this->player_manager->getNullable($player);
				if($session !== null){
					$session->getNetwork()->notify($packet->timestamp);
				}
			}
		}
	}

	/**
	 * @param InventoryCloseEvent $event
	 * @priority MONITOR
	 */
	public function onInventoryClose(InventoryCloseEvent $event) : void{
		$player = $event->getPlayer();
		$session = $this->player_manager->getNullable($player);
		if($session !== null){
			$current = $session->getCurrentMenu();
			if($current !== null && $current->getInventory() === $event->getInventory()){
				$current->getMenu()->onInventoryClose($player);
				$session->removeCurrentMenu();
			}
		}
	}

	/**
	 * @param InventoryTransactionEvent $event
	 * @priority NORMAL
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		$player = $transaction->getSource();

		$session = $this->player_manager->getNullable($player);
		if($session !== null){
			$current = $session->getCurrentMenu();
			if($current !== null){
				$inventory = $current->getInventory();
				$menu = $current->getMenu();
				foreach($transaction->getActions() as $action){
					if($action instanceof SlotChangeAction && $action->getInventory() === $inventory){
						$result = $menu->onInventoryTransaction($player, $action->getSourceItem(), $action->getTargetItem(), $action, $transaction);
						$event->cancel();
						if($result->isCancelled()){
							return;
						}
					}
				}
			}
		}
	}
}