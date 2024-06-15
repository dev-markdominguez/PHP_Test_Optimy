<?php

define('ROOT', __DIR__);

// Require Composer's autoloader to load classes automatically
require_once(ROOT . '/vendor/autoload.php');

// Import the Bootstrap\App class for initializing the application
use Bootstrap\App;

// Import the App\Utils\NewsManager class for managing news-related operations
use App\Utils\NewsManager;

// Import the App\Utils\CommentsManager class for managing comments related to news
use App\Utils\CommentManager;

// Initialize the application
$app = App::getInstance();

// At this point, the application is initialized and ready to use.

$newsManager = NewsManager::getInstance();
$commentManager = CommentManager::getInstance();

$newsManager->deleteNews(11);
// The following line includes a view file to display news and comments.
$newsManager->displayNewsWithComments();

