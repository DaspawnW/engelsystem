<?php

/**
 * Creates a link or url to a given resource.
 *
 * @param $resource
 */
function api_link($resource)
{
    return page_link_to('api') . '&r=' . $resource;
}

function getApiShifts()
{
    $pageSize = isset($_REQUEST['per_page']) && preg_match('/\d+/', $_REQUEST['per_page']) ? (int)$_REQUEST['per_page'] : null;
    $page = isset($_REQUEST['page']) && preg_match('/\d+/', $_REQUEST['page']) ? (int)$_REQUEST['page'] : null;
    $limit = '';
    if (null !== $page && null !== $pageSize) {
        $offset = $page === 0 ? 0 :  ($page - 1)*$pageSize;
        $limit = sprintf('LIMIT %s, %s', $offset, $pageSize);
    }

    $query = "SELECT
                s.*, r.Name as locationName, r.location, r.lat, r.long, t.name as shiftType
              FROM `Shifts` s
              JOIN `ShiftTypes` t ON s.`shifttype_id` = t.`id`
              JOIN `Room` r ON s.`RID` = r.`RID`
              WHERE s.`start` > UNIX_TIMESTAMP() OR s.`end` > UNIX_TIMESTAMP()
              GROUP  BY s.`SID`
              ORDER BY s.`start`
              " . $limit;

    $dbResult = sql_select($query);

    return $dbResult;
}

function api_controller()
{
    $resource = isset($_REQUEST['r']) ? $_REQUEST['r'] : null;

    if (null === $resource) {
        error(_('no resource given.'));

        return;
    }

    if ($resource == 'news') {
        require_once realpath(__DIR__ . '/rest/news.php');
        handle_News();
        return;
    }

   header("Content-Type: application/json; charset=utf-8");
   http_response_code(404);
   echo json_encode(array(
       'error' => 'resource not found'
   ));
}
?>
