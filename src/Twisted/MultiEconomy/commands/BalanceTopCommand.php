<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use Twisted\MultiEconomy\MultiEconomy;
use Twisted\MultiEconomy\tasks\SendTopBalancesTask;

class BalanceTopCommand extends PluginCommand{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
		parent::__construct("balancetop", $plugin);
		$this->setAliases(["baltop"]);
		$this->setDescription("Show the top balances for a currency");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$api = $this->plugin->getAPI();
		if(count($currencies = $api->getCurrencies()) === 0){
			$sender->sendMessage($api->getMessage("no-currencies-configured"));
			return;
		}
		if(empty($args[0])){
			$sender->sendMessage($api->getMessage("command-usage", [
				"{usage}" => "/balancetop <currency> [page]"
			]));
			return;
		}
		if(empty($currencies[strtolower($args[0])])){
			$sender->sendMessage($api->getMessage("currency-not-found", [
				"{currency}" => $args[0],
				"{currencies}" => implode(", ", $api->getCurrencyNames())
			]));
			return;
		}
		$currency = $currencies[strtolower($args[0])];
		$this->plugin->getServer()->getAsyncPool()->submitTask(new SendTopBalancesTask($sender->getName(), $currency, $api->getBalances($currency->getName())->getAll(), (isset($args[1]) ? ((int)$args[1] > 0 ? (int)$args[1] : 1) : 1)));
	}
}