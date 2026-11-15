<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Collections\Dictionary;
use System\Controller;
use System\Database;
use System\View;

/**
 * Gamedata Module Controller
 *
 * @package Modules
 */
class Gamedata extends Controller
{
    /**
     * @protocol    ANY
     * @request     /ASP/gamedata
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load data
        $pdo = Database::GetConnection('stats');
        $armies = $pdo->query("SELECT * FROM army ORDER BY id")->fetchAll();
        $kits = $pdo->query("SELECT * FROM kit ORDER BY id")->fetchAll();
        $vehicles = $pdo->query("SELECT * FROM vehicle ORDER BY id")->fetchAll();
        $weapons = $pdo->query("SELECT * FROM weapon ORDER BY id")->fetchAll();
        $modes = $pdo->query("SELECT * FROM game_mode ORDER BY id")->fetchAll();
        // Add button type for armies
        $counter = count($armies);

        // Add button type for armies
        for ($i = 0; $i < $counter; $i++)
        {
            $armies[$i]['bid'] = ($armies[$i]['id'] > 10) ? 'delete' : 'disable';
            $armies[$i]['title'] = ($armies[$i]['id'] > 10) ? 'Delete Army' : 'Cannot delete vanilla armies';
        }
        // Add button type for kits
        $counter = count($kits);

        // Add button type for kits
        for ($i = 0; $i < $counter; $i++)
        {
            $kits[$i]['bid'] = ($kits[$i]['id'] > 6) ? 'delete' : 'disable';
            $kits[$i]['title'] = ($kits[$i]['id'] > 6) ? 'Delete Kit' : 'Cannot delete vanilla kits';
        }
        // Add button type for vehicles
        $counter = count($vehicles);

        // Add button type for vehicles
        for ($i = 0; $i < $counter; $i++)
        {
            $vehicles[$i]['bid'] = ($vehicles[$i]['id'] > 6) ? 'delete' : 'disable';
            $vehicles[$i]['title'] = ($vehicles[$i]['id'] > 6) ? 'Delete Vehicle' : 'Cannot delete vanilla vehicles';
        }
        // Add button type for weapons
        $counter = count($weapons);

        // Add button type for weapons
        for ($i = 0; $i < $counter; $i++)
        {
            $weapons[$i]['bid'] = ($weapons[$i]['id'] > 17) ? 'delete' : 'disable';
            $weapons[$i]['title'] = ($weapons[$i]['id'] > 17) ? 'Delete Weapon' : 'Cannot delete vanilla weapons';
        }
        // Add button type for weapons
        $counter = count($modes);

        // Add button type for weapons
        for ($i = 0; $i < $counter; $i++)
        {
            $canDelete = ($modes[$i]['id'] > 2 && $modes[$i]['id'] != 99);
            $modes[$i]['bid'] = ($canDelete) ? 'delete' : 'disable';
            $modes[$i]['title'] = ($canDelete) ? 'Delete Game Mode' : 'Cannot delete vanilla game modes';
        }


        // Load view
        $view = new View('index', 'gamedata');
        $view->set('armies', $armies);
        $view->set('kits', $kits);
        $view->set('vehicles', $vehicles);
        $view->set('weapons', $weapons);
        $view->set('modes', $modes);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/modules/gamedata/js/index.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/gamedata/awards
     * @output      html
     */
    public function awards(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load data
        $pdo = Database::GetConnection('stats');

        // Fetch awards, then get thier awarding counts
        $awards = $pdo->query("SELECT * FROM `award` ORDER BY `type`, `backend`")->fetchAll();
        $counts = $pdo->query("SELECT award_id, COUNT(*) AS `count`FROM player_award GROUP BY award_id")->fetchAll();

        $awardCount = [];
        foreach ($counts as $award)
            $awardCount[$award['award_id']] = $award['count'];
        // Apply formatting
        $counter = count($awards);

        // Apply formatting
        for ($i = 0; $i < $counter; $i++)
        {
            $id = $awards[$i]['id'];
            $awards[$i]['count'] = number_format($awardCount[$id] ?? 0);
            $awards[$i]['backend'] = ($awards[$i]['backend'] == 1) ? "Yes" : "No";
            $awards[$i]['type'] = match ((int)$awards[$i]['type']) {
                0 => "Ribbon",
                1 => "Badge",
                2 => "Medal",
                default => "Unknown",
            };
        }

        // Load view
        $view = new View('awards', 'gamedata');
        $view->set('awards', $awards);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/gamedata/js/awards.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/gamedata/unlocks
     * @output      html
     */
    public function unlocks(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load data
        $pdo = Database::GetConnection('stats');
        $unlocks = $pdo->query("SELECT u.*, k.name AS `kitname` FROM `unlock` AS u INNER JOIN `kit` AS k ON k.id = u.kit_id ORDER BY `id`")->fetchAll();
        $kits = $pdo->query("SELECT `id`, `name` FROM `kit` ORDER BY `id`")->fetchAll();

        // Get all unlock requirements
        $unlockChecks = new Dictionary();
        $result = $pdo->query("SELECT ur.parent_id, ur.child_id AS child_id, u.name AS parent_name FROM `unlock_requirement` AS ur JOIN `unlock` AS u ON ur.parent_id = u.id");
        while ($row = $result->fetch())
        {
            $unlockChecks->add($row['child_id'], $row['parent_name']);
        }
        // Modify unlocks array to include requirements
        $counter = count($unlocks);

        // Modify unlocks array to include requirements
        for ($i = 0; $i < $counter; $i++)
        {
            $id = $unlocks[$i]['id'];
            $unlocks[$i]['reqname'] = ($unlockChecks->containsKey($id) ? $unlockChecks[$id] : "None");
        }

        // Load view
        $view = new View('unlocks', 'gamedata');
        $view->set('unlocks', $unlocks);
        $view->set('kits', $kits);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/gamedata/js/unlocks.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/gamedata/mods
     * @output      html
     */
    public function mods(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load data
        $mods = [];
        $pdo = Database::GetConnection('stats');
        $result = $pdo->query("SELECT * FROM `game_mod` ORDER BY `id` ASC");
        while ($row = $result->fetch())
        {
            $auth = (int)$row['authorized'];
            if ($auth == 0)
            {
                $row['status_badge'] = 'important';
                $row['status_text'] = 'Not Authorized';
            }
            else
            {
                $row['status_badge'] = 'success';
                $row['status_text'] = 'Authorized';
            }

            $mods[] = $row;
        }

        // Load view
        $view = new View('game_mods', 'gamedata');
        $view->set('mods', $mods);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/gamedata/js/game_mods.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/add
     * @output      json
     */
    public function postAdd(): void
    {
        // Require action
        $this->requireAction('add', 'edit');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);

            // Define server id
            $id = (isset($_POST['itemId'])) ? (int)$items['itemId'] : 0;
            $name = preg_replace('/[^A-Za-z0-9_\-\s\t\/\.]/', '', trim((string) $items['itemName']));
            $type = preg_replace("/[^A-Za-z_]/", '', (string) $items['itemType']);

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    $qType = $pdo->quoteIdentifier($type);

                    /** @noinspection SqlResolve */
                    $max = (int)$pdo->query("SELECT COALESCE(max(id), -1) FROM $qType")->fetchColumn(0);
                    $pdo->insert($type, [
                        "id" => ++$max,
                        "name" => $name,
                    ]);

                    // Get insert ID
                    echo json_encode(['success' => true, 'itemId' => $max, 'itemName' => $name, 'itemType' => $type]);
                    break;
                case 'edit':
                    $pdo->update($type, ['name' => $name], ['id' => $id]);
                    echo json_encode(['success' => true, 'itemId' => $id, 'itemName' => $name, 'itemType' => $type]);
                    break;
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), ['lastQuery' => $pdo->lastQuery]);
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/delete
     * @output      json
     */
    public function postDelete(): void
    {
        // Require action
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        // Prepared statement!
        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);

            // Get item id
            $itemId = (int)$items['itemId'];
            $type = preg_replace("/[^A-Za-z_]/", '', (string) $items['itemType']);

            // Perform identifier escapes
            $qType = $pdo->quoteIdentifier($type);
            $table = $pdo->quoteIdentifier("player_{$type}");
            $col = $pdo->quoteIdentifier("{$type}_id");

            if ($type != 'game_mode')
            {
                // Prepare statement
                /** @noinspection SqlResolve */
                $stmt = $pdo->prepare("DELETE FROM {$table} WHERE {$col}=:id");
                $stmt->bindValue(':id', $itemId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Prepare statement
            /** @noinspection SqlResolve */
            $stmt = $pdo->prepare("DELETE FROM {$qType} WHERE `id`=:id");
            $stmt->bindValue(':id', $itemId, PDO::PARAM_INT);
            $stmt->execute();

            // Echo success
            $this->sendJsonResponse(true, '', ['itemId' => $itemId, 'itemType' => $type] );
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/addAward
     * @output      json
     */
    public function postAddAward(): void
    {
        // Require action
        $this->requireAction('add', 'edit');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);
            $data = [
                'id' => (int)$items['awardId'],
                'name' => preg_replace('/[^A-Za-z0-9_\-\s\t\/\.]/', '', trim((string) $items['awardName'])),
                'code' => preg_replace("/[^A-Za-z0-9]/", '', (string) $items['awardCode']),
                'type' => (int)$items['awardType'],
                'backend' => (int)$items['awardBackend']
            ];

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    $pdo->insert('award', $data);

                    // Get insert ID
                    $data['success'] = true;
                    $data['mode'] = 'add';
                    echo json_encode($data);
                    break;
                case 'edit':
                    $pdo->update('award', $data, ['id' => (int)$items['originalId']]);

                    $data['success'] = true;
                    $data['mode'] = 'edit';
                    echo json_encode($data);
                    break;
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), ['lastQuery' => $pdo->lastQuery]);
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/deleteAward
     * @output      json
     */
    public function postDeleteAward(): void
    {
        // Require action
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        // Prepared statement!
        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);
            $awardId = (int)$items['awardId'];

            // Prepare statement
            $stmt = $pdo->prepare("DELETE FROM player_award WHERE award_id=:id");
            $stmt->bindValue(':id', $awardId, PDO::PARAM_INT);
            $stmt->execute();

            // Prepare statement
            $stmt = $pdo->prepare("DELETE FROM award WHERE id=:id");
            $stmt->bindValue(':id', $awardId, PDO::PARAM_INT);
            $stmt->execute();

            // Echo success
            $this->sendJsonResponse(true, '', ['awardId' => $awardId]);
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/addUnlock
     * @output      json
     */
    public function postAddUnlock(): void
    {
        // Require action
        $this->requireAction('add', 'edit');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);
            $data = [
                'id' => (int)$items['unlockId'],
                'kit_id' => (int)$items['unlockKit'],
                'name' => preg_replace('/[^A-Za-z0-9_]/', '', trim((string) $items['unlockName'])),
                'desc' => preg_replace('/[^A-Za-z0-9_\-\s\t\/\.&\(\)]/', '', (string) $items['unlockDesc'])
            ];
            $name = $pdo->quoteIdentifier('name');

            // Fetch required unlock name
            $reqName = "None";
            $req = (int)$items['unlockRequired'];
            if ($req != 0)
            {
                $reqName = $pdo->query("SELECT {$name} FROM `unlock` WHERE id=". $req)->fetchColumn(0);
            }

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    $pdo->insert('unlock', $data);

                    // Do we have a required unlock constraint?
                    if ($req != 0)
                    {
                        $pdo->insert('unlock_requirement', ['parent_id' => $req, 'child_id' => $data['id']]);
                    }

                    // Get kit name
                    $data['kit'] = $pdo->query("SELECT {$name} FROM kit WHERE id=". $data['kit_id'])->fetchColumn(0);
                    $data['reqname'] = $reqName;
                    $data['success'] = true;
                    $data['mode'] = 'add';
                    echo json_encode($data);
                    break;
                case 'edit':
                    $pdo->update('unlock', $data, ['id' => (int)$items['originalId']]);

                    // Always empty this!
                    $pdo->delete('unlock_requirement', ['child_id' => $data['id']]);

                    // Do we have a required unlock constraint?
                    if ($req != 0)
                    {
                        $pdo->insert('unlock_requirement', ['parent_id' => $req, 'child_id' => $data['id']]);
                    }

                    // Get kit name
                    $data['kit'] = $pdo->query("SELECT {$name} FROM kit WHERE id=". $data['kit_id'])->fetchColumn(0);
                    $data['reqname'] = $reqName;
                    $data['success'] = true;
                    $data['mode'] = 'edit';
                    echo json_encode($data);
                    break;
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), ['lastQuery' => $pdo->lastQuery]);
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/deleteUnlock
     * @output      json
     */
    public function postDeleteUnlock(): void
    {
        // Require action
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        // Prepared statement!
        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);
            $uId = (int)$items['unlockId'];

            // Perform identifier escapes
            $table = $pdo->quoteIdentifier("unlock");

            // Prepare statement
            $stmt = $pdo->prepare("DELETE FROM player_unlock WHERE unlock_id=:id");
            $stmt->bindValue(':id', $uId, PDO::PARAM_INT);
            $stmt->execute();

            // Prepare statement
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id=:id");
            $stmt->bindValue(':id', $uId, PDO::PARAM_INT);
            $stmt->execute();

            // Echo success
            $this->sendJsonResponse(true, 'Success', ['unlockId' => $uId]);
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/gamedata/addMod
     * @output      json
     */
    public function postAddMod(): void
    {
        // Require action
        $this->requireAction('add', 'edit');

        // Require database connection
        $this->requireDatabase(true);
        $pdo = Database::GetConnection('stats');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);
            $data = [
                'name' => preg_replace('/[^A-Za-z0-9_]/', '', trim((string) $items['shortName'])),
                'longname' => preg_replace('/[^A-Za-z0-9_\-\s\t\/\.&\(\)]/', '', (string) $items['longName']),
                'authorized' => (int)$items['authorized']
            ];

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    $pdo->insert('game_mod', $data);
                    $data['id'] = $pdo->lastInsertId('id');
                    $data['success'] = true;
                    $data['mode'] = 'add';
                    $data['status_badge'] = ($data['authorized'] !== 0) ? 'success' : 'important';
                    $data['status_text'] = ($data['authorized'] !== 0) ? 'Authorized' : 'Not Authorized';
                    echo json_encode($data);
                    break;
                case 'edit':
                    $pdo->update('game_mod', $data, ['id' => (int)$items['originalId']]);
                    $data['id'] = (int)$items['originalId'];
                    $data['success'] = true;
                    $data['mode'] = 'edit';
                    $data['status_badge'] = ($data['authorized'] !== 0) ? 'success' : 'important';
                    $data['status_text'] = ($data['authorized'] !== 0) ? 'Authorized' : 'Not Authorized';
                    echo json_encode($data);
                    break;
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), ['lastQuery' => $pdo->lastQuery]);
            die;
        }
    }
}
