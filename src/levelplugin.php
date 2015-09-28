<?php
namespace levelplugin;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\ServerOperator;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\server\RemoteServerCommandEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\BlockBreakEvent;
class levelplugin extends PluginBase implements Listener {
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "LevelPluginが読み込まれました   " . TextFormat::GREEN . "製作者:maa123");
        if (!file_exists($this->getDataFolder())) { //configを入れるフォルダが有るかチェック
            mkdir($this->getDataFolder(), 0744, true); //なければフォルダを作成
            $this->exps = new Config($this->getDataFolder() . "exps.json", Config::JSON, array());
            $this->levels = new Config($this->getDataFolder() . "level.json", Config::JSON, array());
        }
        $this->exps = new Config($this->getDataFolder() . "exps.json", Config::JSON, array());
        $this->levels = new Config($this->getDataFolder() . "level.json", Config::JSON, array());
    }
    public function onDisable() {
        $this->getLogger()->info(TextFormat::GREEN . "LevelPluginが終了しました   " . TextFormat::GREEN . "製作者:maa123");
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
    }
    public function onPlayerDeath(PlayerDeathEvent $event) {
        $ev = $event->getEntity()->getLastDamageCause();
        if ($ev instanceof EntityDamageByEntityEvent) {
            $killer = $ev->getDamager();
            if ($killer instanceof Player) {
                $exp = $this->exps->get($killer->getName());
                $exp = intval($exp) + 1; //経験値
                $level = intval($this->levels->get($killer->getName()));
                $lupexp = floor((pow(2, ($level + 1)) * 5) / 3);
                if ($lupexp = < $exp) {
                    $exp-= $lupexp;
                    $level++;
                    $killer->sendMessage("[level]" . $level . "レベルになりました");
                    $this->levels->set($killer->getName(), $level);
                    $this->levels->save();
                }
                $this->exps->set($killer->getName(), $exp);
                $this->exps->save();
            }
        }
    }
}
