<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Security\Refresh;

use Coco\SourceWatcherApi\Framework\DAO;
use Coco\SourceWatcherApi\Framework\Exception as FrameworkException;
use Exception as CoreException;

class RefreshTokenDAO extends DAO
{
    /**
     * @param int $userId
     * @param string $value
     * @return RefreshToken
     * @throws FrameworkException
     */
    public function insertRefreshToken(int $userId, string $value): RefreshToken
    {
        try {
            $sqlInstruction = "INSERT INTO refresh_token (user_id, value) VALUES (?, ?);";

            $connection = $this->getConnection();

            $statement = $connection->prepare($sqlInstruction);
            $statement->bindValue(1, $userId);
            $statement->bindValue(2, $value);

            $affectedRows = $statement->executeStatement();

            $id = (int)$connection->lastInsertId();

            $refreshToken = new RefreshToken();
            $refreshToken->setId($id);
            $refreshToken->setUserId($userId);
            $refreshToken->setValue($value);

            return $refreshToken;
        } catch (FrameworkException $e) {
            throw new FrameworkException(sprintf("Something went wrong trying to insert the refresh token: %s", $e->getMessage()));
        } catch (CoreException $e) {
            throw new FrameworkException(sprintf("Something unexpected went wrong: %s", $e->getMessage()));
        }
    }

    /**
     * @param int $userId
     * @param string $value
     * @return array
     * @throws FrameworkException
     */
    public function getRefreshToken(int $userId, string $value): array
    {
        $result = [];

        try {
            $connection = $this->getConnection();

            $sqlInstruction = 'SELECT rt.id FROM refresh_token rt WHERE rt.user_id = ? AND rt.value = ?;';
            $statement = $connection->prepare($sqlInstruction);
            $statement->bindValue(1, $userId);
            $statement->bindValue(2, $value);

            $resultSet = $statement->executeQuery();

            while (($row = $resultSet->fetchAssociative()) !== false) {
                $refreshToken = new RefreshToken();
                $refreshToken->setId(intval($row['id']));
                $refreshToken->setUserId($userId);
                $refreshToken->setValue($value);

                $result[] = $refreshToken;
            }
        } catch (FrameworkException $e) {
            throw new FrameworkException(sprintf("Something went wrong trying to get the refresh token: %s", $e->getMessage()));
        } catch (CoreException $e) {
            throw new FrameworkException(sprintf("Something unexpected went wrong: %s", $e->getMessage()));
        }

        return $result;
    }

    /**
     * @param int $userId
     * @param string $value
     * @return void
     * @throws FrameworkException
     */
    public function deleteRefreshToken(int $userId, string $value): void
    {
        try {
            $sqlInstruction = "DELETE refresh_token WHERE user_id = ? AND value = ?;";

            $connection = $this->getConnection();

            $statement = $connection->prepare($sqlInstruction);
            $statement->bindValue(1, $userId);
            $statement->bindValue(2, $value);

            $affectedRows = $statement->executeStatement();
        } catch (FrameworkException $e) {
            throw new FrameworkException(sprintf("Something went wrong trying to delete the refresh token: %s", $e->getMessage()));
        } catch (CoreException $e) {
            throw new FrameworkException(sprintf("Something unexpected went wrong: %s", $e->getMessage()));
        }
    }
}
