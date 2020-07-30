<?php

namespace Snowdog\Academy\Model;

use Snowdog\Academy\Core\Database;

class BookManager
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(string $title, string $author, string $isbn, bool $adult_only): int
    {
        $statement = $this->database->prepare('INSERT INTO books (title, author, isbn, adult_only) VALUES (:title, :author, :isbn, :adult_only)');
        $statement->bindParam(':title', $title, Database::PARAM_STR);
        $statement->bindParam(':author', $author, Database::PARAM_STR);
        $statement->bindParam(':isbn', $isbn, Database::PARAM_STR);
        $statement->bindParam(':adult_only', $adult_only, Database::PARAM_BOOL);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function update(int $id, string $title, string $author, string $isbn, bool $adult_only): void
    {
        $statement = $this->database->prepare('UPDATE books SET title = :title, author = :author, isbn = :isbn, adult_only = :adult_only WHERE id = :id');
        $statement->bindParam(':id', $id, Database::PARAM_INT);
        $statement->bindParam(':title', $title, Database::PARAM_STR);
        $statement->bindParam(':author', $author, Database::PARAM_STR);
        $statement->bindParam(':isbn', $isbn, Database::PARAM_STR);
        $statement->bindParam(':adult_only', $adult_only, Database::PARAM_BOOL);

        $statement->execute();
    }

    public function getBookById(int $id)
    {
        $query = $this->database->prepare('SELECT * FROM books WHERE id = :id');
        $query->setFetchMode(Database::FETCH_CLASS, Book::class);
        $query->execute([':id' => $id]);

        return $query->fetch(Database::FETCH_CLASS);
    }

    public function getAllBooks(): array
    {
        $query = $this->database->query('SELECT * FROM books');

        return $query->fetchAll(Database::FETCH_CLASS, Book::class);
    }

    public function getAllBooksForChild(): array
    {
        $query = $this->database->query('SELECT * FROM books WHERE adult_only = 0');

        return $query->fetchAll(Database::FETCH_CLASS, Book::class);
    }

    public function getAvailableBooks(): array
    {
        $query = $this->database->query('SELECT * FROM books WHERE borrowed = 0');

        return $query->fetchAll(Database::FETCH_CLASS, Book::class);
    }

    public function getAvailableBooksForChild(): array
    {
        $query = $this->database->query('SELECT * FROM books WHERE borrowed = 0 && adult_only = 0');

        return $query->fetchAll(Database::FETCH_CLASS, Book::class);
    }

    public function getBooksByBorrowTime(int $daysSinceBorrow) :array
    {
        $query = $this->database->prepare('SELECT title, author, isbn,DATEDIFF(NOW(),borrowed_at) AS "daysSince" FROM books INNER JOIN borrows ON books.id=borrows.book_id WHERE DATEDIFF(NOW(),borrowed_at) >= :daysSinceBorrow');
        $query->bindParam(':daysSinceBorrow', $daysSinceBorrow, Database::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }
}
