<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu;

use Closure;
use InvalidArgumentException;
use Renz\TicTacToe\libs\InvMenu\session\PlayerManager;
use Renz\TicTacToe\libs\InvMenu\type\InvMenuTypeRegistry;
use pocketmine\plugin\Plugin;

final class InvMenuHandler{

	/** @var Plugin|null */
	private static $registrant;

	/** @var InvMenuTypeRegistry */
	private static $type_registry;

	/** @var PlayerManager */
	private static $player_manager;

	public static function getRegistrant() : Plugin{
		return self::$registrant;
	}

	public static function register(Plugin $plugin) : void{
		if(self::isRegistered()){
			throw new InvalidArgumentException($plugin->getName() . " attempted to register " . self::class . " twice.");
		}

		self::$registrant = $plugin;
		self::$type_registry = new InvMenuTypeRegistry();
		self::$player_manager = new PlayerManager($plugin);
		$plugin->getServer()->getPluginManager()->registerEvents(new InvMenuEventHandler(self::$player_manager), $plugin);
	}

	public static function isRegistered() : bool{
		return self::$registrant instanceof Plugin;
	}

	public static function getTypeRegistry() : InvMenuTypeRegistry{
		return self::$type_registry;
	}

	public static function getPlayerManager() : PlayerManager{
		return self::$player_manager;
	}
}