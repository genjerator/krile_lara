<?php

namespace App\Jobs;

use App\Mail\CompanyContactMail;
use App\Models\Company;
use App\Models\CompanyEmailSend;
use App\Models\EmailTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCompanyEmail implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $companyEmailSendId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailSend = CompanyEmailSend::with(['company', 'emailTemplate'])->findOrFail($this->companyEmailSendId);
        $company = $emailSend->company;
        $template = $emailSend->emailTemplate;

        // Increment attempts
        $emailSend->incrementAttempts();

        try {
            // Validate company has email
            if (empty($company->email)) {
                throw new \Exception('Company does not have an email address');
            }

            // Validate template is active
            if (!$template->is_active) {
                throw new \Exception('Email template is not active');
            }

            // Send the email
            Mail::to($company->email)->send(new CompanyContactMail($company, $template));

            // Mark as sent
            $emailSend->markAsSent();

            Log::info("Email sent successfully to {$company->email} using template {$template->name}");

        } catch (\Exception $e) {
            // Mark as failed
            $emailSend->markAsFailed($e->getMessage());

            Log::error("Failed to send email to {$company->email}: " . $e->getMessage());

            // Re-throw to let Laravel handle retry logic
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $emailSend = CompanyEmailSend::find($this->companyEmailSendId);

        if ($emailSend) {
            $emailSend->markAsFailed($exception->getMessage());
            Log::error("Job failed permanently for email send {$this->companyEmailSendId}: " . $exception->getMessage());
        }
    }
}
