<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Support\CmsContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class InquiryController extends Controller
{
    public function index(): Response
    {
        abort_unless(session('user.accounttype') === 'admin', 403);

        return response()->view('admin-inquiries', [
            'inquiries' => Inquiry::query()->orderByDesc('id')->simplePaginate(20),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): RedirectResponse
    {
        $inquiry = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Inquiry::create($inquiry);

        $recipient = CmsContent::get('brand.email') ?: config('cms.brand.email');
        $phone = $inquiry['phone'] ?? 'Not provided';
        $body = "Name: {$inquiry['name']}\nEmail: {$inquiry['email']}\nPhone: {$phone}\n\n{$inquiry['message']}";

        try {
            Mail::raw($body, function (Message $email) use ($recipient, $inquiry): void {
                $email->to($recipient)
                    ->replyTo($inquiry['email'])
                    ->subject('New Our Care website inquiry');
            });
        } catch (TransportExceptionInterface $exception) {
            report($exception);
        }

        return to_route('inquiry.create')
            ->with('inquiry_status', 'Thank you for contacting Our Care. We’ve received your inquiry.');
    }
}
