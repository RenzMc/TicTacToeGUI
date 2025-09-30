<?php

declare(strict_types=1);

namespace Renz\TicTacToe\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Renz\TicTacToe\Main;
use Renz\TicTacToe\Game;
use Renz\TicTacToe\Bot\EasyBot;
use Renz\TicTacToe\Bot\MediumBot;
use Renz\TicTacToe\Bot\HardBot;

class TicTacToeCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("tictactoe", "TicTacToe commands", "/tictactoe <start|join|bot|help|list|end>", ["ttt"]);
        $this->plugin = $plugin;
        $this->setPermission($plugin->getCommandPermission());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return true;
        }

        if (count($args) < 1) {
            return false;
        }

        switch (strtolower($args[0])) {
            case "start":
                if (isset($this->plugin->waiting[$sender->getName()])) {
                    $sender->sendMessage($this->plugin->getMessage("already_waiting"));
                    return true;
                }
                $this->plugin->waiting[$sender->getName()] = true;
                $sender->sendMessage($this->plugin->getMessage("waiting_for_player"));
                
                // Only broadcast if enabled in config
                if ($this->plugin->isBroadcastWaitingEnabled()) {
                    $this->plugin->getServer()->broadcastMessage(
                        $this->plugin->getMessage("broadcast_waiting", ["player" => $sender->getName()])
                    );
                }
                break;

            case "bot":
                if (isset($args[1]) && in_array(strtolower($args[1]), ["easy", "medium", "hard"])) {
                    $botDifficulty = strtolower($args[1]);
                    $bot = match ($botDifficulty) {
                        "easy" => new EasyBot(),
                        "medium" => new MediumBot(),
                        "hard" => new HardBot(),
                    };
                    $game = new Game($sender, null, $this->plugin, true, $bot);
                    $this->plugin->games[$sender->getName()] = $game;
                    $game->start();
                    $sender->sendMessage($this->plugin->getMessage("bot_start", ["difficulty" => $botDifficulty]));
                } else {
                    $sender->sendMessage($this->plugin->getMessage("bot_usage"));
                }
                break;

            case "join":
                if (isset($this->plugin->games[$sender->getName()])) {
                    $sender->sendMessage($this->plugin->getMessage("already_in_game"));
                    return true;
                }
                if (isset($this->plugin->waiting[$sender->getName()])) {
                    $sender->sendMessage($this->plugin->getMessage("already_started_game"));
                    return true;
                }
                if (count($args) < 2) {
                    $sender->sendMessage($this->plugin->getMessage("join_usage"));
                    return true;
                }
                $opponentName = $args[1];
                if (!isset($this->plugin->waiting[$opponentName])) {
                    $sender->sendMessage($this->plugin->getMessage("player_not_waiting"));
                    return true;
                }
                $opponent = $this->plugin->getServer()->getPlayerByPrefix($opponentName);
                if ($opponent === null || !$opponent->isOnline()) {
                    $sender->sendMessage($this->plugin->getMessage("player_not_online"));
                    return true;
                }
                $game = new Game($opponent, $sender, $this->plugin);
                $this->plugin->games[$opponent->getName()] = $game;
                $this->plugin->games[$sender->getName()] = $game;
                unset($this->plugin->waiting[$opponent->getName()]);
                $game->start();
                break;

            case "help":
                $sender->sendMessage($this->plugin->getMessage("help_message"));
                break;

            case "list":
                if (empty($this->plugin->waiting)) {
                    $sender->sendMessage($this->plugin->getMessage("no_waiting_players"));
                } else {
                    $sender->sendMessage($this->plugin->getMessage("waiting_players_header"));
                    foreach ($this->plugin->waiting as $playerName => $value) {
                        $sender->sendMessage($this->plugin->getMessage("waiting_players_entry", ["player" => $playerName]));
                    }
                }
                break;

            case "end":
                if (!isset($this->plugin->games[$sender->getName()])) {
                    $sender->sendMessage($this->plugin->getMessage("not_in_game"));
                    return true;
                }
                $this->plugin->games[$sender->getName()]->end();
                break;

            default:
                $sender->sendMessage($this->plugin->getMessage("unknown_command"));
                return false;
        }

        return true;
    }
}