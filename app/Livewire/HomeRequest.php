<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HomeRequest extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $description;
    public $file;
    public $user_id = 0;
    public $test = '';
    public $result_id = 0;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:10240', // MIME 타입 명시
        ];
    }

    public function submit()
    {
        try {
            $this->validate();

            $inquiry = Inquiry::create([
                'user_id'    => $this->user_id,
                'test'       => $this->test,
                'result_id'  => $this->result_id,
                'name'       => $this->name,
                'email'      => $this->email,
                'description'=> $this->description,
                'status'     => 'pending',
            ]);

            // 파일 처리 개선
            if ($this->file) {
                $original = $this->file->getClientOriginalName();
                $ext = $this->file->getClientOriginalExtension();
                $storedName = $inquiry->id . '_' . time() . '.' . $ext;

                // Storage 파사드 사용
                $path = $this->file->storeAs('inquiries', $storedName, 'local');

                $inquiry->update([
                    'file_name' => $original,
                    'file_path' => $storedName,
                ]);
            }

            $this->sendEmail($inquiry);

            $this->reset(['name','email','description','file']);
            session()->flash('success', 'Your inquiry has been submitted successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error occurred while submitting inquiry: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your inquiry: ' . $e->getMessage());
        }
    }

    private function sendEmail($inquiry)
    {
        $html = "<strong>Name:</strong> {$this->name}<br>
                <strong>Email:</strong> {$this->email}<br>
                <strong>Message:</strong><br>" . nl2br(e($this->description));

        Mail::send([], [], function ($message) use ($html, $inquiry) {
            $message->from('info@web-psqc.com', 'Web PSQC');
            $message->to('devteam.200.ok@gmail.com', 'Daniel Ahn');
            $message->subject('New Inquiry from ' . $this->name);
            $message->html($html);

            if (!empty($inquiry->file_path)) {
                $filePath = storage_path('app/inquiries/' . $inquiry->file_path);
                if (file_exists($filePath)) {
                    $message->attach($filePath, ['as' => $inquiry->file_name]);
                }
            }
        });
    }

    public function render()
    {
        return view('livewire.home-request')->layout('layouts.app');
    }
}