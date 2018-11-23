<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use Twisted\MultiEconomy\MultiEconomy;

class BalanceCommand extends PluginCommand{

	/** @var MultiEconomy $plugin */
	private $plugin;

	public function __construct(MultiEconomy $plugin){
		$this->plugin = $plugin;
		parent::__construct("", $plugin);
		$this->setAliases(["bal"]);
		$this->setDescription("Check yours or another players balance");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		$api = $this->plugin->getAPI();
		if(count($currencies = $this->plugin->getAPI()->getCurrencies()) === 0){
			$sender->sendMessage($api->getMessage("no-currencies-configured"));
			return;
		}
		if(empty($args[0])){
			if(!$sender instanceof Player){
				$sender->sendMessage($api->getMessage("use-command-in-game"));
				return;
			}
			$sender->sendMessage($api->getMessage("list-currencies"));
			foreach($currencies as $currency){
				$sender->sendMessage($api->getMessage("own-balance", $sender->getName(), $currency));
			}
			return;
		}
		if($sender instanceof Player){
			foreach($api->getCurrencies() as $currency){
				if(strtolower($currency) == strtolower($args[0])){
					$sender->sendMessage($api->getMessage("own-balance", $sender->getName(), $currency));
					return;
				}
			}
		}
		$target = $this->plugin->getServer()->getPlayer($args[0]) ?? $this->plugin->getServer()->getOfflinePlayer($args[0]);
		if(!$target->hasPlayedBefore()){
			$sender->sendMessage($api->getMessage("player-not-played", $target->getName()));
			return;
		}
		if(empty($args[1])){
			$sender->sendMessage($api->getMessage("list-currencies-other", $target->getName()));
			foreach($currencies as $currency){
				$sender->sendMessage($api->getMessage("other-balance", $target->getName(), $currency));
			}
			return;
		}
		foreach($api->getCurrencies() as $currency){
			if(strtolower($currency) == strtolower($args[1])){
				$sender->sendMessage($api->getMessage("other-balance", $target->getName(), $currency));
				return;
			}
		}
		$sender->sendMessage($api->getMessage("currency-not-found", $sender->getName(), $args[1]));
	}
}