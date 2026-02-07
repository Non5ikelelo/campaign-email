<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Services\CampaignEmailService;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    public function __construct(protected CampaignEmailService $campaignEmailService)
    {
        
    }

    /**
     * Save campaign emails to the database
     * 
     * @param CampaignRequest $request
     * @return Response
     */
    public function store(CampaignRequest $request): Response
    {
        $body = $request->all();
        $result = $this->campaignEmailService->saveCampaignEmailData($body);
        $responseStatus = $result["status"] ? 
            Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;
            
        return response()->json($result["responseMessage"], $responseStatus);
    }
}
