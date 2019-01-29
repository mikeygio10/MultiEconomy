<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use Twisted\MultiEconomy\MultiEconomy;

class PayCommand extends PluginCommand{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
		parent::__construct("pay", $plugin);
		$this->setDescription("Pay another player money for a currency");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		$api = $this->plugin->getAPI();
		if(count($currencies = $this->plugin->getAPI()->getCurrencies()) === 0){
			$sender->sendMessage($api->getMessage("no-currencies-configured"));
			return;
		}
		if(empty($args[2])){
			$sender->sendMessage($api->getMessage("command-usage", [
				"{usage}" => "/pay <target> <currency> <amount>"
			]));
			return;
		}
		if(($target = $sender->getServer()->getPlayer($args[0])) === null){
			$sender->sendMessage($api->getMessage("target-not-played", [
				"{target}" => $args[0]
			]));
			return;
		}
		if($target->getName() == $sender->getName()){
			$sender->sendMessage($api->getMessage("cannot-pay-self"));
			return;
		}
		if(empty($currencies[strtolower($args[0])])){
			$sender->sendMessage($api->getMessage("currency-not-found", [
				"{currency}" => $args[1],
				"{currencies}" => implode(",", $api->getCurrencyNames())
			]));
			return;
		}
		$currency = $currencies[strtolower($args[1])];
		$amount = (int)$args[2];
		if($amount < 1){
			$sender->sendMessage($api->getMessage("value-not-valid"));
			return;
		}
		if($api->getBalance($sender->getName(), $currency->getName()) < $amount){
			$sender->sendMessage($api->getMessage("not-enough-money"));
			return;
		}
		$api->takeFromBalance($sender->getName(), $currency->getName(), $amount);
		$api->addToBalance($target->getName(), $currency->getName(), $amount);
		$sender->sendMessage($api->getMessage("payment-sent", [
			"{target}" => $target->getName(),
			"{currency}" => $currency->getName(),
			"{amount}" => $currency->getDisplayFormat((string)$amount)
		]));
		$target->sendMessage($api->getMessage("payment-received", [
			"{target}" => $sender->getName(),
			"{currency}" => $currency,
			"{amount}" => $currency->getDisplayFormat((string)$amount)
		]));
	}
}