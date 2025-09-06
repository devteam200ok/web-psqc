<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;

class AdminDevelopmentMenu extends Component
{
    public $menu_id = 0;
    public $isGroup = false;
    public $icon = '';
    public $title = '';
    public $role = '';
    public $url = '';

    public $child_id = 0;
    public $child_title = '';
    public $child_url = '';

    public $parent_id = 0;

    protected $listeners = [
        'setIcon' => 'setIcon'
    ];

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function selectParent($id)
    {
        $this->parent_id = $id;
    }

    public function editParent($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $this->menu_id = $menu->id;
            $this->icon = $menu->icon;
            $this->title = $menu->title;
            $this->role = $menu->role;
            $this->url = $menu->url;
            $this->isGroup = ($menu->type == 'group');
        } else {
            session()->flash('error', __('Menu not found.'));
        }
    }

    public function editChild($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $this->child_id = $menu->id;
            $this->child_title = $menu->title;
            $this->child_url = $menu->url;
        } else {
            session()->flash('error', __('Menu not found.'));
        }
    }

    public function resetParent()
    {
        $this->menu_id = 0;
        $this->icon = '';
        $this->title = '';
        $this->role = '';
        $this->url = '';
        $this->isGroup = false;
    }

    public function resetChild()
    {
        $this->child_id = 0;
        $this->child_title = '';
        $this->child_url = '';
    }

    public function storeParent()
    {
        if (empty($this->icon) || empty($this->title) || empty($this->role) || empty($this->url)) {
            session()->flash('error', __('Please fill all fields.'));

            return;
        }
        $order = Menu::where('menu_id', 0)->max('order') + 1;

        if ($this->menu_id > 0) {
            $menu = Menu::find($this->menu_id);
            if ($menu) {
                $menu->update([
                    'icon' => $this->icon,
                    'title' => $this->title,
                    'role' => $this->role,
                    'url' => $this->url,
                ]);
                $this->resetParent();
                session()->flash('message', __('Menu updated successfully.'));
            }
        } else {
            $menu = new Menu();
            $menu->menu_id = 0;
            $menu->order = $order;
            $menu->type = $this->isGroup ? 'group' : 'menu';
            $menu->icon = $this->icon;
            $menu->title = $this->title;
            $menu->role = $this->role;
            $menu->url = $this->url;
            $menu->save();

            if (!$this->isGroup) {
                $this->generate_parent_route($menu->id);
                $this->generate_parent_controller($menu->id);
                $this->generate_parent_page($menu->id);
            }
            
            $this->resetParent();
            session()->flash('message', __('Menu created successfully.'));
        }
    }

    public function storeChild()
    {
        if (empty($this->child_title) || empty($this->child_url)) {
            session()->flash('error', __('Please fill all fields.'));

            return;
        }
        $order = Menu::where('menu_id', $this->parent_id)->max('order') + 1;

        if ($this->child_id > 0) {
            $menu = Menu::find($this->child_id);
            if ($menu) {
                $menu->update([
                    'title' => $this->child_title,
                    'url' => $this->child_url,
                ]);
                $this->resetChild();
                session()->flash('message', __('Menu updated successfully.'));
            }
        } else {
            $menu = new Menu();
            $menu->menu_id = $this->parent_id;
            $menu->order = $order;
            $menu->type = 'menu';
            $menu->icon = '';
            $menu->title = $this->child_title;
            $menu->role = '';
            $menu->url = $this->child_url;
            $menu->save();

            $this->generate_child_route($menu->id);
            $this->generate_child_controller($menu->id);
            $this->generate_child_page($menu->id);

            $this->resetChild();
            session()->flash('message', __('Menu created successfully.'));
        }
    }

    public function upParent($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $prev = Menu::where('order', '<', $menu->order)
                ->where('menu_id', 0)
                ->orderBy('order', 'desc')
                ->first();
            if ($prev) {
                $prevOrder = $prev->order;
                $prev->update(['order' => $menu->order]);
                $menu->update(['order' => $prevOrder]);
            }
        }
    }

    public function downParent($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $next = Menu::where('order', '>', $menu->order)
                ->where('menu_id', 0)
                ->orderBy('order')
                ->first();
            if ($next) {
                $nextOrder = $next->order;
                $next->update(['order' => $menu->order]);
                $menu->update(['order' => $nextOrder]);
            }
        }
    }

    public function upChild($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $prev = Menu::where('order', '<', $menu->order)
                ->where('menu_id', $this->parent_id)
                ->orderBy('order', 'desc')
                ->first();
            if ($prev) {
                $prevOrder = $prev->order;
                $prev->update(['order' => $menu->order]);
                $menu->update(['order' => $prevOrder]);
            }
        }
    }

    public function downChild($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $next = Menu::where('order', '>', $menu->order)
                ->where('menu_id', $this->parent_id)
                ->orderBy('order')
                ->first();
            if ($next) {
                $nextOrder = $next->order;
                $next->update(['order' => $menu->order]);
                $menu->update(['order' => $nextOrder]);
            }
        }
    }

    public function deleteMenu($id)
    {
        $menu = Menu::find($id);
        if ($menu) {

            if($menu->type == 'group') {
                Menu::where('menu_id', $menu->id)->delete();
                $this->parent_id = 0;
            }

            $menu->delete();
            session()->flash('message', __('Menu deleted successfully.'));
        } else {
            session()->flash('error', __('Menu not found.'));
        }
    }

    public function generate_parent_route($id){
        $menu = Menu::find($id);
    
        $route = "    Route::get('/".$menu->role."/".$menu->url."', ".ucfirst($menu->role).ucfirst($menu->url)."::class);";
        $file = base_path('routes/web.php');
        $contents = file_get_contents($file);
        $contents = str_replace("    /* End-Auto-Route */", $route . "\n    /* End-Auto-Route */", $contents);
        file_put_contents($file, $contents);
    
        $controller = "use App\Livewire\\" . ucfirst($menu->role) . ucfirst($menu->url) . ";";
        $contents = file_get_contents($file);
        $contents = str_replace("/* Auto-Controller */", $controller . "\n/* Auto-Controller */", $contents);
        file_put_contents($file, $contents);
    }

    public function generate_parent_controller($id){
        $menu = Menu::find($id);

        $controller = "<?php";
        $controller .= "\n\nnamespace App\Livewire;";
        $controller .= "\n\nuse Livewire\Component;";
        $controller .= "\n\nclass ".ucfirst($menu->role).ucfirst($menu->url)." extends Component";
        $controller .= "\n{";
        $controller .= "\n    public function render()";
        $controller .= "\n    {";
        $controller .= "\n        return view('livewire.".strtolower($menu->role)."-".strtolower($menu->url)."')";
        $controller .= "\n        ->layout('layouts.".strtolower($menu->role)."');";
        $controller .= "\n    }";
        $controller .= "\n}";

        $file = app_path('Livewire/'.ucfirst($menu->role).ucfirst($menu->url).'.php');
        file_put_contents($file, $controller);
    }

    public function generate_parent_page($id){
        $menu = Menu::find($id);

        $page = "@section('css')";
        $page .= "\n@endsection";
        $page .= "\n<div class=\"page-body px-xl-3\">";
        $page .= "\n    <div class=\"container-xl\">";
        $page .= "\n        @include('inc.component.message')";
        $page .= "\n        <div class=\"row\">";
        $page .= "\n            <div class=\"col-xl-6 mb-2\">";
        $page .= "\n                <div class=\"card\">";
        $page .= "\n                    <div class=\"card-body\">";
        $page .= "\n                        <h4 class=\"card-title\">".ucfirst($menu->role)." ".ucfirst($menu->url)."</h4>";
        $page .= "\n                    </div>";
        $page .= "\n                </div>";
        $page .= "\n            </div>";
        $page .= "\n        </div>";
        $page .= "\n    </div>";
        $page .= "\n</div>";
        $page .= "\n@section('js')";
        $page .= "\n@endsection";        

        $file = resource_path('views/livewire/'.strtolower($menu->role).'-'.strtolower($menu->url).'.blade.php');
        file_put_contents($file, $page);
    }

    public function generate_child_route($id){
        $menu = Menu::find($id);
        $parent = Menu::find($menu->menu_id);

        $route = "    Route::get('/".$parent->role."/".$parent->url."/".$menu->url."', ".ucfirst($parent->role).ucfirst($parent->url).ucfirst($menu->url)."::class);";
        $file = base_path('routes/web.php');
        $contents = file_get_contents($file);
        $contents = str_replace("    /* End-Auto-Route */", $route . "\n    /* End-Auto-Route */", $contents);
        file_put_contents($file, $contents);

        $controller = "use App\Livewire\\" . ucfirst($parent->role) . ucfirst($parent->url) . ucfirst($menu->url) . ";";
        $contents = file_get_contents($file);
        $contents = str_replace("/* Auto-Controller */", $controller . "\n/* Auto-Controller */", $contents);
        file_put_contents($file, $contents);
    }

    public function generate_child_controller($id){
        $menu = Menu::find($id);
        $parent = Menu::find($menu->menu_id);

        $controller = "<?php";
        $controller .= "\n\nnamespace App\Livewire;";
        $controller .= "\n\nuse Livewire\Component;";
        $controller .= "\n\nclass ".ucfirst($parent->role).ucfirst($parent->url).ucfirst($menu->url)." extends Component";
        $controller .= "\n{";
        $controller .= "\n    public function render()";
        $controller .= "\n    {";
        $controller .= "\n        return view('livewire.".strtolower($parent->role)."-".strtolower($parent->url)."-".strtolower($menu->url)."')";
        $controller .= "\n        ->layout('layouts.".strtolower($parent->role)."');";
        $controller .= "\n    }";
        $controller .= "\n}";

        $file = app_path('Livewire/'.ucfirst($parent->role).ucfirst($parent->url).ucfirst($menu->url).'.php');
        file_put_contents($file, $controller);
    }

    public function generate_child_page($id){
        $menu = Menu::find($id);
        $parent = Menu::find($menu->menu_id);

        $page = "@section('css')";
        $page .= "\n@endsection";
        $page .= "\n<div class=\"page-body px-xl-3\">";
        $page .= "\n    <div class=\"container-xl\">";
        $page .= "\n        @include('inc.component.message')";
        $page .= "\n        <div class=\"row\">";
        $page .= "\n            <div class=\"col-xl-6 mb-2\">";
        $page .= "\n                <div class=\"card\">";
        $page .= "\n                    <div class=\"card-body\">";
        $page .= "\n                        <h4 class=\"card-title\">".ucfirst($parent->role)." ".ucfirst($parent->url)." ".ucfirst($menu->url)."</h4>";
        $page .= "\n                    </div>";
        $page .= "\n                </div>";
        $page .= "\n            </div>";
        $page .= "\n        </div>";
        $page .= "\n    </div>";
        $page .= "\n</div>";
        $page .= "\n@section('js')";
        $page .= "\n@endsection";
        $file = resource_path('views/livewire/'.strtolower($parent->role).'-'.strtolower($parent->url).'-'.strtolower($menu->url).'.blade.php');
        file_put_contents($file, $page);
    }

    public function render()
    {
        $parents = Menu::where('menu_id', 0)
            ->orderBy('order')
            ->get();

        $parent = Menu::find($this->parent_id);

        $children = Menu::where('menu_id', '=', $this->parent_id)
            ->where('menu_id', '!=', 0)
            ->where('type', '!=', 'group')
            ->orderBy('order')
            ->get();

        return view('livewire.admin-development-menu')
        ->layout('layouts.admin')
        ->with([
            'parents' => $parents,
            'parent' => $parent,
            'children' => $children,
        ]);
    }
}
