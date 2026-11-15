<?php
/**
 * ============================================================================
 *  BF2Stats  v2  ->  v4   data migration
 * ============================================================================
 *
 *  Migrates player data from a "bf2stats v2" database (schema defined in
 *  Web Files/sql.dbschema.php) into a "bf2stats v4" database (schema defined
 *  in Web Files/ASP/system/sql/schema.sql).
 *
 *  HOW IT WORKS
 *  ------------
 *  The script uses cross-database `INSERT ... SELECT` statements, so BOTH the
 *  source (v2) and target (v4) databases must live on the SAME MySQL server.
 *  If they don't, dump the v2 database and import it onto the v4 server first:
 *
 *      mysqldump -u user -p old_v2_db > v2.sql
 *      mysql     -u user -p new_v2_db < v2.sql
 *
 *  The TARGET v4 database must already be created and loaded with the v4
 *  schema + default data BEFORE running this script:
 *
 *      mysql -u user -p v4_db < "Web Files/ASP/system/sql/schema.sql"
 *      mysql -u user -p v4_db < "Web Files/ASP/system/sql/data.sql"
 *
 *  Then run:
 *
 *      php migrate_v2_to_v4.php
 *
 *  WHAT IS MIGRATED
 *  ----------------
 *    v2 player          -> v4 player
 *    v2 army            -> v4 player_army        (unpivoted, armies 0-12)
 *    v2 kits            -> v4 player_kit          (unpivoted)
 *    v2 vehicles        -> v4 player_vehicle      (unpivoted)
 *    v2 weapons         -> v4 player_weapon       (re-mapped to v4 weapon ids)
 *    v2 maps            -> v4 player_map
 *    v2 kills           -> v4 player_kill
 *    v2 unlocks         -> v4 player_unlock       (granted unlocks only)
 *    v2 awards          -> v4 player_award        (via one synthetic round)
 *
 *  KNOWN, INTENTIONAL DATA LOSS / CAVEATS  (read these!)
 *  ----------------------------------------------------
 *   1. PASSWORDS: v2 stored no account passwords. v4 derives "is a bot" from
 *      an empty password. By default each non-bot player (v2 isbot=0) gets a
 *      RANDOM placeholder password so they count as real players (eligible for
 *      SMOC/General, not flagged as bots). They CANNOT log in until the
 *      password is reset. Bots (v2 isbot=1) get an empty password. Change
 *      HUMAN_PASSWORD_MODE below if you prefer everyone treated as a bot.
 *   2. AWARDS dates: all migrated awards reference ONE synthetic round, so
 *      every award's "earned"/"first" date becomes the migration time. Badge
 *      levels (bronze/silver/gold) ARE preserved. Medals are migrated as
 *      "earned once" (a medal's multi-earn COUNT is not preserved).
 *   3. HISTORY: v2 player_history and round_history are time-series snapshots
 *      with no equivalent in v4's per-round model and are NOT migrated.
 *   4. DROPPED columns: clantag, hidden, availunlocks/usedunlocks,
 *      passengerassists, and the SF "shock pad" weapon have no v4 home and are
 *      dropped. v2 `ammos` -> v4 `resupplies`. v2 `rndscore` -> v4 `bestscore`.
 *      v2 vehicles.timepara -> v4 player.timepara.
 *   5. RANGE CLAMPING: v4 uses narrower integer types than v2 (SMALLINT/
 *      MEDIUMINT vs INT). Out-of-range values are clamped to the v4 column
 *      maximum to avoid errors / wraparound under strict SQL mode.
 *   6. v2 army index 13 (AIX2 / Canadian Forces) does not exist in v4 and is
 *      skipped (a warning is printed if non-zero data is found).
 *
 *  This script is non-destructive to the SOURCE db and refuses to run if the
 *  TARGET already contains players (to avoid double-migration). It runs inside
 *  a single transaction and rolls back on any error.
 * ============================================================================
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration must be run from the command line.');
}

// ----------------------------------------------------------------------------
//  CONFIGURATION  -- edit these
// ----------------------------------------------------------------------------

// MySQL server hosting BOTH databases.
$CONF = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'pass' => '',

    // Database (schema) names.
    'src_db' => 'bf2stats_v2',   // existing v2 data
    'dst_db' => 'bf2stats',      // v4 db (already loaded with schema.sql + data.sql)
];

// Try to inherit the TARGET db credentials from the ASP config, if present.
// (Comment this block out to rely solely on $CONF above.)
$aspConfig = __DIR__ . '/Web Files/ASP/system/config/config.php';
if (is_file($aspConfig)) {
    include $aspConfig;
    if (isset($db_host)) $CONF['host'] = $db_host;
    if (isset($db_port)) $CONF['port'] = (int)$db_port;
    if (isset($db_user)) $CONF['user'] = $db_user;
    if (isset($db_pass)) $CONF['pass'] = $db_pass;
    if (isset($db_name)) $CONF['dst_db'] = $db_name;
    // NOTE: the SOURCE db name ($CONF['src_db']) is never overridden here.
}

// 'random' -> non-bot players get an unguessable placeholder password (treated
//             as real players, must reset password to log in).
// 'empty'  -> everyone gets an empty password (everyone treated as a bot).
$HUMAN_PASSWORD_MODE = 'random';

// Migrate the v2 `awards` table into v4 `player_award` (creates one synthetic
// round; see caveat #2). Set to false to skip awards entirely.
$MIGRATE_AWARDS = true;

// ----------------------------------------------------------------------------
//  END CONFIGURATION
// ----------------------------------------------------------------------------

// v4 integer-type maxima used for clamping.
const U_TINY  = 255;
const U_SMALL = 65535;
const U_MED   = 16777215;
const U_INT   = 4294967295;
const S_SMALL = 32767;
const S_MED   = 8388607;
const S_INT   = 2147483647;

$SRC = '`' . str_replace('`', '', $CONF['src_db']) . '`';
$DST = '`' . str_replace('`', '', $CONF['dst_db']) . '`';

function out($msg) { fwrite(STDOUT, $msg . "\n"); }
function fail($msg) { fwrite(STDERR, "\nERROR: " . $msg . "\n"); exit(1); }

// ----------------------------------------------------------------------------
//  Connect
// ----------------------------------------------------------------------------
out("BF2Stats v2 -> v4 migration");
out(str_repeat('=', 60));
out(sprintf("Source : %s", $CONF['src_db']));
out(sprintf("Target : %s", $CONF['dst_db']));
out(sprintf("Server : %s:%d as %s", $CONF['host'], $CONF['port'], $CONF['user']));
out('');

try {
    $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8', $CONF['host'], $CONF['port']);
    $pdo = new PDO($dsn, $CONF['user'], $CONF['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    fail("Could not connect to MySQL: " . $e->getMessage());
}

/** Execute a statement, return affected row count. */
function run(PDO $pdo, $sql) {
    $stmt = $pdo->query($sql);
    return $stmt->rowCount();
}
/** Fetch a single scalar value. */
function scalar(PDO $pdo, $sql) {
    return $pdo->query($sql)->fetchColumn(0);
}
/** Does a table exist in a schema? */
function tableExists(PDO $pdo, $db, $table) {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?"
    );
    $stmt->execute([$db, $table]);
    return (int)$stmt->fetchColumn(0) > 0;
}

