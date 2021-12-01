<?php

namespace phuongaz\RuneStone;

use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;

use phuongaz\RuneStone\command\RuneStoneCommand;
use phuongaz\RuneStone\inventory\RuneStoneInventory;
use phuongaz\RuneStone\manager\ChanceManager;
use phuongaz\RuneStone\form\{
	StoneStoreForm,
	StoneInfoForn
};
use muqsit\invmenu\InvMenuHandler;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;
Class Loader extends PluginBase{

	/** @var array */
	private static $stones = [];

	/**@var self*/
	private static $instance;

	public function onEnable() :void{
		self::$instance = $this;
		$this->registerMenu();
		$this->getServer()->getCommandMap()->register('runestone', new RuneStoneCommand());
		$this->saveResource("stones/protection.yml");
		foreach(glob($this->getDataFolder(). "stones/*.yml") as $stonePath){
			$stone = pathinfo($stonePath, PATHINFO_FILENAME);
			self::$stones[$stone] = $stone;
		}
	}

	public static function getInstance() :self{
		return self::$instance;
	}

	public function registerMenu() :void {
		$class = new RuneStoneInventory();
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
	}

	public function getChanceManager() :ChanceManager{
		return new ChanceManager();
	}

	public function getStone(string $stone, int $count) :?Item{
		$data_stone = $this->getDataByName($stone);
		$ex_id = explode(":", $data_stone["id"]);
		$item = Item::get($ex_id[0], $count, $ex_id[1]);
		$item = $this->enchantItem($item, $data_stone);
		$nbt = $item->getNamedTag();
		$nbt->setString("stone", $stone);
		$nbt->setFloat("chance", (float)$data_stone["chance"]);
		$item->setNamedTag($nbt);
		return $item;
	}

	public function enchantItem(Item $item, array $data) :Item{
		$item->setCustomName(str_replace("&", "§", $data["name"]));
		$item->setLore($data["lore"]);		
		$this->setStats($item, $data["stats"]);

		if($item->getNamedTag()->hasTag("chance", FloatTag::class)){
			$old = $item->getNamedTag("chance", FloatTag::class)->getValue();
			$chance = (float)($old - mt_rand(5, 10));
		}else{
			$chance = (float)(30 - mt_rand(5, 10));
		}
		//$item->setNamedTagEntry(new FloatTag("chance", $chance));
		$nbt = $item->getNamedTag();
		$nbt->setFloat("chance", $chance);
		$item->setNamedTag($nbt);
/*		$enchants = array_map(function(string $enchant) use ($item){
			$ex_ec = explode(":", $enchant);
			return $enchantment = new EnchantmentInstance(Enchantment::getEnchantment($ec[0]), $ec[1]);
		}, $data["enchant"]["custom"]);

		foreach($enchants as $enchant){
			$item->addEnchantment($enchant);
		}*/

/*		$custom = array_map(function(string $enchant){
			//TODO:
		}, $data["enchant"]["custom"]);*/

		return $item;
	}

	public function setStats(Item $item, array $stats){
		$lore = array_merge($item->getLore(), [
			"§l§fHP:§a +".($stats["HP"]/2). "",
			"§l§fATK:§a +".$stats["ATK"],
			"§l§fDEF:§a +".$stats["DEF"] 
		]);
		$item->setLore($lore);

		if(!$item->getNamedTag()->hasTag("hp", FloatTag::class)){
			$nbt = $item->getNamedTag();
			$nbt->setFloat("hp", $stats["HP"]);
			$nbt->setFloat("atk", $stats["ATK"]);
			$nbt->setFloat("def", $stats["DEF"]);
			$item->setNamedTag($nbt);
		}else{
			$current = $this->getStats($item);
			$nbt = $item->getNamedTag();
			$nbt->setFloat("hp", $stats["HP"] + $current["HP"]);
			$nbt->setFloat("atk", $stats["ATK"] + $current["ATK"]);
			$nbt->setFloat("def", $stats["DEF"] + $current["DEF"]);
			$item->setNamedTag($nbt);
		}
		return $item;
	}

	public function getStats(Item $item) :array{
		$nbt = $item->getNamedTag();
		return 
		[
			"HP" => $nbt->getTag("hp", FloatTag::class)->getValue(),
			"ATK" => $nbt->getTag("atk", FloatTag::class)->getValue(),
			"ATK" => $nbt->getTag("def", FloatTag::class)->getValue()
		];
	}

	public function getDataByName(string $stone) :?array{
		return (in_array($stone, self::$stones)) ? yaml_parse_file($this->getDataFolder(). "stones/$stone.yml") : null;
	}

	public function isStone(Item $item) :bool{
		return $item->getNamedTag()->hasTag("stone", StringTag::class);
	}
}
