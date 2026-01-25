<?php
$time_boards = [
    72,73,74,75,76,77,78,79,
    128,129,130,131,132,133,134,135,
    152,153,154,155,156,157,158,159
];
$tracks = [
    "Rave City Riverfront" => 72,
    "Lost Ruins"           => 128,
    "Industrial Drive"     => 152,
];
$names = [];
foreach ($tracks as $trackName => $startId) {
    for ($i = 0; $i < 8; $i++) {
        $currentId = $startId + $i;
        $direction = ($i < 4) ? "Normal" : "Reverse";
        $category = ($i % 4) + 1;
        $names[$currentId] = "$trackName | Cat $category ($direction)";
    }
}

return [
    "title" => "Ridge Racer 7",
    "config" => [
        "icon" => "",
        "game_id" => ["BCES00009", "BLUS30001"],
        "time_boards"  => $time_boards,
        "score_boards" => [],
        "names" => $names,
        "column_names" => "Time"
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