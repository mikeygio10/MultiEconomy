<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy;

use pocketmine\utils\Config;

class API{

	/** @var MultiEconomy $plugin */
	private $plugin;
	/** @var Currency[] $currencies
	 */
	private $currencies = [];

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
	}

	public function registerCurrency(Currency $currency): bool{
		if(isset($this->currencies[$currency->getLowerName()])) return false;
		$this->currencies[$currency->getLowerName()] = $currency;
		return true;
	}

	public function getLangData(string $language): Config{
		return new Config($this->plugin->getDataFolder() . "lang/$language.yml", Config::YAML);
	}

	public function getMessage(string $key, array $values = []): string{
		$message = (string)$this->getLangData($this->plugin->getLanguage())->get($key);
		$prefix = (string)$this->getLangData($this->plugin->getLanguage())->get("prefix");
		$message = str_replace("{prefix}", $prefix, $message);
		$message = str_replace("&", "§", $message);
		foreach($values as $search => $replace) $message = str_replace($search, $replace, $message);
		return $message;
	}

	/**
	 * @return Currency[]
	 */
	public function getCurrencies(): array{
		return $this->currencies;
	}

	public function getCurrencyNames(): array{
		$names = [];
		foreach($this->currencies as $currency => $data) $names[] = $data->getName();
		return $names;
	}

	public function checkBalance(string $player, string $currency): void{
		$data = $this->getBalances($currency);
		if($data->get($player) === false){
			$data->set($player, $this->currencies[$currency]->getStartingAmount());
			$data->save();
		}
	}

	public function addToBalance(string $player, string $currency, int $amount): void{
		$player = strtolower($player);
		$data = $this->getBalances($currency);
		$data->set($player, (int)$data->get($player) + $amount);
		$data->save();
	}

	public function getBalance(string $player, string $currency): ?int{
		$player = strtolower($player);
		$data = $this->getBalances($currency);
		if($data->get($player) === false){
			$data->set($player, $this->currencies[$currency]->getStartingAmount());
			$data->save();
			return null;
		}
		return (int)$data->get($player);
	}

	public function getBalances(string $currency): Config{
		return new Config($this->plugin->getDataFolder() . "currencies/$currency.json", Config::JSON);
	}

	public function setBalance(string $player, string $currency, int $balance): void{
		$player = strtolower($player);
		$data = $this->getBalances($currency);
		$data->set($player, $balance);
		$data->save();
	}

	public function takeFromBalance(string $player, string $currency, int $amount): void{
		$player = strtolower($player);
		$data = $this->getBalances($currency);
		$data->set($player, (int)$data->get($player) - $amount);
		$data->save();
	}
}