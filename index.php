<?php
require_once 'config.php';

function get_region_from_id(string $id): string {
    $third_letter = strtoupper($id[2] ?? '');
    switch ($third_letter) {
        case 'E': return 'EU'; // Europe
        case 'U': return 'US'; // USA
        case 'A': return 'AS'; // Asia
        case 'J': return 'JP'; // Japan
        case 'H': return 'HK'; // Hong Kong
        case 'K': return 'KR'; // Korea
        case 'I': return 'IN'; // International
        case 'T': return 'IN'; // MRTC
        default:  return 'unknown';
    }
}

$games = [];
$parserFiles = glob("parsers/*.php");

if (is_array($parserFiles)) {
    foreach ($parserFiles as $file) {
        $commId = basename($file, ".php");
        /** @var mixed $parser */
        $parser = include $file;
        if (is_array($parser) && isset($parser['title'])) {
            $gameIds = $parser["config"]["game_id"] ?? [$commId];
            if (!is_array($gameIds)) { $gameIds = [$gameIds]; }
            /** @var array<string> $rawIds */
            $rawIds = [];
            foreach($gameIds as $id) { $rawIds[] = (string)$id; }
            $regions = array_unique(array_map('get_region_from_id', $rawIds));
            $games[] = [
                'id' => $commId,
                'title' => (string)$parser['title'],
                'regions' => $regions
            ];
        }
    }
}

// Sort title by alphabetical order
usort($games, function($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RPCN Leaderboards</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; background: #0b0c10; color: #fff; margin: 0; padding: 40px; }
        .container { max-width: 900px; margin: auto; }
        h1 { text-align: center; color: #66fcf1; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 40px; }
        .game-list { list-style: none; padding: 0; border-top: 1px solid #1f2833; }
        .game-item { border-bottom: 1px solid #1f2833; }
        .game-link { display: flex; justify-content: space-between; align-items: center; padding: 15px 10px; text-decoration: none; color: #c5c6c7; transition: 0.2s;}
        .game-link:hover { background: #1f2833; color: #66fcf1; }
        .game-title { font-weight: 500; font-size: 1.05rem; }
        .rpcn-list-regions { display: flex; gap: 8px; align-items: center; }
        .rpcn-list-flags img { height: 32px;width: auto;display: block;border-radius: 2px;box-shadow: 0 0 5px rgba(0,0,0,0.5);}
    </style>
</head>
<body>
    <div class="container">
        <h1>RPCN Leaderboards</h1>
        <ul class="game-list">
            <?php foreach ($games as $game): ?>
                <li class="game-item">
                    <a href="result.php?comm_id=<?php echo urlencode($game['id']); ?>" class="game-link">
                        <span class="game-title"><?php echo htmlspecialchars($game['title']); ?></span>
                        <div class="rpcn-list-regions">
                            <?php foreach ($game['regions'] as $region): 
                                if ($region === '??') continue; 
                            ?>
                                <div class="rpcn-list-flags">
                                    <img src="/img/icons/compat/<?php echo strtoupper($region); ?>.png" 
                                         alt="<?php echo htmlspecialchars($region); ?>"
                                         title="<?php echo htmlspecialchars($region); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>