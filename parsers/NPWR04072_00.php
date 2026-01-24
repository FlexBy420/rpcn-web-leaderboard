<?php
return [
    "title" => "Deadpool",
    "config" => [
        "game_id" => ["BLES01789", "BLUS31146", "NPUB31069"],
        "names" => [
            0 => "Beneath The Streets",
            1 => "The Watching Sentinel",
            2 => "Great White News HQ",
            3 => "Magneto's Prison",
            4 => "Catacombs of Genosha",
            5 => "Office Spaces",
            6 => "Citadel Courtyard",
            9 => "Deadpool's Carnivale",
        ]
    ],
    "formatter" => function($score, $boardId, $config, $info) {
        if (!$info || strlen($info) < 32) {
            return number_format((float)$score);
        }
        // format player data
        $timeRaw  = hexdec(substr($info, 0, 8));  // 00000137 => 311
        $waves    = hexdec(substr($info, 8, 8));  // 00000001 => 1
        $combo    = hexdec(substr($info, 16, 8)); // 00000035 => 53
        $kills    = hexdec(substr($info, 24, 8)); // 000000f4 => 244

        $h = floor($timeRaw / 3600);
        $m = floor(($timeRaw % 3600) / 60);
        $s = $timeRaw % 60;
        $timeStr = sprintf("%dh %dm %ds", $h, $m, $s);

        return sprintf(
            "Score: %s<br>Time: %s<br>Waves: %d<br>Combo: %d<br>Kills: %d",
            number_format($score, 0, ".", " "),
            $timeStr,
            $waves,
            $combo,
            $kills
        );
    }
];
?>