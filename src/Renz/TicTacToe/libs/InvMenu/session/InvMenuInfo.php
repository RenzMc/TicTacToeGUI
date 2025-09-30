<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session;

use Renz\TicTacToe\libs\InvMenu\InvMenu;
use Renz\TicTacToe\libs\InvMenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}
