<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\util;

use Renz\TicTacToe\libs\InvMenu\type\util\builder\ActorFixedInvMenuTypeBuilder;
use Renz\TicTacToe\libs\InvMenu\type\util\builder\BlockActorFixedInvMenuTypeBuilder;
use Renz\TicTacToe\libs\InvMenu\type\util\builder\BlockFixedInvMenuTypeBuilder;
use Renz\TicTacToe\libs\InvMenu\type\util\builder\DoublePairableBlockActorFixedInvMenuTypeBuilder;

final class InvMenuTypeBuilders{

	public function getActorFixedBuilder() : ActorFixedInvMenuTypeBuilder{
		return new ActorFixedInvMenuTypeBuilder();
	}

	public function getBlockFixedBuilder() : BlockFixedInvMenuTypeBuilder{
		return new BlockFixedInvMenuTypeBuilder();
	}

	public function getBlockActorFixedBuilder() : BlockActorFixedInvMenuTypeBuilder{
		return new BlockActorFixedInvMenuTypeBuilder();
	}

	public function getDoublePairableBlockActorFixedBuilder() : DoublePairableBlockActorFixedInvMenuTypeBuilder{
		return new DoublePairableBlockActorFixedInvMenuTypeBuilder();
	}
}