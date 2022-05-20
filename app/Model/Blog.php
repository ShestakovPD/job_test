<?php

namespace App\Model;

use Base\AbstractModel;
use Base\Db;

class Blog extends AbstractModel
{
    private $id;
    private $user_id;
    private $texts;
    private $createdAt;

    public function __construct($data = [])
    {
        if ($data) {
            $this->id = $data['id'];
            $this->user_id = $data['user_id'];
            $this->texts = $data['texts'];
            $this->createdAt = $data['created_at'];
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $id
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getText(): string
    {
        return $this->texts;
    }

    public function setText(string $texts)
    {
        $this->texts = $texts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
/*    public static function setCreatedAt(string $createdAt): self
   {
        $this->createdAt = $createdAt;
        return $this;
   }*/

    public static function getAll()
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM posts ORDER BY id DESC LIMIT 20;";
        $data = $db->fetchAll($select, __METHOD__, [
            ]
        );

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function save()
    {
        $db = Db::getInstance();
        $insert = "INSERT INTO posts (`user_id`, `texts`) VALUES (
            :user_id, :texts)";
        $db->exec($insert, __METHOD__, [
            ':user_id' => $this->user_id,
            ':texts' => $this->texts
        ]);

        $id = $db->lastInsertId();
        $this->id = $id;

        return $id;
    }

    public function delete()
    {
        $db = Db::getInstance();
        $delete = "DELETE FROM posts WHERE `id` = :id";
        $db->exec($delete, __METHOD__, [
            ':id' => $this->id,
        ]);

        $id = $db->lastInsertId();
        $this->id = $id;

        return $id;
    }

    public static function getAllById(int $user_id)
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM posts WHERE `user_id` = :user_id;";
        $data = $db->fetchAll($select, __METHOD__, [
                ':user_id' => $user_id,
            ]
        );

        if (!$data) {
            return null;
        }

        return $data;
    }

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM posts WHERE id = $id";
        $data = $db->fetchOne($select, __METHOD__);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    public static function getByUserId(string $name): ?self
    {

        $db = Db::getInstance();
        $select = "SELECT * FROM posts WHERE `user_id` = :user_id";
        $data = $db->fetchOne($select, __METHOD__, [
            ':name' => $name
        ]);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    public static function getNamePostSender()
    {

        $db = Db::getInstance();
        $select = "SELECT * FROM posts";
        $data1 = $db->fetchAll($select, __METHOD__, []);

        $postUserIds = array_column($data1, 'user_id');
        $userIdsStr = implode(',', array_unique($postUserIds));

        $db = Db::getInstance();
        $select = "SELECT * FROM users WHERE id IN ($userIdsStr)";
        $data2 = $db->fetchAll($select, __METHOD__, []);

        $usersById = array_combine(array_column($data2, 'id'), $data2);


        if (!$data1) {
            return null;
        }

        if (!$data2) {
            return null;
        }

        return $usersById;
    }

}