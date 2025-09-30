<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\transaction;

final class InvMenuTransactionResult{

	/** @var bool */
	private $cancelled;

	public function __construct(bool $cancelled){
		$this->cancelled = $cancelled;
	}

	public function isCancelled() : bool{
		return $this->cancelled;
	}

	public static function cancel() : self{
		return new self(true);
	}

	public static function continue() : self{
		return new self(false);
	}
}