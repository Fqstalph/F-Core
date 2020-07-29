<?php

namespace Fqstalph;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;


class Main extends PluginBase implements Listener {

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function onLogin(PlayerLoginEvent $event)
    {
            $event->getPlayer()->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
    }

	public function onJoin(PlayerJoinEvent $event)
    {
      $player = $event->getPlayer();
		$message = str_replace("{player}", $player->getDisplayName(), $this->getConfig()->get("JoinMessage"));
		$this->getConfig()->get("JoinMessage");
        $event->setJoinMessage($message);
	}

	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
        $message = str_replace("{player}", $player->getDisplayName(), $this->getConfig()->get("QuitMessage"));
        $event->setQuitMessage($message);
	}

	public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool{
		switch($cmd->getName()) {
               case "rand":
			     if($sender->hasPermission("randomtp.cmd")) {
				   if($sender instanceof Player){
                    $players = $this->getServer()->getOnlinePlayers();
                    $random_key = array_rand($players);
                    $random_player = $players[$random_key];

					$sender->teleport($random_player);
					$message = str_replace("{player}", $random_player->getDisplayName(), $this->getConfig()->get("TeleportMessage"));
					$sender->sendMessage($message);
				   }
		} else {
			$sender->sendMessage("§cYou don't have enough permission!");
		}
				break;
				case "heal":
				  if($sender->hasPermission("heal.cmd")) {
				   if($sender instanceof Player){
					   if (isset($args[0])) {
						   $player = $this->getServer()->getPlayer($args[0]);
						   $player->setHealth(20);
						   $player->sendMessage($this->getConfig()->get("HealMessage"));
             } else {
               $sender->setHealth(20);
               $sender->sendMessage($this->getConfig()->get("HealMessage"));
             }
				   } else {
             $sender->sendMessage("§cPuoi eseguire questo comando solo in-game!");
           }
         } else {
           $sender->sendMessage("§cYou don't have enough permission!");
         }
         break;
				case "hub":
				  if($sender instanceof Player){
					     $sender->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
               $sender->sendMessage($this->getConfig()->get("HubMessage"));
				  }	else {
            $sender->sendMessage("§cPuoi eseguire questo comando solo in-game!");
          }
          break;
		}
		return true;
    }

	public function onDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent)
        {
            $killer = $cause->getDamager();
            if ($killer instanceof Player) {
                    $message = str_replace(["{killer}", "{victim}"], [$killer->getDisplayName(), $player->getDisplayName()], $this->getConfig()->get("DeathMessage"));
                    $event->setDeathMessage($message);
            }
        }
    }
}