// ----------------------------------------------------------------------------
//  Pre-flight checks
// ----------------------------------------------------------------------------
out("Running pre-flight checks...");

// Both schemas reachable?
foreach (['src_db' => 'player', 'dst_db' => 'player'] as $k => $probe) {
    if (!tableExists($pdo, $CONF[$k], $probe)) {
        fail(sprintf("Database `%s` not found or has no `%s` table. "
            . "Create/load it before running.", $CONF[$k], $probe));
    }
}

// Required v2 source tables.
foreach (['player','army','kits','vehicles','weapons','maps','kills','unlocks','awards','mapinfo'] as $t) {
    if (!tableExists($pdo, $CONF['src_db'], $t)) {
        out(sprintf("  ! source table `%s` missing - those rows will be skipped.", $t));
    }
}

// Target must be empty of players (avoid double migration).
$existing = (int)scalar($pdo, "SELECT COUNT(*) FROM $DST.`player`");
if ($existing > 0) {
    fail(sprintf("Target `%s` already contains %d players. Refusing to run to "
        . "avoid a double migration. Re-load the v4 schema for a clean import.",
        $CONF['dst_db'], $existing));
}

// Duplicate player names (v4 requires player.name UNIQUE, case-insensitive).
$dups = $pdo->query(
    "SELECT LOWER(LEFT(name,32)) AS n, COUNT(*) AS c
     FROM $SRC.`player`
     GROUP BY LOWER(LEFT(name,32)) HAVING c > 1
     ORDER BY c DESC LIMIT 20"
)->fetchAll();
if ($dups) {
    out('');
    out("Found duplicate player names (v4 requires unique names). Resolve these");
    out("in the SOURCE db first (rename or delete), then re-run:");
    foreach ($dups as $d) {
        out(sprintf("    %-34s x%d", $d['n'], $d['c']));
    }
    $totalDup = (int)scalar($pdo,
        "SELECT COUNT(*) FROM (SELECT 1 FROM $SRC.`player`
         GROUP BY LOWER(LEFT(name,32)) HAVING COUNT(*) > 1) t");
    fail(sprintf("%d duplicate name group(s) total. Aborting.", $totalDup));
}

