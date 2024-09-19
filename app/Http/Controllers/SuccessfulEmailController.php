<?php

namespace App\Http\Controllers;

use App\Models\SuccessfulEmail;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\Request;

class SuccessfulEmailController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'affiliate_id' => 'required|integer',
            'envelope' => 'required|string',
            'from' => 'required|string',
            'subject' => 'required|string',
            'dkim' => 'nullable|string',
            'SPF' => 'nullable|string',
            'spam_score' => 'nullable|numeric',
            'email' => 'required|string',
            'raw_text' => 'required|string',
            'sender_ip' => 'nullable|string',
            'to' => 'required|string',
            'timestamp' => 'required|integer',
        ]);

        $validatedData['raw_text'] = $this->extractPlainText($validatedData['email']);
        $validatedData['processed_at'] = Carbon::now();

        $successfulEmail = SuccessfulEmail::create($validatedData);
        return response()->json($successfulEmail, 201);
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
    
    public function showAll()
    {
        $email = SuccessfulEmail::all();
        return response()->json($email);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'affiliate_id' => 'required|integer',
            'envelope' => 'required|string',
            'from' => 'required|string',
            'subject' => 'required|string',
            'dkim' => 'nullable|string',
            'SPF' => 'nullable|string',
            'spam_score' => 'nullable|numeric',
            'email' => 'required|string',
            'raw_text' => 'required|string',
            'sender_ip' => 'nullable|string',
            'to' => 'required|string',
            'timestamp' => 'required|integer',
        ]);

        $validatedData['raw_text'] = $this->extractPlainText($validatedData['email']);

        $email = SuccessfulEmail::findOrFail($id);
        if (empty($email)) {
            return response()->json('E-mail not found!');
        }
        $email->update($validatedData);

        return response()->json($email);
    }

    public function index()
    {
        $emails = SuccessfulEmail::all();
        return response()->json($emails);
    }

    public function destroy($id)
    {
        $email = SuccessfulEmail::findOrFail($id);
        $email->delete();

        return response()->json(['message' => 'Email deleted successfully']);
    }

}
