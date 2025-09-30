<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type;

use Renz\TicTacToe\libs\InvMenu\type\util\InvMenuTypeBuilders;
use InvalidArgumentException;

final class InvMenuTypeRegistry{

	/** @var InvMenuType[] */
	private $types = [];

	/** @var InvMenuTypeBuilders */
	private $builders;

	public function __construct(){
		$this->builders = new InvMenuTypeBuilders();
	}

	public function register(string $identifier, InvMenuType $type) : void{
		if(isset($this->types[$identifier])){
			throw new InvalidArgumentException("A menu type with the identifier &quot;" . $identifier . "&quot; is already registered");
		}

		$this->types[$identifier] = $type;
	}

	public function get(string $identifier) : InvMenuType{
		if(!isset($this->types[$identifier])){
			throw new InvalidArgumentException("No menu type with the identifier &quot;" . $identifier . "&quot; is registered");
		}

		return $this->types[$identifier];
	}

	public function getBuilders() : InvMenuTypeBuilders{
		return $this->builders;
	}
}