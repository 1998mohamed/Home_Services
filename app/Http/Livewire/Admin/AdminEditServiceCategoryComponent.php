<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Livewire\WithFileUploads;

class AdminEditServiceCategoryComponent extends Component
{
    use WithFileUploads;
    public $category_id;
    public $name;
    public $slug;
    public $image;
    public $newimage;
    public $featured;

    public function mount($category_id)
    {
        $scategory = ServiceCategory::find($category_id);
        $this->category_id = $scategory->id;
        $this->name = $scategory->name;
        $this->slug = $scategory->slug;
        $this->image = $scategory->image;
        $this->featured = $scategory->featured;

    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->name,'-');
    }

    public function updated($fields)
    {
        $this->validateOnly($fields,[
            'name' => 'required',
            'slug' => 'slug'
        ]);

        if($this->newimage)
        {
            $this->validateOnly($fields, [
                'newimage' => 'required|mimes:jpeg,png'
            ]);  
        }
    }

    public function updateServiceCategory()
    {
        $this->validate(
            [
            'name' => 'required',
            'slug' => 'required'
        ]);

        if($this->newimage)
        {
            $this->validate([
                'newimage' => 'required|mimes:jpeg,png'
            ]);  
        }
    
        $scategories = ServiceCategory::find($this->category_id);
        $scategories->name = $this->name;
        $scategories->slug = $this->slug;
        if($this->newimage)
        {
            $imageName = Carbon::now()->timestamp . '-' . $this->newimage->extension();
            $this->newimage->storeAs('categories',$imageName);
            $scategories->image = $imageName; 
        }
        $scategories->featured = $this->featured;
        $scategories->save();
            session()->flash('message','Category has been updated Successfully');
    }

    public function render()
    {
        return view('livewire.admin.admin-edit-service-category-component')
        ->layout('layouts.base');
    }
}
