<?php
declare(strict_types=1);

namespace Twisted\MultiEconomy\tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Twisted\MultiEconomy\Currency;
use Twisted\MultiEconomy\MultiEconomy;

class SendTopBalancesTask extends AsyncTask{

	/** @var string $target */
	private $target;
	/** @var Currency */
	private $currency;
	/** @var string $balances */
	private $balances;
	/** @var int $page */
	private $page;

	public function __construct(string $target, Currency $currency, array $balances, int $page){
		$this->target = $target;
		$this->currency = $currency;
		$this->balances = serialize($balances);
		$this->page = $page;
	}

	public function onRun(){
		$balances = unserialize($this->balances);
		arsort($balances);
		$max = ceil(count($balances) / 10);
		$page = (int)min($max, max(1, $this->page));
		$top = [];
		$i = 1;
		foreach($balances as $player => $balance){
			$currentPage = (int)ceil($i / 10);
			if($currentPage !== $page) break;
			$top[$i] = [$player, $balance];
			++$i;
		}
		$this->setResult(serialize($top));
	}

	public function onCompletion(Server $server){
		if(($target = $server->getPlayer($this->target)) === null) return;
		/** @var MultiEconomy $plugin */
		$plugin = $server->getPluginManager()->getPlugin("MultiEconomy");
		$api = $plugin->getAPI();
		$top = unserialize($this->getResult());
		$currency = $this->currency;
		if(count($top) === 0){
			$target->sendMessage($api->getMessage("no-top-balances", [
				"{currency}" => $currency->getName(),
				"{page}" => $this->page
			]));
			return;
		}
		$target->sendMessage($api->getMessage("top-balances-title", [
			"{currency}" => $currency->getName(),
			"{page}" => $this->page
		]));
		foreach($top as $place => $info){
			$target->sendMessage($api->getMessage("top-balances-format", [
				"{place}" => $place,
 				"{player}" => $info[0],
				"{balance}" => $currency->getDisplayFormat((string)$info[1])
			]));
		}
	}
}