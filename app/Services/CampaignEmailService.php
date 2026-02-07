<?php

namespace App\Services;

use App\Enums\Status;
use App\Jobs\ProcessCampaignEmail;
use App\Models\Campaign;
use App\Models\EmailJob;

class CampaignEmailService
{
    /** 
     * @param array $requestData
     * @return array
     */
    public function saveCampaignEmailData(array $requestData): array
    {
         $recipientEmails = [];

        if (isset($requestData["recipient_emails"])) {
            $recipientEmails = $requestData["recipient_emails"];
            unset($requestData['email_recipients']);
        }

        $requestData['recipient_count'] = !empty($recipientEmails) ? count($recipientEmails) :0;

        try {            
            $campaign = Campaign::create($requestData);

            if ($campaign && !empty($recipientEmails)) {
                foreach ($recipientEmails as $recipientEmail) {
                    EmailJob::create([
                        'campaign_id' => $campaign->id,
                        'recipient_email' => $recipientEmail
                    ]);
                }
            }

            return [
                "status"=> true,
                "responseMessage" => [
                    "campaignID" => $campaign->id,
                ]
            ];

        } catch (\Exception $exception) {
            return [
                "status"=> false,
                "responseMessage" => [
                    "error" => $exception->getMessage(),
                ]
            ];
        }
    }

    /**
     * @return void
     */
    public function sendCampaignEmails(): void
    {
        $campaignStatuses = [Status::QUEUED, Status::FAILED];
        $campaigns = Campaign::whereIn('status', $campaignStatuses)
            ->orderByDesc('created_at')
            ->get();

        foreach ($campaigns as $campaign) {

            $campaign->status = Status::PROCESSING;
            $campaign->save();

            $jobStatuses = [Status::PENDING, Status::FAILED];
            $emailJobs = EmailJob::where('campaign_id', $campaign->id)
                ->whereIn('status', $jobStatuses)
                ->get();

            ProcessCampaignEmail::dispatch($campaign, $emailJobs);
        }
    }
}
