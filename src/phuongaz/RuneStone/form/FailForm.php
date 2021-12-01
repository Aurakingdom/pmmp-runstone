<?php

namespace phuongaz\RuneStone\form;

use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;

Class FailForm{

	private static $data = [];
	private $player;

	public function __construct(Player $player, array $data){
		self::$data = $data;
		$this->player = $player;
	}

	public function init() :void{
		$form = new CustomForm(function(Player $player, ?array $data){
			if(is_null($data)) return;
		});
		$item_1 = self::$data[0];
		$item_2 = self::$data[1];
		$chance = $item->
		$form->setTitle("KHẢM NGỌC");
		$form->addLabel("Vật phẩm: ".$item_1->getCustomName());
		$form->addLabel("Ngọc: ".$item_2->getCustomName());
		$form->addLabel("Tỉ lệ thành công: ".$chance;
		$form->sendToPlayer($this->player);
	}
}