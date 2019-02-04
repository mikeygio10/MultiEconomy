<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use Twisted\MultiEconomy\commands\AddToBalanceCommand;
use Twisted\MultiEconomy\commands\BalanceCommand;
use Twisted\MultiEconomy\commands\BalanceTopCommand;
use Twisted\MultiEconomy\commands\PayCommand;
use Twisted\MultiEconomy\commands\RemoveFromBalanceCommand;
use Twisted\MultiEconomy\commands\SetBalanceCommand;

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
		$this->registerCurrencies();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->registerAll("MultiCommand", [
			new AddToBalanceCommand($this),
			new BalanceCommand($this),
			new BalanceTopCommand($this),
			new PayCommand($this),
			new RemoveFromBalanceCommand($this),
			new SetBalanceCommand($this)
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
			"eng",
			"chn"
		];
		return in_array(strtolower($lang), $supported) ? strtolower($lang) : "eng";
	}

	public function registerCurrencies(): void{
		try{
			foreach($this->getConfig()->get("currencies") as $currency => $data){
				$currency = new Currency((string)$data["name"], (string)$data["symbol"], (bool)$data["symbol-after"], (int)$data["starting-amount"], (int)$data["min-amount"], (int)$data["max-amount"]);
				$this->getAPI()->registerCurrency($currency);
			}
		}catch(\Throwable $exception){
			$this->getServer()->getLogger()->logException($exception);
		}
	}

	public function onJoin(PlayerJoinEvent $event): void{
		foreach($this->api->getCurrencies() as $name => $data){
			$this->api->checkBalance($event->getPlayer()->getName(), $name);
		}
	}
}