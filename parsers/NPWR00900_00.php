<?php
$names = [
    0 => "Episode 1: The Party",
    1 => "Episode 2: Top Teddy",
    2 => "Episode 3: Big Ted is Watching",
    3 => "Episode 4: Night of the Living Ted",
    4 => "Episode 6: R153 OF ROBO-B34R",
    5 => "Episode 7: When Aliens Attack",
    6 => "Episode 5: The Oil Baron's Ball", // annoying, but its their actual ids
    7 => "Episode 8: X-Bears",
    8 => "Episode 9: The Treasure of Bear Beard",
    9 => "Episode 10: The Horrible Vampiricorn",

    11 => "1-1 Killer: Party Massacre",
    12 => "1-2 Friendly: The Peace Loving Party",
    13 => "1-3 Insanity: The Craziest Party Ever",
    14 => "1-4 Top Hat: Bring Your Own Hat",

    15 => "2-1 Untouchable: Top Dodger",
    16 => "2-2 Speed Run: Race to the Top",
    17 => "2-3 Killer: Top Assasin",
    18 => "2-4 Top Hat: Doubletop Teddy",

    19 => "3-1 Killer: Big Ted is Struggling!",
    20 => "3-2 Invisible: Counter-Espionage",
    21 => "3-3 Insanity: Big Ted is Deranged!",
    22 => "3-4 Top Hat: The Army's Secret Weapon",

    23 => "4-1 Untouchable: Night of the Dodging Ted",
    24 => "4-2 Speed Run: The Un-ted Hustle",
    25 => "4-3 Killer: Zombear Buster",
    26 => "4-4 Top Hat: The Reanimating Headpieces",

    27 => "6-1 Invisible: R4D4R J4MM3R",
    28 => "6-2 Friendly: NO F1GH71NG!",
    29 => "6-3 Killer: TROUBL3-5HOO73R",
    30 => "6-4 Top Hat: PROJ3C7 7OP-H47",

    31 => "7-1 Speed Run: Beyond the Speed of Light",
    32 => "7-2 Killer: Alien Exterminator",
    33 => "7-3 Insanity: Space Madness!",
    34 => "7-4 Top Hat: Outer-Space Chic",

    35 => "5-1 Speed Run: Oil Race",
    36 => "5-2 Friendly: The Amicable Baron",
    37 => "5-3 Killer: The Butcher's Ball",
    38 => "5-4 Top Hat: The Baron's Brand New Hat",

    39 => "8-1 Speed Run: Supersonic Heroes",
    40 => "8-2 Friendly: Peaceful Vigilante",
    41 => "8-3 Killer: Death to all Mutants!",
    42 => "8-4 Top Hat: Stylish Super Heroes",

    43 => "9-1 Killer: Pirate Hunter",
    44 => "9-2 Speed Run: Speedy Piracy",
    45 => "9-3 Insanity: Pirates' Dementia",
    46 => "9-4 Top Hat: The Vogue Pirates",

    47 => "10-1 Speed Run: The Hasty Vampires",
    48 => "10-2 Killer: The Monstrous Vampires",
    49 => "10-3 Untouchable: The Illusive Vampires",
    50 => "10-4 Top Hat: The Fashionable Vampires",

    55 => "Global",

];
return [
    "title" => "Naughty Bear",
    "config" => [
        "icon" => "",
        "game_id" => ["BLES00945", "BLUS30507", "BLUS30700", "NPUB31067"],
        "score_boards" => [],
        "names" => $names,
    ],
    "formatter" => function($score, $boardId, $config) {
        return number_format($score, 0, ".", " ");
    }
];
?>