<?php

declare(strict_types=1);

namespace Renz\TicTacToe;

use Renz\TicTacToe\libs\libEco;
use Renz\TicTacToe\Bot\Bot;
use Renz\TicTacToe\Bot\EasyBot;
use Renz\TicTacToe\Bot\MediumBot;
use Renz\TicTacToe\Bot\HardBot;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use Renz\TicTacToe\libs\InvMenu\InvMenu;
use Renz\TicTacToe\libs\InvMenu\InvMenuHandler;
use pocketmine\utils\TextFormat;
use Renz\TicTacToe\libs\InvMenu\transaction\InvMenuTransaction;
use Renz\TicTacToe\libs\InvMenu\transaction\InvMenuTransactionResult;

class Game {

    private Player $playerX;
    private ?Player $playerO;
    private array $board = [];
    private ?Player $currentPlayer = null;
    private Main $plugin;
    private InvMenu $menu;
    private bool $isVsBot;
    private ?Bot $bot = null;
    private array $guiSettings;

    public function __construct(Player $playerX, ?Player $playerO, Main $plugin, bool $isVsBot = false, ?Bot $bot = null) {
        $this->playerX = $playerX;
        $this->playerO = $playerO;
        $this->plugin = $plugin;
        $this->board = array_fill(0, 9, " ");
        $this->currentPlayer = $playerX;
        $this->isVsBot = $isVsBot;
        $this->guiSettings = $plugin->getGuiSettings();

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($plugin);
        }
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->readonly();

