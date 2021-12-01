<?php

namespace phuongaz\RuneStone\command;

use phuongaz\RuneStone\inventory\RuneStoneInventory;
use phuongaz\RuneStone\Loader;

use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;


Class RuneStoneCommand extends Command{
	

	public function __construct(){
		parent::__construct("runestone", "Kham ngoc");
	}

	public function execute(CommandSender $sender, string $label, array $args) :bool {
		if($sender instanceof Player){
			$item = Loader::getInstance()->getStone("protection", 10);
			$sender->getInventory()->addItem($item);
			$menu = new RuneStoneInventory();
			$inv = $menu->create();
			$inv->send($sender);
		}else echo "debug command";
		return true;
	}
}