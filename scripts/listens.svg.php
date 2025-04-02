<?php
$response = json_decode(file_get_contents('https://api.listenbrainz.org/1/user/fkeiler/listens?count=4'));

function to_listen($payload_listen)
{
    $seconds_ago = time() - $payload_listen->listened_at;

    $time_ago = match (true) {
        $seconds_ago < 60 => $seconds_ago . ' seconds ago',
        $seconds_ago < 3600 => intdiv($seconds_ago, 60) . ' minutes ago',
        $seconds_ago < 86400 => intdiv($seconds_ago, 3600) . ' hours ago',
        default => date('c', $payload_listen->listened_at),
    };

    return [
        'time_ago' => $time_ago,
        'title' => $payload_listen->track_metadata->track_name,
        'artist' => $payload_listen->track_metadata->artist_name
    ];
}

$listens = array_map('to_listen', $response->payload->listens)
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 315 300" width="315">
    <style>
        :root {
            color-scheme: light dark;
        }

        text {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .color-bg-default {
            fill: light-dark(#fff, #010409);
        }

        .color-fg-muted {
            fill: light-dark(#59636e, #9198a1);
        }

        .color-fg-default {
            fill: light-dark(#1f2328, #f0f6fc);
        }

        .border-color-default-fill {
            fill: light-dark(#d1d9e0, #3d444d);
        }

        .border-color-default-stroke {
            stroke: light-dark(#d1d9e0, #3d444d);
        }
    </style>
    <rect class="color-bg-default border-color-default-stroke" x="1" y="1" width="313" height="298" rx="6" />
    <g transform="translate(16, 16)">
        <text class="color-fg-default" x="0" y="14" font-size="14" font-weight="500">Recent Listens</text>

        <g transform="translate(0, 30)">
            <path class="border-color-default-stroke" d="M4, 0 l0, 236" stroke="#d1d9e0" />
            <?php foreach ($listens as $i => $listen): ?>
                <g transform="translate(0, <?= $i * 60  ?>)">
                    <circle class="border-color-default-fill" cx="4" cy="0" fill="#d1d9e0" r="4" />
                    <text class="color-fg-muted" x="16" y="4" font-size="12"><?= $listen["time_ago"] ?></text>
                    <text class="color-fg-default" x="16" y="22" font-size="14"><?= $listen["title"] ?></text>
                    <text class="color-fg-muted" x="16" y="38" font-size="14"><?= $listen["artist"] ?></text>
                </g>
            <?php endforeach; ?>
        </g>
    </g>
</svg>