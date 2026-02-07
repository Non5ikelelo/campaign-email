<?php

namespace App\Jobs;

use App\Enums\Status;
use App\Mail\CampaignEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;

class ProcessCampaignEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     * 
     * @param Campaign $campaign
     * @param Collection $emailJobs
     */
    public function __construct(
        private Campaign $campaign, 
        private Collection $emailJobs
    ) {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $errors = [];
        foreach ($this->emailJobs as $emailJob) {
            try {
                Log::info("Sending campaign email to: " . $emailJob->recipient_email);

                Mail::to($emailJob->recipient_email)->queue(
                    new CampaignEmail($this->campaign)
                );

                $emailJob->status = Status::SENT;
                $emailJob->save();

                Log::info('Email sent to: '. $emailJob->recipient_email);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                $emailJob->status = Status::FAILED;
                $emailJob->save();

                $errors[] = $emailJob->recipient_email;
            }
            
        }

        if (count($errors) > 0) {

            Log::info(sprintf(
                '%s out of %s emails sent. The following emails failed to send: ', 
                count($errors), 
                $this->campaign->recipient_count
                ), ['failed_emails' => $errors]);

            $status = Status::FAILED;
        } else {

            Log::info(sprintf('%d out of %d Campaign emails sent.', 
                $this->campaign->recipient_count, $this->campaign->recipient_count
                )
            );
            $status = Status::DONE;
        }

        $this->campaign->status = $status;
        $this->campaign->save();
    }
}
