<?php

foreach ($newsList as $news) {
    echo("############ NEWS " . $news->getTitle() . "(".$news->getId().") ############\n");
    echo($news->getBody() . "\n");
    foreach ($commentsList as $comment) {
        if ($comment->getNewsId() == $news->getId()) {
            echo("Comment " . $comment->getId() . " : " . $comment->getBody() . "\n");
        }
    }
}

?>