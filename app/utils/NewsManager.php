<?php

namespace App\Utils;

use Config\DB;
use App\Utils\CommentManager;
use App\Model\News;

class NewsManager
{
	private static $instance = null;
	private $db;
	private $commentManager;

	private function __construct()
	{
		$this->db = DB::getInstance();
		$this->commentManager = CommentManager::getInstance();
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function displayNewsWithComments()
    {
        $newsList = $this->listNews();
        $commentsList = $this->commentManager->listComments();
        require_once __DIR__ . '/../Views/news_display.php';
    }

	/**
	 * List all news
	 */
	public function listNews()
	{
		$rows = $this->db->select('SELECT * FROM `news`');

		$news = [];
		foreach ($rows as $row) {
			$n = new News();
			$news[] = $n->setId($row['id'])
			  ->setTitle($row['title'])
			  ->setBody($row['body'])
			  ->setCreatedAt($row['created_at']);
		}

		return $news;
	}

	/**
	 * Add a record in news table
	 */
	public function addNews($title, $body)
	{
	    $sql = "INSERT INTO `news` (`title`, `body`, `created_at`) VALUES (:title, :body, :created_at)";
	    $stmt = $this->db->prepare($sql);
	    
	    // Bind parameters
	    $params = [
            ':title' => $title,
            ':body' => $body,
            ':created_at' => date('Y-m-d')
        ];
	    
	    // Execute the statement
	    $stmt->execute($params);
	    
	    // Return the last inserted ID
	    return $this->db->lastInsertId();
	}

	/**
	 * Deletes a news, and also linked comments
	 */
	public function deleteNews($id)
	{
	    $comments = $this->commentManager->listComments();
	    $idsToDelete = [];

	    foreach ($comments as $comment) {
	        if ($comment->getNewsId() == $id) {
	            $idsToDelete[] = $comment->getId();
	        }
	    }

	    foreach ($idsToDelete as $commentId) {
	        $this->commentManager->deleteComment($commentId);
	    }

	    $sql = "DELETE FROM `news` WHERE `id` = :id";
	    $stmt = $this->db->prepare($sql);
	    
	    $params = [
            ':id' => $id
        ];
	    
	    // Execute the statement
	    return $stmt->execute($params);
	}
}
