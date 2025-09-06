<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Api;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Mail;

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
            'file' => 'nullable|file|max:10240', // 10MB
        ];
    }

    public function mount()
    {
        if (auth()->check()) {
            $this->user_id = auth()->id();
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }

        if (request()->has('test')) {
            $this->test = request()->get('test');
        }

        if (request()->has('result_id')) {
            $this->result_id = request()->get('result_id');
        }
    }

    public function submit()
    {
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

        if ($this->file) {
            $original   = $this->file->getClientOriginalName();
            $ext        = $this->file->getClientOriginalExtension();
            $storedName = $inquiry->id.'.'.$ext;

            $this->file->storeAs('inquiries', $storedName, 'local');

            $inquiry->update([
                'file_name' => $original,
                'file_path' => $storedName,
            ]);
        }

        $html = "<strong>Name:</strong> {$this->name}<br>
                <strong>Email:</strong> {$this->email}<br>
                <strong>Message:</strong><br>" . nl2br(e($this->description));

        try {
            Mail::send([], [], function ($message) use ($html, $inquiry) {
                $message->from('info@devteam-test.com', 'DevTeam Test');
                $message->to('devteam.200.ok@gmail.com', 'Daniel Ahn');
                $message->subject('새로운 문의가 도착했습니다');

                // ✅ HTML 본문 설정 (setBody 대신 html 사용)
                $message->html($html);

                // 선택: 텍스트 대체 본문도 추가
                $message->text(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html)));

                if (!empty($inquiry->file_path)) {
                    $message->attach(
                        storage_path('app/inquiries/'.$inquiry->file_path),
                        ['as' => $inquiry->file_name]
                    );
                }
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', '메일 전송에 실패했습니다.');
            return;
        }

        $this->reset(['name','email','description','file']);
        session()->flash('success', '문의 내용이 성공적으로 제출되었습니다.');
    }

    public function render()
    {
        return view('livewire.home-request')
            ->layout('layouts.app');
    }
}