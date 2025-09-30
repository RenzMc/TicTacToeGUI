<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type\util\builder;

use Renz\TicTacToe\libs\InvMenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}
