<?php

namespace App\Http\Requests;

use App\Models\Post;
use App\Traits\PostResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostsRequest extends FormRequest
{
    use PostResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updatePost', Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'slug' => 'string',
            'is_approved' => 'boolean',
            'preview' => 'string|max:255',
            'detail' => 'string',
        ];
    }
}
