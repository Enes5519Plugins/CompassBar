<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class CompassBar extends PluginBase implements Listener{

	public const REFRESH_RATE = 1;

	protected $barTasks = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$command->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			$sender->sendMessage(TextFormat::RED . "You can use this command in the game.");
			return true;
		}

		if(!isset($this->barTasks[$sender->getName()])){
			$this->barTasks[$sender->getName()] = $sender->getServer()->getScheduler()->scheduleRepeatingTask(new ShowBarTask($this, $sender), self::REFRESH_RATE);
			$sender->sendMessage(TextFormat::GREEN . "CompassBar is now on!");
		}else{
			$this->cancelTask($sender->getName());
			$sender->sendMessage(TextFormat::RED . "CompassBar is now off.");
		}

		return true;
	}

	private function cancelTask(string $p){
		if(isset($this->barTasks[$p])){
			$this->getServer()->getScheduler()->cancelTask($this->barTasks[$p]->getTaskId());
			unset($this->barTasks[$p]);
		}
	}

	public function onQuit(PlayerQuitEvent $event){
		$this->cancelTask($event->getPlayer()->getName());
	}

	public function onJoin(PlayerJoinEvent $event){
		$this->getServer()->dispatchCommand($event->getPlayer(), "compass");
	}
}