<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuccessfulEmail;
use Carbon\Carbon;
use DOMDocument;

class ProcessEmails extends Command
{
    protected $signature = 'emails:process';
    protected $description = 'Process email records that have not been processed yet.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $emails = SuccessfulEmail::whereNull('processed_at')->where('timestamp', '<', Carbon::now()->subHour()->timestamp)->get();

        foreach ($emails as $email) {
            $email->raw_text = $this->extractPlainText($email->email);
            $email->processed_at = Carbon::now();
            $email->save();
        }

        $this->info('Emails processed successfully.');
    }

    private function extractPlainText($html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $text = $dom->textContent;

        $text = preg_replace('/<br\s*\/?>/', "\n", $text); 
        $text = preg_replace('/<p[^>]*>/', "\n\n", $text); 
        $text = preg_replace('/[^\P{C}\n]/u', '', $text);

        return trim($text);
    }
}
