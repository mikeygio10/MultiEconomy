<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use Twisted\MultiEconomy\Currency;
use Twisted\MultiEconomy\MultiEconomy;

class BalanceCommand extends PluginCommand{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
		parent::__construct("mygems", $plugin);
		$this->setAliases([""]);
		$this->setDescription("Check yours or another targets balance");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		$api = $this->plugin->getAPI();
		if(count($currencies = $api->getCurrencies()) === 0){
			$sender->sendMessage($api->getMessage("no-currencies-configured"));
			return;
		}
		if(empty($args[0])){
			if(!$sender instanceof Player){
				$sender->sendMessage($api->getMessage("use-command-in-game"));
				return;
			}
			$sender->sendMessage($api->getMessage("list-currencies"));
			/**
			 * @var string   $currency
			 * @var Currency $data
			 */
			foreach($currencies as $currency => $data){
				$sender->sendMessage($api->getMessage("own-balance", [
					"{target}" => $sender->getName(),
					"{currency}" => $data->getName(),
					"{balance}" => $data->getDisplayFormat((string)$api->getBalance($sender->getName(), $currency))
				]));
			}
			return;
		}
		if($sender instanceof Player){
			if(!empty($currencies[strtolower($args[0])])){
				$currency = $currencies[strtolower($args[0])];
				$sender->sendMessage($api->getMessage("own-balance", [
					"{target}" => $sender->getName(),
					"{currency}" => $currency->getName(),
					"{balance}" => $currency->getDisplayFormat((string)$api->getBalance($sender->getName(), $currency->getLowerName()))
				]));
				return;
			}
		}
		$target = $this->plugin->getServer()->getPlayer($args[0]) ?? $this->plugin->getServer()->getOfflinePlayer($args[0]);
		if(!$target->hasPlayedBefore()){
			$sender->sendMessage($api->getMessage("target-not-played", [
				"{target}" => $target->getName()
			]));
			return;
		}
		if(empty($args[1])){
			$sender->sendMessage($api->getMessage("list-currencies-other", [
				"{target}" => $target->getName()
			]));
			/**
			 * @var string   $currency
			 * @var Currency $data
			 */
			foreach($currencies as $currency => $data){
				$sender->sendMessage($api->getMessage("other-balance", [
					"{target}" => $target->getName(),
					"{currency}" => $data->getName(),
					"{balance}" => $data->getDisplayFormat((string)$api->getBalance($target->getName(), $currency))
				]));
			}
			return;
		}
		if(!empty($currencies[strtolower($args[0])])){
			$currency = $currencies[strtolower($args[0])];
			$sender->sendMessage($api->getMessage("own-balance", [
				"{target}" => $target->getName(),
				"{currency}" => $currency->getName(),
				"{balance}" => $currency->getDisplayFormat((string)$api->getBalance($target->getName(), $currency->getLowerName()))
			]));
			return;
		}
		$sender->sendMessage($api->getMessage("currency-not-found", [
			"{currency}" => strtolower($args[0]),
			"{currencies}" => implode(",", $api->getCurrencyNames())
		]));
	}
}
