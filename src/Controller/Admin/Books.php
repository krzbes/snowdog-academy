<?php

namespace Snowdog\Academy\Controller\Admin;

use Snowdog\Academy\Model\Book;
use Snowdog\Academy\Model\BookManager;

class Books extends AdminAbstract
{
    private BookManager $bookManager;
    private ?Book $book;

    public function __construct(BookManager $bookManager)
    {
        parent::__construct();
        $this->bookManager = $bookManager;
        $this->book = null;
    }

    public function index(): void
    {
        require __DIR__ . '/../../view/admin/books/list.phtml';
    }

    public function newBook(): void
    {
        require __DIR__ . '/../../view/admin/books/edit.phtml';
    }

    public function newBookPost(): void
    {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];

        if (empty($title) || empty($author) || empty($isbn)) {
            $_SESSION['flash'] = 'Missing data';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            return;
        }

        $this->bookManager->create($title, $author, $isbn);

        $_SESSION['flash'] = "Book $title by $author saved!";
        header('Location: /admin');
    }

    public function edit(int $id): void
    {
        $book = $this->bookManager->getBookById($id);
        if ($book instanceof Book) {
            $this->book = $book;
            require __DIR__ . '/../../view/admin/books/edit.phtml';
        } else {
            header('HTTP/1.0 404 Not Found');
            require __DIR__ . '/../../view/errors/404.phtml';
        }
    }

    public function editPost(int $id): void
    {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];

        if (empty($title) || empty($author) || empty($isbn)) {
            $_SESSION['flash'] = 'Missing data';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            return;
        }

        $this->bookManager->update($id, $title, $author, $isbn);

        $_SESSION['flash'] = "Book $title by $author saved!";
        header('Location: /admin');
    }

    private function getBooks(): array
    {
        return $this->bookManager->getAllBooks();
    }

    public function borrowedBooks()
    {
        require __DIR__ . '/../../view/admin/books/borrowedList.phtml';
    }
    private function getBooksByBorrowTime(int $days)
    {
        return $this->bookManager->getBooksByBorrowTime($days);
    }
}
