<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|max:100',
            'description' => 'nullable',
            'opened' => 'required|boolean',
            'duration' => 'required|number|min:1',
            'year_launched' => 'required|number|min:1',
            'video_file' => 'required',
            'trailer_file' => 'required',
            'banner_file' => 'required',
            'thumb_file' => 'required',
            'thumb_half' => 'required',
            'rating' => ['required'],
            'categories' => 'nullable|array|exists:categories,id,deleted_at,NULL',
            'cast_members' => 'nullable|array|exists:cast_members,id,deleted_at,NULL',
            'genres' => 'nullable|array|exists:genres,id,deleted_at,NULL',
        ];
    }
}
