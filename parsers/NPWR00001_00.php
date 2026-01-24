<?php
return [
    "title" => "Ridge Racer 7",
    "config" => [
        "icon" => "",
        "game_id" => ["BCES00009", "BLUS30001"],
        "time_boards"  => [72,73,74,75,76,77,78,79,128,129,130,131,132,133,134,135,152,153,154,155,156,157,158,159],
        "score_boards" => [],
        "names" => [
            72 => "Rave City Riverfront (Single Time Attack, Machine Category 1, Normal)",
            73 => "Rave City Riverfront (Single Time Attack, Machine Category 2, Normal)",
            74 => "Rave City Riverfront (Single Time Attack, Machine Category 3, Normal)",
            75 => "Rave City Riverfront (Single Time Attack, Machine Category 4, Normal)",
            76 => "Rave City Riverfront (Single Time Attack, Machine Category 1, Reverse)",
            77 => "Rave City Riverfront (Single Time Attack, Machine Category 2, Reverse)",
            78 => "Rave City Riverfront (Single Time Attack, Machine Category 3, Reverse)",
            79 => "Rave City Riverfront (Single Time Attack, Machine Category 4, Reverse)",
            128 => "Lost Ruins (Single Time Attack, Machine Category 1, Normal)",
            129 => "Lost Ruins (Single Time Attack, Machine Category 2, Normal)",
            130 => "Lost Ruins (Single Time Attack, Machine Category 3, Normal)",
            131 => "Lost Ruins (Single Time Attack, Machine Category 4, Normal)",
            132 => "Lost Ruins (Single Time Attack, Machine Category 1, Reverse)",
            133 => "Lost Ruins (Single Time Attack, Machine Category 2, Reverse)",
            134 => "Lost Ruins (Single Time Attack, Machine Category 3, Reverse)",
            135 => "Lost Ruins (Single Time Attack, Machine Category 4, Reverse)",
            152 => "Industrial Drive (Single Time Attack, Machine Category 1, Normal)",
            153 => "Industrial Drive (Single Time Attack, Machine Category 2, Normal)",
            154 => "Industrial Drive (Single Time Attack, Machine Category 3, Normal)",
            155 => "Industrial Drive (Single Time Attack, Machine Category 4, Normal)",
            156 => "Industrial Drive (Single Time Attack, Machine Category 1, Reverse)",
            157 => "Industrial Drive (Single Time Attack, Machine Category 2, Reverse)",
            158 => "Industrial Drive (Single Time Attack, Machine Category 3, Reverse)",
            159 => "Industrial Drive (Single Time Attack, Machine Category 4, Reverse)",
        ]
    ],
    "formatter" => function($score, $boardId, $config) {
        if (in_array($boardId, $config["time_boards"])) {
            // RR7 Logic: Score = TimeInMS * 3
            // To get time, we divide score by 3
            $timeMs = floor($score / 3);

            $min = floor($timeMs / 60000);
            $sec = floor(($timeMs % 60000) / 1000);
            $ms  = $timeMs % 1000;
            
            return sprintf("%02d:%02d.%03d", $min, $sec, $ms);
        }
        return number_format($score, 0, ".", " ");
    }
];
?>