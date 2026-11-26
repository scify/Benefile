<?php

namespace App\Http\Controllers;

use App\Models\MedicalUpload;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a file of a medical visit to a logged-in user with an active account.
 *
 * The files are in the private storage (storage/app/private/medical-uploads),
 * never in public/: nobody can open them by their address.
 */
class MedicalUploadController extends Controller
{
    public function show(int $upload): StreamedResponse
    {
        $file = MedicalUpload::findOrFail($upload);
        $path = (string) $file->path;

        // Files of an installation before this change: their path is the old public folder.
        // Move them to storage/app/private/medical-uploads/ (see the README).
        if (str_starts_with($path, '/uploads/')) {
            $path = 'medical-uploads/'.basename((string) $file->title);
        }

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, (string) $file->title);
    }
}
