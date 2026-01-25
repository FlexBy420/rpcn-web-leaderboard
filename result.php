<?php
require_once 'config.php';

$errorMessage = null;
$displayRows = [];
$commId = $_GET["comm_id"] ?? null;
$boardIdParam = $_GET["board_id"] ?? null;
$isTimeBoard = false;

// config
$cacheDir = CACHE_DIR;
$cacheTime = CACHE_TIME;
$commIdStr = is_string($commId) ? $commId : "";

// validate communication id
if ($commIdStr === "") {
    $errorMessage = "No communication ID provided.";
} elseif (!preg_match('/^[A-Z0-9]{4}\d{5}_\d{2}$/', $commIdStr)) {
    $errorMessage = "Invalid Communication ID format.";
    $commIdStr = "";
}

// load parser
$parser = null;
if ($errorMessage === null && $commIdStr !== "") {
    $parserPath = "parsers/{$commIdStr}.php";
    if (file_exists($parserPath)) {
        /** @var mixed $loaded */
        $loaded = include $parserPath;
        if (is_array($loaded) && isset($loaded['config']) && is_array($loaded['config'])) {
            $parser = $loaded;
        } else {
            $errorMessage = "Parser file is invalid or corrupted.";
        }
    } else {
        $errorMessage = "Missing parser for " . htmlspecialchars($commIdStr);
    }
}

$names = [];
$boardId = null;

if ($errorMessage === null && is_array($parser)) {
    $pConfig = $parser['config'];
    /** @var array<int|string, string> $names_tmp */
    $names_tmp = (isset($pConfig['names']) && is_array($pConfig['names'])) ? $pConfig['names'] : [];
    $names = $names_tmp;

    if ($boardIdParam !== null) {
        $requestedId = is_numeric($boardIdParam) ? (int)$boardIdParam : null;
        if ($requestedId !== null && array_key_exists($requestedId, $names)) {
            $boardId = $requestedId;
        } else {
            $errorMessage = "Board ID " . htmlspecialchars((string)$boardIdParam) . " doesn't exist.";
        }
    } elseif (count($names) === 1) {
        // if only one board_id defined, show it
        $boardId = (int)array_key_first($names);
    }
}

