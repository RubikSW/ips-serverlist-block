class BattleMetricsGameServerBlock
{
    private $apiKey = 'BATTLEMETRICS API KEY';

    public function render($overrides = [])
    {
        $servers = [
            '22160069',
            '21612865',
            '24214687',
            '21641829',
        ];

        $totalPlayers = 0;
        $totalServers = count($servers);

        foreach ($servers as $serverId) {
            $serverInfo = $this->queryServer($serverId);
            if ($serverInfo) {
                $totalPlayers += $serverInfo['players'];
            }
        }

        echo "<div style='padding: 15px;'>";

        if ($totalPlayers === 0) {
            echo "<p style='text-align: center; margin-top: 0; font-weight: 600; font-size: 15px; margin-bottom: 0; color: slategrey'>Cannot reach Battlemetrics API</p>";
            echo "</div>";
            return;
        }

        echo "<p style='text-align: center; margin-top: 0; font-weight: 600; font-size: 15px;'>There are $totalPlayers players on $totalServers servers</p>";

        foreach ($servers as $serverId) {
            $serverInfo = $this->queryServer($serverId);

            if ($serverInfo) {
                $serverInfo = array_merge($serverInfo, $overrides[$serverId] ?? []);

                echo "<div style='margin:10px'>";
                echo "<div style='width: 100%; background-color: #16171d; border-radius: 6px; overflow: hidden;'>";

                $percentage = ($serverInfo['max_players'] != 0) ? ($serverInfo['players'] / $serverInfo['max_players'] * 100) : 0;

                echo "<a href='steam://connect/" . $serverInfo['ip'] . ":" . $serverInfo['port'] . "'><div style='width: $percentage%; background-color: #374161; height: 20px; border-radius: 5px; display: flex; align-items: center;'>";

                echo "<span style='display: block; padding: 5px; position: absolute; line-height: 18px; font-size: 10px;'>" . $serverInfo['name'] . ' - ' . $serverInfo['players'] . '/' . $serverInfo['max_players'] . "</span>";
                echo "</div></a>";
                echo "</div>";

                echo "</div>";
            } else {
                echo "Failed to fetch information for server $serverId";
            }
        }
        echo "</div>";
    }

    private function queryServer($serverId)
    {
        $apiUrl = 'https://api.battlemetrics.com/servers/' . $serverId;
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->apiKey]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        return [
            'name' => $data['data']['attributes']['name'],
            'players' => $data['data']['attributes']['players'],
            'max_players' => $data['data']['attributes']['maxPlayers'],
            'connect_link' => $data['data']['attributes']['connect'],
            'ip' => $data['data']['attributes']['ip'],
            'port' => $data['data']['attributes']['port'],
        ];
    }
}

$serverBlock = new BattleMetricsGameServerBlock();
$serverBlock->render([
    '22160069' => ['name' => '◕‿◕ TitsRP│Battlepass│High-FPS│Custom'],
    '21612865' => ['name' => '◕‿◕ TitsRP Prophunters | Custom Taunts | XP'],
]);
