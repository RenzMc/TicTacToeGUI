<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\util;

use pocketmine\block\Block;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Tile;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\World;

final class InvMenuTypeHelper{

	public static function createNetworkTranslatedInventory(Inventory $inventory, string $custom_name) : Inventory{
		if($custom_name !== ""){
			// TODO: Add a proper way to set inventory custom names
		}
		return $inventory;
	}

	public static function getBlockActorDataAt(Player $player, Vector3 $pos, ?string $default_custom_name = null) : ?CompoundTag{
		$world = $player->getWorld();

		$x = (int) $pos->x;
		$y = (int) $pos->y;
		$z = (int) $pos->z;

		if(!$world->isChunkLoaded($x >> 4, $z >> 4)){
			return null;
		}

		$tile = $world->getTileAt($x, $y, $z);
		if($tile instanceof Chest && $default_custom_name !== null){
			$tag = $tile->getSpawnCompound();
			$tag->setString(Tile::TAG_CUSTOM_NAME, $default_custom_name);
			return $tag;
		}

		return $tile !== null ? $tile->getSpawnCompound() : null;
	}

	public static function getInvFromBlockInWorld(World $world, Vector3 $pos, bool $force_create = false) : ?Inventory{
		$tile = $world->getTileAt((int) $pos->x, (int) $pos->y, (int) $pos->z);
		if($tile instanceof Chest){
			return $tile->getInventory();
		}

		return null;
	}
}