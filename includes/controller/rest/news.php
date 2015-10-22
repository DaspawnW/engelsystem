<?php


    /**
    *   returns json of all news available in system
    **/
    function get_News() {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(sql_select("SELECT News.Betreff, News.Datum, News.Text, News.Treffen, News.ID, User.Nick FROM News
                                     JOIN User
                                     ON News.UID = User.UID
                                     ORDER BY News.Datum;"));
        die();
    }

    function handle_News() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            get_News();
        } else {
            http_response_code(404);
            echo json_encode(array(
                'error' => 'resource not found'
            ));
            die();
        }
    }


?>