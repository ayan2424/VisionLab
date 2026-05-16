@extends('layouts.admin')

@section('title', 'AI Settings - VisionLab Admin')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">

    <div class="flex justify-between items-center border-b border-gray-800 pb-5">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">AI Settings & Ecosystem</h2>
            <p class="mt-1 text-sm text-gray-400">Manage API keys, active models, and AI features across all workspaces.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-400">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: API Keys & Features -->
        <div class="space-y-8 lg:col-span-1">
            
            <!-- API Keys Card -->
            <div class="bg-[#111111] border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#161616]">
                    <h3 class="text-base font-semibold leading-6 text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        Provider API Keys
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.ai-settings.keys') }}" method="POST" class="space-y-4">
                        @csrf
                        @foreach($apiKeys as $key)
                            @php $provider = explode('_', $key->key)[0]; @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">{{ $key->description }}</label>
                                <div class="flex rounded-md shadow-sm">
                                    <input type="password" name="keys[{{ $key->key }}]" 
                                        placeholder="{{ $key->value ? '••••••••••••••••••••••••' : 'Enter API key' }}"
                                        class="flex-1 min-w-0 block w-full rounded-none rounded-l-md bg-black border-gray-800 text-gray-300 focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                                    <button type="button" onclick="testConnection('{{ $provider }}')"
                                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-800 bg-[#1a1a1a] text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                                        Test
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <div class="pt-4">
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 focus:ring-offset-[#111]">
                                Save Keys
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Global Features Card -->
            <div class="bg-[#111111] border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#161616]">
                    <h3 class="text-base font-semibold leading-6 text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Global Config
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.ai-settings.features') }}" method="POST" class="space-y-5">
                        @csrf
                        @foreach($features as $feature)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-200">{{ $feature->description }}</h4>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $feature->key }}" value="1" class="sr-only peer" {{ $feature->value === 'true' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                                </label>
                            </div>
                        @endforeach
                        
                        <div class="pt-4 border-t border-gray-800">
                            @php $tokenLimit = $limits->firstWhere('key', 'max_tokens_per_request'); @endphp
                            <label class="block text-sm font-medium text-gray-300 mb-1">Max Tokens per Request</label>
                            <input type="number" name="max_tokens_per_request" value="{{ $tokenLimit->value ?? 4096 }}" 
                                class="block w-full rounded-md bg-black border-gray-800 text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-[#1a1a1a] hover:bg-gray-800 focus:outline-none">
                                Apply Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: AI Models -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-[#111111] border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#161616] flex justify-between items-center">
                    <h3 class="text-base font-semibold leading-6 text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Model Catalog
                    </h3>
                    <button onclick="document.getElementById('add-model-modal').classList.remove('hidden')" class="text-sm bg-violet-600 hover:bg-violet-700 text-white px-3 py-1.5 rounded-md transition-colors">
                        + Add Model
                    </button>
                </div>
                
                <div class="divide-y divide-gray-800">
                    @foreach(['chat', 'autocomplete', 'agent', 'edit'] as $role)
                        @if(isset($modelsByRole[$role]) && $modelsByRole[$role]->count() > 0)
                            <div class="p-6">
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center">
                                    {{ ucfirst($role) }} Models
                                    @if($role === 'autocomplete')
                                        <span class="ml-2 px-2 py-0.5 rounded text-xs bg-yellow-500/10 text-yellow-500 border border-yellow-500/20">Requires high speed</span>
                                    @endif
                                </h4>
                                <div class="space-y-3">
                                    @foreach($modelsByRole[$role] as $model)
                                        <div class="flex items-center justify-between p-3 rounded-lg border {{ $model->is_active ? ($model->is_default ? 'bg-violet-900/10 border-violet-500/30' : 'bg-[#161616] border-gray-800') : 'bg-black opacity-50 border-gray-900' }} transition-colors">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if($model->provider === 'google')
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-500/10"><svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg></span>
                                                    @elseif($model->provider === 'anthropic')
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-amber-500/10"><svg class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2zm0 6l6.5 12h-13L12 8z"/></svg></span>
                                                    @elseif($model->provider === 'openai')
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-emerald-500/10"><svg class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-13h4v8h-4z"/></svg></span>
                                                    @else
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-gray-500/10"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-white">
                                                        {{ $model->display_name }}
                                                        @if($model->is_default)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-violet-500/20 text-violet-400">Default</span>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $model->model_id }} • {{ number_format($model->context_length) }} ctx</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <!-- Make Default -->
                                                @if(!$model->is_default && $model->is_active)
                                                    <form action="{{ route('admin.ai-settings.models.default', $model) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" title="Make Default" class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-800 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Toggle Active -->
                                                <form action="{{ route('admin.ai-settings.models.toggle', $model) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="{{ $model->is_active ? 'Disable' : 'Enable' }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-800 rounded transition-colors">
                                                        @if($model->is_active)
                                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                                        @endif
                                                    </button>
                                                </form>

                                                <!-- Delete -->
                                                @if(!$model->is_default)
                                                    <form action="{{ route('admin.ai-settings.models.destroy', $model) }}" method="POST" onsubmit="return confirm('Remove this model?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" title="Delete" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-gray-800 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Model Modal -->
<div id="add-model-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('add-model-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-[#111] border border-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-800">
                <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">Add New AI Model</h3>
            </div>
            <form action="{{ route('admin.ai-settings.models.store') }}" method="POST">
                @csrf
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Provider</label>
                        <select name="provider" class="mt-1 block w-full bg-black border border-gray-800 rounded-md text-white shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                            <option value="anthropic">Anthropic</option>
                            <option value="google">Google (Gemini)</option>
                            <option value="openai">OpenAI</option>
                            <option value="deepseek">DeepSeek</option>
                            <option value="openrouter">OpenRouter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Model ID <span class="text-xs text-gray-500">(e.g., claude-3-5-sonnet-20241022)</span></label>
                        <input type="text" name="model_id" required class="mt-1 block w-full bg-black border border-gray-800 rounded-md text-white shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Display Name</label>
                        <input type="text" name="display_name" required class="mt-1 block w-full bg-black border border-gray-800 rounded-md text-white shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Role</label>
                            <select name="role" class="mt-1 block w-full bg-black border border-gray-800 rounded-md text-white shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                                <option value="chat">Chat</option>
                                <option value="autocomplete">Autocomplete</option>
                                <option value="agent">Agent (Autonomous)</option>
                                <option value="edit">Edit (Inline)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Context Length</label>
                            <input type="number" name="context_length" value="128000" class="mt-1 block w-full bg-black border border-gray-800 rounded-md text-white shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-[#161616] sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-800">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Add Model
                    </button>
                    <button type="button" onclick="document.getElementById('add-model-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-700 shadow-sm px-4 py-2 bg-black text-base font-medium text-gray-300 hover:bg-gray-900 hover:text-white focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function testConnection(provider) {
    const btn = event.currentTarget;
    const originalText = btn.innerText;
    btn.innerText = 'Testing...';
    btn.disabled = true;

    fetch('{{ route("admin.ai-settings.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ provider })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => {
        alert('Connection failed: ' + err.message);
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection
