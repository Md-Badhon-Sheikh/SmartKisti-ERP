<?php

namespace App\Services\Sms;

use Illuminate\Support\Manager;

class SmsManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('services.sms.driver', 'log');
    }

    protected function createLogDriver(): LogSmsDriver
    {
        return new LogSmsDriver;
    }
}
