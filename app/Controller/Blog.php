<?php

namespace App\Controller;

use App\Model\Blog as BlogModel;
use App\Model\User as UserModel;
use Base\AbstractController;
use Base\Db;


class Blog extends AbstractController
{
    public $posts;
    public $blogs;
    public $posts_user_id;
    public $posts_name;

    public function indexAction()
    {
        if (!$this->user) {
            $this->redirect('/user/register');
        }

        return $this->view->render('Blog/index.phtml', [
            'user' => $this->user
        ]);
    }

    public function allpostsAction()
    {

        if (!$this->user) {
            $this->redirect('/user/register');
        }

        $this->posts = BlogModel::getAll();
        $this->posts_name = BlogModel::getNamePostSender();


        return $this->view->render('Blog/blog.phtml', [
            'user' => $this->user,
            'posts' => $this->posts,
            'posts_name' => $this->posts_name
        ]);
    }

    public function sendPostAction()
    {
        $message = htmlspecialchars(trim($_POST['message']));
        $user_id = $_SESSION['id'];
        $createdAt = date("Y-m-d H:i:s");

        $success = true;
        if (isset($_POST['message'])) {

            if (!$message) {
                $this->view->assign('error', 'Текст не может быть пустым');
                $success = false;
            }

            if ($success) {
                $blogs = (new BlogModel())->setUserId($user_id)->setText($message)/*->setCreatedAt($createdAt)*/;

                $blogs->save();

                $sendPostUserId = $blogs->getId();

                if (!empty($_FILES['userfile']['tmp_name'])) {
                    $fileContent = file_get_contents($_FILES['userfile']['tmp_name']);
                    file_put_contents('images/' . $sendPostUserId . '.png', $fileContent);
                }

                $this->redirect('/blog/index');
            }
        }

        return $this->view->render('Blog/blog.phtml', [
            'user' => $this->user,
        ]);
    }

    public function deletePostAction()
    {
        $id = ($_POST['id']);
        $blogs = (new BlogModel())->setId($id);
        $blogs->delete();
        $this->redirect('/blog/index');

        return $this->view->render('Blog/blog.phtml', [
            'user' => $this->user,
        ]);
    }

    public function apiAction()
    {
        $user_id_api = ((int)$_POST['user_id_api']);

        if (!$this->user) {
            $this->redirect('/user/register');
        }

        $this->posts = BlogModel::getAll();
        $this->posts_name = BlogModel::getNamePostSender();
        $this->posts_user_id = BlogModel::getAllById($user_id_api);

        return $this->view->render('Blog/blog.phtml', [
            'user' => $this->user,
            'posts' => $this->posts,
            'posts_name' => $this->posts_name,
            'posts_user_id' => $this->posts_user_id
        ]);
    }


}