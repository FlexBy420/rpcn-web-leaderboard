<?php
return [
    "title" => "Fighting Vipers",
    "config" => [
        "icon" => "",
        "game_id" => "NPEB01164",
        "score_boards" => [0],
        "names" => [
            0 => "Wins"
        ]
    ],
    "formatter" => function($score, $boardId, $config) {
        return number_format($score, 0, ".", " ");
    }
];
?>