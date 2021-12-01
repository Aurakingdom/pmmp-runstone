<?php

namespace phuongaz\RuneStone\handler;

use pocketmine\Player;
use pocketmine\event\entity\{
	EntityDamageByEntityEvent,EntityDamageEvent, EntityArmorChangeEvent
};
use pocketmine\event\Listener;


class EventHandler implements Listener{

	public function onDamge(EntityDamageEvent $event) :void {
		if($event instanceof EntityDamageByEntityEvent){
        	$entity = $event->getEntity();
        	$damager = $event->getDamager();
          	if($damager instanceof Player){
          		$item = $damager->getInventory()->getItemInHand();
          		$atk = 0;
          		if($item->getNamedTagEntry("atk") !== null){
          			$atk = $item->getNamedTagEntry("atk")->getValue();
          		}
	            $damage = $event->getBaseDamage() + $atk / 2;
	            $event->setBaseDamage($damage);
          	}
          	if($entity instanceof Player){
          		$item = $entity->getInventory()->getItemInHand();
          		$def = 0;
		        foreach($player->getArmorInventory()->getContents() as $item){
			        if($item->getNamedTagEntry("def") !== null){
			        	$def += $item->getNamedTagEntry("def")->getValue();
			        }
		        }
	            $damage = $event->getBaseDamage() - $def / 2;
	           	$event->setBaseDamage($damage);
          	}	
        }  		
	}

	public function onArmorChange(EntityArmorChangeEvent $event) :void{
		$hp = 20;
        $player = $event->getEntity();
        foreach($player->getArmorInventory()->getContents() as $item){
	        if($item->getNamedTagEntry("hp") !== null){
	        	$hp +=  $item->getNamedTagEntry("hp")->getValue();
	        }
        }
        $player->setMaxHealth($hp);
	}
}