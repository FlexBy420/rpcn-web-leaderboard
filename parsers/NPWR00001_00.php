<?php
return [
    "title" => "Ridge Racer 7",
    "config" => [
        "icon" => "",
        "game_id" => ["BCES00009", "BLUS30001"],
        "time_boards"  => [75],
        "score_boards" => [],
        "names" => [
            75 => "Rave City Riverfront (Time Attack Machine 4)",
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