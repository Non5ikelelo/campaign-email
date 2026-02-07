<?php

namespace App\Console\Commands;

use App\Services\CampaignEmailService;
use Illuminate\Console\Command;

class SendCampaignEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-campaign-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send unsent campaign emails';

    /**
     * @param CampaignEmailService $campaignEmailService
     */
    public function __construct(protected CampaignEmailService $campaignEmailService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing campaign emails...');
        $this->campaignEmailService->sendCampaignEmails();
        $this->info('Campaign processing done.');
    }
}
