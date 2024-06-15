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

    /**
     * Adds a new comment to the database for a specific news article.
     * Refactored to use prepared statements for SQL Injection prevention.
     * Additional Error handling
     *
     * @param string $body The body/content of the comment.
     * @param int $newsId The ID of the news article the comment belongs to.
     * @return int|false The ID of the newly inserted comment or false on failure.
     */
	public function addCommentForNews($body, $newsId)
    {
        $sql = "INSERT INTO `comment` (`body`, `created_at`, `news_id`) VALUES (:body, :created_at, :news_id)";

        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':body' => $body,
                ':created_at' => date('Y-m-d'),
                ':news_id' => $newsId,
            ];

            $stmt->execute($params);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('PDOException: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteComment($id)
    {
        $sql = "DELETE FROM `comment` WHERE `id` = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);

        } catch (PDOException $e) {
            error_log('PDOException: ' . $e->getMessage());
            return false;
        }
    }
}
