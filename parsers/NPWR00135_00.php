<?php
return [
    'title' => 'Snakeline',
    'config' => [
        "game_id" => ["NPHA80027"],
        'names' => [
            32 => "Ball Frenzy - Ball Mania (Level 1)",
            33 => "Ball Frenzy - Stir Crazy (Level 2)",
            34 => "Ball Frenzy - Ball Karma (Level 3)",
            35 => "Ball Frenzy - Twisted Star (Level 4)",
            36 => "Ball Frenzy - Tic Tac Toe (Level 5)",
            37 => "Ball Frenzy - Crazy Island (Level 6)",
            38 => "Ball Frenzy - Sunshine (Level 7)",
            39 => "Ball Frenzy - Gridlock (Level 8)",
            40 => "Ball Frenzy - Rush Hour (Level 9)",
            41 => "Ball Frenzy - Beam Me Up (Level 10)",
        ],
        'time_boards' => []
    ],
    'formatter' => function($score, $boardId, $config, $info) {
        $score = (float)$score;
        $points = floor($score / 1099511627776);
        $timePart = fmod($score, 1099511627776);
        $timeMs = (1099511627776 - $timePart) / 256;

        $ms = (int)$timeMs;
        $h = floor($ms / 3600000);
        $m = floor(($ms % 3600000) / 60000);
        $s = floor(($ms % 60000) / 1000);
        $cc = floor(($ms % 1000) / 10);

        return sprintf("%d — %d:%02d:%02d:%02d", $points, $h, $m, $s, $cc);
    }
];