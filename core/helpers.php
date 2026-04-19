<?php
/**
 * Absolute URL to a path under public (for PayHere return/notify URLs).
 */
function app_absolute_url(string $path = ''): string {
    $https = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = defined('BASE_URL') ? BASE_URL : '';
    $path = ltrim($path, '/');
    return $path === '' ? ($scheme . '://' . $host . $base) : ($scheme . '://' . $host . $base . '/' . $path);
}

function view($name, $data = []) {
    extract($data);
    $viewPath = __DIR__ . "/../views/$name.php";
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View not found: $name");
    }
}

/**
 * True if an itinerary bullet is airport pickup, drop-off, or airport transfer (display filter).
 */
function package_itinerary_line_is_airport_pickup_or_dropoff(string $line): bool {
    $t = mb_strtolower(trim($line), 'UTF-8');
    if ($t === '') {
        return false;
    }

    $nearAirport =
        (strpos($t, 'airport') !== false)
        || (bool) preg_match('/\b(bandaranaike|katunayake|negombo\s+airport|jaffna\s+international\s+airport)\b/', $t);

    if (preg_match('/transfer\s+to\s+.*(bandaranaike|katunayake|colombo\s+airport)/', $t)) {
        return true;
    }
    if (preg_match('/transfer\s+from\s+.*(bandaranaike|katunayake)/', $t)) {
        return true;
    }

    if (!$nearAirport) {
        return false;
    }

    if (preg_match('/pick[\-\s]?up|pickup|drop[\-\s]?off|dropoff/', $t)) {
        return true;
    }
    if (preg_match('/\btransfer\b/', $t)) {
        return true;
    }
    if (preg_match('/\b(arrival|arrive|departure|depart)\b/', $t)) {
        return true;
    }
    if (preg_match('/\b(from|to)\s+([a-z,\s]+\s+)?airport\b/', $t)) {
        return true;
    }

    return false;
}

/**
 * @param array $itinerary List of days: [ 'day'=>int, 'title'=>string, 'activities'=>string[] ], ...
 * @return array Same structure with airport pickup/drop lines removed from activities.
 */
function filter_package_itinerary_remove_airport_lines(array $itinerary): array {
    $out = [];
    foreach ($itinerary as $day) {
        if (!is_array($day)) {
            continue;
        }
        $acts = $day['activities'] ?? null;
        if (!is_array($acts)) {
            $out[] = $day;
            continue;
        }
        $filtered = [];
        foreach ($acts as $act) {
            $s = trim((string) $act);
            if ($s !== '' && !package_itinerary_line_is_airport_pickup_or_dropoff($s)) {
                $filtered[] = $act;
            }
        }
        $day['activities'] = $filtered;
        $out[] = $day;
    }
    return $out;
}

/**
 * Default bullets when a "Departure" day only lists airport transfers (stripped) or a lone "Departure" line.
 *
 * @param array $itinerary After {@see filter_package_itinerary_remove_airport_lines}
 * @return array
 */
function package_itinerary_enrich_bare_departure_days(array $itinerary): array {
    $defaultActs = [
        'Breakfast at your hotel',
        'Morning at leisure or light sightseeing (time permitting)',
        'Checkout and scenic journey toward Colombo or your onward stop',
        'Photo stops or a short break for shopping along the way',
        'Departure',
    ];
    $out = [];
    foreach ($itinerary as $day) {
        if (!is_array($day)) {
            $out[] = $day;
            continue;
        }
        $title = isset($day['title']) ? (string) $day['title'] : '';
        $isDepartureDay = $title !== '' && (bool) preg_match('/\bdeparture\b/iu', $title);
        $acts = $day['activities'] ?? [];
        if (!$isDepartureDay || !is_array($acts)) {
            $out[] = $day;
            continue;
        }
        $bare = false;
        if (count($acts) === 0) {
            $bare = true;
        } elseif (count($acts) === 1) {
            $one = mb_strtolower(trim((string) $acts[0]), 'UTF-8');
            if ($one === 'departure') {
                $bare = true;
            }
        }
        if ($bare) {
            $day['activities'] = $defaultActs;
        }
        $out[] = $day;
    }
    return $out;
}

/**
 * Itinerary shown on package pages and booking summaries: strip airport transfer lines, then fill bare departure days.
 */
function package_itinerary_for_tourist_display(array $itinerary): array {
    return package_itinerary_enrich_bare_departure_days(
        filter_package_itinerary_remove_airport_lines($itinerary)
    );
}