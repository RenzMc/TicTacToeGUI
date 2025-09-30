<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session\network;

use Closure;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;

final class NetworkStackLatencyEntry{

	/** @var int */
	public $timestamp;

	/** @var NetworkStackLatencyPacket */
	public $payload;

	/** @var Closure */
	public $then;

	public function __construct(int $timestamp, NetworkStackLatencyPacket $payload, Closure $then){
		$this->timestamp = $timestamp;
		$this->payload = $payload;
		$this->then = $then;
	}
}