        if ($this->isVsBot) {
            $this->bot = $bot ?? new EasyBot();
        } else {
            $this->bot = $bot;
        }
    }

    public function setBot(Bot $bot): void {
        $this->bot = $bot;
    }

    public function getBoard(): array {
        return $this->board;
    }

    public function getPlayers(): array {
        return [$this->playerX, $this->playerO];
    }

    public function start(): void {
        $this->sendBoard();
        $this->playerX->sendMessage($this->plugin->getMessage("game_start_x"));

        if ($this->isVsBot) {
            $this->playerX->sendMessage($this->plugin->getMessage("game_start_bot"));
        } else {
            if ($this->playerO instanceof Player) {
                $this->playerO->sendMessage($this->plugin->getMessage("game_start_o"));
            }
        }
    }

    public function handleMove(?Player $player, int $position): void {
        if ($player !== null && $player !== $this->currentPlayer) {
            $this->menu->setName(TextFormat::RED . "It's not your turn!");
            $this->menu->send($this->playerX);
            if ($this->playerO instanceof Player) {
                $this->menu->send($this->playerO);
            }
            return;
        }

        if ($position < 1 || $position > 9 || $this->board[$position - 1] !== " ") {
            if ($player !== null) {
                $player->sendMessage($this->plugin->getMessage("invalid_move"));
            }
            return;
        }

        $this->board[$position - 1] = $player === $this->playerX ? "X" : "O";
        if ($this->checkWin()) {
            $this->end($player);
            return;
        }

        if (!in_array(" ", $this->board, true)) {
            $this->end(null);
            return;
        }

        $this->currentPlayer = $this->currentPlayer === $this->playerX ? $this->playerO : $this->playerX;
        $this->sendBoard();

        if ($this->isVsBot && $this->currentPlayer === null) {
            $this->bot->makeMove($this);
        }
    }

    private function sendBoard(): void {
        $title = $this->guiSettings["title"] ?? "TicTacToe";
        $this->menu->setName($title . " - " . ($this->currentPlayer === $this->playerX ? "X's Turn" : "O's Turn"));

        $inventory = $this->menu->getInventory();
        
        $layout = [
            11, 12, 13,
            20, 21, 22,
            29, 30, 31
        ];

        for ($i = 0; $i < 9; $i++) {
            $item = $this->getBoardItem($i);
            $inventory->setItem($layout[$i], $item);
        }

        $borderColor = $this->getDyeColor($this->guiSettings["border_color"] ?? "BLACK");
        for ($i = 0; $i < 54; $i++) {
            if (!in_array($i, $layout)) {
                $item = VanillaBlocks::STAINED_GLASS_PANE()->setColor($borderColor)->asItem();
                $item->setCustomName(TextFormat::BLACK . "");
                $inventory->setItem($i, $item);
            }
        }

        $this->menu->setListener(function(InvMenuTransaction $transaction) use ($layout): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $slot = $transaction->getAction()->getSlot();
            if (in_array($slot, $layout)) {
                $position = array_search($slot, $layout, true);
                if ($position !== false) {
                    $this->handleMove($player, $position + 1);
                }
            }
            return $transaction->discard();
        });

        $this->menu->setInventoryCloseListener(function(Player $player) {
            $this->endWithForfeit($player);
        });

        $this->menu->send($this->playerX);
        if (!$this->isVsBot && $this->playerO instanceof Player) {
            $this->menu->send($this->playerO);
        }
    }

    private function getBoardItem(int $position): Item {
        $value = $this->board[$position];
        if ($value === "X") {
            $xColor = $this->getDyeColor($this->guiSettings["x_color"] ?? "RED");
            $item = VanillaBlocks::WOOL()->setColor($xColor)->asItem();
            $item->setCustomName(TextFormat::RED . "X");
        } elseif ($value === "O") {
            $oColor = $this->getDyeColor($this->guiSettings["o_color"] ?? "WHITE");
            $item = VanillaBlocks::WOOL()->setColor($oColor)->asItem();
            $item->setCustomName(TextFormat::WHITE . "O");
        } else {
            $emptyColor = $this->getDyeColor($this->guiSettings["empty_cell_color"] ?? "WHITE");
            $item = VanillaBlocks::STAINED_GLASS_PANE()->setColor($emptyColor)->asItem();
            $item->setCustomName(TextFormat::AQUA . (string)($position + 1));
        }
        return $item;
    }
    
    /**
     * Convert color name to DyeColor object
     */
    private function getDyeColor(string $colorName): DyeColor {
        return match (strtoupper($colorName)) {
            "BLACK" => DyeColor::BLACK(),
            "RED" => DyeColor::RED(),
            "GREEN" => DyeColor::GREEN(),
            "BROWN" => DyeColor::BROWN(),
            "BLUE" => DyeColor::BLUE(),
            "PURPLE" => DyeColor::PURPLE(),
            "CYAN" => DyeColor::CYAN(),
            "LIGHT_GRAY" => DyeColor::LIGHT_GRAY(),
            "GRAY" => DyeColor::GRAY(),
            "PINK" => DyeColor::PINK(),
            "LIME" => DyeColor::LIME(),
            "YELLOW" => DyeColor::YELLOW(),
            "LIGHT_BLUE" => DyeColor::LIGHT_BLUE(),
            "MAGENTA" => DyeColor::MAGENTA(),
            "ORANGE" => DyeColor::ORANGE(),
            default => DyeColor::WHITE(),
        };
    }

    private function checkWin(): bool {
        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];

        foreach ($winningCombinations as $combination) {
            if ($this->board[$combination[0]] !== " " &&
                $this->board[$combination[0]] === $this->board[$combination[1]] &&
                $this->board[$combination[1]] === $this->board[$combination[2]]) {
                return true;
            }
        }

        return false;
    }

    public function end(?Player $winner = null): void {
        if ($winner !== null) {
            $reward = $this->plugin->getRewardMoney();
            $economy = new libEco();
            if ($economy !== null) {
                $economy->addMoney($winner, $reward);
                $winner->sendMessage($this->plugin->getMessage("win_with_money", ["money" => $reward]));
            } else {
                $winner->sendMessage($this->plugin->getMessage("win_without_money"));
            }
            if (($winner === $this->playerX ? $this->playerO : $this->playerX) instanceof Player) {
                ($winner === $this->playerX ? $this->playerO : $this->playerX)->sendMessage($this->plugin->getMessage("lose_game"));
            }
        } else {
            $this->playerX->sendMessage($this->plugin->getMessage("draw_game"));
            if ($this->playerO instanceof Player) {
                $this->playerO->sendMessage($this->plugin->getMessage("draw_game"));
            }
        }

        $this->playerX->removeCurrentWindow();
        if ($this->playerO instanceof Player) {
            $this->playerO->removeCurrentWindow();
        }
        $this->playerX->sendMessage($this->plugin->getMessage("game_end"));
        $this->plugin->removeGame($this);
    }

    public function endWithForfeit(Player $player): void {
        $otherPlayer = $player === $this->playerX ? $this->playerO : $this->playerX;
        $player->sendMessage($this->plugin->getMessage("forfeit_self"));
        if ($otherPlayer instanceof Player) {
            $otherPlayer->sendMessage($this->plugin->getMessage("forfeit_opponent", ["player" => $player->getName()]));

            $reward = $this->plugin->getRewardMoney();
            $economy = new libEco();
            if ($economy !== null) {
                $economy->addMoney($otherPlayer, $reward);
                $otherPlayer->sendMessage($this->plugin->getMessage("reward_message", ["money" => $reward]));
            }
        }

        $this->playerX->removeCurrentWindow();
        if ($this->playerO instanceof Player) {
            $this->playerO->removeCurrentWindow();
        }

        $this->plugin->removeGame($this);
    }
}