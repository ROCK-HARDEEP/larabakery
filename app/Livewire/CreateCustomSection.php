<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CustomSection;

class CreateCustomSection extends Component
{
    use WithFileUploads;

    public $title;
    public $subtitle;
    public $content;
    public $image;
    public $button_text;
    public $button_link;
    public $button_style = 'btn-primary';
    public $background_color = '#ffffff';
    public $text_color = '#000000';
    public $layout = 'default';
    public $page = 'home';
    public $position = 0;
    public $is_active = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        'content' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
        'button_text' => 'nullable|string|max:255',
        'button_link' => 'nullable|url',
        'button_style' => 'nullable|string|max:255',
        'background_color' => 'nullable|string|max:7',
        'text_color' => 'nullable|string|max:7',
        'layout' => 'required|string|in:default,left-image,right-image,hero',
        'page' => 'required|string|in:home,about,contact',
        'position' => 'required|integer|min:0',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'content' => $this->content,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'button_style' => $this->button_style,
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'layout' => $this->layout,
            'page' => $this->page,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('custom-sections', 'public');
        }

        CustomSection::create($data);

        session()->flash('message', 'Custom section created successfully.');
        
        $this->reset();
    }

    public function render()
    {
        return view('livewire.create-custom-section');
    }
}