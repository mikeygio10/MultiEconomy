<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use Twisted\MultiEconomy\MultiEconomy;

class RemoveFromBalanceCommand extends PluginCommand{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
		parent::__construct("removefrombalance", $plugin);
		$this->setAliases(["remfrombal"]);
		$this->setDescription("Remove from a players balance");
		$this->setPermission("multieconomy.removefrombalance");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		if(!$this->testPermission($sender)) return;
		$api = $this->plugin->getAPI();
		if(count($currencies = $api->getCurrencies()) === 0){
			$sender->sendMessage($api->getMessage("no-currencies-configured"));
			return;
		}
		if(empty($args[2])){
			$sender->sendMessage($api->getMessage("command-usage", [
				"{usage}" => "/removefrombalance <target> <currency> <amount>"
			]));
			return;
		}
		if(($target = $sender->getServer()->getPlayer($args[0])) === null){
			$sender->sendMessage($api->getMessage("target-not-played", [
				"{target}" => $args[0]
			]));
			return;
		}
		if(empty($currencies[strtolower($args[1])])){
			$sender->sendMessage($api->getMessage("currency-not-found", [
				"{currency}" => $args[1],
				"{currencies}" => implode(", ", $api->getCurrencyNames())
			]));
			return;
		}
		$currency = $currencies[strtolower($args[1])];
		$amount = (int)$args[2];
		if($amount < 1){
			$sender->sendMessage($api->getMessage("value-not-valid"));
			return;
		}
		$api->takeFromBalance($target->getName(), $currency->getName(), $amount);
		$sender->sendMessage($api->getMessage("target-balance-removed", [
			"{target}" => $target->getName(),
			"{currency}" => $currency->getName(),
			"{amount}" => $currency->getDisplayFormat((string)$amount)
		]));
		$target->sendMessage($api->getMessage("own-balance-removed", [
			"{target}" => $sender->getName(),
			"{currency}" => $currency->getName(),
			"{amount}" => $currency->getDisplayFormat((string)$amount)
		]));
	}
}