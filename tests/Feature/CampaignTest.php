<?php

namespace Tests\Feature;

use App\Jobs\ProcessCampaignEmail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Campaign;
use App\Models\EmailJob;
use Illuminate\Support\Facades\Queue;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function testValidCampaignRequest(): void
    {
        $response = $this->postJson('/api/campaign', [
            "name" => "Spring Sale 2",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1@test.com", "email2@test.com"]
        ]);

        $response->assertStatus(200);
    }

    public function testInvalidCampaignRequest(): void
    {
        $response = $this->postJson('/api/campaign', [
            "name" => "Spring Sale 2",
            "subject" => "50% Off This Weekend!",
            "recipient_emails" => ["email1@test.com", "email2@test.com"]
        ]);

        $response->assertStatus(422);
    }

    public function testInvalidEmail()
    {
        $response = $this->postJson('/api/campaign', [
            "name" => "Spring Sale 2",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1test.com"]
        ]);

        $response->assertStatus(422);
    }

    public function testDuplicateEmails()
    {
        $response = $this->postJson('/api/campaign', [
            "name" => "Spring Sale 2",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1@test.com", "email1@test.com"]
        ]);

        $response->assertStatus(422);
    }

        public function testCampaignSaveToDB(): void
    {
        Campaign::truncate();

        $response = $this->postJson('/api/campaign', [
            "name" => "Test Campaign",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1@test.com", "email2@test.com"]
        ]);

        $campaign = Campaign::first();


        $this->assertIsObject($campaign);
        $this->assertEquals("Test Campaign", $campaign->name);
    }

    public function testEmailJobsSavedToDB(): void
    {
        EmailJob::truncate();

        $response = $this->postJson('/api/campaign', [
            "name" => "Test Campaign",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1@test.com", "email2@test.com"]
        ]);

        $jobs = EmailJob::all();


        $this->assertEquals(2, count($jobs));
    }

    public function testEmailJobsQueued(): void
    {
        Queue::fake();
        EmailJob::truncate();
        Campaign::truncate();

        $response = $this->postJson('/api/campaign', [
            "name" => "Test Campaign",
            "subject" => "50% Off This Weekend!",
            "body" => "Check out our amazing deals...",
            "recipient_emails" => ["email1@test.com", "email2@test.com"]
        ]);

        $this->artisan("app:send-campaign-emails")
            ->expectsOutput("Processing campaign emails...")
            ->expectsOutput("Campaign processing done.")
            ->assertSuccessful();

        Queue::assertPushed(ProcessCampaignEmail::class);
    }
}
