<?php


namespace App\Http\Controllers;


use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    public function download(string $hash)
    {
        $attachment = Attachment::where(DB::raw('MD5(path)'), $hash)->first();
        abort_if(!$attachment, Response::HTTP_NOT_FOUND);;

        $file = public_path() . $attachment->pathToOriginal;

        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $basename = $attachment->name;

        header("Content-type: application/".$ext);
        header('Content-length: '.filesize($file));
        header("Content-Disposition: attachment; filename=\"$basename\"");
        readfile($file);
        exit;
    }
}