[![MIT License](https://img.shields.io/apm/l/atomic-design-ui.svg?)](https://github.com/tterb/atomic-design-ui/blob/master/LICENSEs)
# Atomic transaction provider library

The project is a library that provides a simple mechanism for atomic transactions.
Just create an instance of a transaction and appends some atoms.
When you commit the transaction you are sure that either
all the atoms are committed or none.




## Atoms

All the atoms you want to execute in a transaction need to be objects
implementing an `Atomic\Atom` interface. The interface provides two methods:
`commit()` and `rollback()`. Because the mechanism is versatile and can
be used in many cases, the library supports not only database transactions.
You can use it for file transfer, image manipulation, and many others.

Because you may want to be sure that the transaction is executed as a whole,
every atom needs to have a `rollback()` method. The rollback is executed
only when the commit of the atom was successful but there was a subsequent atom
whose commit failed. In this case, the mechanism rolls back every committed
atom in the reversed order.

Example of an atom:

```php
class FileCreatorAtom implements Atomic\Atom {

    public function __construct(private string $filename, private string $content)
    {
    }

    public function commit(): void
    {
        file_put_contents($this->filename, $this->content);
    }

    public function rollback(): void
    {
        unlink($this->filename);
    }
}
```

## Transaction

Before you execute the transaction you have to register the required atoms.
It is done by calling `append()` or `prepend()` on the transaction object.

When every atom is registered you can call `execute()` method on the transaction.
It is important to `try { ... } catch` the execution as this is the way
to know if the transaction was successful or not. Failed and rolled back commits
throw `Atomic\CommitException`.

Besides the commit exception, there is an `Atomic\RollbackException` also.
It is thrown when not only some commit fails, but some rollback fails either.

To `commit()` or `rollback()` be considered failed it must throw an exception.

```php
$fileAtom1 = new FileCreatorAtom("file1.txt", "I'll be saved first");
$fileAtom2 = new FileCreatorAtom("file2.txt", "I'll be saved second");
$fileAtom3 = new FileCreatorAtom("file2.txt", "I'll be saved third");

$transaction = new Atomic\Transaction();

$transaction->append($fileAtom2);
$transaction->append($fileAtom3);
$transaction->prepend($fileAtom1); // i'll be before others

try {
    $transaction->execute();
    // here execution succeeded
} catch (Atomic\CommitException $e) {
    // here execution failed, rolled back
} catch (Atomic\RollbackException $e) {
    // here execution failed, rollback also failed
}
```
## Contributing

Contributions are always welcome!
You can post a bug report as an issue,
create a pull request with some changes or contact me on Twitter.

To clone the project:

```bash
  git clone https://github.com/daropotter/atomic
  cd atomic
  composer install
```

Make sure all tests and static code analysis pass:

```bash
  make qa
```

or (if you don't want to use `make`):

```bash
  vendor/bin/phpcs
  vendor/bin/psalm
  vendor/bin/phpunit
```

## License

[MIT](https://choosealicense.com/licenses/mit/)


## 🔗 Links
[![github](https://img.shields.io/badge/github-000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/daropotter)
[![gitlab](https://img.shields.io/badge/gitlab-0A66C2?style=for-the-badge&logo=gitlab&logoColor=white)](https://gitlab.com/daropotter)
[![twitter](https://img.shields.io/badge/twitter-1DA1F2?style=for-the-badge&logo=twitter&logoColor=white)](https://twitter.com/daropotter)

