<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
class CourseExtensionController extends Controller
{
    public function update(Request $request, \App\Models\Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'extensions' => 'array',
            'extensions.*.id' => 'required|exists:extensions,id',
            'extensions.*.is_required' => 'boolean'
        ]);

        $course->extensions()->delete(); // Clear old course_extensions

        if (!empty($validated['extensions'])) {
            foreach ($validated['extensions'] as $extData) {
                \App\Models\CourseExtension::create([
                    'course_id' => $course->id,
                    'extension_id' => $extData['id'],
                    'is_required' => $extData['is_required'] ?? false,
                ]);
            }
        }

        // Now, update WorkspaceExtensions for all workspaces under this course
        $workspaces = $course->workspaces;
        foreach ($workspaces as $workspace) {
            // First, get all global extensions
            $globalExtensions = \App\Models\Extension::where('is_global', true)->where('is_active', true)->pluck('id')->toArray();
            $courseExtensions = \App\Models\CourseExtension::where('course_id', $course->id)->pluck('extension_id')->toArray();

            $activeExtensionIds = array_unique(array_merge($globalExtensions, $courseExtensions));

            // Mark existing workspace extensions that are no longer active as disabled
            \App\Models\WorkspaceExtension::where('workspace_id', $workspace->id)
                ->whereNotIn('extension_id', $activeExtensionIds)
                ->update(['is_enabled' => false, 'sync_status' => 'pending']);

            // Create or update active ones
            foreach ($activeExtensionIds as $extId) {
                \App\Models\WorkspaceExtension::updateOrCreate(
                    ['workspace_id' => $workspace->id, 'extension_id' => $extId],
                    ['is_enabled' => true, 'sync_status' => 'pending']
                );
            }

            // Dispatch sync job
            \App\Jobs\SyncWorkspaceExtensions::dispatch($workspace->id);
        }

        return back()->with('success', 'Marketplace policy updated and synchronizing to active workspaces.');
    }
}
