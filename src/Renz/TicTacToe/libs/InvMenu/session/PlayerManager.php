<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session;

use Renz\TicTacToe\libs\InvMenu\session\network\handler\PlayerNetworkHandlerRegistry;
use Renz\TicTacToe\libs\InvMenu\session\network\PlayerNetwork;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class PlayerManager{

	/** @var Plugin */
	private $plugin;

	/** @var PlayerNetworkHandlerRegistry */
	private $network_handler_registry;

	/** @var PlayerSession[] */
	private $sessions = [];

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->network_handler_registry = new PlayerNetworkHandlerRegistry();

		$server = Server::getInstance();
		$server->getPluginManager()->registerEvent(PlayerLoginEvent::class, function(PlayerLoginEvent $event) : void{
			$this->create($event->getPlayer());
		}, EventPriority::MONITOR, $plugin);

		$server->getPluginManager()->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event) : void{
			$this->destroy($event->getPlayer());
		}, EventPriority::MONITOR, $plugin);
	}

	private function create(Player $player) : void{
		$this->sessions[$player->getId()] = new PlayerSession(
			$player,
			new PlayerNetwork(
				$player,
				$this->network_handler_registry->get($player),
				new PlayerWindowDispatcher($player)
			)
		);
	}

	private function destroy(Player $player) : void{
		if(isset($this->sessions[$player_id = $player->getId()])){
			$this->sessions[$player_id]->finalize();
			unset($this->sessions[$player_id]);
		}
	}

	public function get(Player $player) : PlayerSession{
		if(isset($this->sessions[$player_id = $player->getId()])){
			return $this->sessions[$player_id];
		}

		$this->create($player);
		return $this->sessions[$player_id];
	}

	public function getNullable(Player $player) : ?PlayerSession{
		return $this->sessions[$player->getId()] ?? null;
	}

	public function getNetworkHandlerRegistry() : PlayerNetworkHandlerRegistry{
		return $this->network_handler_registry;
	}
}