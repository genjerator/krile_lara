<?php

namespace App\Console\Commands;

use App\Jobs\SendCompanyEmail;
use App\Models\Company;
use App\Models\CompanyEmailSend;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SendCompanyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-to-companies
                            {template? : Template ID or name to use}
                            {--force : Resend even if already sent this template}
                            {--limit= : Limit number of emails}
                            {--dry-run : Preview without sending}
                            {--list-templates : Show available templates and exit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to companies using a template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Handle --list-templates option
        if ($this->option('list-templates')) {
            $this->listTemplates();
            return 0;
        }

        // Load template
        $template = $this->loadTemplate();

        if (!$template) {
            $this->error('No active email templates found. Please create one first.');
            return 1;
        }

        $this->info("Using template: {$template->name}");
        $this->info("Subject: {$template->subject}");
        $this->newLine();

        // Query companies
        $query = Company::whereNotNull('email');

        // Exclude companies that already received this template (unless --force)
        if (!$this->option('force')) {
            $query->whereDoesntHave('emailSends', function ($q) use ($template) {
                $q->where('email_template_id', $template->id)
                  ->where('status', 'sent');
            });
        }

        // Apply limit
        if ($limit = $this->option('limit')) {
            $query->limit((int)$limit);
        }

        $companies = $query->get();

        // Filter out invalid emails
        $validCompanies = $companies->filter(function ($company) {
            $validator = Validator::make(
                ['email' => $company->email],
                ['email' => 'email']
            );
            return !$validator->fails();
        });

        $skippedCount = $companies->count() - $validCompanies->count();
        $totalCompanies = Company::count();
        $alreadySentCount = 0;

        if (!$this->option('force')) {
            $alreadySentCount = Company::whereHas('emailSends', function ($q) use ($template) {
                $q->where('email_template_id', $template->id)
                  ->where('status', 'sent');
            })->count();
        }

        // Display statistics
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total companies', $totalCompanies],
                ['Companies with emails', $companies->count()],
                ['Valid email addresses', $validCompanies->count()],
                ['Invalid/skipped emails', $skippedCount],
                ['Already sent (this template)', $alreadySentCount],
                ['Will be sent', $validCompanies->count()],
            ]
        );

        if ($validCompanies->isEmpty()) {
            $this->warn('No companies to send emails to.');
            return 0;
        }

        // Dry run - just show what would happen
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN - No emails will be sent');
            $this->newLine();
            $this->info('Would send emails to:');
            foreach ($validCompanies->take(10) as $company) {
                $this->line("  - {$company->name} <{$company->email}>");
            }
            if ($validCompanies->count() > 10) {
                $this->line("  ... and " . ($validCompanies->count() - 10) . " more");
            }
            return 0;
        }

        // Ask for confirmation
        if (!$this->confirm("Send {$validCompanies->count()} emails using template '{$template->name}'?")) {
            $this->info('Cancelled.');
            return 0;
        }

        // Create email send records and dispatch jobs
        $this->info('Dispatching email jobs...');
        $bar = $this->output->createProgressBar($validCompanies->count());
        $bar->start();

        foreach ($validCompanies as $company) {
            // Create email send record
            $emailSend = CompanyEmailSend::create([
                'company_id' => $company->id,
                'email_template_id' => $template->id,
                'status' => 'pending',
                'attempts' => 0,
            ]);

            // Dispatch job
            SendCompanyEmail::dispatch($emailSend->id);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->info("✓ Dispatched {$validCompanies->count()} email jobs to queue");
        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Start the queue worker: php artisan queue:work --verbose');
        $this->line('  2. Monitor progress in the logs');
        $this->line('  3. Check failed jobs: php artisan queue:failed');
        $this->line('  4. Retry failed jobs: php artisan queue:retry all');

        return 0;
    }

    protected function listTemplates()
    {
        $templates = EmailTemplate::active()->get();

        if ($templates->isEmpty()) {
            $this->warn('No active email templates found.');
            return;
        }

        $this->info('Available email templates:');
        $this->newLine();

        $rows = $templates->map(function ($template) {
            return [
                $template->id,
                $template->name,
                $template->subject,
                $template->is_active ? '✓' : '✗',
            ];
        });

        $this->table(
            ['ID', 'Name', 'Subject', 'Active'],
            $rows
        );
    }

    protected function loadTemplate(): ?EmailTemplate
    {
        $templateArg = $this->argument('template');

        if ($templateArg) {
            // Try to load by name first
            $template = EmailTemplate::where('name', $templateArg)->first();

            // If not found by name, try by ID (if it looks like a UUID)
            if (!$template && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $templateArg)) {
                $template = EmailTemplate::find($templateArg);
            }

            if (!$template) {
                $this->error("Template not found: {$templateArg}");
                return null;
            }

            return $template;
        }

        // No template specified, use first active template
        return EmailTemplate::active()->first();
    }
}
