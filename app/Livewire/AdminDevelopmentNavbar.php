<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDevelopmentNavbar extends Component
{
    public $url;
    public $title;

    public function generate()
    {
        $this->generateNavbarMenu($this->title, $this->url);
        $this->generatePage($this->title, $this->url);
        $this->generateLivewire($this->title, $this->url);
        $this->generateRoute($this->url);
    }

    public function generateNavbarMenu($title, $url)
    {
        $menu = '                            <li class="nav-item">
                                <a class="nav-link" href="{{ url("/") }}/'.$url.'">
                                    <span class="nav-link-title fw-bold text-dark">'.$title.'</span>
                                </a>
                            </li>';
        $file = base_path('resources/views/inc/app_navbar.blade.php');
        $contents = file_get_contents($file);
        $contents = str_replace("                            {{-- Auto Generated Menu --}}", $menu . "\n                            {{-- Auto Generated Menu --}}", $contents);
        file_put_contents($file, $contents);
    }

    public function generatePage($title, $url)
    {
        $page = "@section('title')";
        $page .= "\n    @include('inc.component.seo')";
        $page .= "\n@endsection";
        $page .= "\n@section('css')";
        $page .= "\n@endsection";
        $page .= "\n<div class=\"page-body px-xl-3\">";
        $page .= "\n    <div class=\"container-xl\">";
        $page .= "\n        @include('inc.component.message')";
        $page .= "\n        <div class=\"row\">";
        $page .= "\n            <div class=\"col-xl-6 mb-2\">";
        $page .= "\n                <div class=\"card\">";
        $page .= "\n                    <div class=\"card-body\">";
        $page .= "\n                        <h4 class=\"card-title\">".ucfirst($url)."</h4>";
        $page .= "\n                    </div>";
        $page .= "\n                </div>";
        $page .= "\n            </div>";
        $page .= "\n        </div>";
        $page .= "\n    </div>";
        $page .= "\n</div>";
        $page .= "\n@section('js')";
        $page .= "\n@endsection";        

        $file = resource_path('views/livewire/home-'.strtolower($url).'.blade.php');
        file_put_contents($file, $page);
    }

    public function generateLivewire($title, $url)
    {
        $controller = "<?php";
        $controller .= "\n\nnamespace App\Livewire;";
        $controller .= "\n\nuse Livewire\Component;";
        $controller .= "\n\nclass Home".ucfirst($url)." extends Component";
        $controller .= "\n{";
        $controller .= "\n    public function render()";
        $controller .= "\n    {";
        $controller .= "\n        return view('livewire.home-".strtolower($url)."')";
        $controller .= "\n        ->layout('layouts.app');";
        $controller .= "\n    }";
        $controller .= "\n}";

        $file = app_path('Livewire/Home'.ucfirst($url).'.php');
        file_put_contents($file, $controller);
    }

    public function generateRoute($url)
    {
        $route = "Route::get('/".$url."', Home".ucfirst($url)."::class);";
        $file = base_path('routes/web.php');
        $contents = file_get_contents($file);
        $contents = str_replace("/* Auto-HomeMenu */", $route . "\n/* Auto-HomeMenu */", $contents);
        file_put_contents($file, $contents);

        $controller = "use App\Livewire\Home" .ucfirst($url) . ";";
        $contents = file_get_contents($file);
        $contents = str_replace("/* Auto-HomeController */", $controller . "\n/* Auto-HomeController */", $contents);
        file_put_contents($file, $contents);
    }

    public function render()
    {
        return view('livewire.admin-development-navbar')
            ->layout('layouts.admin');
    }
}
