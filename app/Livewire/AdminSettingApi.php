<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;

class AdminSettingApi extends Component
{
    public $sendgrid_key;
    public $openai_key;
    public $paypal_mode;
    public $paypal_client_id_live;
    public $paypal_secret_live;
    public $paypal_client_id_sandbox;
    public $paypal_secret_sandbox;
    public $toss_mode;
    public $toss_client_key_test;
    public $toss_secret_key_test;
    public $toss_client_key;
    public $toss_secret_key;

    public function mount()
    {
        $api = Api::first();

        if (!$api) {
            $api = new Api();
            $api->save();
        }
        
        if ($api) {
            $this->sendgrid_key = $api->sendgrid_key;
            $this->openai_key = $api->openai_key;
            $this->paypal_mode = $api->paypal_mode;
            $this->paypal_client_id_live = $api->paypal_client_id_live;
            $this->paypal_secret_live = $api->paypal_secret_live;
            $this->paypal_client_id_sandbox = $api->paypal_client_id_sandbox;
            $this->paypal_secret_sandbox = $api->paypal_secret_sandbox;
            $this->toss_mode = $api->toss_mode;
            $this->toss_client_key_test = $api->toss_client_key_test;
            $this->toss_secret_key_test = $api->toss_secret_key_test;
            $this->toss_client_key = $api->toss_client_key;
            $this->toss_secret_key = $api->toss_secret_key;
        }
    }

    public function update()
    {
        $api = Api::first();
        $api->sendgrid_key = $this->sendgrid_key;
        $api->openai_key = $this->openai_key;
        $api->paypal_mode = $this->paypal_mode;
        $api->paypal_client_id_live = $this->paypal_client_id_live;
        $api->paypal_secret_live = $this->paypal_secret_live;
        $api->paypal_client_id_sandbox = $this->paypal_client_id_sandbox;
        $api->paypal_secret_sandbox = $this->paypal_secret_sandbox;
        $api->toss_mode = $this->toss_mode;
        $api->toss_client_key_test = $this->toss_client_key_test;
        $api->toss_secret_key_test = $this->toss_secret_key_test;
        $api->toss_client_key = $this->toss_client_key;
        $api->toss_secret_key = $this->toss_secret_key;
        $api->save();

        session()->flash('message', 'API settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-api')
        ->layout('layouts.admin');
    }
}
