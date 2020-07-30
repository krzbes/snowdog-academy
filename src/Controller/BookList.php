<?php

namespace Snowdog\Academy\Controller;

use Snowdog\Academy\Model\BookManager;

class BookList
{
    private BookManager $bookManager;

    public function __construct(BookManager $bookManager)
    {
        $this->bookManager = $bookManager;
    }

    public function index(): void
    {
        require __DIR__ . '/../view/index/books.phtml';
    }

    private function getBooks(): array
    {
        if($_SESSION['is_adult'])
        {
            return $this->bookManager->getAvailableBooks();
        }
        else
        {
            return $this->bookManager->getAvailableBooksForChild();
        }

    }
}
