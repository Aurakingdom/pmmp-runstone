<?php

namespace phuongaz\RuneStone\manager;

use pocketmine\item\Item;
use phuongaz\RuneStone\Loader;

Class ChanceManager{

	public function addChance(Item $item, float $chance) :?Item{
		if(Loader::getInstance()->isStone($item)){
			$old = $item->getNamedTagEntry("Chance")->getValue();
			$item->setNamedTagEntry(new FloatTag("Chance"), $old + $chance);
			return $item;
		}
		return null;
	}

	public function chance(Item $item) :bool{
		if(Loader::getInstance()->isStone($item)){
			$percentage = mt_rand(1, 10000);
			$chance = $item->getNamedTagEntry("Chance")->getValue();
			return ($percentage >= 1 && $percentage <= $chance*100);
		}
		return null;
	}

	public function calculateChance(Item $item, Item $stone) :float{
		if(!Loader::getInstance()->isStone($stone)) {var_dump("BUG IN ChanceManager.php line 29"); return 0.0;}
		$stone_chance = $stone->getNamedTagEntry("chance")->getValue();
		if($item->getNamedTagEntry("chance") !== null){
			$item_chance = $item->getNamedTagEntry("chance")->getValue();
		}else{
			$item_chance = 30;
		}
		$chance = 100 - ($item_chance + $stone_chance);
		return (float)$chance;
	}
}