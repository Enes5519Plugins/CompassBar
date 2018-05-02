<?php

declare(strict_types=1);

namespace Enes5519\CompassBar;

use pocketmine\utils\TextFormat;

class Utils{

	# BASED FROM MiNET (https://github.com/NiclasOlofsson/MiNET/blob/master/src/MiNET/TestPlugin/NiceLobby/NiceLobbyPlugin.cs)

	public static function wrap(float $angle){
		return (float) ($angle + ceil(-$angle/360)*360);
	}

	public static function getCompass(float $direction, int $width = 25){
		$direction = self::wrap($direction);
		$direction = $direction*2/10;

		$direction += 72;

		$compass = explode("-", substr(str_repeat("| -", 72), 0, -1));
		$compass[0] = TextFormat::GOLD . 'S ' . TextFormat::RESET;

		$compass[9] = TextFormat::GOLD . 'S';
		$compass[9 + 1] = 'W ' . TextFormat::RESET;

		$compass[(18)] = TextFormat::GOLD . 'W ' . TextFormat::RESET;

		$compass[(18 + 9)] = TextFormat::GOLD . 'N';
		$compass[(18 + 9 + 1)] = 'W ' . TextFormat::RESET;

		$compass[36] = TextFormat::GOLD . 'N ' . TextFormat::RESET;

		$compass[36 + 9] = TextFormat::GOLD . 'N';
		$compass[36 + 9 + 1] = 'E ' . TextFormat::RESET;

		$compass[54] = TextFormat::GOLD . 'E ' . TextFormat::RESET;

		$compass[54 + 9] = TextFormat::GOLD . 'S';
		$compass[54 + 9 + 1] = 'E ' . TextFormat::RESET;

		$compass = array_merge(array_merge($compass, $compass), $compass);

		return implode(array_slice($compass, (int) ($direction - floor($width/2)), $width));
	}
}