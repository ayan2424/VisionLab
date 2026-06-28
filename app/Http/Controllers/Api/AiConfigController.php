<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use Illuminate\Http\Request;

class AiConfigController extends Controller
{
    /**
     * Get the dynamic model configuration for the VisionLab Agent extension.
     */
    public function getModels(Request $request)
    {
        $providers = AiProvider::where('is_active', true)->with(['aiModels' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $modelsConfig = [];

        foreach ($providers as $provider) {
            foreach ($provider->aiModels as $model) {
                $modelsConfig[] = [
                    'title' => $model->title,
                    'model' => $model->model,
                    'provider' => $provider->name, // 'bedrock', 'openai', etc.
                    'apiKey' => $provider->api_key,
                    'apiBase' => $provider->api_base,
                ];
            }
        }

        // Return in the exact format the extension expects in its config.models array
        return response()->json([
            'models' => $modelsConfig
        ]);
    }
}
