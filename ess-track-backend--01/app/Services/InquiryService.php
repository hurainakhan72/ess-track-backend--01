<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Services\MailService;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;

class InquiryService {
    private $inquiryModel;
    private $mailService;

    public function __construct($db) {
        $this->inquiryModel = new Inquiry($db);
        $this->mailService = new MailService();
    }

    public function submitInquiry($data) {
        if ($this->inquiryModel->checkSpam($data['phone'])) {
            throw new ValidationException(
                'Maximum 3 inquiries allowed per phone number. For further assistance, please call: 021-34330887-88',
                ['phone' => 'Maximum 3 inquiries allowed per phone number.']
            );
        }

        $id = $this->inquiryModel->create($data);
        if (!$id) {
            throw new DatabaseException('Error submitting inquiry.');
        }

        $this->mailService->sendInquiryNotification($data, $id);

        return $id;
    }
}
