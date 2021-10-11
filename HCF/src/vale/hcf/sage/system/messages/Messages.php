<?php

namespace vale\hcf\sage\system\messages;
interface Messages{


	public const INVALID_KOTH  =  "&cThe Koth &e{kothName}&c was never registered!";
    public const KOTH_NOT_ACTIVATED = "&c{kothName} KOTH is not activated";
	public const NO_KOTHS = "&cYou cannot see the list, because there is no registered KOTH";
    public const KOTH_LIST_ARENAS = "&6KOTH Arenas: ";
    public const KOTH_LIST =  "&a{kothName}&r&f &8[&f{position}&8:&f{worldName}&8]&r &e(&f{status}&e)";
	public const HAS_MORE_MONEY = "";
	public const NOT_ONLINE = "&r&c(&c&l!&r&c) The player &r&6{playerName} &r&cwas not found in the player database.";
	public const MONEY_HELP = "";
	public const ENDERPEARL = "&r&c(&c&l!&r&c) &r&cYou are on pearl cooldown for &c&l{CD}s&r&c.";
	public const RAVEN_RECLAIM = "the player {player_name} reclaimed Raven";
	public const SAGE_RECLAIM = "the player &{player_name} reclaimed Sage";
	public const CUPID_RECLAIM = "the player &{player_name} reclaimed CUPID";
	public const AEGIS_RECLAIM = "the player &{player_name} reclaimed AEGIS";
}