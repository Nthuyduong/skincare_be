<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailHelper
{

    public static function sendMailCc(string $emailFrom, string $nameFrom, string $mailReply, string $nameReply, string $emailTo, array $emailCc, string $subject, array $info, array $attachments = [])
    {
        try {
            Mail::send(["html" => $info["template"]], $info, function ($message) use ($nameFrom, $mailReply, $nameReply, $attachments, $emailCc, $subject, $emailTo, $emailFrom) {
                $message->from(trim($emailFrom), $nameFrom);
                $message->to(trim($emailTo))
                    ->replyTo(trim($mailReply), $nameReply)
                    ->subject($subject);
                if(count($emailCc) != 0 ){
                    foreach ($emailCc as $cc) {
                        if($cc != ''){
                            $message->cc(trim($cc), $name = null);
                        }
                    }
                }
                foreach ($attachments as $attachment) {
                    $message->attach($attachment['path'], [
                        'as' => data_get($attachment, 'name', basename($attachment['path'])),
                        'mime' => File::mimeType($attachment['path']),
                    ]);
                }
            });
            Log::info("Send Mail Success to " . $emailTo . " / with subject " . $subject);
            return true;
        } catch (\Exception $e) {
            Log::info("Send Mail Error");
            Log::info("Mail Info from: " . $emailFrom);
            Log::info("Mail Info to: " . $emailTo);
            Log::info("Subject: " . $subject);
            Log::error("Error: " . $e);
            return false;
        }
    }
}
