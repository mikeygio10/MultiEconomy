<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use Twisted\MultiEconomy\commands\BalanceCommand;

class MultiEconomy extends PluginBase implements Listener{

	/** @var API $api */
	private $api;

	public function onEnable(): void{
		$this->api = new API($this);
		@mkdir($this->getDataFolder() . "currencies");
		@mkdir($this->getDataFolder() . "lang");
		$this->saveDefaultConfig();
		$this->checkLanguage();
		$this->saveResource("lang/" . $this->getLanguage() . ".yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->registerAll("MultiCommand", [
			new BalanceCommand($this)
		]);
	}

	public function getAPI(): API{
		return $this->api;
	}

	public function checkLanguage(): void{
		if($this->getLanguage() !== (string)$this->getConfig()->get("lang")){
			$this->getConfig()->set("lang", "eng");
			$this->getLogger()->error("Language not supported, setting default (eng)");
		}
	}

	public function getLanguage(): string{
		$lang = (string)$this->getConfig()->get("lang");
		$supported = [
			"eng"
		];
		return in_array(strtolower($lang), $supported) ? strtolower($lang) : "eng";
	}

	public function onJoin(PlayerJoinEvent $event): void{
		$player = $event->getPlayer();
		foreach($this->api->getCurrencies() as $currency){
			if($this->api->getBalance($player->getName(), $currency) === null) $this->api->setBalance($player->getName(), $currency, $this->api->getCurrencyData($currency)["starting-amount"]);
		}
	}
}