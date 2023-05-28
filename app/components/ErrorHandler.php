<?php

namespace app\components;

use app\enums\HttpCode;
use app\exceptions\ValidationException;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ErrorHandler as BaseErrorHandler;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use Throwable;

class ErrorHandler extends BaseErrorHandler
{
    protected function renderException($exception): void
    {
        $data = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];

        if (method_exists($exception, 'getName')) {
            $data['name'] = $exception->getName();
        }

        Yii::error($data, 'mainError');

        $response = new Response();

        $response->data = $this->getData($exception);
        $response->format = Response::FORMAT_JSON;
        $response->statusCode = $this->getStatusCode($exception);
        $response->send();

        exit;
    }

    private function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof NotFoundHttpException) {
            return HttpCode::NOT_FOUND->value;
        }

        if ($exception instanceof ForbiddenHttpException) {
            return HttpCode::FORBIDDEN->value;
        }

        if ($exception instanceof BadRequestHttpException) {
            return HttpCode::BAD_REQUEST->value;
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return HttpCode::UNAUTHORIZED->value;
        }

        if ($exception instanceof ValidationException) {
            return HttpCode::BAD_REQUEST->value;
        }

        return HttpCode::SERVER_ERROR->value;
    }

    /**
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    private function getData(Throwable $exception): array
    {
        if ($exception instanceof ValidationException) {
            /** @var array<string, mixed> $data */
            $data = json_decode($exception->getMessage(), true);

            return $data;
        }

        if ($exception instanceof BadRequestHttpException) {
            /** @phpstan-ignore-next-line */
            return [
                'message' => [$exception->getMessage()],
            ];
        }

        /** @phpstan-ignore-next-line */
        return [
            'message' => $exception->getMessage(),
        ];
    }
}
