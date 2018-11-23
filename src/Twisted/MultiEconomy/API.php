<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy;

use pocketmine\utils\Config;

class API{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
	}

	public function getLangData(string $language): Config{
		return new Config($this->plugin->getDataFolder() . "lang/$language.yml", Config::YAML);
	}

	public function getMessage(string $key, string $player = "", string $currency = ""): string{
		$prefix = (string)$this->getLangData($this->plugin->getLanguage())->get("prefix");
		$prefix = str_replace("&", "§", $prefix);
		$symbol = $this->getCurrencyData($currency)["symbol"];
		$balance = (int)$this->getBalance($player, $currency);
		$balance = $this->getCurrencyData($currency)["symbol-after"] ? $balance = $balance . $symbol : $balance = $symbol . $balance;
		$message = (string)$this->getLangData($this->plugin->getLanguage())->get($key);
		$message = str_replace([
			"&",
			"{prefix}",
			"{player}",
			"{currency}",
			"{balance}",
			"{currencies}"
		], [
			"§",
			$prefix,
			$player,
			$currency,
			$balance,
			implode(", ", $this->getCurrencies())
		], $message);
		return $message;
	}

	public function getCurrencies(): array{
		$data = $this->plugin->getConfig()->get("currencies");
		if(!is_array($data)) return [];
		$currencies = [];
		foreach($data as $currency => $info) $currencies[] = $info["name"];
		return $currencies;
	}

	public function getCurrencyData(string $currency): array{
		$data = $this->plugin->getConfig()->getNested("currencies.$currency");
		if(!is_array($data)) $data = [
			"name" => "Dollars",
			"symbol" => "$",
			"symbol-after" => false,
			"starting-amount" => 0,
			"min-amount" => 0,
			"max-amount" => 999999
		];
		return $data;
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
			$config = $this->plugin->getConfig();
			$data->set($player, (int)$config->getNested("currencies.$currency.starting-amount"));
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