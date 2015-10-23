<?php


    /**
    *   url: GET - ?p=api&r=news
    *   returns json array of all news available in system
    *   [
    *       {
    *           "Betreff": "",
    *           "Datum": "",
    *           "Text": "",
    *           "Treffen": "",
    *           "ID": "",
    *           "Nick": ""
    *       }
    *   ]
    **/
    function get_allNews() {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(sql_select("SELECT News.Betreff, News.Datum, News.Text, News.Treffen, News.ID, User.Nick FROM News
                                     JOIN User
                                     ON News.UID = User.UID
                                     ORDER BY News.Datum;"));
        die();
    }

    /**
        *   url: GET - /?p=api&r=news&id=:id
        *   returns json object with one news item by :id
        *       {
        *           "Betreff": "",
        *           "Datum": "",
        *           "Text": "",
        *           "Treffen": "",
        *           "ID": "",
        *           "Nick": "",
        *           "comments": [] // JSON Array with comments
        *       }
        **/
    function get_News() {
        header("Content-Type: application/json; charset=utf-8");
        $newsId = $_REQUEST['id'];
        // get news by newsId
        $newsItem =sql_select("SELECT News.Betreff, News.Datum, News.Text, News.Treffen, News.ID, User.Nick FROM News
                                    JOIN User
                                    ON News.UID = User.UID
                                    WHERE News.ID = " . sql_escape($newsId) . ";");
        
        if (count($newsItem) != 1) {
            // no item found
            http_response_code(404);
            echo json_encode(array(
                'error' => 'resource not found'
            ));
        } else {
            $newsItem = $newsItem[0];
            // get all comments for this news item
            $comments = sql_select("SELECT User.Nick, NewsComments.Text, NewsComments.Datum FROM NewsComments
                        JOIN User
                          ON User.UID = NewsComments.UID
                          WHERE NewsComments.Refid = " . sql_escape($newsItem['ID']) . ";");
                          
            $newsItem['comments'] = $comments;
            
            echo json_encode($newsItem);
        }
        die();
    }

    function handle_News() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_REQUEST['id'])) {
            get_allNews();
            return;
        } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            get_News();
        } else{
            header("Content-Type: application/json; charset=utf-8");
            http_response_code(404);
            echo json_encode(array(
                'error' => 'resource not found'
            ));
            die();
            return;
        }
    }


?>