<?php

declare(strict_types=1);

namespace app\components;

use app\models\AbstractUser as User;
use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Validator;
use yii\base\Component;

class JwtComponent extends Component
{
    private string $issuedBy;
    private Builder $tokenBuilder;
    private Sha256 $algorithm;
    private InMemory $signingKey;
    private \DateTimeImmutable $time;

    public function init(): void
    {
        parent::init();

        $this->issuedBy = env('JWT_ISSUED_BY', 'app_issued_by');
        $this->tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $this->algorithm = new Sha256();
        /** @phpstan-ignore-next-line */
        $this->signingKey = InMemory::plainText(env('JWT_KEY', '34hbif(29$438n^fao3(wndjkldet8re5/2945H4n453$2dfnkjf45'));
        $this->time = new \DateTimeImmutable();
    }

    public function generateRefreshToken(User $user): string
    {
        $tokenLifeTime = env('REFRESH_TOKEN_LIFETIME', '1 day');

        $token = $this->tokenBuilder
            ->issuedAt($this->time)
            ->issuedBy($this->issuedBy)
            ->expiresAt($this->time->modify('+' . $tokenLifeTime))
            ->withClaim('uid', $user->id)
            ->withClaim('rand', time() . rand(1, 1000))
            ->getToken($this->algorithm, $this->signingKey);

        return $token->toString();
    }

    public function generateAccessToken(User $user): string
    {
        $tokenLifeTime = env('ACCESS_TOKEN_LIFETIME', '1 minute');

        $token = $this->tokenBuilder
            ->issuedAt($this->time)
            ->issuedBy($this->issuedBy)
            ->expiresAt($this->time->modify('+' . $tokenLifeTime))
            ->withClaim('uid', $user->id)
            ->withClaim('rand', time() . rand(1, 1000))
            ->getToken($this->algorithm, $this->signingKey);

        return $token->toString();
    }

    public function generateUUIDToken(User $user): string
    {
        $tokenLifeTime = env('UUID_TOKEN_LIFETIME', '2 minute');

        $token = $this->tokenBuilder
            ->issuedAt($this->time)
            ->issuedBy($this->issuedBy)
            ->expiresAt($this->time->modify('+' . $tokenLifeTime))
            ->withClaim('uid', $user->id)
            ->withClaim('rand', time() . rand(1, 1000))
            ->getToken($this->algorithm, $this->signingKey);

        return $token->toString();
    }

    public function isValid(string $jwt): bool
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwt);
        $validator = new Validator();

        if (!$validator->validate($token, new IssuedBy($this->issuedBy))) {
            return false;
        }

        return !$token->isExpired($this->time);
    }

    public function getUserIdFromToken(string $jwt): ?int
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwt);

        assert($token instanceof UnencryptedToken);

        if (!$token->claims()->has('uid')) {
            return null;
        }

        return $this->getIntValue($token->claims()->get('uid'));
    }

    public function getExpiresAtFromToken(string $jwt): ?DateTimeImmutable
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwt);

        assert($token instanceof UnencryptedToken);

        if (!$token->claims()->has('exp')) {
            return null;
        }

        return $this->getDateTimeImmutableValue($token->claims()->get('exp'));
    }

    private function getIntValue(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        return null;
    }

    private function getDateTimeImmutableValue(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        return null;
    }
}