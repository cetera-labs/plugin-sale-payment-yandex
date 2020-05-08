<?php
namespace SalePaymentYandex;

use YandexCheckout\Client\BaseClient;
use YandexCheckout\Common\Exceptions\ApiException;
use YandexCheckout\Common\Exceptions\AuthorizeException;
use YandexCheckout\Common\Exceptions\BadApiRequestException;
use YandexCheckout\Common\Exceptions\ExtensionNotFoundException;
use YandexCheckout\Common\Exceptions\ForbiddenException;
use YandexCheckout\Common\Exceptions\InternalServerError;
use YandexCheckout\Common\Exceptions\NotFoundException;
use YandexCheckout\Common\Exceptions\ResponseProcessingException;
use YandexCheckout\Common\Exceptions\TooManyRequestsException;
use YandexCheckout\Common\Exceptions\UnauthorizedException;
use YandexCheckout\Common\HttpVerb;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Helpers\UUID;
use YandexCheckout\Model\PaymentInterface;
use YandexCheckout\Model\RefundInterface;
use YandexCheckout\Model\Webhook\Webhook;
use YandexCheckout\Request\PaymentOptionsRequest;
use YandexCheckout\Request\PaymentOptionsRequestInterface;
use YandexCheckout\Request\PaymentOptionsRequestSerializer;
use YandexCheckout\Request\PaymentOptionsResponse;
use YandexCheckout\Request\Payments\CreatePaymentRequest;
use YandexCheckout\Request\Payments\CreatePaymentRequestInterface;
use YandexCheckout\Request\Payments\CreatePaymentResponse;
use YandexCheckout\Request\Payments\CreatePaymentRequestSerializer;
use YandexCheckout\Request\Payments\Payment\CancelResponse;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequest;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestInterface;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestSerializer;
use YandexCheckout\Request\Payments\Payment\CreateCaptureResponse;
use YandexCheckout\Request\Payments\PaymentResponse;
use YandexCheckout\Request\Payments\PaymentsRequest;
use YandexCheckout\Request\Payments\PaymentsRequestInterface;
use YandexCheckout\Request\Payments\PaymentsRequestSerializer;
use YandexCheckout\Request\Payments\PaymentsResponse;
use YandexCheckout\Request\Receipts\AbstractReceiptResponse;
use YandexCheckout\Request\Receipts\CreatePostReceiptRequest;
use YandexCheckout\Request\Receipts\CreatePostReceiptRequestInterface;
use YandexCheckout\Request\Receipts\CreatePostReceiptRequestSerializer;
use YandexCheckout\Request\Receipts\ReceiptResponseFactory;
use YandexCheckout\Request\Receipts\ReceiptsResponse;
use YandexCheckout\Request\Refunds\CreateRefundRequest;
use YandexCheckout\Request\Refunds\CreateRefundRequestInterface;
use YandexCheckout\Request\Refunds\CreateRefundRequestSerializer;
use YandexCheckout\Request\Refunds\CreateRefundResponse;
use YandexCheckout\Request\Refunds\RefundResponse;
use YandexCheckout\Request\Refunds\RefundsRequest;
use YandexCheckout\Request\Refunds\RefundsRequestInterface;
use YandexCheckout\Request\Refunds\RefundsRequestSerializer;
use YandexCheckout\Request\Refunds\RefundsResponse;
use YandexCheckout\Request\Webhook\WebhookListResponse;

class Client extends  YandexCheckout\Client {

    public function createReceiptNew($receipt, $idempotenceKey = null)
    {
        $path = self::RECEIPTS_PATH;

        $headers = array();

        if ($idempotenceKey) {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = $idempotenceKey;
        } else {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = UUID::v4();
        }

        $httpBody = json_encode($receipt);

        $response = $this->execute($path, HttpVerb::POST, null, $httpBody, $headers);

        $receiptResponse = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $factory = new ReceiptResponseFactory();
            $receiptResponse = $factory->factory($resultArray);
        } else {
            $this->handleError($response);
        }

        return $receiptResponse;
    }

}