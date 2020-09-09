<?php

namespace App\Channels;

use AsyncAws\Sqs\Input\SendMessageRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsChannel
{
    protected $awsQueueName = 'smsblast-queue';

    protected $loggingName = 'NOTIFICATION_SMS_SENT';

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        $this->process($notifiable, $message);
    }

    protected function process($notifiable, $message)
    {
        $enableNotification = config('notifications.notify');

        if ($enableNotification === false) {
            return false;
        }

        $phoneNumber = $this->getPhoneNumber($notifiable);

        if ($phoneNumber instanceof Collection) {
            $phoneNumber->each(function ($phoneNumberValue) use ($message) {
                $this->pushQueue($phoneNumberValue, $message);
            });

            return true;
        }

        $this->pushQueue($phoneNumber, $message);
    }

    protected function cleanPhoneNumber($phoneNumber)
    {
        return preg_replace('/^0{1}/', '62', $phoneNumber);
    }

    protected function pushQueue($phoneNumber, $message)
    {
        /**
         * @var \AsyncAws\Core\AwsClientFactory $aws
         * @var \AsyncAws\Sqs\SqsClient $sqs
         */
        $aws      = app('aws');
        $sqs      = $aws->sqs();
        $queueUrl = $sqs->getQueueUrl(['QueueName' => $this->awsQueueName])->getQueueUrl();

        $messageRequest = new SendMessageRequest([
            'QueueUrl'          => $queueUrl,
            'DelaySeconds'      => 10,
            'MessageAttributes' => [
                'PhoneNumber'   => [
                    'DataType'  => 'String',
                    'StringValue' => $phoneNumber
                ]
            ],
            'MessageBody' => $message,
        ]);

        $sqs->sendMessage($messageRequest);

        Log::info($this->loggingName, [
            'phone_number' => $phoneNumber,
            'message' => $message
        ]);
    }

    /**
     * In development/staging environment, we need to carefully send notifications.
     * Redirect/send to developer instead to real numbers
     * @param $notifiable
     * @return string|\Illuminate\Support\Collection
     * @throws \Exception
     */
    protected function getPhoneNumber($notifiable)
    {
        $configPhoneNumbers = config('notifications.notify_to');

        if ($configPhoneNumbers === null) {
            return $this->cleanPhoneNumber($notifiable->phone_number);
        }

        return Str::of($configPhoneNumbers)->explode(',')->map(function ($phoneNumber) {
            return $this->cleanPhoneNumber($phoneNumber);
        });
    }
}
