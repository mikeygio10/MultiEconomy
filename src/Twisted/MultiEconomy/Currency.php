<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy;

class Currency{

	/** @var string $name */
	private $name;
	/** @var string $symbol */
	private $symbol;
	/** @var bool $symbolafter */
	private $symbolafter;
	/** @var int $startingamount */
	private $startingamount;
	/** @var int $minamount */
	private $minamount;
	/** @var int $maxamount */
	private $maxamount;

	public function __construct(string $name, string $symbol, bool $symbolafter, int $startingamount, int $minamount, int $maxamount){
		$this->name = $name;
		$this->symbol = $symbol;
		$this->symbolafter = $symbolafter;
		$this->startingamount = $startingamount;
		$this->minamount = $minamount;
		$this->maxamount = $maxamount;
	}

	public function getName(): string{
		return $this->name;
	}

	public function getLowerName(): string{
		return strtolower($this->name);
	}

	public function getSymbol(): string{
		return $this->symbol;
	}

	public function isSymbolAfter(): bool{
		return $this->symbolafter;
	}

	public function getDisplayFormat(string $balance): string{
		return $this->symbolafter ? $balance . $this->symbol : $this->symbol . $balance;
	}

	public function getStartingAmount(): int{
		return $this->startingamount;
	}

	public function getMinAmount(): int{
		return $this->minamount;
	}

	public function getMaxAmount(): int{
		return $this->maxamount;
	}
}