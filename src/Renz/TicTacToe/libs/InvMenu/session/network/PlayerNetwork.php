<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\handler\PlayerNetworkHandler;
use Renz\TicTacToe\libs\InvMenu\session\PlayerWindowDispatcher;
use pocketmine\player\Player;

final class PlayerNetwork{

	/** @var Player */
	private $player;

	/** @var PlayerNetworkHandler */
	private $handler;

	/** @var PlayerWindowDispatcher */
	private $window_dispatcher;

	/** @var NetworkStackLatencyEntry[] */
	private $pending = [];

	public function __construct(Player $player, PlayerNetworkHandler $handler, PlayerWindowDispatcher $window_dispatcher){
		$this->player = $player;
		$this->handler = $handler;
		$this->window_dispatcher = $window_dispatcher;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getHandler() : PlayerNetworkHandler{
		return $this->handler;
	}

	public function getWindowDispatcher() : PlayerWindowDispatcher{
		return $this->window_dispatcher;
	}

	public function dropPending() : void{
		foreach($this->pending as $entry){
			($entry->then)(false);
		}
		$this->pending = [];
	}

	public function wait(Closure $then) : void{
		$entry = $this->handler->createNetworkStackLatencyEntry($then);
		if($entry !== null){
			$this->pending[$entry->timestamp] = $entry;
			$entry->payload->handle($this->player);
		}else{
			$then(true);
		}
	}

	public function notify(int $timestamp) : void{
		if(isset($this->pending[$timestamp])){
			$entry = $this->pending[$timestamp];
			unset($this->pending[$timestamp]);
			($entry->then)(true);
		}
	}
}