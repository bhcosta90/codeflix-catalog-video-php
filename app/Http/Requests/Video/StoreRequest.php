<?php

namespace App\Http\Requests\Video;

use Core\Video\Domain\Enum\Rating;
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
            'description' => 'required',
            'opened' => 'required|boolean',
            'duration' => 'required|numeric|min:1',
            'year_launched' => 'required|numeric|min:1',
            'video_file' => 'nullable|mimetypes:video/mp4',
            'trailer_file' => 'nullable|mimetypes:video/mp4',
            'banner_file' => 'nullable|image',
            'thumb_file' => 'nullable|image',
            'thumb_half' => 'nullable|image',
            'rating' => 'required|in:' . implode(',', array_column(Rating::cases(), 'value')),
            'categories' => 'nullable|array|exists:categories,id,deleted_at,NULL',
            'cast_members' => 'nullable|array|exists:cast_members,id,deleted_at,NULL',
            'genres' => 'nullable|array|exists:genres,id,deleted_at,NULL',
        ];
    }
}
