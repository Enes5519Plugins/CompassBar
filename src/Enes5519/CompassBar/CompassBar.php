<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\utils\Bossbar;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

class CompassBar extends PluginBase implements Listener{

    public const BOSSBAR_ID = 500;

    /** @var int */
    public $refreshRate = 4;
    /** @var TaskHandler[] */
    protected $barTasks = [];
    /** @var Bossbar */
    protected $bossBar;

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        $this->refreshRate = (int) $this->getConfig()->get("refresh-rate", 4);
        if($this->refreshRate < 1){
            $this->getLogger()->warning("Refresh rate property in config.yml is less than 1. Resetting to 1");
            $this->getConfig()->set("refresh-rate", 1);
            $this->getConfig()->save();
            $this->refreshRate = 1;
        }

        Utils::init();
        $this->bossBar = new Bossbar("Loading Compass Bar");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new ShowBarTask($this), $this->refreshRate);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!($sender instanceof Player)){
            $sender->sendMessage(TextFormat::RED . "You can use this command in the game.");
            return true;
        }

        if($sender->getBossbar(self::BOSSBAR_ID) == null){
            $sender->addBossbar($this->bossBar, self::BOSSBAR_ID);
            $sender->sendMessage(TextFormat::GREEN . "CompassBar is now on!");
        }else{
            $sender->removeBossbar(self::BOSSBAR_ID);
            $sender->sendMessage(TextFormat::RED . "CompassBar is now off.");
        }

        return true;
    }

    /**
     * @return Bossbar
     */
    public function getBossBar() : Bossbar{
        return $this->bossBar;
    }

    public function onQuit(PlayerQuitEvent $event){
        $event->getPlayer()->removeBossbar(self::BOSSBAR_ID);
    }
}