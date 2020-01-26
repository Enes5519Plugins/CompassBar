<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Player;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class CompassBar extends PluginBase implements Listener{
	/** @var Player[] */
	public $enabled = [];
	/** @var int */
	private $entityId;

	public function onEnable(){
		$this->entityId = Entity::$entityCount++;

		$this->saveDefaultConfig();

		$refreshRate = (int) $this->getConfig()->get("refresh-rate", 4);
		if($refreshRate < 1){
			$this->getLogger()->warning("Refresh rate property in config.yml is less than 1. Resetting to 1");
			$this->getConfig()->set("refresh-rate", 1);
			$this->getConfig()->save();
			$refreshRate = 1;
		}

		Utils::init();
		$this->getServer()->getPluginManager()->registerEvent(PlayerQuitEvent::class, $this, EventPriority::NORMAL, new MethodEventExecutor("onQuit"), $this, false);
		$this->getScheduler()->scheduleRepeatingTask(new ShowBarTask($this), $refreshRate);
	}


	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!($sender instanceof Player)){
			$sender->sendMessage(TextFormat::RED . "You can use this command in the game.");
			return true;
		}

		if($args[0] === "off")){
			$this->removeBossBar($sender);
			$sender->sendMessage(TextFormat::RED . "CompassBar is now off.");
		}else{
			$this->enabled[$sender->getLowerCaseName()] = $sender;

			$pk = new AddActorPacket();
			$pk->entityRuntimeId = $this->entityId;
			$pk->type = EntityIds::SLIME;
			$pk->metadata = [
				Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, ((1 << Entity::DATA_FLAG_INVISIBLE) | (1 << Entity::DATA_FLAG_IMMOBILE))],
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, '']
			];
			$pk->position = new Vector3();
			$sender->sendDataPacket($pk);

			$this->sendBossPacket($sender, '', BossEventPacket::TYPE_SHOW);

			$sender->sendMessage(TextFormat::GREEN . "CompassBar is now on!");
		}

		return true;
	}

	public function removeBossBar(Player $player) : void{
		$this->sendBossPacket($player, '', BossEventPacket::TYPE_HIDE);

		$pk = new RemoveActorPacket();
		$pk->entityUniqueId = $this->entityId;
		$player->sendDataPacket($pk);
	}

	public function onQuit(PlayerQuitEvent $event){
		if(!isset($this->enabled[$event->getPlayer()->getLowerCaseName()])){
			$this->removeBossBar($event->getPlayer());
		}
	}

	public function sendBossPacket(Player $player, string $title, int $eventType = BossEventPacket::TYPE_TITLE) : void{
		$pk = new BossEventPacket();
		$pk->bossEid = $this->entityId;
		$pk->eventType = $eventType;
		$pk->title = $title;

		if($eventType === BossEventPacket::TYPE_SHOW){
			$pk->healthPercent = 1.0;
			$pk->unknownShort = $pk->color = $pk->overlay = 0;
		}

		$player->sendDataPacket($pk);
	}
}
