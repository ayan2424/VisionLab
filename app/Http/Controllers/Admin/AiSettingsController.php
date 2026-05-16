<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\AiSetting;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    /**
     * Display the AI settings management page.
     */
    public function index()
    {
        $apiKeys = AiSetting::getByCategory('api_keys');
        $features = AiSetting::getByCategory('features');
        $limits = AiSetting::getByCategory('limits');
        $models = AiModel::orderBy('role')->orderBy('sort_order')->get();

        // Group models by role for display
        $modelsByRole = $models->groupBy('role');

        return view('admin.ai-settings', compact('apiKeys', 'features', 'limits', 'models', 'modelsByRole'));
    }

    /**
     * Update API keys.
     */
    public function updateKeys(Request $request)
    {
        $keys = $request->input('keys', []);
        
        foreach ($keys as $key => $value) {
            if ($value !== null && $value !== '••••••••') {
                AiSetting::setValue($key, $value, 'api_keys');
            }
        }

        return back()->with('success', 'API keys updated successfully.');
    }

    /**
     * Update feature flags.
     */
    public function updateFeatures(Request $request)
    {
        $features = ['autocomplete_enabled', 'indexing_enabled', 'telemetry_enabled', 'allow_marketplace'];
        
        foreach ($features as $feature) {
            AiSetting::setValue(
                $feature,
                $request->has($feature) ? 'true' : 'false',
                'features'
            );
        }

        // Update limits
        if ($request->filled('max_tokens_per_request')) {
            AiSetting::setValue(
                'max_tokens_per_request',
                $request->input('max_tokens_per_request'),
                'limits',
                'Max tokens per AI request'
            );
        }

        return back()->with('success', 'Feature settings updated.');
    }

    /**
     * Toggle a model's active state.
     */
    public function toggleModel(AiModel $model)
    {
        $model->update(['is_active' => !$model->is_active]);

        return back()->with('success', "{$model->display_name} " . ($model->is_active ? 'enabled' : 'disabled') . '.');
    }

    /**
     * Set a model as default for its role.
     */
    public function setDefault(AiModel $model)
    {
        $model->makeDefault();

        return back()->with('success', "{$model->display_name} set as default for {$model->role}.");
    }

    /**
     * Add a new AI model.
     */
    public function storeModel(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:50',
            'model_id' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'role' => 'required|in:chat,autocomplete,agent,edit',
            'context_length' => 'nullable|integer|min:1024',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['context_length'] = $validated['context_length'] ?? 128000;
        $validated['sort_order'] = AiModel::where('role', $validated['role'])->max('sort_order') + 1;

        AiModel::create($validated);

        return back()->with('success', "Model {$validated['display_name']} added.");
    }

    /**
     * Delete an AI model.
     */
    public function destroyModel(AiModel $model)
    {
        $name = $model->display_name;
        $model->delete();

        return back()->with('success', "Model {$name} removed.");
    }

    /**
     * Test an API key connection.
     */
    public function testConnection(Request $request)
    {
        $provider = $request->input('provider');
        $apiKey = AiSetting::getValue("{$provider}_api_key");

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'No API key set for this provider.']);
        }

        try {
            $result = match ($provider) {
                'anthropic' => $this->testAnthropic($apiKey),
                'google' => $this->testGoogle($apiKey),
                'openai' => $this->testOpenAI($apiKey),
                'deepseek' => $this->testDeepSeek($apiKey),
                default => ['success' => false, 'message' => 'Unknown provider.'],
            };

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function testAnthropic(string $apiKey): array
    {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-5-haiku-20241022',
            'max_tokens' => 10,
            'messages' => [['role' => 'user', 'content' => 'Hi']],
        ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'Anthropic API connected!']
            : ['success' => false, 'message' => 'Failed: ' . $response->json('error.message', 'Unknown error')];
    }

    private function testGoogle(string $apiKey): array
    {
        $response = \Illuminate\Support\Facades\Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            ['contents' => [['parts' => [['text' => 'Hi']]]]]
        );

        return $response->successful()
            ? ['success' => true, 'message' => 'Google Gemini API connected!']
            : ['success' => false, 'message' => 'Failed: ' . $response->json('error.message', 'Unknown error')];
    }

    private function testOpenAI(string $apiKey): array
    {
        $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4.1-mini',
                'max_tokens' => 10,
                'messages' => [['role' => 'user', 'content' => 'Hi']],
            ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'OpenAI API connected!']
            : ['success' => false, 'message' => 'Failed: ' . $response->json('error.message', 'Unknown error')];
    }

    private function testDeepSeek(string $apiKey): array
    {
        $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
            ->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'max_tokens' => 10,
                'messages' => [['role' => 'user', 'content' => 'Hi']],
            ]);

        return $response->successful()
            ? ['success' => true, 'message' => 'DeepSeek API connected!']
            : ['success' => false, 'message' => 'Failed: ' . $response->json('error.message', 'Unknown error')];
    }
}
