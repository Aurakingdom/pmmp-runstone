<?php

namespace phuongaz\RuneStone\inventory;

use phuongaz\RuneStone\Loader;
use phuongaz\RuneStone\form\FailForm;

use muqsit\invmenu\InvMenu;
use muqsit\invmenuutils\InvMenuListenerUtils;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\nbt\tag\StringTag;

Class RuneStoneInventory{

	private static $old_data = [];
	private $data = false;

	public function getLoader() :Loader{
		return Loader::getInstance();
	}

	public function create(){
		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName("§l§dKHẢM NGỌC\n§l§eLOCM");
		$inv = $menu->getInventory();
		$vines = Item::get(106, 0, 1)->setCustomName("");
		$concrete = Item::get(159, 13, 1)->setCustomName("§l§aXem tỉ lệ thành công");
		$inv->setItem(2, $vines);
		$inv->setItem(4, $concrete);
		return $menu;
	}

	public function getStart(){
		$menu = $this->create();
		$menu->setListener(function(InvMenuTransaction $transaction) :InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$action = $transaction->getAction();
			$clicked = $transaction->getItemClicked();
			if($action->getSlot() === 4){
				$inv = $action->getInventory();
				$item_1 = $inv->getItem(0);
				$item_2 = $inv->getItem(1);
				if($item_1 == Block::get(Item::AIR)){
					$inv->setItem(4, Item::get(159, 14, 1)->setCustomName("Cần đặt ngọc và vật phẩm\nvào để khám"));
					return $transaction->discard();
				}
				if(Loader::getInstance()->isStone($item_2)){
					$inv->setItem(4, Item::get(159, 14, 1)->setCustomName("Cần đặt ngọc và vật phẩm\nvào để khám"));
					return $transaction->discard();
				}
				$player->removeWindow($inv);
				$this->form($player, $item_1, $item_2);
				return $transaction->continue();
			}
			if($action->getSlot() == 2) return $transaction->discard();
			return $transaction->continue();
		});
		return $menu;
	}

	public function form(Player $player, Item $item_1, Item $item_2) :void{
		$tag = $item_2->getNamedTag()->getTag("stone", StringTag::class)->getValue();
		$data = Loader::getInstance()->getDataByName($tag);
		$item = Loader::getInventory()->enchantItem($item_1, $data);
		$chance = Loader::getInstance()->getChanceManager()->calculateChance($item_1, $item_2);
		$simpleform = new SimpleForm(function(Player $player, ?int $data)use ($chance, $item_1, $item_2, $item){
			if(is_null($data)) return;
			if($data == 0){
				$run = Loader::getInstance()->getChanceManager()->chance($chance);
				if($run){
					$this->sussces($player, $item, $item_1, $item_2);
				}else{
					$this->fail($player, $item_1, $item_2);
				}
			}
		});

		$form->setTitle("§l§dKHAM NGOC");
		$content = "";
		$content .= "Bạn có chắc muốn khảm không?\n";
		$content .= "Tỉ lệ thành công: $chance\n";
		$content .= "Tỉ lệ thất bại: ". (100 - $chance)."\n";
		$content .= "Khi thành công bạn sẽ nhận được vật phẩm:\n";
		$stats = implode("\n +", Loader::getInstance()->getStats($item));
		$content .= $stats;

		$form->addButton("Khảm");
		$form->addButton("Từ chối");
		$form->addButton("Mua bùa may mắn");
		$form->setContent($content);
		$form->sendToPlayer($player);
	}

	public function succes(PLayer $player, Item $item, Item $item_1, Item $item_2){
		$menu = $this->create();
		$menu->getInventory()->setItem(0, $item_1);
		$menu->getInventory()->setItem(1, $item_2);
		$menu->getInventory()->setItem(3, $item);
		$concrete = Item::get(399, 0, 1)->setCustomName("§l§aThành công");
		$menu->setListener(function(InvMenuTransaction $transaction) :InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$action = $transaction->getAction();
			$clicked = $transaction->getItemClicked();
			if($action->getSlot() == 3){
				return $transaction->continue();	
			}else{
				return $transaction->discard();				
			}
		});
		$menu->send($player);
	}

	public function fail(PLayer $player, Item $item_1, Item $item_2){
		$menu = $this->create();
		$menu->getInventory()->setItem(0, $item_1);
		$menu->getInventory()->setItem(1, $item_2);
		$concrete = Item::get(399, 0, 1)->setCustomName("§l§cThất bại");
		$menu->setListener(InvMenu::readonly());
		$menu->send($player);
	}
}