<?php declare(strict_types=1);

namespace Novuso\Common\Application\FileStorage;

use Novuso\Common\Application\FileStorage\Exception\DuplicateStorageException;
use Novuso\Common\Application\FileStorage\Exception\FileStorageException;
use Novuso\Common\Application\FileStorage\Exception\StorageNotFoundException;
use Novuso\System\Collection\HashTable;

/**
 * Class StorageService
 */
final class StorageService
{
    /**
     * Storage table
     *
     * @var HashTable
     */
    protected $storage;

    /**
     * Constructs StorageService
     */
    public function __construct()
    {
        $this->storage = HashTable::of('string', FileStorage::class);
    }

    /**
     * Retrieves file storage
     *
     * @param string $key The storage key
     *
     * @return FileStorage
     *
     * @throws StorageNotFoundException When the key is not found
     */
    public function getStorage(string $key): FileStorage
    {
        if (!$this->storage->has($key)) {
            throw StorageNotFoundException::fromKey($key);
        }

        return $this->storage->get($key);
    }

    /**
     * Copies a file across file storage instances
     *
     * @param string $sourceKey       The source storage key
     * @param string $sourcePath      The source file path
     * @param string $destinationKey  The destination storage key
     * @param string $destinationPath The destination file path
     *
     * @return void
     *
     * @throws FileStorageException When an error occurs
     */
    public function copyStorageToStorage(
        string $sourceKey,
        string $sourcePath,
        string $destinationKey,
        string $destinationPath
    ): void {
        $this->getStorage($destinationKey)->putFile(
            $destinationPath,
            $this->getStorage($sourceKey)->getFileResource($sourcePath)
        );
    }

    /**
     * Moves a file across file storage instances
     *
     * @param string $sourceKey       The source storage key
     * @param string $sourcePath      The source file path
     * @param string $destinationKey  The destination storage key
     * @param string $destinationPath The destination file path
     *
     * @return void
     *
     * @throws FileStorageException When an error occurs
     */
    public function moveStorageToStorage(
        string $sourceKey,
        string $sourcePath,
        string $destinationKey,
        string $destinationPath
    ): void {
        $this->getStorage($destinationKey)->putFile(
            $destinationPath,
            $this->getStorage($sourceKey)->getFileResource($sourcePath)
        );

        $this->getStorage($sourceKey)->removeFile($sourcePath);
    }

    /**
     * Adds file storage
     *
     * @param string      $key     The storage key
     * @param FileStorage $storage The file storage
     *
     * @return void
     *
     * @throws DuplicateStorageException When the key is already in use
     */
    public function addStorage(string $key, FileStorage $storage): void
    {
        if ($this->storage->has($key)) {
            throw DuplicateStorageException::fromKey($key);
        }

        $this->storage->set($key, $storage);
    }
}
