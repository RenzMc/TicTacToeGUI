<?php

declare(strict_types=1);

namespace Renz\TicTacToe;

use Renz\TicTacToe\libs\libEco;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use Renz\TicTacToe\libs\InvMenu\InvMenuHandler;
use Renz\TicTacToe\command\TicTacToeCommand;
use Renz\TicTacToe\Bot\EasyBot;
use Renz\TicTacToe\Bot\MediumBot;
use Renz\TicTacToe\Bot\HardBot;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    public array $games = [];
    public array $waiting = [];
    private int $rewardMoney;
    private libEco $libEco;
    private array $config;
    private array $messages;
    private array $guiSettings;
    private array $gameSettings;
    private string $economyType;
    private string $defaultBotDifficulty;
    private bool $broadcastWaiting;
    private string $commandPermission;

    public function onEnable(): void {
        // Save default config if it doesn't exist
        $this->saveDefaultConfig();
        
        // Load configuration
        $this->loadConfiguration();
        
        // Initialize economy
        $this->initializeEconomy();
        
        // Register events and commands
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("tictactoe", new TicTacToeCommand($this));

        // Register InvMenu handler
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }
    
    /**
     * Load all configuration settings
     */
    private function loadConfiguration(): void {
        $this->config = $this->getConfig()->getAll();
        
        // Load economy settings
        $this->economyType = $this->config["economy"]["type"] ?? "auto";
        $this->rewardMoney = (int)($this->config["economy"]["reward"] ?? 1000);
        
        // Load game settings
        $this->gameSettings = $this->config["game"] ?? [];
        $this->defaultBotDifficulty = $this->gameSettings["default_bot_difficulty"] ?? "easy";
        $this->broadcastWaiting = (bool)($this->gameSettings["broadcast_waiting"] ?? true);
        
        // Load GUI settings
        $this->guiSettings = $this->config["gui"] ?? [];
        
        // Load messages
        $this->messages = $this->config["messages"] ?? [];
        
        // Load permissions
        $this->commandPermission = $this->config["permissions"]["command"] ?? "tictactoe.cmd";
    }
    
    /**
     * Initialize the economy system
     */
    private function initializeEconomy(): void {
        $this->libEco = new libEco();
        $this->libEco->isInstall();
    }

    /**
     * Get the reward money amount
     */
    public function getRewardMoney(): int {
        return $this->rewardMoney;
    }
    
    /**
     * Get a formatted message from the config
     */
    public function getMessage(string $key, array $replacements = []): string {
        $prefix = $this->messages["prefix"] ?? "&7[&aTicTacToe&7] ";
        $message = $this->messages[$key] ?? "";
        
        // If message is not found, return empty string
        if ($message === "") {
            return "";
        }
        
        // Add prefix to message if it's not the help message (which already includes formatting)
        if ($key !== "help_message") {
            $message = $prefix . $message;
        }
        
        // Replace placeholders
        foreach ($replacements as $placeholder => $value) {
            $message = str_replace("{{$placeholder}}", (string)$value, $message);
        }
        
        // Convert color codes
        return $this->colorize($message);
    }
    
    /**
     * Convert color codes in a string
     */
    public function colorize(string $message): string {
        return TextFormat::colorize($message);
    }
    
    /**
     * Get GUI settings
     */
    public function getGuiSettings(): array {
        return $this->guiSettings;
    }
    
    /**
     * Get game settings
     */
    public function getGameSettings(): array {
        return $this->gameSettings;
    }
    
    /**
     * Get default bot difficulty
     */
    public function getDefaultBotDifficulty(): string {
        return $this->defaultBotDifficulty;
    }
    
    /**
     * Check if waiting broadcasts are enabled
     */
    public function isBroadcastWaitingEnabled(): bool {
        return $this->broadcastWaiting;
    }
    
    /**
     * Get command permission
     */
    public function getCommandPermission(): string {
        return $this->commandPermission;
    }

    /**
     * Handle player quit event
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        if (isset($this->games[$player->getName()])) {
            $this->removeGame($this->games[$player->getName()]);
        }
        unset($this->waiting[$player->getName()]);
    }

    /**
     * Remove a game
     */
    public function removeGame(Game $game): void {
        foreach ($game->getPlayers() as $player) {
            if ($player instanceof Player) {
                unset($this->games[$player->getName()]);
            }
        }
    }

    /**
     * Start a new game
     */
    public function startGame(Player $playerX, ?Player $playerO, string $botDifficulty = "easy"): void {
        $isVsBot = $playerO === null;
        $game = new Game($playerX, $playerO, $this, $isVsBot);
        if ($isVsBot) {
            switch ($botDifficulty) {
                case "medium":
                    $game->setBot(new MediumBot());
                    break;
                case "hard":
                    $game->setBot(new HardBot());
                    break;
                case "easy":
                default:
                    $game->setBot(new EasyBot());
                    break;
            }
        }
        $game->start();
        $this->games[spl_object_hash($game)] = $game;
    }
}