// fetch data from API
if ($errorMessage === null && is_array($parser) && $boardId !== null) {
    $apiUrl = SCORE_API . urlencode($commIdStr) . "/" . $boardId;

    if (!is_dir($cacheDir)) { mkdir($cacheDir, 0777, true); }
    $cacheFile = $cacheDir . "/{$commIdStr}_{$boardId}.json";
    $json = "";

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        $content = file_get_contents($cacheFile);
        $json = is_string($content) ? $content : "";
    } else {
        $ch = curl_init($apiUrl);
        if ($ch !== false) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $response = curl_exec($ch);
            if (is_string($response) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
                $json = $response;
                file_put_contents($cacheFile, $json);
            } elseif (file_exists($cacheFile)) {
                $content = file_get_contents($cacheFile);
                $json = is_string($content) ? $content : "";
            } else {
                $errorMessage = "API connection failed.";
            }
        }
    }

    if ($json !== "") {
        /** @var mixed $apiData */
        $apiData = json_decode($json, true);
        $scoresToProcess = (is_array($apiData) && isset($apiData['scores']) && is_array($apiData['scores'])) ? $apiData['scores'] : [];
        if ($scoresToProcess === [] && is_array($apiData) && isset($apiData[0]['scores']) && is_array($apiData[0]['scores'])) {
            $scoresToProcess = $apiData[0]['scores'];
        }

        if ($scoresToProcess !== []) {
            $pConfig = $parser['config'];
            /** @var array<int> $timeBoards */
            $timeBoards = (isset($pConfig['time_boards']) && is_array($pConfig['time_boards'])) ? $pConfig['time_boards'] : [];
            $isTimeBoard = in_array($boardId, $timeBoards, true);

            foreach ($scoresToProcess as $row) {
                if (!is_array($row) || !isset($parser['formatter']) || !is_callable($parser['formatter'])) continue;

                $formattedValue = (string)$parser['formatter']($row["score"] ?? 0, $boardId, $pConfig, $row["info"] ?? null);
                $sortValue = (float)($row["score"] ?? 0);

                if ($isTimeBoard && strpos($formattedValue, ':') !== false) {
                    if (preg_match('/(\d+):(\d+)\.(\d+)/', $formattedValue, $matches)) {
                        $sortValue = ((float)$matches[1] * 60000) + ((float)$matches[2] * 1000) + (float)$matches[3];
                    }
                }

                $displayRows[] = [
                    "user" => is_string($row["online_name"] ?? null) ? $row["online_name"] : "Unknown",
                    "sort" => $sortValue,
                    "val"  => $formattedValue
                ];
            }

            usort($displayRows, function($a, $b) use ($isTimeBoard) {
                return $isTimeBoard ? ($a["sort"] <=> $b["sort"]) : ($b["sort"] <=> $a["sort"]);
            });
            $displayRows = array_slice($displayRows, 0, MAX_DISPLAY_ROWS);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo is_array($parser) ? htmlspecialchars((string)$parser['title']) : "Error"; ?> - RPCN</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; background: #0b0c10; color: #fff; margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: auto; }
        .header { display: flex; align-items: center; gap: 20px; margin-bottom: 40px; }
        .back-link { color: #45a29e; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 0.9em; }
        .back-link:hover { color: #66fcf1; }
        h1 { color: #66fcf1; margin: 0; font-size: 1.5rem; }
        .error-box { background: #441111; border: 1px solid #ff4444; color: #ffaaaa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .list { list-style: none; padding: 0; border-top: 1px solid #1f2833; }
        .list-item { border-bottom: 1px solid #1f2833; }
        .list-link { display: block; padding: 15px; text-decoration: none; color: #c5c6c7; transition: 0.2s; }
        .list-link:hover { background: #1f2833; color: #66fcf1; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #1f2833; border-radius: 5px; overflow: hidden; }
        th, td { text-align: left; padding: 15px; border-bottom: 1px solid #0b0c10; }
        th { background: #0b0c10; color: #45a29e; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        .score-val { color: #66fcf1; font-family: "Consolas", monospace; font-weight: bold; }
        .no-data { padding: 40px; text-align: center; color: #45a29e; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <?php 
                $showBackToBoards = ($boardId !== null && count($names) > 1) || $errorMessage !== null;
                $backUrl = $showBackToBoards ? "result.php?comm_id=" . urlencode($commIdStr) : "index.php";
            ?>
            <a href="<?php echo $backUrl; ?>" class="back-link">← BACK</a>
            <h1><?php echo is_array($parser) ? htmlspecialchars((string)$parser['title']) : "Leaderboard Error"; ?></h1>
        </div>

        <?php if ($errorMessage !== null): ?>
            <div class="error-box">
                <strong>Error: </strong><?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (is_array($parser)): ?>
            <?php if ($boardId === null): ?>
                <?php 
                $groups = [];
                foreach ($names as $id => $fullName) {
                    if (strpos($fullName, '|') !== false) {
                        list($group, $level) = explode('|', $fullName);
                        $groups[$group][$id] = $level;
                    } else {
                        $groups['General'][$id] = $fullName;
                    }
                }
                ?>
            
                <?php foreach ($groups as $groupName => $boards): ?>
                    <h3 style="color: #66fcf1; margin-top: 30px; border-bottom: 1px solid #45a29e; padding-bottom: 5px;">
                        <?php echo htmlspecialchars($groupName); ?>
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 20px;">
                        <?php foreach ($boards as $id => $name): ?>
                            <a href="?comm_id=<?php echo urlencode($commIdStr); ?>&board_id=<?php echo $id; ?>" 
                               style="display: block; padding: 10px; background: #1f2833; color: #c5c6c7; text-decoration: none; border-radius: 4px; text-align: center; transition: 0.3s;"
                               onmouseover="this.style.background='#45a29e'; this.style.color='#0b0c10';"
                               onmouseout="this.style.background='#1f2833'; this.style.color='#c5c6c7';">
                                <?php echo htmlspecialchars($name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h2 style="color: #45a29e; margin-bottom: 10px;"><?php echo htmlspecialchars($names[$boardId] ?? "Scoreboard"); ?></h2>
                <?php if (empty($displayRows) && $errorMessage === null): ?>
                    <div class="no-data">No scores found for this board.</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">Rank</th><th>Player</th>
                                <th>
                                    <?php 
                                    if (isset($pConfig['column_names'][$boardId])) {
                                        echo htmlspecialchars($pConfig['column_names'][$boardId]);
                                    } else {
                                        echo $isTimeBoard ? "Time" : "Score";
                                    }
                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($displayRows as $idx => $row): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?>.</td>
                                <td><strong><?php echo htmlspecialchars($row["user"]); ?></strong></td>
                                <td class="score-val"><?php echo $row["val"]; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>