out("  ok");
out('');

// ----------------------------------------------------------------------------
//  Migrate
// ----------------------------------------------------------------------------
$pdo->beginTransaction();
$counts = [];

try {

    // ---- player -----------------------------------------------------------
    // v2.player (+ vehicles.timepara) -> v4.player
    $pwExpr = ($HUMAN_PASSWORD_MODE === 'empty')
        ? "''"
        : "CASE WHEN p.isbot = 1 THEN '' ELSE MD5(CONCAT(p.id, RAND())) END";

    $sql = "
    INSERT INTO $DST.`player`
        (`id`,`name`,`password`,`email`,`country`,`lastip`,`joined`,`lastonline`,
         `time`,`rounds`,`rank_id`,`score`,`cmdscore`,`skillscore`,`teamscore`,
         `kills`,`deaths`,`captures`,`neutralizes`,`captureassists`,`neutralizeassists`,
         `defends`,`damageassists`,`heals`,`revives`,`resupplies`,`repairs`,
         `targetassists`,`driverspecials`,`driverassists`,`teamkills`,`teamdamage`,
         `teamvehicledamage`,`suicides`,`killstreak`,`deathstreak`,`banned`,`kicked`,
         `cmdtime`,`sqltime`,`sqmtime`,`lwtime`,`timepara`,`wins`,`losses`,`bestscore`,
         `chng`,`decr`,`mode0`,`mode1`,`mode2`,`permban`,`bantime`,`online`)
    SELECT
        p.id,
        LEFT(p.name, 32),
        $pwExpr,
        NULL,
        IF(p.country IS NULL OR p.country = '', 'xx', p.country),
        IF(p.ip IS NULL OR p.ip = '', '0.0.0.0', LEFT(p.ip, 15)),
        p.joined,
        p.lastonline,
        LEAST(p.time, " . U_INT . "),
        LEAST(p.rounds, " . U_SMALL . "),
        LEAST(p.rank, 21),
        LEAST(p.score, " . S_MED . "),
        LEAST(p.cmdscore, " . S_MED . "),
        LEAST(p.skillscore, " . S_MED . "),
        LEAST(p.teamscore, " . S_MED . "),
        LEAST(p.kills, " . U_MED . "),
        LEAST(p.deaths, " . U_MED . "),
        LEAST(p.captures, " . U_MED . "),
        LEAST(p.neutralizes, " . U_MED . "),
        LEAST(p.captureassists, " . U_MED . "),
        LEAST(p.neutralizeassists, " . U_MED . "),
        LEAST(p.defends, " . U_MED . "),
        LEAST(p.damageassists, " . U_MED . "),
        LEAST(p.heals, " . U_MED . "),
        LEAST(p.revives, " . U_MED . "),
        LEAST(p.ammos, " . U_MED . "),
        LEAST(p.repairs, " . U_MED . "),
        LEAST(p.targetassists, " . U_MED . "),
        LEAST(p.driverspecials, " . U_MED . "),
        LEAST(p.driverassists, " . U_MED . "),
        LEAST(p.teamkills, " . U_MED . "),
        LEAST(p.teamdamage, " . U_MED . "),
        LEAST(p.teamvehicledamage, " . U_MED . "),
        LEAST(p.suicides, " . U_SMALL . "),
        LEAST(p.killstreak, " . U_SMALL . "),
        LEAST(p.deathstreak, " . U_SMALL . "),
        LEAST(p.banned, " . U_SMALL . "),
        LEAST(p.kicked, " . U_SMALL . "),
        LEAST(p.cmdtime, " . U_INT . "),
        LEAST(p.sqltime, " . U_INT . "),
        LEAST(p.sqmtime, " . U_INT . "),
        LEAST(p.lwtime, " . U_INT . "),
        LEAST(COALESCE(v.timepara, 0), " . S_MED . "),
        LEAST(p.wins, " . U_SMALL . "),
        LEAST(p.losses, " . U_SMALL . "),
        LEAST(p.rndscore, " . U_SMALL . "),
        LEAST(p.chng, 1),
        LEAST(p.decr, 1),
        LEAST(p.mode0, " . U_SMALL . "),
        LEAST(p.mode1, " . U_SMALL . "),
        LEAST(p.mode2, " . U_SMALL . "),
        LEAST(p.permban, 1),
        0,
        0
    FROM $SRC.`player` p
    LEFT JOIN $SRC.`vehicles` v ON v.id = p.id";
    $counts['player'] = run($pdo, $sql);
    out(sprintf("  player           : %8d", $counts['player']));

    // ---- maps: auto-add any referenced custom maps missing from v4 --------
    $added = run($pdo, "
        INSERT IGNORE INTO $DST.`map` (`id`,`name`,`displayname`)
        SELECT mi.id, LEFT(mi.name, 48), LEFT(mi.name, 48)
        FROM $SRC.`mapinfo` mi
        LEFT JOIN $DST.`map` m ON m.id = mi.id
        WHERE m.id IS NULL
          AND mi.name <> ''
          AND EXISTS (SELECT 1 FROM $SRC.`maps` mp WHERE mp.mapid = mi.id)");
    if ($added > 0) out(sprintf("  map (custom added): %6d", $added));

    // ---- player_map -------------------------------------------------------
    $counts['player_map'] = run($pdo, "
        INSERT INTO $DST.`player_map`
            (`player_id`,`map_id`,`score`,`time`,`kills`,`deaths`,`games`,
             `wins`,`losses`,`bestscore`,`worstscore`)
        SELECT mp.id, mp.mapid, 0, LEAST(mp.time, " . U_INT . "), 0, 0, 0,
               LEAST(mp.win, " . U_SMALL . "), LEAST(mp.loss, " . U_SMALL . "),
               LEAST(mp.best, " . U_SMALL . "), LEAST(mp.worst, " . U_SMALL . ")
        FROM $SRC.`maps` mp
        JOIN $DST.`player` p ON p.id = mp.id
        JOIN $DST.`map`    m ON m.id = mp.mapid");
    out(sprintf("  player_map       : %8d", $counts['player_map']));

    // ---- player_army (unpivot 0-12) --------------------------------------
    $armyParts = [];
    for ($n = 0; $n <= 12; $n++) {
        $armyParts[] = "
        SELECT a.id, $n,
               LEAST(a.time$n, " . U_INT . "),
               LEAST(a.win$n, " . U_SMALL . "),
               LEAST(a.loss$n, " . U_SMALL . "),
               LEAST(a.score$n, " . S_INT . "),
               LEAST(a.best$n, " . S_SMALL . "),
               LEAST(a.worst$n, " . S_SMALL . "),
               LEAST(a.brnd$n, " . S_SMALL . ")
        FROM $SRC.`army` a JOIN $DST.`player` p ON p.id = a.id
        WHERE a.time$n<>0 OR a.win$n<>0 OR a.loss$n<>0 OR a.score$n<>0
           OR a.best$n<>0 OR a.worst$n<>0 OR a.brnd$n<>0";
    }
    $counts['player_army'] = run($pdo, "
        INSERT INTO $DST.`player_army`
            (`player_id`,`army_id`,`time`,`wins`,`losses`,`score`,`best`,`worst`,`brnd`)
        " . implode("\n        UNION ALL\n", $armyParts));
    out(sprintf("  player_army      : %8d", $counts['player_army']));

    // Warn about non-migratable army index 13 (no v4 army id 13).
    $a13 = (int)scalar($pdo, "
        SELECT COUNT(*) FROM $SRC.`army`
        WHERE time13<>0 OR win13<>0 OR loss13<>0 OR score13<>0
           OR best13<>0 OR worst13<>0 OR brnd13<>0");
    if ($a13 > 0) {
        out(sprintf("  ! %d players have army index 13 data (no v4 army) - skipped.", $a13));
    }

    // ---- player_kit (unpivot 0-6) ----------------------------------------
    $kitParts = [];
    for ($n = 0; $n <= 6; $n++) {
        $kitParts[] = "
        SELECT k.id, $n, LEAST(k.time$n, " . U_INT . "), 0,
               LEAST(k.kills$n, " . U_MED . "), LEAST(k.deaths$n, " . U_MED . ")
        FROM $SRC.`kits` k JOIN $DST.`player` p ON p.id = k.id
        WHERE k.time$n<>0 OR k.kills$n<>0 OR k.deaths$n<>0";
    }
    $counts['player_kit'] = run($pdo, "
        INSERT INTO $DST.`player_kit`
            (`player_id`,`kit_id`,`time`,`score`,`kills`,`deaths`)
        " . implode("\n        UNION ALL\n", $kitParts));
    out(sprintf("  player_kit       : %8d", $counts['player_kit']));

    // ---- player_vehicle (unpivot 0-6) ------------------------------------
    $vehParts = [];
    for ($n = 0; $n <= 6; $n++) {
        $vehParts[] = "
        SELECT v.id, $n, LEAST(v.time$n, " . U_INT . "), 0,
               LEAST(v.kills$n, " . U_MED . "), LEAST(v.deaths$n, " . U_MED . "),
               LEAST(v.rk$n, " . U_MED . ")
        FROM $SRC.`vehicles` v JOIN $DST.`player` p ON p.id = v.id
        WHERE v.time$n<>0 OR v.kills$n<>0 OR v.deaths$n<>0 OR v.rk$n<>0";
    }
    $counts['player_vehicle'] = run($pdo, "
        INSERT INTO $DST.`player_vehicle`
            (`player_id`,`vehicle_id`,`time`,`score`,`kills`,`deaths`,`roadkills`)
        " . implode("\n        UNION ALL\n", $vehParts));
    out(sprintf("  player_vehicle   : %8d", $counts['player_vehicle']));

    // ---- player_weapon (re-map v2 columns -> v4 weapon ids) --------------
    // weapon_id => [time, kills, deaths, fired, hits, deployed]  ('0' = no v2 column)
    $weaponMap = [
        0  => ['time0','kills0','deaths0','fired0','hit0','0'],
        1  => ['time1','kills1','deaths1','fired1','hit1','0'],
        2  => ['time2','kills2','deaths2','fired2','hit2','0'],
        3  => ['time3','kills3','deaths3','fired3','hit3','0'],
        4  => ['time4','kills4','deaths4','fired4','hit4','0'],
        5  => ['time5','kills5','deaths5','fired5','hit5','0'],
        6  => ['time6','kills6','deaths6','fired6','hit6','0'],
        7  => ['time7','kills7','deaths7','fired7','hit7','0'],
        8  => ['time8','kills8','deaths8','fired8','hit8','0'],
        // 9 = Knife
        9  => ['knifetime','knifekills','knifedeaths','knifefired','knifehit','0'],
        // 10 = Defibrillator : no v2 data
        11 => ['c4time','c4kills','c4deaths','c4fired','c4hit','0'],
        12 => ['claymoretime','claymorekills','claymoredeaths','claymorefired','claymorehit','0'],
        13 => ['handgrenadetime','handgrenadekills','handgrenadedeaths','handgrenadefired','handgrenadehit','0'],
        14 => ['atminetime','atminekills','atminedeaths','atminefired','atminehit','0'],
        15 => ['grapplinghooktime','0','grapplinghookdeaths','0','0','grapplinghookdeployed'],
        16 => ['ziplinetime','0','ziplinedeaths','0','0','ziplinedeployed'],
        17 => ['tacticaltime','0','0','0','0','tacticaldeployed'],
    ];
    $wepParts = [];
    foreach ($weaponMap as $wid => $c) {
        list($time, $kills, $deaths, $fired, $hits, $deployed) = $c;
        $te = ($time     === '0') ? '0' : "LEAST(w.$time, " . U_INT . ")";
        $ke = ($kills    === '0') ? '0' : "LEAST(w.$kills, " . U_MED . ")";
        $de = ($deaths   === '0') ? '0' : "LEAST(w.$deaths, " . U_MED . ")";
        $fe = ($fired    === '0') ? '0' : "LEAST(w.$fired, " . U_INT . ")";
        $he = ($hits     === '0') ? '0' : "LEAST(w.$hits, " . U_INT . ")";
        $pe = ($deployed === '0') ? '0' : "LEAST(w.$deployed, " . U_SMALL . ")";
        // Non-zero filter from the columns that actually exist for this weapon.
        $nz = [];
        foreach ([$time,$kills,$deaths,$fired,$hits,$deployed] as $col) {
            if ($col !== '0') $nz[] = "w.$col<>0";
        }
        $where = $nz ? ('WHERE ' . implode(' OR ', $nz)) : '';
        $wepParts[] = "
        SELECT w.id, $wid, $te, 0, $ke, $de, $fe, $he, $pe
        FROM $SRC.`weapons` w JOIN $DST.`player` p ON p.id = w.id
        $where";
    }
    $counts['player_weapon'] = run($pdo, "
        INSERT INTO $DST.`player_weapon`
            (`player_id`,`weapon_id`,`time`,`score`,`kills`,`deaths`,`fired`,`hits`,`deployed`)
        " . implode("\n        UNION ALL\n", $wepParts));
    out(sprintf("  player_weapon    : %8d", $counts['player_weapon']));

    // Note dropped "shock pad" weapon stats, if any.
    $shock = (int)scalar($pdo, "
        SELECT COUNT(*) FROM $SRC.`weapons`
        WHERE shockpadtime<>0 OR shockpadkills<>0 OR shockpaddeaths<>0
           OR shockpadfired<>0 OR shockpadhit<>0");
    if ($shock > 0) {
        out(sprintf("  ! %d players have 'shock pad' stats (no v4 weapon) - dropped.", $shock));
    }

    // ---- player_kill ------------------------------------------------------
    $counts['player_kill'] = run($pdo, "
        INSERT INTO $DST.`player_kill` (`attacker`,`victim`,`count`)
        SELECT k.attacker, k.victim, LEAST(k.count, " . U_SMALL . ")
        FROM $SRC.`kills` k
        JOIN $DST.`player` a  ON a.id  = k.attacker
        JOIN $DST.`player` vt ON vt.id = k.victim");
    out(sprintf("  player_kill      : %8d", $counts['player_kill']));

    // ---- player_unlock (granted unlocks only) ----------------------------
    // Assumes v2 unlocks.kit holds the unlock id (11,22,...,555) matching v4.
    $counts['player_unlock'] = run($pdo, "
        INSERT INTO $DST.`player_unlock` (`player_id`,`unlock_id`,`timestamp`)
        SELECT u.id, u.kit, COALESCE(p.joined, 0)
        FROM $SRC.`unlocks` u
        JOIN $DST.`player` p  ON p.id  = u.id
        JOIN $DST.`unlock` un ON un.id = u.kit
        WHERE u.state <> 'n'");
    out(sprintf("  player_unlock    : %8d", $counts['player_unlock']));

    // ---- awards (via one synthetic round) --------------------------------
    $counts['player_award'] = 0;
    $hasAwards = $MIGRATE_AWARDS
        && tableExists($pdo, $CONF['src_db'], 'awards')
        && (int)scalar($pdo, "SELECT COUNT(*) FROM $SRC.`awards`") > 0;

    if ($hasAwards) {
        $now = time();
        // Synthetic round using guaranteed-present reference rows
        // (map 0, server 1, mod 1=bf2, gamemode 0=Conquest, armies 0 & 2).
        run($pdo, "
            INSERT INTO $DST.`round`
                (`map_id`,`server_id`,`mod_id`,`gamemode_id`,`team1_army_id`,
                 `team2_army_id`,`time_start`,`time_end`,`time_imported`,
                 `winner`,`tickets1`,`tickets2`)
            VALUES (0, 1, 1, 0, 0, 2, $now, $now, $now, 0, 0, 0)");
        $rid = (int)$pdo->lastInsertId();
        out(sprintf("  synthetic round  : id=%d", $rid));

        // Badges (award id < 2,000,000): one row per level 1..N (N<=3, gold).
        $badges = run($pdo, "
            INSERT INTO $DST.`player_award` (`player_id`,`award_id`,`round_id`,`level`)
            SELECT a.id, a.awd, $rid, n.lvl
            FROM (
                SELECT id, awd, LEAST(GREATEST(MAX(level), 1), 3) AS maxlvl
                FROM $SRC.`awards`
                WHERE awd < 2000000
                GROUP BY id, awd
            ) a
            JOIN (SELECT 1 AS lvl UNION ALL SELECT 2 UNION ALL SELECT 3) n
                 ON n.lvl <= a.maxlvl
            JOIN $DST.`player` p  ON p.id = a.id
            JOIN $DST.`award`  aw ON aw.id = a.awd");

        // Medals + ribbons (award id >= 2,000,000): one 'earned' row each.
        $medals = run($pdo, "
            INSERT INTO $DST.`player_award` (`player_id`,`award_id`,`round_id`,`level`)
            SELECT a.id, a.awd, $rid, 1
            FROM (SELECT DISTINCT id, awd FROM $SRC.`awards` WHERE awd >= 2000000) a
            JOIN $DST.`player` p  ON p.id = a.id
            JOIN $DST.`award`  aw ON aw.id = a.awd");

        $counts['player_award'] = $badges + $medals;
        out(sprintf("  player_award     : %8d  (badges %d + medals/ribbons %d)",
            $counts['player_award'], $badges, $medals));
    } else {
        out("  player_award     : skipped");
    }

    $pdo->commit();

} catch (Throwable $e) {
    $pdo->rollBack();
    fail("Migration failed and was rolled back: " . $e->getMessage());
}

out('');
out(str_repeat('=', 60));
out("Migration complete. Rows inserted:");
foreach ($counts as $t => $c) {
    out(sprintf("    %-16s %8d", $t, $c));
}
out('');
out("Reminder: review the CAVEATS section at the top of this file");
out("(passwords, award dates, history, dropped columns).");
