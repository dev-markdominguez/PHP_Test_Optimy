<?php

namespace App\Utils;

use Config\DB;
use App\Model\Comment;

class CommentManager
{
	private static $instance = null;
	private $db;

	private function __construct()
	{
		// Autoloader handles class loading so no need for requiring each news and comments classes
		// Initialize the database connection
		$this->db = DB::getInstance();
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
     * Retrieves a list of all comments from the database.
     *
     * @return array Array of Comment objects representing all comments.
     */
	public function listComments()
	{
		$rows = $this->db->select('SELECT * FROM `comment`');

		$comments = [];
		foreach ($rows as $row) {
			$comment = new Comment();
			$comments[] = $comment->setId($row['id'])
				->setBody($row['body'])
				->setCreatedAt($row['created_at'])
				->setNewsId($row['news_id']);
		}

		return $comments;
	}

	/**
     * Retrieves comments associated with a specific news article.
     *
     * @param int $newsId The ID of the news article to fetch comments for.
     * @return array Array of Comment objects associated with the news article.
     */
    public function getCommentsByNewsId($newsId)
    {
        $comments = [];
        
        foreach ($this->listComments() as $comment) {
            if ($comment->getNewsId() == $newsId) {
                $comments[] = $comment;
            }
        }
        
        return $comments;
    }

    public function addCommentForNews($body, $newsId)
	{
		$sql = "INSERT INTO `comment` (`body`, `created_at`, `news_id`) VALUES('". $body . "','" . date('Y-m-d') . "','" . $newsId . "')";
		$this->db->exec($sql);
		return $this->db->lastInsertId($sql);
	}

	public function deleteComment($id)
	{
		$sql = "DELETE FROM `comment` WHERE `id`=" . $id;
		return $this->db->exec($sql);
	}
}
